<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Notifications\NewOrderNotification;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function __construct(
        private readonly ShippingCalculator $shipping,
        private readonly FacebookPixelService $facebookPixel,
    ) {}

    /**
     * ينشئ طلبًا من بيانات الواجهة الأمامية.
     * يحسب المبالغ من جانب الخادم ويفحص المخزون بقفل صف لمنع البيع الزائد.
     */
    public function place(array $data, ?UploadedFile $receipt = null): Order
    {
        [$order, $brandId] = DB::transaction(function () use ($data, $receipt): array {
            $product = Product::withoutGlobalScopes()
                ->with('priceTiers')
                ->lockForUpdate()
                ->findOrFail($data['product_id']);

            $variant = null;
            if (! empty($data['variant_id'])) {
                $variant = ProductVariant::where('product_id', $product->id)
                    ->lockForUpdate()
                    ->findOrFail($data['variant_id']);
            }

            $qty = max(1, (int) ($data['qty'] ?? 1));
            $basePrice = $variant?->price ?? $product->price;
            $unitPrice = $basePrice;
            $tierLabel = null;

            if (! empty($data['price_tier_id'])) {
                $tier = $product->priceTiers->firstWhere('id', (int) $data['price_tier_id']);
                if ($tier) {
                    $unitPrice = (int) $tier->price;
                    $tierLabel = $tier->label;
                }
            } else {
                $resolved = $product->resolveTierPrice($qty, $basePrice);
                if ($resolved) {
                    $unitPrice = $resolved['price'];
                    $tierLabel = $resolved['label'];
                }
            }

            $subtotal = $unitPrice * $qty;

            // فحص الحد الأدنى للطلب
            $minOrder = (int) setting('checkout.min_order_total', 0, $product->brand_id);
            if ($subtotal < $minOrder) {
                throw ValidationException::withMessages([
                    'qty' => "الحد الأدنى للطلب هو {$minOrder} ج.م",
                ]);
            }

            // فحص المخزون بقفل صف
            $stockHolder = $variant ?? $product;
            if ($stockHolder->track_stock && $stockHolder->stock < $qty) {
                throw ValidationException::withMessages([
                    'qty' => "الكمية المطلوبة غير متوفرة، المتاح: {$stockHolder->stock}",
                ]);
            }

            // الشحن
            $shippingResult = $this->shipping->calculate(
                $data['governorate'],
                $subtotal,
                $product->brand_id
            );
            $shipping = $shippingResult['fee'];

            // رفع إيصال التحويل إن وُجد
            $receiptPath = null;
            if ($receipt && ($data['payment_method'] ?? null) === 'transfer') {
                $receiptPath = $receipt->store("receipts/{$product->brand_id}", 'public');
            }

            $order = Order::create([
                'brand_id' => $product->brand_id,
                'customer_name' => $data['customer_name'],
                'customer_phone' => $data['customer_phone'],
                'governorate' => $data['governorate'],
                'address' => $data['address'],
                'notes' => $data['notes'] ?? null,
                'payment_method' => $data['payment_method'] ?? 'cod',
                'receipt_path' => $receiptPath,
                'status' => 'pending',
                'subtotal' => $subtotal,
                'shipping' => $shipping,
                'total' => $subtotal + $shipping,
            ]);

            $order->items()->create([
                'product_id' => $product->id,
                'product_variant_id' => $variant?->id,
                'product_name' => $product->name,
                'variant_name' => $variant?->name
                    ? trim($variant->name.($tierLabel ? " — {$tierLabel}" : ''))
                    : $tierLabel,
                'price' => $unitPrice,
                'qty' => $qty,
                'line_total' => $subtotal,
            ]);

            // خصم المخزون
            if ($stockHolder->track_stock) {
                $stockHolder->decrement('stock', $qty);
                $stockHolder->refresh();

                if ($stockHolder->stock <= $stockHolder->low_stock_threshold) {
                    event('stock.low', [$stockHolder, $product->brand_id]);
                }
            }

            $product->increment('sales_count', $qty);

            return [$order->load('items', 'brand'), $product->brand_id];
        });

        // إرسال الإشعار بعد اكتمال الـ transaction
        $this->notifyNewOrder($order, $brandId);

        $order->setAttribute('fb_pixel', $this->trackPurchase($order));

        return $order;
    }

    /**
     * Server-side Purchase event (CAPI) + browser payload for deduplicated fbq track.
     *
     * @return array<string, mixed>|null
     */
    private function trackPurchase(Order $order): ?array
    {
        $item = $order->items->first();
        if (! $item) {
            return null;
        }

        $currency = setting('store.currency', 'EGP', $order->brand_id);
        $nameParts = preg_split('/\s+/', trim($order->customer_name), 2) ?: [];

        return $this->facebookPixel->track(
            'Purchase',
            [
                'content_ids' => [(string) $item->product_id],
                'content_type' => 'product',
                'content_name' => $item->product_name,
                'value' => (float) $order->total,
                'currency' => $currency,
                'num_items' => (int) $order->items->sum('qty'),
                'order_id' => $order->order_no,
            ],
            $order->brand_id,
            [
                'ph' => $order->customer_phone,
                'fn' => $nameParts[0] ?? '',
                'ln' => $nameParts[1] ?? '',
                'ct' => $order->governorate,
                'country' => 'eg',
                'external_id' => (string) $order->id,
            ],
            request(),
            currency: $currency,
            queueBrowser: false,
        );
    }

    private function notifyNewOrder(Order $order, int $brandId): void
    {
        $recipients = User::where(function ($q) use ($brandId) {
            $q->where('brand_id', $brandId)
                ->whereHas('roles', fn ($r) => $r->whereIn('name', ['brand_admin', 'brand_staff']));
        })->orWhereHas('roles', fn ($r) => $r->where('name', 'super_admin'))->get();

        if ($recipients->isNotEmpty()) {
            Notification::send($recipients, new NewOrderNotification($order));
        }
    }
}
