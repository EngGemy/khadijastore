<?php

namespace App\Services\AI;

use App\Models\Governorate;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\OrderService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class OrderChatFlow
{
    private const KEY = 'ai_widget_flow';

    private array $flow;

    public function __construct(private readonly OrderService $orders) {}

    // ─── واجهة عامة ──────────────────────────────────────────────────────────

    public function isActive(): bool
    {
        return session(self::KEY . '.state', 'idle') !== 'idle';
    }

    public function getState(): string
    {
        return session(self::KEY . '.state', 'idle');
    }

    public function reset(): void
    {
        session()->forget(self::KEY);
    }

    /**
     * نقطة الدخول الوحيدة — تُعالج المدخل وتُرجع استجابة منظّمة.
     */
    public function handle(string $input): array
    {
        $this->flow = session(self::KEY, $this->blank());
        $state      = $this->flow['state'];

        // أوامر خاصة مُشفَّرة قادمة من أزرار الـ UI
        if (str_starts_with($input, '__')) {
            $result = $this->handleCommand($input);
        } else {
            $result = match ($state) {
                'product_search'  => $this->stepProductSearch($input),
                'variant_select'  => $this->stepVariantSelect($input),
                'name'            => $this->stepName($input),
                'phone'           => $this->stepPhone($input),
                'gov'             => $this->stepGov($input),
                'address'         => $this->stepAddress($input),
                'confirm'         => $this->stepConfirm($input),
                default           => $this->startFlow($input),
            };
        }

        session([self::KEY => $this->flow]);

        return $result;
    }

    // ─── أوامر خاصة ──────────────────────────────────────────────────────────

    private function handleCommand(string $cmd): array
    {
        // __SELECT_PRODUCT__:id
        if (str_starts_with($cmd, '__SELECT_PRODUCT__:')) {
            $id = (int) substr($cmd, strlen('__SELECT_PRODUCT__:'));
            return $this->selectProduct($id);
        }

        // __SELECT_VARIANT__:id
        if (str_starts_with($cmd, '__SELECT_VARIANT__:')) {
            $id = (int) substr($cmd, strlen('__SELECT_VARIANT__:'));
            return $this->selectVariant($id);
        }

        // __SELECT_GOV__:name
        if (str_starts_with($cmd, '__SELECT_GOV__:')) {
            $name = substr($cmd, strlen('__SELECT_GOV__:'));
            return $this->stepGov($name);
        }

        // __CANCEL__
        if ($cmd === '__CANCEL__') {
            $this->reset();
            $this->flow = $this->blank();
            return $this->reply('تم إلغاء الطلب. يمكنك البدء من جديد في أي وقت.', 'chat', quick: ['اطلب منتج', 'تصفح المنتجات', 'استفسار']);
        }

        // __START_ORDER__
        if ($cmd === '__START_ORDER__') {
            $this->flow['state'] = 'product_search';
            return $this->reply('ما اسم المنتج أو الفئة التي تريد طلبها؟', 'chat');
        }

        return $this->reply('أمر غير معروف.', 'chat');
    }

    // ─── خطوة: البداية (كشف النية من النص الحر) ─────────────────────────────

    private function startFlow(string $input): array
    {
        $this->flow['state'] = 'product_search';
        return $this->stepProductSearch($input);
    }

    // ─── خطوة 1: البحث عن المنتج ─────────────────────────────────────────────

    private function stepProductSearch(string $input): array
    {
        $products = $this->searchProducts($input);

        if ($products->isEmpty()) {
            $this->flow['state'] = 'product_search';
            return $this->reply(
                'لم أجد منتجاً مطابقاً. جرّب كلمة أخرى أو تصفّح المنتجات.',
                'chat',
                quick: ['تصفح المنتجات', 'إلغاء']
            );
        }

        $this->flow['state'] = 'product_search';

        return $this->reply(
            'وجدت ' . $products->count() . ' منتج. اختر ما يناسبك:',
            'products',
            data: ['products' => $this->formatProducts($products)]
        );
    }

    // ─── اختيار منتج بعد عرض البطاقات ───────────────────────────────────────

    private function selectProduct(int $id): array
    {
        $product = Product::withoutGlobalScopes()
            ->where('is_active', true)
            ->with(['variants', 'media'])
            ->find($id);

        if (! $product) {
            return $this->reply('المنتج غير متاح حالياً. اختر منتجاً آخر.', 'chat');
        }

        $this->flow['product'] = [
            'id'       => $product->id,
            'name'     => $product->name,
            'price'    => $product->price,
            'brand_id' => $product->brand_id,
        ];

        $variants = $product->variants->where('stock', '>', 0)->values();

        if ($variants->count() > 1) {
            $this->flow['state'] = 'variant_select';
            return $this->reply(
                'اخترت: **' . $product->name . '**. الآن اختر المقاس أو اللون:',
                'variants',
                data: ['variants' => $variants->map(fn ($v) => [
                    'id'    => $v->id,
                    'name'  => $v->name,
                    'price' => $v->price,
                    'stock' => $v->stock,
                ])->values()->all()]
            );
        }

        // لا متغيّرات — انتقل لجمع البيانات
        $this->flow['variant_id'] = $variants->first()?->id;
        $this->flow['state']      = 'name';

        return $this->reply(
            'ممتاز! اخترت **' . $product->name . '**.' . "\n" . 'ما اسمك الكريم؟',
            'chat'
        );
    }

    // ─── خطوة 2: اختيار المتغيّر ─────────────────────────────────────────────

    private function stepVariantSelect(string $input): array
    {
        // المستخدم ربما كتب اسم المتغيّر — نحاول مطابقته
        $productId = $this->flow['product']['id'] ?? null;
        if (! $productId) {
            $this->flow['state'] = 'product_search';
            return $this->reply('حدث خطأ. ابدأ من جديد.', 'chat');
        }

        $variant = ProductVariant::where('product_id', $productId)
            ->where(fn ($q) => $q->where('name', 'like', "%{$input}%"))
            ->first();

        if (! $variant) {
            return $this->reply('لم أفهم. اضغط على أحد الخيارات أعلاه.', 'chat');
        }

        return $this->selectVariant($variant->id);
    }

    private function selectVariant(int $id): array
    {
        $variant = ProductVariant::find($id);
        if (! $variant) {
            return $this->reply('الخيار غير متاح. اختر من القائمة.', 'chat');
        }

        $this->flow['variant_id'] = $id;
        $this->flow['state']      = 'name';

        return $this->reply(
            'تم اختيار: **' . $variant->name . '**.' . "\n" . 'ما اسمك الكريم؟',
            'chat'
        );
    }

    // ─── خطوة 3: الاسم ───────────────────────────────────────────────────────

    private function stepName(string $input): array
    {
        $name = trim($input);
        if (mb_strlen($name) < 3 || mb_strlen($name) > 120) {
            return $this->reply('الرجاء إدخال اسم صحيح (3 أحرف على الأقل).', 'chat');
        }

        $this->flow['name']  = $name;
        $this->flow['state'] = 'phone';

        return $this->reply('شكراً ' . $name . '! وما رقم موبايلك؟ (مثال: 01012345678)', 'chat');
    }

    // ─── خطوة 4: الموبايل ────────────────────────────────────────────────────

    private function stepPhone(string $input): array
    {
        $phone = preg_replace('/\D/', '', trim($input));

        if (! preg_match('/^01[0-9]{9}$/', $phone)) {
            return $this->reply('الرقم غير صحيح. يجب أن يبدأ بـ 01 ويتكون من 11 رقمًا.', 'chat');
        }

        $this->flow['phone'] = $phone;
        $this->flow['state'] = 'gov';

        $govs = Governorate::active()->pluck('name')->all();

        return $this->reply('اختر محافظتك:', 'gov_select', data: ['govs' => $govs]);
    }

    // ─── خطوة 5: المحافظة ────────────────────────────────────────────────────

    private function stepGov(string $input): array
    {
        $gov = trim($input);
        $exists = Governorate::where('name', $gov)->where('is_active', true)->exists();

        if (! $exists) {
            $govs = Governorate::active()->pluck('name')->all();
            return $this->reply('المحافظة غير صحيحة. اختر من القائمة:', 'gov_select', data: ['govs' => $govs]);
        }

        $this->flow['gov']   = $gov;
        $this->flow['state'] = 'address';

        return $this->reply('وما عنوانك بالتفصيل؟ (الحي — الشارع — رقم المبنى)', 'chat');
    }

    // ─── خطوة 6: العنوان ─────────────────────────────────────────────────────

    private function stepAddress(string $input): array
    {
        $address = trim($input);
        if (mb_strlen($address) < 10) {
            return $this->reply('الرجاء كتابة العنوان بالتفصيل (على الأقل 10 أحرف).', 'chat');
        }

        $this->flow['address'] = $address;
        $this->flow['state']   = 'confirm';

        return $this->buildConfirmation();
    }

    // ─── خطوة 7: التأكيد ─────────────────────────────────────────────────────

    private function stepConfirm(string $input): array
    {
        $lower = mb_strtolower(trim($input));
        $yes   = ['نعم', 'أكد', 'اكد', 'تمام', 'موافق', 'yes', 'يلا', 'ok', 'ايوه', 'اه', 'آه'];
        $no    = ['لا', 'الغاء', 'إلغاء', 'cancel', 'no', 'تعديل', 'تغيير'];

        foreach ($yes as $w) {
            if (str_contains($lower, $w)) {
                return $this->placeOrder();
            }
        }

        foreach ($no as $w) {
            if (str_contains($lower, $w)) {
                $this->reset();
                $this->flow = $this->blank();
                return $this->reply('تم إلغاء الطلب. يمكنك البدء من جديد.', 'chat', quick: ['اطلب منتج', 'تصفح المنتجات']);
            }
        }

        return $this->buildConfirmation('لم أفهم. هل تؤكد الطلب؟');
    }

    // ─── إنشاء الطلب ─────────────────────────────────────────────────────────

    private function placeOrder(): array
    {
        try {
            $order = $this->orders->place([
                'product_id'     => $this->flow['product']['id'],
                'variant_id'     => $this->flow['variant_id'] ?: null,
                'qty'            => 1,
                'customer_name'  => $this->flow['name'],
                'customer_phone' => $this->flow['phone'],
                'governorate'    => $this->flow['gov'],
                'address'        => $this->flow['address'],
                'payment_method' => 'cod',
                'notes'          => 'طلب عبر المساعد الذكي',
            ]);

            $waUrl = null;
            if ($order->brand?->whatsapp) {
                $text  = "طلب عبر المساعد الذكي\nرقم الطلب: {$order->order_no}\nالإجمالي: {$order->total} ج.م";
                $waUrl = 'https://wa.me/' . preg_replace('/\D/', '', $order->brand->whatsapp)
                    . '?text=' . rawurlencode($text);
            }

            $this->reset();
            $this->flow = $this->blank();

            return $this->reply(
                '🎉 تم تسجيل طلبك بنجاح!',
                'order_done',
                data: [
                    'order_no'      => $order->order_no,
                    'total'         => $order->total,
                    'whatsapp_url'  => $waUrl,
                ],
                quick: ['اطلب منتجاً آخر', 'تصفح المنتجات']
            );

        } catch (\Illuminate\Validation\ValidationException $e) {
            $msg = collect($e->errors())->flatten()->first() ?? 'خطأ في بيانات الطلب.';
            return $this->reply('عذراً: ' . $msg . ' راجع بياناتك وحاول مجدداً.', 'chat');
        } catch (\Throwable $e) {
            Log::error('OrderChatFlow::placeOrder', ['error' => $e->getMessage()]);
            return $this->reply('حدث خطأ أثناء إنشاء الطلب. يرجى المحاولة لاحقاً.', 'chat');
        }
    }

    // ─── مساعد: بناء رسالة التأكيد ───────────────────────────────────────────

    private function buildConfirmation(string $prefix = 'تفضّل ملخص طلبك. هل تؤكد؟'): array
    {
        $product = $this->flow['product'];

        // احسب رسوم الشحن تقريبياً
        $gov         = Governorate::where('name', $this->flow['gov'])->first();
        $shippingFee = $gov?->shipping_fee ?? '—';
        $price       = $product['price'];

        // لو فيه variant، اجلب سعره
        if ($this->flow['variant_id']) {
            $v = ProductVariant::find($this->flow['variant_id']);
            if ($v) {
                $price = $v->price;
            }
        }

        $total = is_numeric($shippingFee) ? ($price + $shippingFee) : $price;

        return $this->reply($prefix, 'summary', data: [
            'product'   => $product['name'],
            'price'     => $price,
            'shipping'  => $shippingFee,
            'total'     => $total,
            'name'      => $this->flow['name'],
            'phone'     => $this->flow['phone'],
            'gov'       => $this->flow['gov'],
            'address'   => $this->flow['address'],
        ], quick: ['تأكيد الطلب', 'إلغاء']);
    }

    // ─── بحث المنتجات ────────────────────────────────────────────────────────

    private function searchProducts(string $query): Collection
    {
        $words = collect(explode(' ', $query))
            ->filter(fn ($w) => mb_strlen($w) > 1)->take(4);

        $q = Product::withoutGlobalScopes()
            ->where('is_active', true)
            ->with(['variants', 'media'])
            ->take(6);

        if ($words->isNotEmpty()) {
            $q->where(function ($sub) use ($words) {
                foreach ($words as $word) {
                    $sub->orWhere('name', 'like', "%{$word}%");
                }
            });
        } else {
            $q->where('is_featured', true)->orderByDesc('sales_count');
        }

        return $q->get();
    }

    private function formatProducts(Collection $products): array
    {
        return $products->map(fn (Product $p) => [
            'id'    => $p->id,
            'name'  => $p->name,
            'price' => $p->price,
            'thumb' => $p->getFirstMediaUrl('cover', 'thumb'),
            'stock' => $p->isOutOfStock() ? 'نفد' : 'متاح',
        ])->values()->all();
    }

    // ─── مساعد: بناء الرد ────────────────────────────────────────────────────

    private function reply(string $text, string $action, array $data = [], array $quick = []): array
    {
        return compact('text', 'action', 'data', 'quick');
    }

    private function blank(): array
    {
        return [
            'state'      => 'idle',
            'product'    => null,
            'variant_id' => null,
            'name'       => null,
            'phone'      => null,
            'gov'        => null,
            'address'    => null,
        ];
    }
}
