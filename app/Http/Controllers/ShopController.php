<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Governorate;
use App\Models\HomeBlock;
use App\Models\Listing;
use App\Models\Order;
use App\Models\Product;
use App\Services\FacebookPixelService;
use App\Services\SettingsService;
use App\Services\ThemeResolver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function __construct(
        private readonly ThemeResolver $themes,
        private readonly SettingsService $settings,
        private readonly FacebookPixelService $facebookPixel,
    ) {}

    /** الصفحة الرئيسية */
    public function index(): View
    {
        $home = $this->homePageData();
        $homeBlocks = $this->resolveHomeBlocks();
        $directory = $this->directoryData();

        return view('shop.index', array_merge(
            compact('home', 'homeBlocks', 'directory'),
            $this->themeData(),
            $this->settingsData(),
        ));
    }

    /** صفحة المتجر — الرئيسية (أقسام + روابط) */
    public function brand(string $slug): View|RedirectResponse
    {
        $brand = $this->findBrand($slug);
        if ($brand instanceof RedirectResponse) {
            return $brand;
        }

        $departments = Category::filterDepartmentsForStore($brand->id);
        $manufacturerBrands = Category::filterBrandGroupsForStore($brand->id);
        $featuredProducts = $this->fetchProducts('featured', 6, $brand->id);
        $productCount = Product::where('brand_id', $brand->id)->where('is_active', true)->count();

        $seo = $this->brandSeo($brand, $brand->name, route('brand.show', $brand->slug));
        $fbPageView = $this->facebookPixel->track('PageView', [], $brand->id, queueBrowser: false);

        return view('shop.brand-home', array_merge(
            compact('brand', 'departments', 'manufacturerBrands', 'featuredProducts', 'productCount', 'seo', 'fbPageView'),
            $this->themeData($brand),
            $this->settingsData($brand->id),
        ));
    }

    /** صفحة براندات المصنّعين داخل المتجر */
    public function brandManufacturers(string $slug): View|RedirectResponse
    {
        $brand = $this->findBrand($slug);
        if ($brand instanceof RedirectResponse) {
            return $brand;
        }

        $manufacturerBrands = Category::filterBrandGroupsForStore($brand->id);

        $seo = $this->brandSeo(
            $brand,
            'براندات '.$brand->name,
            route('brand.manufacturers', $brand->slug),
        );
        $fbPageView = $this->facebookPixel->track('PageView', [], $brand->id, queueBrowser: false);

        return view('shop.brand-manufacturers', array_merge(
            compact('brand', 'manufacturerBrands', 'seo', 'fbPageView'),
            $this->themeData($brand),
            $this->settingsData($brand->id),
        ));
    }

    /** كatalog المنتجات — بحث وفلتر */
    public function brandShop(Request $request, string $slug): View|RedirectResponse
    {
        $brand = $this->findBrand($slug);
        if ($brand instanceof RedirectResponse) {
            return $brand;
        }

        $filterDepartments = Category::filterDepartmentsForStore($brand->id);
        $filterBrandGroups = Category::filterBrandGroupsForStore($brand->id);

        $query = Product::query()
            ->where('brand_id', $brand->id)
            ->where('is_active', true)
            ->with(['variants', 'category.parent']);

        $searchQuery = trim((string) $request->query('q', ''));
        if ($searchQuery !== '') {
            $query->where(function ($q) use ($searchQuery) {
                $q->where('name', 'like', '%'.$searchQuery.'%')
                    ->orWhere('short_description', 'like', '%'.$searchQuery.'%');
            });
        }

        $deptId = $request->integer('dept');
        if ($deptId) {
            $childIds = Category::query()->where('parent_id', $deptId)->pluck('id');
            $query->where(function ($q) use ($deptId, $childIds) {
                $q->where('category_id', $deptId);
                if ($childIds->isNotEmpty()) {
                    $q->orWhereIn('category_id', $childIds);
                }
            });
        }

        $sort = (string) $request->query('sort', 'all');

        $manufacturerSlug = Str::slug((string) $request->query('manufacturer', ''));
        $activeManufacturer = $filterBrandGroups->firstWhere('slug', $manufacturerSlug);
        if ($activeManufacturer) {
            $query->whereIn('category_id', explode(',', $activeManufacturer->ids));
        }

        $sort = (string) $request->query('sort', 'all');
        match ($sort) {
            'bestseller' => $query->orderByDesc('sales_count'),
            'new' => $query->orderByDesc('created_at'),
            'deals' => $query->whereNotNull('compare_price')
                ->whereColumn('compare_price', '>', 'price')
                ->orderBy('sort'),
            default => $query->orderBy('sort'),
        };

        $products = $query->get();

        $seo = $this->brandSeo(
            $brand,
            'منتجات '.$brand->name,
            route('brand.shop', $brand->slug),
        );
        $fbPageView = $this->facebookPixel->track('PageView', [], $brand->id, queueBrowser: false);

        return view('shop.brand-shop', array_merge(
            compact(
                'brand',
                'products',
                'filterDepartments',
                'filterBrandGroups',
                'searchQuery',
                'deptId',
                'manufacturerSlug',
                'activeManufacturer',
                'sort',
                'seo',
                'fbPageView',
            ),
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
            'variants' => $product->variants->map(fn ($v) => [
                'id' => $v->id,
                'name' => $v->name,
                'price' => $v->price,
                'stock' => $v->stock,
                'track_stock' => $v->track_stock,
                'low_stock_threshold' => $v->low_stock_threshold,
                'is_out_of_stock' => $v->isOutOfStock(),
                'is_popular' => $v->is_popular,
                'option_values' => $v->option_values ?? [],
            ])->values(),
            'attribute_ids' => $product->variants
                ->flatMap(fn ($v) => collect($v->option_values ?? [])->pluck('attribute_id'))
                ->unique()
                ->values()
                ->all(),
            'price_tiers' => $product->priceTiers->map(fn ($t) => [
                'min' => $t->min_qty,
                'price' => $t->price,
                'label' => $t->label,
            ])->values(),
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

        $seo = [
            'title' => $product->meta_title ?: $product->name.' · '.($product->brand->name ?? ''),
            'description' => $product->meta_description ?: $product->short_description,
            'image' => $product->getFirstMediaUrl('cover', 'large'),
            'url' => route('product.show', $product->slug),
            'price' => $product->price,
            'currency' => setting('store.currency', 'EGP', $product->brand_id),
            'availability' => $product->isOutOfStock() ? 'OutOfStock' : 'InStock',
            'brand' => $product->brand->name ?? '',
            'sku' => $product->slug,
        ];

        $currency = setting('store.currency', 'EGP', $product->brand_id);
        $fbPageView = $this->facebookPixel->track('PageView', [], $product->brand_id, queueBrowser: false);
        $fbViewContent = $this->facebookPixel->track('ViewContent', [
            'content_ids' => [(string) $product->id],
            'content_type' => 'product',
            'content_name' => $product->name,
            'value' => (float) $product->price,
            'currency' => $currency,
        ], $product->brand_id, currency: $currency);

        return view('shop.product', array_merge(
            compact('product', 'productData', 'govs', 'checkout', 'seo', 'fbPageView', 'fbViewContent', 'currency'),
            $this->themeData($product->brand),
            $this->settingsData($product->brand_id),
        ));
    }

    // ─────────────────────────────────────────────────────────────────────────

    private function findBrand(string $slug): Brand|RedirectResponse
    {
        $brand = Brand::where('slug', $slug)->where('is_active', true)->first();

        if (! $brand) {
            $brand = Brand::where('is_active', true)
                ->whereRaw("JSON_SEARCH(old_slugs, 'one', ?) IS NOT NULL", [$slug])
                ->first();

            if ($brand) {
                return redirect()->route('brand.show', $brand->slug, 301);
            }

            abort(404);
        }

        return $brand;
    }

    /**
     * @return array{title: string, description: string|null, image: string, url: string}
     */
    private function brandSeo(Brand $brand, string $title, string $url): array
    {
        return [
            'title' => $brand->meta_title ?: $title.' · '.setting('store.name', 'متجر العلامات', $brand->id),
            'description' => $brand->meta_description ?: $brand->description,
            'image' => $brand->getFirstMediaUrl('logo', 'thumb'),
            'url' => $url,
        ];
    }

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
            $avgRating = round(Product::withoutGlobalScopes()->where('is_active', true)->avg('rating') ?? 0, 1);

            $formattedOrders = $totalOrders > 999
                ? number_format($totalOrders / 1000, 1).'k'
                : (string) $totalOrders;

            $tokens = [
                '{brands_count}' => (string) $brandsCount,
                '{total_orders}' => $formattedOrders,
                '{avg_rating}' => (string) $avgRating,
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
                    'eyebrow' => $s['home.hero.eyebrow'] ?? 'منصة واحدة · آلاف الخيارات · ثقة من أول طلب',
                    'title_line1' => $s['home.hero.title_line1'] ?? 'تسوّق',
                    'title_highlight' => $s['home.hero.title_highlight'] ?? 'بثقة',
                    'title_line2' => $s['home.hero.title_line2'] ?? 'من أول طلب',
                    'paragraph' => $s['home.hero.paragraph'] ?? 'منتجات أصلية 100% من أشهر البراندات العالمية والمحلية. توصيل سريع، دفع عند الاستلام.',
                    'primary_btn_text' => $s['home.hero.primary_btn_text'] ?? 'اكتشف المنتجات',
                    'primary_btn_link' => $s['home.hero.primary_btn_link'] ?? '#products',
                    'secondary_btn_text' => $s['home.hero.secondary_btn_text'] ?? 'تصفّح البراندات',
                    'secondary_btn_link' => $s['home.hero.secondary_btn_link'] ?? '#brands',
                    'stats' => $stats,
                    'cards' => $heroCards,
                    'custom_cards' => $s['home.hero.cards'] ?? [],
                ],
                'seo' => [
                    'title' => $s['home.seo.title'] ?? setting('store.name', 'متجر العلامات').' · اختار الأفضل بكل ثقة',
                    'description' => $s['home.seo.description'] ?? 'اكتشف منتجات أصلية من أشهر البراندات العالمية والمحلية. توصيل سريع، دفع عند الاستلام، وضمان رضا تام.',
                    'image' => $s['home.seo.image'] ?? '',
                ],
            ];
        });
    }

    /** Load and resolve dynamic data for each active HomeBlock */
    private function resolveHomeBlocks(): Collection
    {
        return Cache::remember('home.blocks.resolved', 3600, function () {
            $blocks = HomeBlock::where('is_active', true)->orderBy('sort')->get();
            $hasBrandFilter = $blocks->contains(
                fn (HomeBlock $block) => $block->type === 'brands_filter',
            );

            $brands = null;
            $products = [];

            return $blocks->map(function (HomeBlock $block) use (&$brands, &$products, $hasBrandFilter) {
                $data = $block->data ?? [];

                if (in_array($block->type, ['brands_marquee', 'brands_grid', 'brands_filter'])) {
                    if ($brands === null) {
                        $brands = Brand::where('is_active', true)
                            ->withCount(['products' => fn ($q) => $q->where('is_active', true)])
                            ->get();
                    }
                    $limit = isset($data['limit']) ? (int) $data['limit'] : null;
                    $block->resolvedBrands = $limit ? $brands->take($limit) : $brands;
                }

                if ($block->type === 'products_grid') {
                    $source = $data['source'] ?? 'featured';
                    $limit = (int) ($data['limit'] ?? 8);

                    // مع فلتر البراندات نحتاج منتجات من كل المتاجر — ليس المميّزة فقط
                    if ($hasBrandFilter) {
                        $limit = max($limit, 48);
                        if ($source === 'featured') {
                            $source = 'filterable';
                        }
                    }

                    $cacheKey = "block.products.{$source}.{$limit}";
                    $block->resolvedProducts = $products[$cacheKey] ??= $this->fetchProducts($source, $limit);
                }

                return $block;
            });
        });
    }

    private function fetchProducts(string $source, int $limit, ?int $brandId = null): Collection
    {
        $limit = max(1, $limit);

        $run = function (string $resolvedSource) use ($limit, $brandId): Collection {
            $query = Product::withoutGlobalScopes()
                ->where('is_active', true)
                ->with(['brand:id,name,slug', 'variants']);

            if ($brandId) {
                $query->where('brand_id', $brandId);
            }

            match ($resolvedSource) {
                'latest' => $query->orderByDesc('created_at'),
                'best_selling', 'filterable', 'all' => $query->orderByDesc('sales_count')->orderBy('sort'),
                default => $query->where('is_featured', true)->orderBy('sort'),
            };

            return $query->limit($limit)->get();
        };

        $result = $run($source);

        if ($result->isNotEmpty()) {
            return $result;
        }

        if ($source === 'featured') {
            return $run('best_selling');
        }

        if ($source === 'best_selling') {
            return $run('latest');
        }

        return $result;
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
            'storeLogo' => store_logo_url($brandId),
            'storeCurrency' => setting('store.currency', 'EGP', $brandId),
            'storeSupportPhone' => setting('store.support_phone', '', $brandId),
            'storeSupportWhatsapp' => setting('store.support_whatsapp', '', $brandId),
            'storeEmail' => setting('store.email', '', $brandId),
            'storeAddress' => setting('store.address', '', $brandId),
            'storeSocial' => setting('store.social', [], $brandId),
        ];
    }

    /** بيانات دليل الخدمات للرئيسية */
    private function directoryData(): array
    {
        return Cache::remember('home.directory.data', 1800, function () {
            $doctorCount = Listing::withoutGlobalScopes()->where('type', 'doctor')->where('is_active', true)->count();
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
