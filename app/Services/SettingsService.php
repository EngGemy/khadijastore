<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    private const CACHE_TTL = 3600; // 1 hour

    private function cacheKey(?int $brandId): string
    {
        return 'settings.all.'.($brandId ?? 'global');
    }

    private function keyCacheKey(string $key, ?int $brandId): string
    {
        return 'settings.key.'.($brandId ?? 'global').'.'.$key;
    }

    public function get(string $key, $default = null, ?int $brandId = null)
    {
        $cacheKey = $this->keyCacheKey($key, $brandId);

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($key, $default, $brandId) {
            // 1) Try brand-specific
            if ($brandId !== null) {
                $brandSetting = Setting::where('brand_id', $brandId)->where('key', $key)->first();
                if ($brandSetting && $brandSetting->value !== null) {
                    return $brandSetting->value;
                }
            }

            // 2) Fallback to global
            $globalSetting = Setting::whereNull('brand_id')->where('key', $key)->first();
            if ($globalSetting && $globalSetting->value !== null) {
                return $globalSetting->value;
            }

            return $default;
        });
    }

    public function set(string $key, $value, ?int $brandId = null): void
    {
        Setting::updateOrCreate(
            ['brand_id' => $brandId, 'key' => $key],
            ['value' => $value]
        );

        $this->bustCache($brandId);
    }

    public function all(?int $brandId = null): array
    {
        $cacheKey = $this->cacheKey($brandId);

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($brandId) {
            // Start with global settings
            $global = Setting::whereNull('brand_id')->pluck('value', 'key')->toArray();

            // Merge brand overrides
            if ($brandId !== null) {
                $brand = Setting::where('brand_id', $brandId)->pluck('value', 'key')->toArray();
                return array_merge($global, $brand);
            }

            return $global;
        });
    }

    public function bustCache(?int $brandId = null): void
    {
        Cache::forget($this->cacheKey($brandId));
        Cache::forget($this->cacheKey(null)); // global

        // Also bust individual key caches for this brand + global
        $keys = Setting::where('brand_id', $brandId)->orWhereNull('brand_id')->pluck('key')->unique();
        foreach ($keys as $key) {
            Cache::forget($this->keyCacheKey($key, $brandId));
            Cache::forget($this->keyCacheKey($key, null));
        }
    }
}
