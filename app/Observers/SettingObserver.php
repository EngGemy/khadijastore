<?php

namespace App\Observers;

use App\Models\Setting;
use App\Services\PublicStoragePublisher;
use App\Services\SettingsService;

class SettingObserver
{
    public function __construct(private readonly SettingsService $settings) {}

    public function saved(Setting $setting): void
    {
        $this->settings->bustCache($setting->brand_id);

        if ($setting->key === 'store.logo' && filled($setting->value)) {
            $path = is_array($setting->value) ? ($setting->value[0] ?? null) : $setting->value;
            if (is_string($path)) {
                PublicStoragePublisher::publishPath($path);
            }
        }
    }

    public function deleted(Setting $setting): void
    {
        $this->settings->bustCache($setting->brand_id);
    }
}
