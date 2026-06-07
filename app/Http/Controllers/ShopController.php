<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Governorate;
use App\Models\HomeBlock;
use App\Models\Listing;
use App\Models\Order;
use App\Models\Product;
use App\Services\SettingsService;
use App\Services\ThemeResolver;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
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
        $home       = $this->homePageData();
        $homeBlocks = $this->resolveHomeBlocks();
        $directory  = $this->directoryData();

        return view('shop.index', array_merge(
            compact('home', 'homeBlocks', 'directory'),
            $this->themeData(),
            $this->settingsData(),
        ));
    }

    /** صفحة البراند */
    public function brand(string $slug)
    {
        $brand = Brand::where('slug', $slug)->where('is_active', true)->first();

        if (! $brand) {
            // إعادة توجيه 301 من slug قديم
            $brand = Brand::where('is_active', true)
                ->whereRaw("JSON_SEARCH(old_slugs, 'one', ?) IS NOT NULL", [$slug])
                ->first();

            if ($brand) {
                return redirect()->route('brand.show', $brand->slug, 301);
            }

            abort(404);
        }

        $brand->load(['products' => fn ($q) => $q->where('is_active', true)->orderBy('sort')->with('variants')]);

        $seo = [
            'title'       => $brand->meta_title ?: $brand->name . ' · ' . setting('store.name', 'متجر العلامات'),
            'description' => $brand->meta_description ?: $brand->description,
            'image'       => $brand->getFirstMediaUrl('logo', 'thumb'),
            'url'         => route('brand.show', $brand->slug),
        ];

        return view('shop.brand', array_merge(
            compact('brand', 'seo'),
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
            ->with(['variants', 'brand', 'approvedReviews', 'priceTiers'])
            ->firstOrFail();

        $defaultVariant = $product->variants->first(fn ($v) => ! $v->isOutOfStock())
            ?? $product->variants->first();

        $gallery = $product->getMedia('gallery')->map(
            fn ($m) => $m->getUrl('large')
        )->values()->all();

        $productData = [
            'id'               => $product->id,
            'name'             => $product->name,
            'desc'             => $product->short_description ?? '',
            'old'              => $product->compare_price ?? $product->price,
            'brand'            => $product->brand->name,
            'mark'             => $product->brand->mark,
            'wa'               => preg_replace('/\D/', '', $product->brand->whatsapp ?? ''),
            'vf'               => $product->brand->vodafone_cash ?? '',
            'ip'               => $product->brand->instapay ?? '',
            'variant_id'       => $defaultVariant?->id,
            'stock'            => $product->stock,
            'track_stock'      => $product->track_stock,
            'low_stock_threshold' => $product->low_stock_threshold,
            'cover'            => $product->getFirstMediaUrl('cover', 'large'),
            'gallery'          => $gallery,
            'video_url'        => $product->video_url ?? '',
            'variants'         => $product->variants->map(fn ($v) => [
                'id' => $v->id,
                'name' => $v->name,
                'price' => $v->price,
                'stock' => $v->stock,
                'track_stock' => $v->track_stock,
                'is_out_of_stock' => $v->isOutOfStock(),
                'is_popular' => $v->is_popular,
                'option_values' => $v->option_values ?? [],
            ])->values(),
            'price_tiers'      => $product->priceTiers->map(fn ($t) => [
                'min' => $t->min_qty,
                'price' => $t->price,
                'label' => $t->label,
            ])->values(),
        ];

        $govs = Governorate::active()->get()
            ->map(fn ($g) => [
                'id'       => $g->id,
                'name'     => $g->name,
                'fee'      => $g->shipping_fee,
                'free_over'=> $g->free_over,
            ])->values();

        $checkout = [
            'cod_enabled'      => setting('checkout.cod_enabled', true, $product->brand_id),
            'whatsapp_enabled' => setting('checkout.whatsapp_enabled', true, $product->brand_id),
            'transfer_enabled' => setting('checkout.transfer_enabled', true, $product->brand_id),
            'min_order_total'  => (int) setting('checkout.min_order_total', 0, $product->brand_id),
            'terms_text'       => setting('checkout.terms_text', '', $product->brand_id),
        ];

        $seo = [
            'title'       => $product->meta_title ?: $product->name . ' · ' . ($product->brand->name ?? ''),
            'description' => $product->meta_description ?: $product->short_description,
            'image'       => $product->getFirstMediaUrl('cover', 'large'),
            'url'         => route('product.show', $product->slug),
            'price'       => $product->price,
            'currency'    => 'EGP',
            'availability'=> $product->isOutOfStock() ? 'OutOfStock' : 'InStock',
            'brand'       => $product->brand->name ?? '',
            'sku'         => $product->slug,
        ];

        return view('shop.product', array_merge(
            compact('product', 'productData', 'govs', 'checkout', 'seo'),
            $this->themeData($product->brand),
            $this->settingsData($product->brand_id),
        ));
    }

    // ─────────────────────────────────────────────────────────────────────────

    /** Collect all home.* settings for the homepage */
    private function homePageData(): array
    {
        return Cache::remember('home.page.data', 3600, function () {
            $s = app(SettingsService::class)->all();

            $statsRaw = $s['home.hero.stats'] ?? [
                ['value' => '{brands_count}+', 'label' => 'براندات · Brands'],
                ['value' => '{total_orders}+', 'label' => 'طلب مكتمل'],
                ['value' => '{avg_rating}★',   'label' => 'متوسط التقييم'],
            ];

            // Resolve dynamic tokens in stats
            $brandsCount = Brand::where('is_active', true)->count();
            $totalOrders = Order::withoutGlobalScopes()->whereNotIn('status', ['cancelled'])->count();
            $avgRating   = round(Product::withoutGlobalScopes()->where('is_active', true)->avg('rating') ?? 0, 1);

            $formattedOrders = $totalOrders > 999
                ? number_format($totalOrders / 1000, 1) . 'k'
                : (string) $totalOrders;

            $tokens = [
                '{brands_count}' => (string) $brandsCount,
                '{total_orders}' => $formattedOrders,
                '{avg_rating}'   => (string) $avgRating,
            ];

            $stats = [];
            foreach ((array) $statsRaw as $stat) {
                $val = $stat['value'] ?? '';
                foreach ($tokens as $token => $resolved) {
                    $val = str_replace($token, $resolved, $val);
                }
                $stats[] = ['value' => $val, 'label' => $stat['label'] ?? ''];
            }

            // Hero floating cards (first 3 active brands)
            $heroCards = Brand::where('is_active', true)->take(3)->get();

            return [
                'hero' => [
                    'eyebrow'            => $s['home.hero.eyebrow']            ?? 'منصة واحدة · آلاف الخيارات · ثقة من أول طلب',
                    'title_line1'        => $s['home.hero.title_line1']        ?? 'تسوّق',
                    'title_highlight'    => $s['home.hero.title_highlight']    ?? 'بثقة',
                    'title_line2'        => $s['home.hero.title_line2']        ?? 'من أول طلب',
                    'paragraph'          => $s['home.hero.paragraph']          ?? 'منتجات أصلية 100% من أشهر البراندات العالمية والمحلية. توصيل سريع، دفع عند الاستلام.',
                    'primary_btn_text'   => $s['home.hero.primary_btn_text']   ?? 'اكتشف المنتجات',
                    'primary_btn_link'   => $s['home.hero.primary_btn_link']   ?? '#products',
                    'secondary_btn_text' => $s['home.hero.secondary_btn_text'] ?? 'تصفّح البراندات',
                    'secondary_btn_link' => $s['home.hero.secondary_btn_link'] ?? '#brands',
                    'stats'              => $stats,
                    'cards'              => $heroCards,
                    'custom_cards'       => $s['home.hero.cards'] ?? [],
                ],
                'seo' => [
                    'title'       => $s['home.seo.title']       ?? setting('store.name', 'متجر العلامات') . ' · اختار الأفضل بكل ثقة',
                    'description' => $s['home.seo.description'] ?? 'اكتشف منتجات أصلية من أشهر البراندات العالمية والمحلية. توصيل سريع، دفع عند الاستلام، وضمان رضا تام.',
                    'image'       => $s['home.seo.image']       ?? '',
                ],
            ];
        });
    }

    /** Load and resolve dynamic data for each active HomeBlock */
    private function resolveHomeBlocks(): Collection
    {
        return Cache::remember('home.blocks.resolved', 3600, function () {
            $blocks = HomeBlock::where('is_active', true)->orderBy('sort')->get();

            $brands   = null;
            $products = [];

            return $blocks->map(function (HomeBlock $block) use (&$brands, &$products) {
                $data = $block->data ?? [];

                if (in_array($block->type, ['brands_marquee', 'brands_grid'])) {
                    if ($brands === null) {
                        $brands = Brand::where('is_active', true)
                            ->withCount(['products' => fn ($q) => $q->where('is_active', true)])
                            ->get();
                    }
                    $limit = isset($data['limit']) ? (int) $data['limit'] : null;
                    $block->resolvedBrands = $limit ? $brands->take($limit) : $brands;
                }

                if ($block->type === 'products_grid') {
                    $source  = $data['source'] ?? 'featured';
                    $limit   = (int) ($data['limit'] ?? 8);
                    $cacheKey = "block.products.{$source}.{$limit}";
                    $block->resolvedProducts = $products[$cacheKey] ??= $this->fetchProducts($source, $limit);
                }

                return $block;
            });
        });
    }

    private function fetchProducts(string $source, int $limit, ?int $brandId = null): Collection
    {
        $query = Product::withoutGlobalScopes()
            ->where('is_active', true)
            ->with(['brand:id,name,slug', 'variants'])
            ->take($limit);

        if ($brandId) {
            $query->where('brand_id', $brandId);
        }

        return match ($source) {
            'latest'       => $query->orderByDesc('created_at')->get(),
            'best_selling' => $query->orderByDesc('sales_count')->get(),
            default        => $query->where('is_featured', true)->orderBy('sort')->get(),
        };
    }

    private function themeData(?Brand $brand = null): array
    {
        $tokens = $this->themes->resolve($brand);

        return [
            'themeCss'  => $this->themes->toCssVariables($tokens),
            'stripText' => $tokens['strip_text'] ?? 'شحن مجاني داخل القاهرة والجيزة · الدفع عند الاستلام',
        ];
    }

    private function settingsData(?int $brandId = null): array
    {
        return [
            'storeName'            => setting('store.name', 'متجر العلامات', $brandId),
            'storeTagline'         => setting('store.tagline', '', $brandId),
            'storeCurrency'        => setting('store.currency', 'EGP', $brandId),
            'storeSupportPhone'    => setting('store.support_phone', '', $brandId),
            'storeSupportWhatsapp' => setting('store.support_whatsapp', '', $brandId),
            'storeEmail'           => setting('store.email', '', $brandId),
            'storeAddress'         => setting('store.address', '', $brandId),
            'storeSocial'          => setting('store.social', [], $brandId),
        ];
    }

    /** بيانات دليل الخدمات للرئيسية */
    private function directoryData(): array
    {
        return Cache::remember('home.directory.data', 1800, function () {
            $doctorCount  = Listing::withoutGlobalScopes()->where('type', 'doctor')->where('is_active', true)->count();
            $nurseryCount = Listing::withoutGlobalScopes()->where('type', 'nursery')->where('is_active', true)->count();

            $specialties = Listing::withoutGlobalScopes()
                ->where('type', 'doctor')->where('is_active', true)
                ->whereNotNull('data')
                ->get()
                ->map(fn ($l) => $l->data['specialty'] ?? null)
                ->filter()->unique()->count();

            $govCount = Listing::withoutGlobalScopes()
                ->where('is_active', true)
                ->whereNotNull('governorate')
                ->distinct('governorate')
                ->count('governorate');

            $featuredDoctors = Listing::withoutGlobalScopes()
                ->where('type', 'doctor')->where('is_active', true)
                ->with('media')->orderByDesc('is_featured')->orderBy('sort')
                ->take(3)->get();

            $featuredNurseries = Listing::withoutGlobalScopes()
                ->where('type', 'nursery')->where('is_active', true)
                ->with('media')->orderByDesc('is_featured')->orderBy('sort')
                ->take(3)->get();

            return compact('doctorCount', 'nurseryCount', 'specialties', 'govCount', 'featuredDoctors', 'featuredNurseries');
        });
    }
}
