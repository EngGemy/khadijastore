<?php

use App\Http\Controllers\Admin\FacebookPixelSettingsController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\AssistantController;
use App\Http\Controllers\DirectoryController;
use App\Http\Controllers\FacebookPixelTrackController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\RobotsController;
use App\Http\Controllers\ShippingQuoteController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

/*
| واجهة المتجر (Blade) — نفس التصميم، بيانات حقيقية من قاعدة البيانات.
| لوحة التحكم منفصلة على /admin (Filament).
*/

Route::get('/', [ShopController::class, 'index'])->name('home');
Route::get('/brand/{slug}', [ShopController::class, 'brand'])->name('brand.show');
Route::get('/brand/{slug}/brands', [ShopController::class, 'brandManufacturers'])->name('brand.manufacturers');
Route::get('/brand/{slug}/shop', [ShopController::class, 'brandShop'])->name('brand.shop');
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

Route::post('/facebook-pixel/track', [FacebookPixelTrackController::class, 'store'])
    ->middleware('throttle:60,1')
    ->name('facebook-pixel.track');

// SEO
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [RobotsController::class, 'index'])->name('robots');

// دليل الخدمات (أطباء / حضانات)
Route::get('/directory/{type}', [DirectoryController::class, 'index'])
    ->whereIn('type', ['doctor', 'nursery'])
    ->name('directory.index');

Route::get('/directory/{type}/{slug}', [DirectoryController::class, 'show'])
    ->whereIn('type', ['doctor', 'nursery'])
    ->name('directory.show');

Route::get('/api/v1/directory/{type}', [DirectoryController::class, 'apiList'])
    ->whereIn('type', ['doctor', 'nursery'])
    ->middleware('throttle:60,1')
    ->name('directory.api');

// المساعد الذكي
Route::get('/assistant', [AssistantController::class, 'page'])->name('assistant.page');
Route::post('/assistant/chat', [AssistantController::class, 'chat'])->middleware('throttle:20,1')->name('assistant.chat');
Route::post('/assistant/compare', [AssistantController::class, 'compare'])->middleware('throttle:30,1')->name('assistant.compare');
Route::post('/assistant/widget', [AssistantController::class, 'widgetChat'])->middleware('throttle:30,1')->name('assistant.widget');

// إعدادات فيسبوك بكسل (للأدمن — JSON API بجانب صفحة Filament)
Route::middleware(['auth'])->prefix('admin/api')->group(function () {
    Route::get('/facebook-pixel', [FacebookPixelSettingsController::class, 'show'])->name('admin.facebook-pixel.show');
    Route::put('/facebook-pixel', [FacebookPixelSettingsController::class, 'update'])->name('admin.facebook-pixel.update');
    Route::post('/facebook-pixel/test-token', [FacebookPixelSettingsController::class, 'testToken'])->name('admin.facebook-pixel.test');
});
