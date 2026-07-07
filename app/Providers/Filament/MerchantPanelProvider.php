<?php

namespace App\Providers\Filament;

use App\Filament\Auth\MerchantLogin;
use App\Providers\Filament\Concerns\ConfiguresArabicPanel;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;

class MerchantPanelProvider extends PanelProvider
{
    use ConfiguresArabicPanel;

    public function panel(Panel $panel): Panel
    {
        return $this->configureArabicPanel(
            $panel
                ->id('merchant')
                ->path('merchant')
                ->login(MerchantLogin::class)
                ->brandName(function (): string {
                    $brand = auth()->user()?->brand;

                    return $brand?->name ?? setting('store.name', 'متجر العلامات');
                })
                ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
                ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
                ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
                ->pages([Dashboard::class])
                ->databaseNotifications()
                ->databaseNotificationsPolling('30s')
        );
    }
}
