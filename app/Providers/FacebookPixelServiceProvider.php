<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\FacebookPixelService;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class FacebookPixelServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(FacebookPixelService::class);
    }

    public function boot(): void
    {
        Blade::directive('fbPixelEvent', function (string $expression): string {
            return "<?php
                \$__fbArgs = [{$expression}];
                \$__fbEventName = \$__fbArgs[0] ?? '';
                \$__fbParams = \$__fbArgs[1] ?? [];
                \$__fbEventId = \$__fbArgs[2] ?? null;
                echo app(\\App\\Services\\FacebookPixelService::class)->renderBrowserEventScript(\$__fbEventName, \$__fbParams, \$__fbEventId);
            ?>";
        });
    }
}
