<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateFacebookPixelSettingsRequest;
use App\Models\FacebookPixelSetting;
use App\Services\FacebookPixelService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * REST admin API for per-brand Facebook Pixel settings.
 *
 * Used by Filament and external tooling; store owners manage credentials per brand.
 */
class FacebookPixelSettingsController extends Controller
{
    public function __construct(
        private readonly FacebookPixelService $pixelService,
    ) {}

    public function show(Request $request): JsonResponse
    {
        $this->authorizeBrandAccess($request);
        $brandId = $this->resolveBrandId($request);

        $settings = FacebookPixelSetting::query()->where('brand_id', $brandId)->first();

        return response()->json([
            'data' => $settings ? $this->maskSensitive($settings) : null,
        ]);
    }

    public function update(UpdateFacebookPixelSettingsRequest $request): JsonResponse
    {
        $brandId = $request->resolveBrandId();
        $settings = $this->pixelService->saveSettings($brandId, $request->settingsPayload());

        return response()->json([
            'message' => 'تم حفظ إعدادات فيسبوك بكسل بنجاح.',
            'data' => $this->maskSensitive($settings),
        ]);
    }

    public function testToken(Request $request): JsonResponse
    {
        $this->authorizeBrandAccess($request);

        $validated = $request->validate([
            'pixel_id' => ['required', 'string', 'regex:/^\d{10,20}$/'],
            'access_token' => ['required', 'string', 'min:20'],
        ]);

        $valid = $this->pixelService->validateAccessToken(
            $validated['pixel_id'],
            $validated['access_token'],
        );

        return response()->json([
            'valid' => $valid,
            'message' => $valid
                ? 'رمز الوصول صالح ويمكنه الوصول إلى البكسل.'
                : 'فشل التحقق — تأكد من Pixel ID وصلاحيات التوكن.',
        ], $valid ? 200 : 422);
    }

    private function authorizeBrandAccess(Request $request): void
    {
        $user = $request->user();
        abort_unless($user && ($user->isSuperAdmin() || $user->hasRole('brand_admin')), 403);
    }

    private function resolveBrandId(Request $request): int
    {
        if ($request->user()->isSuperAdmin() && $request->filled('brand_id')) {
            return (int) $request->input('brand_id');
        }

        return (int) $request->user()->brand_id;
    }

    /**
     * @return array<string, mixed>
     */
    private function maskSensitive(FacebookPixelSetting $settings): array
    {
        return [
            'brand_id' => $settings->brand_id,
            'pixel_id' => $settings->pixel_id,
            'access_token' => $settings->access_token ? '********' : null,
            'test_event_code' => $settings->test_event_code,
            'is_enabled' => $settings->is_enabled,
            'capi_enabled' => $settings->capi_enabled,
            'track_pageview' => $settings->track_pageview,
            'track_viewcontent' => $settings->track_viewcontent,
            'track_addtocart' => $settings->track_addtocart,
            'track_initiatecheckout' => $settings->track_initiatecheckout,
            'track_purchase' => $settings->track_purchase,
            'track_lead' => $settings->track_lead,
        ];
    }
}
