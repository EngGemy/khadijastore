<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceModeMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('admin*') || $request->is('api*')) {
            return $next($request);
        }

        if (setting('store.maintenance_mode', false)) {
            return response()->view('maintenance', [], 503);
        }

        return $next($request);
    }
}
