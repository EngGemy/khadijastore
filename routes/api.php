<?php

use App\Http\Controllers\Api\CatalogController;
use App\Http\Controllers\Api\OrderController;
use Illuminate\Support\Facades\Route;

/*
| API عامة تخدم الواجهة الأمامية (Tailwind) دون أي تغيير فيها.
| الواجهة تستهلك هذه الـ endpoints لجلب البيانات وإرسال الطلبات.
*/

Route::prefix('v1')->group(function () {
    // الكتالوج
    Route::get('brands', [CatalogController::class, 'brands']);
    Route::get('products', [CatalogController::class, 'products']);              // المميّزة
    Route::get('brands/{brandSlug}/products', [CatalogController::class, 'products']);
    Route::get('products/{slug}', [CatalogController::class, 'product']);
    Route::get('governorates', [CatalogController::class, 'governorates']);

    // الثيم (للمناسبات)
    Route::get('theme', [CatalogController::class, 'theme']);
    Route::get('brands/{brandSlug}/theme', [CatalogController::class, 'theme']);

    // الطلبات — rate limited لمنع الإساءة
    Route::post('orders', [OrderController::class, 'store'])
        ->middleware('throttle:20,1');
});
