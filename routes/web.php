<?php

use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ShippingQuoteController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;

/*
| واجهة المتجر (Blade) — نفس التصميم، بيانات حقيقية من قاعدة البيانات.
| لوحة التحكم منفصلة على /admin (Filament).
*/

Route::get('/', [ShopController::class, 'index'])->name('home');
Route::get('/brand/{slug}', [ShopController::class, 'brand'])->name('brand.show');
Route::get('/product/{slug}', [ShopController::class, 'product'])->name('product.show');

// استقبال الطلب من فورم صفحة المنتج (COD / واتساب / تحويل + رفع إيصال)
Route::post('/order', [OrderController::class, 'store'])
    ->middleware('throttle:20,1')
    ->name('order.store');

// استقبال مراجعة العميل من صفحة المنتج
Route::post('/product/{slug}/review', [ReviewController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('product.review');

// حساب الشحن الحي
Route::post('/shipping/quote', [ShippingQuoteController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('shipping.quote');
