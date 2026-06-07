<?php

namespace App\Services\AI;

use App\Models\Listing;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AssistantContextBuilder
{
    private int    $productLimit;
    private string $storeName;

    public function __construct()
    {
        $this->productLimit = config('ai.context_products', 20);
        $this->storeName    = setting('store.name', 'متجر العلامات');
    }

    /**
     * ابنِ السياق الكامل للـ LLM — منتجات + دليل + تعليمات.
     */
    public function build(string $userQuery, ?int $brandId = null): array
    {
        $products = $this->fetchProducts($userQuery, $brandId);
        $listings = $this->fetchListings($userQuery);           // دائماً — بغض النظر عن الكلمات

        // ── سياق المنتجات ──────────────────────────────────────────────
        $productLines = $products->map(function (Product $p) {
            $price = $this->priceRange($p);
            $stock = $p->isOutOfStock() ? 'نفد المخزون' : 'متاح';
            $url   = route('product.show', $p->slug);
            return "• [{$p->id}] {$p->name} | {$price} ج.م | {$stock} | {$url}";
        })->implode("\n");

        // ── سياق الدليل (الأطباء والحضانات) ───────────────────────────
        $listingLines = '';
        if ($listings->isNotEmpty()) {
            $listingLines = $listings->map(fn ($l) => $this->formatListing($l))->implode("\n");
        }

        // ── System Prompt ──────────────────────────────────────────────
        $system = <<<SYSTEM
أنت مساعد ذكي لـ «{$this->storeName}» — منصة تجمع متجراً إلكترونياً ودليل أطباء وحضانات.

**صلاحياتك:**
1. المنتجات: رشّح من القائمة أدناه فقط، اذكر السعر والرابط.
2. الأطباء والحضانات: أجب من بيانات الدليل أدناه، اذكر رقم الهاتف وواتساب والعنوان والتخصص والرسوم.
3. الأسئلة العامة والمقارنات: أجب بإيجاز ولطف بالعربية.
4. إذا لم تجد معلومة في البيانات: قُل ذلك واقترح بديلاً.

**مهم:** لا تخترع أرقام هواتف أو أسعار غير موجودة. ردودك بالعربية دائماً وموجزة.

---
**المنتجات المتاحة:**
{$productLines}

---
**دليل الأطباء والحضانات:**
{$listingLines}
SYSTEM;

        $productMap = $products->keyBy('id')->map(fn (Product $p) => [
            'id'    => $p->id,
            'name'  => $p->name,
            'price' => $p->price,
            'thumb' => $p->getFirstMediaUrl('cover', 'thumb'),
            'url'   => route('product.show', $p->slug),
            'stock' => $p->isOutOfStock() ? 'نفد' : 'متاح',
        ])->values()->all();

        return [
            'system'      => $system,
            'products'    => $productMap,
            'product_map' => $products->keyBy('id')->all(),
        ];
    }

    // ─── جلب الدليل ── دائماً، مع تخصيص حسب الاستفسار ───────────────────────

    private function fetchListings(string $query): Collection
    {
        // تحقّق أن الجدول موجود قبل الاستعلام
        if (! Schema::hasTable('listings')) {
            return collect();
        }

        $cacheKey = 'ai.listings.' . md5($query);

        return Cache::remember($cacheKey, 180, function () use ($query) {

            // نظّف الاستفسار وأزل الـ tashkeel وحوّل ه → ة للمطابقة
            $normalized = $this->normalizeArabic($query);

            // الكلمات بعد التطبيع
            $words = collect(preg_split('/\s+/', $normalized))
                ->filter(fn ($w) => mb_strlen($w) > 1)->take(5);

            $base = Listing::withoutGlobalScopes()
                ->where('is_active', true)
                ->orderByDesc('is_featured')
                ->orderByDesc('rating');

            if ($words->isNotEmpty()) {
                $specific = (clone $base)
                    ->where(function ($sub) use ($words, $query) {
                        foreach ($words as $word) {
                            // ابحث بالكلمة الأصلية وبالكلمة المطبّعة
                            $sub->orWhere('name',        'like', "%{$word}%")
                                ->orWhere('summary',     'like', "%{$word}%")
                                ->orWhere('governorate', 'like', "%{$word}%")
                                ->orWhere('type',        'like', "%{$word}%");
                        }
                        // بحث في JSON data بالكلمات الأصلية
                        foreach (preg_split('/\s+/', $query) as $raw) {
                            if (mb_strlen($raw) > 1) {
                                $sub->orWhereRaw("data LIKE ?", ["%{$raw}%"]);
                            }
                        }
                    })
                    ->take(10)->get();

                // إذا وجد نتائج → أكمل بالمميزين إن احتجنا
                if ($specific->isNotEmpty()) {
                    if ($specific->count() < 6) {
                        $extra = (clone $base)
                            ->whereNotIn('id', $specific->pluck('id'))
                            ->take(6 - $specific->count())->get();
                        return $specific->merge($extra);
                    }
                    return $specific;
                }
            }

            // fallback — المميزون والأعلى تقييماً
            return $base->take(8)->get();
        });
    }

    // ─── تطبيع عربي لتجاوز فروق ة/ه، أ/ا، إلخ ───────────────────────────────

    private function normalizeArabic(string $text): string
    {
        // ة → ه (أو يمكن العكس — نحن ننظّم الاستفسار فقط للمطابقة)
        $text = str_replace(['ة', 'ۃ'], 'ه', $text);
        // أ، إ، آ → ا
        $text = str_replace(['أ', 'إ', 'آ', 'ٱ'], 'ا', $text);
        // ى → ي
        $text = str_replace(['ى', 'ئ'], 'ي', $text);
        // حذف التشكيل
        $text = preg_replace('/[\x{0610}-\x{061A}\x{064B}-\x{065F}]/u', '', $text);
        return mb_strtolower(trim($text));
    }

    // ─── تنسيق إدراج الدليل في الـ prompt ───────────────────────────────────

    private function formatListing(Listing $l): string
    {
        $type    = $l->type === 'doctor' ? '🩺 طبيب' : '🏫 حضانة';
        $rating  = $l->rating   ? " | تقييم: {$l->rating}/5" : '';
        $phone   = $l->phone    ? " | هاتف: {$l->phone}" : '';
        $wa      = $l->whatsapp ? " | واتساب: {$l->whatsapp}" : '';
        $gov     = $l->governorate ? " ({$l->governorate})" : '';
        $address = $l->address  ? " | {$l->address}" : '';
        $summary = $l->summary  ? " | {$l->summary}" : '';

        $extra = '';
        $data  = is_array($l->data) ? $l->data : [];

        if ($l->type === 'doctor') {
            $parts = array_filter([
                ($data['specialty']        ?? '') ? 'تخصص: ' . $data['specialty']        : '',
                ($data['clinic_name']      ?? '') ? 'عيادة: ' . $data['clinic_name']      : '',
                ($data['experience_years'] ?? '') ? 'خبرة: '  . $data['experience_years'] . ' سنة' : '',
                ($data['consultation_fee'] ?? '') ? 'كشف: '   . $data['consultation_fee'] . ' ج.م'  : '',
                ($data['working_hours']    ?? '') ? 'مواعيد: '. $data['working_hours']    : '',
            ]);
            $extra = $parts ? ' | ' . implode(' | ', $parts) : '';
        } elseif ($l->type === 'nursery') {
            $fromM = $data['age_from_months'] ?? null;
            $toM   = $data['age_to_months']   ?? null;
            $feeF  = $data['monthly_fee_from'] ?? null;
            $feeT  = $data['monthly_fee_to']   ?? null;
            $parts = array_filter([
                ($fromM !== null && $toM !== null)
                    ? 'السن: ' . round($fromM / 12, 1) . '–' . round($toM / 12, 1) . ' سنة' : '',
                ($feeF || $feeT)
                    ? 'رسوم: ' . ($feeF ?? '') . '–' . ($feeT ?? '') . ' ج/شهر' : '',
                !empty($data['programs'])
                    ? 'برامج: ' . implode('، ', array_slice((array)$data['programs'], 0, 3)) : '',
            ]);
            $extra = $parts ? ' | ' . implode(' | ', $parts) : '';
        }

        return "• {$l->name}{$gov} [{$type}]{$rating}{$phone}{$wa}{$address}{$extra}{$summary}";
    }

    // ─── جلب المنتجات ─────────────────────────────────────────────────────────

    private function fetchProducts(string $query, ?int $brandId): Collection
    {
        $cacheKey = 'ai.ctx.' . md5($query . '.' . ($brandId ?? 'all'));

        return Cache::remember($cacheKey, 120, function () use ($query, $brandId) {
            $q = Product::withoutGlobalScopes()
                ->where('is_active', true)
                ->with(['variants:id,product_id,name,price,stock,track_stock', 'media'])
                ->take($this->productLimit);

            if ($brandId) {
                $q->where('brand_id', $brandId);
            }

            $words = collect(explode(' ', $query))
                ->filter(fn ($w) => mb_strlen($w) > 1)->take(4);

            if ($words->isNotEmpty()) {
                $q->where(function ($sub) use ($words) {
                    foreach ($words as $word) {
                        $sub->orWhere('name', 'like', "%{$word}%")
                            ->orWhere('short_description', 'like', "%{$word}%");
                    }
                });

                $results = $q->orderByDesc('sales_count')->get();

                if ($results->count() < 4) {
                    $extra = Product::withoutGlobalScopes()
                        ->where('is_active', true)
                        ->with(['variants:id,product_id,name,price,stock,track_stock', 'media'])
                        ->where('is_featured', true)
                        ->when($brandId, fn ($q2) => $q2->where('brand_id', $brandId))
                        ->whereNotIn('id', $results->pluck('id'))
                        ->take($this->productLimit - $results->count())
                        ->orderByDesc('sales_count')->get();
                    return $results->merge($extra);
                }

                return $results;
            }

            return $q->where('is_featured', true)->orderByDesc('sales_count')->get();
        });
    }

    private function priceRange(Product $p): string
    {
        if ($p->variants->isNotEmpty()) {
            $prices = $p->variants->pluck('price')->filter()->sort()->values();
            if ($prices->isEmpty()) {
                return (string) $p->price;
            }
            if ($prices->count() === 1 || $prices->first() === $prices->last()) {
                return (string) $prices->first();
            }
            return $prices->first() . '–' . $prices->last();
        }
        return (string) $p->price;
    }
}
