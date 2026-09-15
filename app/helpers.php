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
            'home.blocks.resolved.v4',
            'home.blocks.resolved.v5',
            'home.products.v2',
            'home.products.v3',
            'home.offers.v1',
            'home.offers.v2',
            'home.featured_brand.v1',
            'home.section_banners.v1',
            'home.store_shelves.v1',
            'home.page.data',
            'home.directory.data',
            'nav.directory.counts',
            'nav.brands',
            'featured.storefront.brand',
        ] as $key) {
            Cache::forget($key);
        }
    }
}

if (! function_exists('home_store_shelves')) {
    /**
     * Group storefront products into brand shelves (featured brand first).
     *
     * @param  \Illuminate\Support\Collection<int, \App\Models\Product>  $products
     * @return \Illuminate\Support\Collection<int, object{brand: \App\Models\Brand, products: \Illuminate\Support\Collection}>
     */
    function home_store_shelves(\Illuminate\Support\Collection $products, int $perShelf = 10): \Illuminate\Support\Collection
    {
        $featuredId = featured_storefront_brand()?->id;

        $grouped = $products
            ->filter(fn ($p) => $p->brand !== null)
            ->groupBy('brand_id');

        $shelves = $grouped->map(function (\Illuminate\Support\Collection $group) use ($perShelf) {
            $brand = $group->first()->brand;
            $sorted = $group
                ->sortBy([
                    fn ($p) => $p->is_featured ? 0 : 1,
                    fn ($p) => -((int) ($p->sales_count ?? 0)),
                    fn ($p) => (int) ($p->sort ?? 0),
                ])
                ->values()
                ->take($perShelf);

            return (object) [
                'brand' => $brand,
                'products' => $sorted,
                'count' => $group->count(),
            ];
        })->values();

        return $shelves->sortBy(function ($shelf) use ($featuredId) {
            if ($featuredId && (int) $shelf->brand->id === (int) $featuredId) {
                return [0, 0];
            }

            return [1, -((int) ($shelf->brand->products_count ?? $shelf->count))];
        })->values();
    }
}

if (! function_exists('featured_storefront_brand_slug')) {
    /**
     * Slug of the elevated marketplace brand (سند للعطارة by default).
     */
    function featured_storefront_brand_slug(): string
    {
        $fromSetting = setting('store.featured_brand_slug', null);

        if (is_string($fromSetting) && $fromSetting !== '') {
            return $fromSetting;
        }

        return (string) config('store.featured_brand_slug', 'attar');
    }
}

if (! function_exists('featured_storefront_brand')) {
    /**
     * Active featured brand model, or null when missing/inactive.
     */
    function featured_storefront_brand(): ?\App\Models\Brand
    {
        $slug = featured_storefront_brand_slug();

        if ($slug === '') {
            return null;
        }

        return Cache::remember('featured.storefront.brand', 600, function () use ($slug) {
            return \App\Models\Brand::query()
                ->where('slug', $slug)
                ->where('is_active', true)
                ->first();
        });
    }
}

if (! function_exists('prioritize_featured_brand')) {
    /**
     * Sort a brand collection with the featured brand first (others keep relative order).
     *
     * @param  \Illuminate\Support\Collection<int, \App\Models\Brand>  $brands
     * @return \Illuminate\Support\Collection<int, \App\Models\Brand>
     */
    function prioritize_featured_brand(\Illuminate\Support\Collection $brands): \Illuminate\Support\Collection
    {
        $featuredId = featured_storefront_brand()?->id;

        if (! $featuredId) {
            return $brands->values();
        }

        return $brands
            ->sortBy(fn (\App\Models\Brand $b) => (int) $b->id === (int) $featuredId ? 0 : 1)
            ->values();
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
            return prioritize_featured_brand(
                \App\Models\Brand::query()
                    ->where('is_active', true)
                    ->orderBy('name')
                    ->get(['id', 'name', 'slug', 'mark', 'logo_path'])
            );
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

if (! function_exists('media_public_url')) {
    /**
     * Spatie media URL that LiteSpeed can serve (real public/storage file).
     * Prefers conversion when published; falls back to original; auto-publishes if needed.
     */
    function media_public_url(?\Spatie\MediaLibrary\MediaCollections\Models\Media $media, ?string $conversion = null): ?string
    {
        if (! $media) {
            return null;
        }

        $try = function (?string $conv) use ($media): ?string {
            if ($conv !== null && $conv !== '' && ! $media->hasGeneratedConversion($conv)) {
                return null;
            }

            $relative = ltrim(str_replace('\\', '/', $media->getPathRelativeToRoot($conv ?? '')), '/');
            if ($relative === '') {
                return null;
            }

            $public = public_path('storage/'.$relative);
            if (is_file($public)) {
                return ($conv !== null && $conv !== '') ? $media->getUrl($conv) : $media->getUrl();
            }

            $source = storage_path('app/public/'.$relative);
            if (is_file($source)) {
                \App\Services\PublicStoragePublisher::publishPath($relative);

                if (is_file(public_path('storage/'.$relative))) {
                    return ($conv !== null && $conv !== '') ? $media->getUrl($conv) : $media->getUrl();
                }
            }

            return null;
        };

        if ($conversion) {
            $url = $try($conversion);
            if ($url) {
                return $url;
            }
        }

        return $try('');
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
            return media_public_url($media, $thumb ? 'thumb' : null);
        }

        if (filled($brand->logo_path)) {
            return asset('storage/'.ltrim($brand->logo_path, '/'));
        }

        return null;
    }
}

if (! function_exists('category_banner_url')) {
    /**
     * Public URL for a category/department promo banner.
     */
    function category_banner_url(?\App\Models\Category $category, bool $wide = true): ?string
    {
        return $category?->bannerUrl($wide);
    }
}

if (! function_exists('product_cover_url')) {
    /**
     * Public URL for a product cover (thumb/large with original fallback).
     *
     * @param  bool|string  $conversion  true/'thumb', 'large', or false for original
     */
    function product_cover_url(?\App\Models\Product $product, bool|string $conversion = true): ?string
    {
        if (! $product) {
            return null;
        }

        $media = $product->getFirstMedia('cover');
        if (! $media) {
            return null;
        }

        $name = match (true) {
            $conversion === true, $conversion === 'thumb' => 'thumb',
            $conversion === 'large' => 'large',
            default => null,
        };

        return media_public_url($media, $name);
    }
}
