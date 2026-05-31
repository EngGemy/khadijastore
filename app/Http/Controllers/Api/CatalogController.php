<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Governorate;
use App\Models\Product;
use App\Services\ThemeResolver;
use Illuminate\Http\JsonResponse;

class CatalogController extends Controller
{
    /** قائمة البراندات النشطة (للصفحة الرئيسية) */
    public function brands(): JsonResponse
    {
        $brands = Brand::where('is_active', true)
            ->withCount(['products' => fn ($q) => $q->where('is_active', true)])
            ->get()
            ->map(fn (Brand $b) => [
                'slug' => $b->slug,
                'name' => $b->name,
                'mark' => $b->mark,
                'category_label' => $b->category_label,
                'description' => $b->description,
                'products_count' => $b->products_count,
                'whatsapp' => $b->whatsapp,
            ]);

        return response()->json(['data' => $brands]);
    }

    /** منتجات براند معيّن أو المميّزة عبر كل البراندات */
    public function products(?string $brandSlug = null): JsonResponse
    {
        $query = Product::withoutGlobalScopes()
            ->where('is_active', true)
            ->with('variants')
            ->with('brand:id,name,slug,whatsapp');

        if ($brandSlug) {
            $brand = Brand::where('slug', $brandSlug)->firstOrFail();
            $query->where('brand_id', $brand->id);
        } else {
            $query->where('is_featured', true);
        }

        $products = $query->orderBy('sort')->get()->map(fn (Product $p) => [
            'id' => $p->id,
            'slug' => $p->slug,
            'name' => $p->name,
            'mark' => $p->mark,
            'brand' => $p->brand->name,
            'brand_slug' => $p->brand->slug,
            'price' => $p->price,
            'compare_price' => $p->compare_price,
            'discount_percent' => $p->discount_percent,
            'badge' => $p->badge,
            'rating' => $p->rating,
            'sales_count' => $p->sales_count,
            'cover' => $p->getFirstMediaUrl('cover', 'large') ?: null,
        ]);

        return response()->json(['data' => $products]);
    }

    /** تفاصيل منتج واحد */
    public function product(string $slug): JsonResponse
    {
        $product = Product::withoutGlobalScopes()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->with(['variants', 'brand'])
            ->firstOrFail();

        return response()->json([
            'data' => [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'features' => $product->features,
                'usage_steps' => $product->usage_steps,
                'price' => $product->price,
                'compare_price' => $product->compare_price,
                'video_url' => $product->video_url,
                'gallery' => $product->getMedia('gallery')->map->getUrl('large'),
                'variants' => $product->variants->map(fn ($v) => [
                    'id' => $v->id, 'name' => $v->name,
                    'subtitle' => $v->subtitle, 'price' => $v->price,
                    'is_popular' => $v->is_popular,
                ]),
                'brand' => [
                    'name' => $product->brand->name,
                    'mark' => $product->brand->mark,
                    'whatsapp' => $product->brand->whatsapp,
                    'vodafone_cash' => $product->brand->vodafone_cash,
                    'instapay' => $product->brand->instapay,
                ],
            ],
        ]);
    }

    /** المحافظات + رسوم الشحن */
    public function governorates(): JsonResponse
    {
        return response()->json([
            'data' => Governorate::active()->get(['name', 'shipping_fee']),
        ]);
    }

    /** الثيم الفعّال (متغيرات CSS) — للمناسبات */
    public function theme(ThemeResolver $resolver, ?string $brandSlug = null): JsonResponse
    {
        $brand = $brandSlug ? Brand::where('slug', $brandSlug)->first() : null;
        $tokens = $resolver->resolve($brand);

        return response()->json([
            'data' => [
                'tokens' => $tokens,
                'css' => $resolver->toCssVariables($tokens),
            ],
        ]);
    }
}
