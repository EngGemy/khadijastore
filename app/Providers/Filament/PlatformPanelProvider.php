<?php

namespace App\Providers\Filament;

use App\Filament\Auth\PlatformLogin;
use App\Providers\Filament\Concerns\ConfiguresArabicPanel;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;

class PlatformPanelProvider extends PanelProvider
{
    use ConfiguresArabicPanel;

    public function panel(Panel $panel): Panel
    {
        return $this->configureArabicPanel(
            $panel
                ->default()
                ->id('platform')
                ->path('platform')
                ->login(PlatformLogin::class)
                ->brandName(fn (): string => setting('store.name', 'متجر العلامات').' · المنصة')
                ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
                ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
                ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
                ->pages([Dashboard::class])
                ->databaseNotifications()
                ->databaseNotificationsPolling('30s')
        );
    }
}
