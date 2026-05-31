<?php

namespace App\Observers;

use App\Models\Setting;
use App\Services\SettingsService;

class SettingObserver
{
    public function __construct(private readonly SettingsService $settings) {}

    public function saved(Setting $setting): void
    {
        $this->settings->bustCache($setting->brand_id);
    }

    public function deleted(Setting $setting): void
    {
        $this->settings->bustCache($setting->brand_id);
    }
}
