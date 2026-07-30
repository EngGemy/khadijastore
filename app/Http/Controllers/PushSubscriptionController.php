<?php

namespace App\Http\Controllers;

use App\Models\PushSubscription;
use App\Services\WebPushService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PushSubscriptionController extends Controller
{
    public function vapidPublicKey(WebPushService $webPush): JsonResponse
    {
        if (! $webPush->isConfigured()) {
            return response()->json([
                'ok' => false,
                'message' => 'Web Push is not configured',
            ], 503);
        }

        return response()->json([
            'ok' => true,
            'publicKey' => $webPush->publicKey(),
        ]);
    }

    public function subscribe(Request $request): JsonResponse
    {
        $data = $request->validate([
            'endpoint' => ['required', 'string', 'max:2000'],
            'keys.p256dh' => ['required', 'string', 'max:255'],
            'keys.auth' => ['required', 'string', 'max:255'],
            'contentEncoding' => ['nullable', 'string', 'max:32'],
            'customer_phone' => ['nullable', 'string', 'max:32'],
            'brand_id' => ['nullable', 'integer', 'exists:brands,id'],
        ]);

        $phone = isset($data['customer_phone'])
            ? preg_replace('/\D/', '', $data['customer_phone'])
            : null;

        $sub = PushSubscription::query()->updateOrCreate(
            ['endpoint_hash' => PushSubscription::hashEndpoint($data['endpoint'])],
            [
                'endpoint' => $data['endpoint'],
                'public_key' => $data['keys']['p256dh'],
                'auth_token' => $data['keys']['auth'],
                'content_encoding' => $data['contentEncoding'] ?? 'aesgcm',
                'customer_phone' => $phone ?: null,
                'brand_id' => $data['brand_id'] ?? null,
                'user_id' => $request->user()?->id,
                'last_seen_at' => now(),
                'user_agent' => substr((string) $request->userAgent(), 0, 512) ?: null,
            ]
        );

        // If phone was previously saved in session (post-order), attach it
        if (! $sub->customer_phone && session('last_order_phone')) {
            $sessionPhone = preg_replace('/\D/', '', (string) session('last_order_phone'));
            if ($sessionPhone) {
                $sub->update(['customer_phone' => $sessionPhone]);
            }
        }

        return response()->json([
            'ok' => true,
            'id' => $sub->id,
        ]);
    }

    public function unsubscribe(Request $request): JsonResponse
    {
        $data = $request->validate([
            'endpoint' => ['required', 'string', 'max:2000'],
        ]);

        PushSubscription::query()
            ->where('endpoint_hash', PushSubscription::hashEndpoint($data['endpoint']))
            ->delete();

        return response()->json(['ok' => true]);
    }

    public function attachPhone(Request $request): JsonResponse
    {
        $data = $request->validate([
            'endpoint' => ['required', 'string', 'max:2000'],
            'customer_phone' => ['required', 'string', 'max:32'],
        ]);

        $phone = preg_replace('/\D/', '', $data['customer_phone']) ?: '';
        if ($phone === '') {
            return response()->json(['ok' => false, 'message' => 'رقم غير صالح'], 422);
        }

        $sub = PushSubscription::query()
            ->where('endpoint_hash', PushSubscription::hashEndpoint($data['endpoint']))
            ->first();
        if (! $sub) {
            return response()->json(['ok' => false, 'message' => 'الاشتراك غير موجود'], 404);
        }

        $sub->update([
            'customer_phone' => $phone,
            'last_seen_at' => now(),
        ]);

        session(['last_order_phone' => $phone]);

        return response()->json(['ok' => true]);
    }
}
