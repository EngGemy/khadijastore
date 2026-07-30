<?php

namespace App\Services;

use App\Models\PushSubscription;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class WebPushService
{
    public function isConfigured(): bool
    {
        return (bool) config('webpush.enabled')
            && filled(config('webpush.vapid.public_key'))
            && filled(config('webpush.vapid.private_key'));
    }

    public function publicKey(): ?string
    {
        $key = config('webpush.vapid.public_key');

        return filled($key) ? (string) $key : null;
    }

    /**
     * @param  array{title:string,body?:string,url?:string,icon?:string,tag?:string}  $payload
     * @return array{sent:int,failed:int,expired:int}
     */
    public function sendToSubscriptions(iterable $subscriptions, array $payload): array
    {
        $sent = 0;
        $failed = 0;
        $expired = 0;

        if (! $this->isConfigured()) {
            Log::warning('WebPush skipped: VAPID keys not configured');

            return compact('sent', 'failed', 'expired');
        }

        $webPush = $this->client();
        $json = json_encode([
            'title' => $payload['title'] ?? 'متجر العلامات',
            'body' => $payload['body'] ?? '',
            'url' => $payload['url'] ?? '/',
            'icon' => $payload['icon'] ?? '/favicon.ico',
            'tag' => $payload['tag'] ?? 'alamat-shop',
        ], JSON_UNESCAPED_UNICODE);

        foreach ($subscriptions as $sub) {
            if (! $sub instanceof PushSubscription) {
                continue;
            }

            try {
                $subscription = Subscription::create([
                    'endpoint' => $sub->endpoint,
                    'publicKey' => $sub->public_key,
                    'authToken' => $sub->auth_token,
                    'contentEncoding' => $sub->content_encoding ?: 'aesgcm',
                ]);
                $webPush->queueNotification($subscription, $json);
            } catch (\Throwable $e) {
                $failed++;
                Log::warning('WebPush queue failed', ['id' => $sub->id, 'error' => $e->getMessage()]);
            }
        }

        foreach ($webPush->flush() as $report) {
            if ($report->isSuccess()) {
                $sent++;
                continue;
            }

            $failed++;
            $endpoint = $report->getRequest()?->getUri()?->__toString();
            if ($report->isSubscriptionExpired() && $endpoint) {
                $expired++;
                PushSubscription::query()
                    ->where(function ($q) use ($endpoint) {
                        $q->where('endpoint_hash', PushSubscription::hashEndpoint($endpoint))
                            ->orWhere('endpoint', $endpoint);
                    })
                    ->delete();
            }
        }

        return compact('sent', 'failed', 'expired');
    }

    /**
     * @param  array{title:string,body?:string,url?:string,icon?:string,tag?:string}  $payload
     * @return array{sent:int,failed:int,expired:int}
     */
    public function sendToPhone(string $phone, array $payload, ?int $brandId = null): array
    {
        $digits = preg_replace('/\D/', '', $phone) ?: '';
        if ($digits === '') {
            return ['sent' => 0, 'failed' => 0, 'expired' => 0];
        }

        $query = PushSubscription::query()->where('customer_phone', $digits);
        if ($brandId) {
            $query->where(function ($q) use ($brandId) {
                $q->whereNull('brand_id')->orWhere('brand_id', $brandId);
            });
        }

        return $this->sendToSubscriptions($query->get(), $payload);
    }

    /**
     * @param  array{title:string,body?:string,url?:string,icon?:string,tag?:string}  $payload
     * @return array{sent:int,failed:int,expired:int}
     */
    public function broadcast(array $payload, ?int $brandId = null): array
    {
        $query = PushSubscription::query();
        if ($brandId) {
            $query->where(function ($q) use ($brandId) {
                $q->whereNull('brand_id')->orWhere('brand_id', $brandId);
            });
        }

        /** @var Collection<int, PushSubscription> $subs */
        $subs = $query->get();

        return $this->sendToSubscriptions($subs, $payload);
    }

    protected function client(): WebPush
    {
        $subject = (string) config('webpush.vapid.subject');
        if (! str_starts_with($subject, 'mailto:') && ! str_starts_with($subject, 'https://')) {
            $subject = 'mailto:'.$subject;
        }

        return new WebPush([
            'VAPID' => [
                'subject' => $subject,
                'publicKey' => config('webpush.vapid.public_key'),
                'privateKey' => config('webpush.vapid.private_key'),
            ],
        ]);
    }
}
