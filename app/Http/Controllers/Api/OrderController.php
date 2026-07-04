<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orders) {}

    /** إنشاء طلب جديد من الواجهة الأمامية */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        $order = $this->orders->place(
            $request->validated(),
            $request->file('receipt'),
        );

        // رابط واتساب البراند مع ملخص الطلب (لإتمام التحويل/التأكيد)
        $brand = $order->brand;
        $waText = $this->buildWhatsappText($order);
        $waUrl = $brand->whatsapp
            ? 'https://wa.me/'.preg_replace('/\D/', '', $brand->whatsapp).'?text='.rawurlencode($waText)
            : null;

        return response()->json([
            'success' => true,
            'message' => 'تم استلام طلبك بنجاح! سنتواصل معك للتأكيد.',
            'data' => [
                'order_no' => $order->order_no,
                'total' => $order->total,
                'whatsapp_url' => $waUrl,
            ],
            'fb_pixel' => $order->getAttribute('fb_pixel'),
        ], 201);
    }

    private function buildWhatsappText(Order $order): string
    {
        $lines = [
            "طلب جديد · {$order->order_no}",
            "البراند: {$order->brand->name}",
        ];
        foreach ($order->items as $item) {
            $lines[] = "المنتج: {$item->product_name}".($item->variant_name ? " ({$item->variant_name})" : '')." ×{$item->qty}";
        }
        $lines[] = "الاسم: {$order->customer_name}";
        $lines[] = "الموبايل: {$order->customer_phone}";
        $lines[] = "المحافظة: {$order->governorate}";
        $lines[] = "العنوان: {$order->address}";
        $lines[] = 'الشحن: '.($order->shipping === 0 ? 'مجاني' : $order->shipping.' ج.م');
        $lines[] = "الإجمالي: {$order->total} ج.م";

        if ($order->payment_method === 'transfer') {
            $lines[] = '— تم الدفع بالتحويل، مرفق صورة الإيصال —';
        }

        return implode("\n", $lines);
    }
}
