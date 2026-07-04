<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\FacebookPixelSetting;
use App\Services\FacebookPixelService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Sends a single event to Meta Conversions API without blocking the HTTP request.
 */
class SendFacebookCapiEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    /** @var array<int, int> */
    public array $backoff = [30, 120, 300];

    /**
     * @param  array<string, mixed>  $customData
     * @param  array<string, mixed>  $userData
     * @param  array<string, mixed>  $context
     */
    public function __construct(
        public int $settingsId,
        public string $eventName,
        public string $eventId,
        public array $customData = [],
        public array $userData = [],
        public array $context = [],
    ) {
        $this->onQueue(config('facebook-pixel.queue', 'default'));
    }

    public function handle(FacebookPixelService $pixelService): void
    {
        $settings = FacebookPixelSetting::query()->find($this->settingsId);

        if (! $settings || ! $settings->is_enabled || ! $settings->capi_enabled) {
            return;
        }

        if (! $settings->isEventEnabled($this->eventName)) {
            return;
        }

        $pixelService->sendCapiEvent(
            $settings,
            $this->eventName,
            $this->eventId,
            $this->customData,
            $this->userData,
            $this->context,
        );
    }
}
