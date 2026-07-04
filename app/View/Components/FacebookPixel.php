<?php

declare(strict_types=1);

namespace App\View\Components;

use App\Services\FacebookPixelService;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Injects Meta Pixel base script and queued browser events for a brand store.
 */
class FacebookPixel extends Component
{
    public function __construct(
        public ?int $brandId = null,
        public ?string $pageViewEventId = null,
    ) {}

    public function render(): View|Closure|string
    {
        $service = app(FacebookPixelService::class);
        $settings = $service->forBrand($this->brandId);

        return view('components.facebook-pixel', [
            'settings' => $settings,
            'browserEvents' => $service->consumeBrowserEvents(),
            'requireConsent' => (bool) config('facebook-pixel.require_consent', false),
            'consentCookie' => config('facebook-pixel.consent_cookie_name', 'marketing_consent'),
            'consentValue' => (string) config('facebook-pixel.consent_cookie_value', '1'),
        ]);
    }
}
