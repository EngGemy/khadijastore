<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Governorate;
use App\Models\Order;
use App\Models\Product;
use App\Services\SettingsService;
use App\Services\ThemeResolver;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function __construct(
        private readonly ThemeResolver $themes,
        private readonly SettingsService $settings,
    ) {}

    /** الصفحة الرئيسية */
    public function index(): View
    {
        $brands = Brand::where('is_active', true)
            ->withCount(['products' => fn ($q) => $q->where('is_active', true)])
            ->get();

        $products = Product::withoutGlobalScopes()
            ->where('is_active', true)
            ->where('is_featured', true)
            ->with(['brand:id,name,slug', 'variants'])
            ->orderBy('sort')
            ->take(8)
            ->get();

        $heroStats = [
            'total_orders' => Order::withoutGlobalScopes()->whereNotIn('status', ['cancelled'])->count(),
            'avg_rating'   => round(Product::withoutGlobalScopes()->where('is_active', true)->avg('rating') ?? 0, 1),
        ];

        return view('shop.index', array_merge(
            compact('brands', 'products', 'heroStats'),
            $this->themeData(),
            $this->settingsData(),
        ));
    }

    /** صفحة البراند */
    public function brand(string $slug): View
    {
        $brand = Brand::where('slug', $slug)->where('is_active', true)->firstOrFail();

        $brand->load(['products' => fn ($q) => $q->where('is_active', true)->orderBy('sort')->with('variants')]);

        return view('shop.brand', array_merge(
            compact('brand'),
            $this->themeData($brand),
            $this->settingsData($brand->id),
        ));
    }

    /** صفحة المنتج */
    public function product(string $slug): View
    {
        $product = Product::withoutGlobalScopes()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->with(['variants', 'brand', 'approvedReviews'])
            ->firstOrFail();

        $defaultVariant = $product->variants->first(fn ($v) => ! $v->isOutOfStock())
            ?? $product->variants->first();

        $gallery = $product->getMedia('gallery')->map(
            fn ($m) => $m->getUrl('large')
        )->values()->all();

        $productData = [
            'id' => $product->id,
            'name' => $product->name,
            'desc' => $product->short_description ?? '',
            'old' => $product->compare_price ?? $product->price,
            'brand' => $product->brand->name,
            'mark' => $product->brand->mark,
            'wa' => preg_replace('/\D/', '', $product->brand->whatsapp ?? ''),
            'vf' => $product->brand->vodafone_cash ?? '',
            'ip' => $product->brand->instapay ?? '',
            'variant_id' => $defaultVariant?->id,
            'stock' => $product->stock,
            'track_stock' => $product->track_stock,
            'low_stock_threshold' => $product->low_stock_threshold,
            'cover' => $product->getFirstMediaUrl('cover', 'large'),
            'gallery' => $gallery,
            'video_url' => $product->video_url ?? '',
        ];

        $govs = Governorate::active()->get()
            ->map(fn ($g) => [
                'id' => $g->id,
                'name' => $g->name,
                'fee' => $g->shipping_fee,
                'free_over' => $g->free_over,
            ])->values();

        $checkout = [
            'cod_enabled' => setting('checkout.cod_enabled', true, $product->brand_id),
            'whatsapp_enabled' => setting('checkout.whatsapp_enabled', true, $product->brand_id),
            'transfer_enabled' => setting('checkout.transfer_enabled', true, $product->brand_id),
            'min_order_total' => (int) setting('checkout.min_order_total', 0, $product->brand_id),
            'terms_text' => setting('checkout.terms_text', '', $product->brand_id),
        ];

        return view('shop.product', array_merge(
            compact('product', 'productData', 'govs', 'checkout'),
            $this->themeData($product->brand),
            $this->settingsData($product->brand_id),
        ));
    }

    private function themeData(?Brand $brand = null): array
    {
        $tokens = $this->themes->resolve($brand);

        return [
            'themeCss' => $this->themes->toCssVariables($tokens),
            'stripText' => $tokens['strip_text'] ?? 'شحن مجاني داخل القاهرة والجيزة · الدفع عند الاستلام',
        ];
    }

    private function settingsData(?int $brandId = null): array
    {
        return [
            'storeName' => setting('store.name', 'متجر العلامات', $brandId),
            'storeTagline' => setting('store.tagline', '', $brandId),
            'storeCurrency' => setting('store.currency', 'EGP', $brandId),
            'storeSupportPhone' => setting('store.support_phone', '', $brandId),
            'storeSupportWhatsapp' => setting('store.support_whatsapp', '', $brandId),
            'storeEmail' => setting('store.email', '', $brandId),
            'storeAddress' => setting('store.address', '', $brandId),
            'storeSocial' => setting('store.social', [], $brandId),
        ];
    }
}
