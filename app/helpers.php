<?php

use App\Services\SettingsService;

if (! function_exists('setting')) {
    /**
     * Get a setting value with fallback: brand-specific → global → default.
     */
    function setting(string $key, $default = null, ?int $brandId = null)
    {
        return app(SettingsService::class)->get($key, $default, $brandId);
    }
}
