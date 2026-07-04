<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\TrackFacebookPixelEventRequest;
use App\Services\FacebookPixelService;
use Illuminate\Http\JsonResponse;

/**
 * Lightweight endpoint for browser-initiated pixel events with CAPI deduplication.
 */
class FacebookPixelTrackController extends Controller
{
    public function __construct(
        private readonly FacebookPixelService $facebookPixel,
    ) {}

    public function store(TrackFacebookPixelEventRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $fbPixel = $this->facebookPixel->track(
            $validated['event_name'],
            $validated['custom_data'] ?? [],
            (int) $validated['brand_id'],
            $validated['user_data'] ?? [],
            $request,
            queueBrowser: false,
        );

        return response()->json([
            'fb_pixel' => $fbPixel,
        ]);
    }
}
