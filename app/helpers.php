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
    /**
     * Drop every homepage cache key so brand/product/home-block edits show immediately.
     */
    function forget_home_blocks_cache(): void
    {
        foreach ([
            'home.blocks.resolved',
            'home.blocks.resolved.v2',
            'home.blocks.resolved.v3',
            'home.products.v2',
            'home.page.data',
            'home.directory.data',
            'nav.directory.counts',
            'nav.brands',
        ] as $key) {
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

if (! function_exists('nav_directory_counts')) {
    /**
     * @return array{doctorCount: int, nurseryCount: int}
     */
    function nav_directory_counts(): array
    {
        return Cache::remember('nav.directory.counts', 600, function () {
            $doctor = \App\Models\Listing::withoutGlobalScopes()
                ->where('type', 'doctor')
                ->where('is_active', true)
                ->count();

            $nursery = \App\Models\Listing::withoutGlobalScopes()
                ->where('type', 'nursery')
                ->where('is_active', true)
                ->count();

            return [
                'doctorCount' => $doctor,
                'nurseryCount' => $nursery,
            ];
        });
    }
}

if (! function_exists('nav_active_brands')) {
    function nav_active_brands(): \Illuminate\Support\Collection
    {
        return Cache::remember('nav.brands', 600, function () {
            return \App\Models\Brand::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'slug', 'mark']);
        });
    }
}

if (! function_exists('nav_home_section_url')) {
    /**
     * Homepage section anchor when content exists, otherwise a full-page fallback.
     */
    function nav_home_section_url(string $section, ?string $fallbackRoute = null): string
    {
        $counts = nav_directory_counts();

        $onHomepage = match ($section) {
            'doctors' => $counts['doctorCount'] > 0,
            'nurseries' => $counts['nurseryCount'] > 0,
            default => true,
        };

        if ($onHomepage) {
            return route('home').'#'.$section;
        }

        return $fallbackRoute ?? route('home').'#'.$section;
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

if (! function_exists('brand_logo_url')) {
    /**
     * Public URL for a brand logo (Spatie media → logo_path fallback).
     */
    function brand_logo_url(?\App\Models\Brand $brand, bool $thumb = false): ?string
    {
        if (! $brand) {
            return null;
        }

        $url = $thumb
            ? $brand->getFirstMediaUrl('logo', 'thumb')
            : $brand->getFirstMediaUrl('logo');

        if (is_string($url) && $url !== '') {
            return $url;
        }

        if (filled($brand->logo_path)) {
            return asset('storage/'.ltrim($brand->logo_path, '/'));
        }

        return null;
    }
}
