<?php

use App\Http\Middleware\ForceHttps;
use App\Http\Middleware\MaintenanceModeMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // الواجهة الأمامية على نطاق مختلف — فعّل CORS لاستهلاك الـ API
        $middleware->validateCsrfTokens(except: [
            'api/*',
        ]);

        $middleware->web(prepend: [
            ForceHttps::class,
        ]);

        $middleware->trustProxies(at: '*');

        $middleware->web(append: [
            MaintenanceModeMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
