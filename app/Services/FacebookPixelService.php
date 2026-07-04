<?php

declare(strict_types=1);

namespace App\Services;

use App\Jobs\SendFacebookCapiEvent;
use App\Models\FacebookPixelSetting;
use FacebookAds\Api;
use FacebookAds\Object\ServerSide\ActionSource;
use FacebookAds\Object\ServerSide\CustomData;
use FacebookAds\Object\ServerSide\Event;
use FacebookAds\Object\ServerSide\EventRequest;
use FacebookAds\Object\ServerSide\UserData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Browser Pixel + Conversions API orchestration with shared event_id deduplication.
 */
class FacebookPixelService
{
    /** @var list<array{event_name: string, event_id: string, params: array<string, mixed>}> */
    private array $browserEvents = [];

    /**
     * Resolve active pixel settings for a brand (store tenant).
     */
    public function forBrand(?int $brandId): ?FacebookPixelSetting
    {
        if (! $brandId || ! config('facebook-pixel.enabled', true)) {
            return null;
        }

        return FacebookPixelSetting::query()
            ->where('brand_id', $brandId)
            ->where('is_enabled', true)
            ->first();
    }

    /**
     * Track an event on browser (queued script) and CAPI (queued job) with the same event_id.
     *
     * @param  array<string, mixed>  $customData
     * @param  array<string, mixed>  $userData  Raw PII — hashed before CAPI dispatch.
     * @return array{event_id: string, event_name: string, params: array<string, mixed>}|null
     */
    public function track(
        string $eventName,
        array $customData,
        int $brandId,
        array $userData = [],
        ?Request $request = null,
        ?string $eventId = null,
        ?string $currency = null,
        bool $queueBrowser = true,
    ): ?array {
        $settings = $this->forBrand($brandId);

        if (! $settings || ! $settings->isEventEnabled($eventName)) {
            return null;
        }

        $request ??= request();

        if (! $this->hasConsent($request)) {
            return null;
        }

        $eventId ??= (string) Str::uuid();
        $params = $this->normalizeCommerceParams($customData, $currency);
        $context = $this->buildRequestContext($request);

        if ($queueBrowser) {
            $this->queueBrowserEvent($eventName, $eventId, $params);
        }

        if ($settings->capi_enabled) {
            SendFacebookCapiEvent::dispatch(
                $settings->id,
                $eventName,
                $eventId,
                $params,
                $userData,
                $context,
            );
        }

        return [
            'event_id' => $eventId,
            'event_name' => $eventName,
            'params' => $params,
        ];
    }

    /**
     * Queue a browser-side fbq() call rendered by the Blade component / directive.
     *
     * @param  array<string, mixed>  $params
     */
    public function queueBrowserEvent(string $eventName, string $eventId, array $params = []): void
    {
        $this->browserEvents[] = [
            'event_name' => $eventName,
            'event_id' => $eventId,
            'params' => $params,
        ];
    }

    /**
     * @return list<array{event_name: string, event_id: string, params: array<string, mixed>}>
     */
    public function consumeBrowserEvents(): array
    {
        $events = $this->browserEvents;
        $this->browserEvents = [];

        return $events;
    }

    /**
     * Render inline script for @fbPixelEvent directive output.
     *
     * @param  array<string, mixed>  $params
     */
    public function renderBrowserEventScript(string $eventName, array $params = [], ?string $eventId = null): string
    {
        $eventId ??= (string) Str::uuid();
        $encodedParams = json_encode($params, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP);
        $encodedId = json_encode($eventId, JSON_UNESCAPED_UNICODE);

        return "<script>window.__fbPixelTrack && window.__fbPixelTrack({$encodedId}, ".json_encode($eventName).", {$encodedParams});</script>";
    }

