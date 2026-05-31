<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\Product;
use App\Models\ProductVariant;

class OrderObserver
{
    /** تسجيل الحالة الأولى عند إنشاء الطلب */
    public function created(Order $order): void
    {
        OrderStatusHistory::create([
            'order_id' => $order->id,
            'from_status' => null,
            'to_status' => $order->status,
            'changed_by' => auth()->id(),
        ]);
    }

    /** عند تعديل الطلب: استرجاع المخزون عند الإلغاء + تسجيل تغيير الحالة */
    public function updating(Order $order): void
    {
        if (
            $order->isDirty('status')
            && $order->status === 'cancelled'
            && $order->getOriginal('status') !== 'cancelled'
        ) {
            $this->restoreStock($order);
        }
    }

    public function updated(Order $order): void
    {
        if ($order->wasChanged('status')) {
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'from_status' => $order->getOriginal('status'),
                'to_status' => $order->status,
                'changed_by' => auth()->id(),
            ]);
        }
    }

    private function restoreStock(Order $order): void
    {
        $items = $order->items()->get();

        foreach ($items as $item) {
            if ($item->product_variant_id) {
                $variant = ProductVariant::find($item->product_variant_id);
                if ($variant?->track_stock) {
                    $variant->increment('stock', $item->qty);
                }
            } else {
                $product = Product::withoutGlobalScopes()->find($item->product_id);
                if ($product?->track_stock) {
                    $product->increment('stock', $item->qty);
                }
            }
        }
    }
}
