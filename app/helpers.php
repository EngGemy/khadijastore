<?php

use App\Services\SettingsService;
use Illuminate\Support\Facades\Cache;

if (! function_exists('setting')) {
    /**
     * Get a setting value with fallback: brand-specific → global → default.
     */
    function setting(string $key, $default = null, ?int $brandId = null)
    {
        return app(SettingsService::class)->get($key, $default, $brandId);
    }
}

if (! function_exists('forget_home_blocks_cache')) {
    function forget_home_blocks_cache(): void
    {
        foreach (['home.blocks.resolved', 'home.blocks.resolved.v2', 'home.products.v2'] as $key) {
            Cache::forget($key);
        }
    }
}

if (! function_exists('brand_page_url')) {
    function brand_page_url(string $slug): string
    {
        return route('brand.show', $slug);
    }
}

if (! function_exists('store_logo_url')) {
    /**
     * Public URL for the global store logo (settings → store.logo).
     */
    function store_logo_url(?int $brandId = null): ?string
    {
        $path = setting('store.logo', null, $brandId);

        if (is_array($path)) {
            $path = $path[0] ?? null;
        }

        if (! is_string($path) || $path === '') {
            return null;
        }

        return asset('storage/'.ltrim($path, '/'));
    }
}
