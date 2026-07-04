<?php

use App\Providers\AppServiceProvider;
use App\Providers\FacebookPixelServiceProvider;
use App\Providers\Filament\AdminPanelProvider;

return [
    AppServiceProvider::class,
    FacebookPixelServiceProvider::class,
    AdminPanelProvider::class,
];
