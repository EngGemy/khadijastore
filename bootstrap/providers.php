<?php

use App\Providers\AppServiceProvider;
use App\Providers\FacebookPixelServiceProvider;
use App\Providers\Filament\MerchantPanelProvider;
use App\Providers\Filament\PlatformPanelProvider;

return [
    AppServiceProvider::class,
    FacebookPixelServiceProvider::class,
    PlatformPanelProvider::class,
    MerchantPanelProvider::class,
];