    /**
     * Send event synchronously to CAPI (called from queued job only).
     *
     * @param  array<string, mixed>  $customData
     * @param  array<string, mixed>  $userData  Raw PII fields.
     * @param  array<string, mixed>  $context  fbp, fbc, ip, user_agent, event_source_url
     */
    public function sendCapiEvent(
        FacebookPixelSetting $settings,
        string $eventName,
        string $eventId,
        array $customData = [],
        array $userData = [],
        array $context = [],
    ): void {
        try {
            Api::init(null, null, $settings->access_token);

            $user = $this->buildUserData($userData, $context);
            $custom = $this->buildCustomData($customData);

            $event = (new Event)
                ->setEventName($eventName)
                ->setEventTime(time())
                ->setEventId($eventId)
                ->setEventSourceUrl($context['event_source_url'] ?? null)
                ->setActionSource(ActionSource::WEBSITE)
                ->setUserData($user)
                ->setCustomData($custom);

            $request = (new EventRequest($settings->pixel_id))
                ->setEvents([$event]);

            if ($settings->test_event_code) {
                $request->setTestEventCode($settings->test_event_code);
            }

            $request->execute();
        } catch (\Throwable $e) {
            Log::warning('Facebook CAPI event failed', [
                'event' => $eventName,
                'event_id' => $eventId,
                'brand_id' => $settings->brand_id,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Verify Pixel ID + access token against Graph API.
     */
    public function validateAccessToken(string $pixelId, string $accessToken): bool
    {
        try {
            $version = config('facebook-pixel.graph_api_version', 'v21.0');
            $timeout = config('facebook-pixel.timeout', 10);

            $response = Http::timeout($timeout)
                ->get("https://graph.facebook.com/{$version}/{$pixelId}", [
                    'fields' => 'id',
                    'access_token' => $accessToken,
                ]);

            return $response->successful() && ($response->json('id') === $pixelId);
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * Upsert per-brand pixel settings (admin).
     *
     * @param  array<string, mixed>  $data
     */
    public function saveSettings(int $brandId, array $data): FacebookPixelSetting
    {
        $settings = FacebookPixelSetting::query()->firstOrNew(['brand_id' => $brandId]);

        if (
            isset($data['access_token'])
            && $data['access_token'] === '********'
            && $settings->exists
        ) {
            unset($data['access_token']);
        }

        $settings->fill($data);
        $settings->brand_id = $brandId;
        $settings->save();

        return $settings;
    }

    public function hasConsent(?Request $request = null): bool
    {
        if (! config('facebook-pixel.require_consent', false)) {
            return true;
        }

        $request ??= request();
        $cookieName = config('facebook-pixel.consent_cookie_name', 'marketing_consent');
        $expected = (string) config('facebook-pixel.consent_cookie_value', '1');

        return $request->cookie($cookieName) === $expected;
    }

    /**
     * @param  array<string, mixed>  $raw
     * @return array<string, string>
     */
    public function hashUserData(array $raw): array
    {
        $hashed = [];

        if (! empty($raw['em'])) {
            $hashed['em'] = $this->hashValue($this->normalizeEmail((string) $raw['em']));
        }

        if (! empty($raw['ph'])) {
            $hashed['ph'] = $this->hashValue($this->normalizePhone((string) $raw['ph']));
        }

        if (! empty($raw['fn'])) {
            $hashed['fn'] = $this->hashValue($this->normalizeName((string) $raw['fn']));
        }

        if (! empty($raw['ln'])) {
            $hashed['ln'] = $this->hashValue($this->normalizeName((string) $raw['ln']));
        }

        if (! empty($raw['ge'])) {
            $gender = strtolower(substr(trim((string) $raw['ge']), 0, 1));
            if (in_array($gender, ['m', 'f'], true)) {
                $hashed['ge'] = $this->hashValue($gender);
            }
        }

        if (! empty($raw['db'])) {
            $hashed['db'] = $this->hashValue($this->normalizeDateOfBirth((string) $raw['db']));
        }

        if (! empty($raw['ct'])) {
            $hashed['ct'] = $this->hashValue($this->normalizeCity((string) $raw['ct']));
        }

        if (! empty($raw['st'])) {
            $hashed['st'] = $this->hashValue($this->normalizeState((string) $raw['st']));
        }

        if (! empty($raw['zp'])) {
            $hashed['zp'] = $this->hashValue($this->normalizeZip((string) $raw['zp']));
        }

        if (! empty($raw['country'])) {
            $hashed['country'] = $this->hashValue(strtolower(trim((string) $raw['country'])));
        }

        if (! empty($raw['external_id'])) {
            $hashed['external_id'] = $this->hashValue(strtolower(trim((string) $raw['external_id'])));
        }

        return $hashed;
    }

    /**
     * @param  array<string, mixed>  $userData
     * @param  array<string, mixed>  $context
     */
    private function buildUserData(array $userData, array $context): UserData
    {
        $hashed = $this->hashUserData($userData);

        $user = new UserData;

        if (isset($hashed['em'])) {
            $user->setEmail($hashed['em']);
        }
        if (isset($hashed['ph'])) {
            $user->setPhone($hashed['ph']);
        }
        if (isset($hashed['fn'])) {
            $user->setFirstName($hashed['fn']);
        }
        if (isset($hashed['ln'])) {
            $user->setLastName($hashed['ln']);
        }
        if (isset($hashed['ge'])) {
            $user->setGender($hashed['ge']);
        }
        if (isset($hashed['db'])) {
            $user->setDateOfBirth($hashed['db']);
        }
        if (isset($hashed['ct'])) {
            $user->setCity($hashed['ct']);
        }
        if (isset($hashed['st'])) {
            $user->setState($hashed['st']);
        }
        if (isset($hashed['zp'])) {
            $user->setZipCode($hashed['zp']);
        }
        if (isset($hashed['country'])) {
            $user->setCountryCode($hashed['country']);
        }
        if (isset($hashed['external_id'])) {
            $user->setExternalId($hashed['external_id']);
        }

        if (! empty($context['fbp'])) {
            $user->setFbp((string) $context['fbp']);
        }
        if (! empty($context['fbc'])) {
            $user->setFbc((string) $context['fbc']);
        }
        if (! empty($context['ip'])) {
            $user->setClientIpAddress((string) $context['ip']);
        }
        if (! empty($context['user_agent'])) {
            $user->setClientUserAgent((string) $context['user_agent']);
        }

        return $user;
    }

    /**
     * @param  array<string, mixed>  $customData
     */
    private function buildCustomData(array $customData): CustomData
    {
        $custom = new CustomData;

        if (isset($customData['value'])) {
            $custom->setValue((float) $customData['value']);
        }
        if (! empty($customData['currency'])) {
            $custom->setCurrency((string) $customData['currency']);
        }
        if (! empty($customData['content_ids'])) {
            $custom->setContentIds(array_map('strval', (array) $customData['content_ids']));
        }
        if (! empty($customData['content_type'])) {
            $custom->setContentType((string) $customData['content_type']);
        }
        if (! empty($customData['content_name'])) {
            $custom->setContentName((string) $customData['content_name']);
        }
        if (isset($customData['num_items'])) {
            $custom->setNumItems((int) $customData['num_items']);
        }
        if (! empty($customData['order_id'])) {
            $custom->setOrderId((string) $customData['order_id']);
        }

        return $custom;
    }

    /**
     * @param  array<string, mixed>  $customData
     * @return array<string, mixed>
     */
    private function normalizeCommerceParams(array $customData, ?string $currency = null): array
    {
        $params = $customData;

        if (! isset($params['currency'])) {
            $params['currency'] = $currency ?? config('facebook-pixel.default_currency', 'EGP');
        }

        if (isset($params['value'])) {
            $params['value'] = round((float) $params['value'], 2);
        }

        if (isset($params['content_ids'])) {
            $params['content_ids'] = array_values(array_map('strval', (array) $params['content_ids']));
        }

        return $params;
    }

    /**
     * @return array<string, mixed>
     */
    private function buildRequestContext(Request $request): array
    {
        return [
            'fbp' => $request->cookie('_fbp'),
            'fbc' => $request->cookie('_fbc'),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'event_source_url' => $request->fullUrl(),
        ];
    }

    private function hashValue(string $value): string
    {
        return hash('sha256', $value);
    }

    private function normalizeEmail(string $email): string
    {
        return strtolower(trim($email));
    }

    private function normalizePhone(string $phone): string
    {
        return preg_replace('/\D+/', '', $phone) ?? '';
    }

    private function normalizeName(string $name): string
    {
        $normalized = strtolower(trim($name));

        return preg_replace('/[^a-z]/', '', $normalized) ?? '';
    }

    private function normalizeDateOfBirth(string $dob): string
    {
        $digits = preg_replace('/\D+/', '', $dob) ?? '';

        return strlen($digits) === 8 ? $digits : $digits;
    }

    private function normalizeCity(string $city): string
    {
        $normalized = strtolower(trim($city));

        return preg_replace('/[^a-z]/', '', $normalized) ?? '';
    }

    private function normalizeState(string $state): string
    {
        return $this->normalizeCity($state);
    }

    private function normalizeZip(string $zip): string
    {
        $normalized = strtolower(trim($zip));

        return preg_replace('/[\s-]/', '', $normalized) ?? '';
    }
}
