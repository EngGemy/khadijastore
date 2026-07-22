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
     * Directory sections always go to dedicated pages.
     * Other sections keep homepage anchors when content exists.
     */
    function nav_home_section_url(string $section, ?string $fallbackRoute = null): string
    {
        if ($section === 'doctors') {
            return route('directory.index', 'doctor');
        }

        if ($section === 'nurseries') {
            return route('directory.index', 'nursery');
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

        $media = $brand->getFirstMedia('logo');

        if ($media) {
            if ($thumb && $media->hasGeneratedConversion('thumb')) {
                $thumbPath = $media->getPath('thumb');
                if (is_string($thumbPath) && file_exists($thumbPath)) {
                    return $media->getUrl('thumb');
                }
            }

            return $media->getUrl();
        }

        if (filled($brand->logo_path)) {
            return asset('storage/'.ltrim($brand->logo_path, '/'));
        }

        return null;
    }
}

if (! function_exists('product_cover_url')) {
    /**
     * Public URL for a product cover (thumb with original fallback).
     */
    function product_cover_url(?\App\Models\Product $product, bool $thumb = true): ?string
    {
        if (! $product) {
            return null;
        }

        $media = $product->getFirstMedia('cover');

        if (! $media) {
            return null;
        }

        if ($thumb && $media->hasGeneratedConversion('thumb')) {
            $thumbPath = $media->getPath('thumb');
            if (is_string($thumbPath) && file_exists($thumbPath)) {
                return $media->getUrl('thumb');
            }
        }

        return $media->getUrl();
    }
}
