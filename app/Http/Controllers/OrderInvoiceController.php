<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;

class OrderInvoiceController extends Controller
{
    public function show(int|string $order): View
    {
        $order = Order::withoutGlobalScopes()
            ->with(['items.product.media', 'brand'])
            ->findOrFail($order);

        Gate::authorize('view', $order);

        return view('orders.invoice', [
            'order' => $order,
            'storeName' => setting('store.name', 'متجر سند'),
            'storeLogo' => store_logo_url($order->brand_id),
            'storePhone' => setting('store.support_whatsapp', setting('store.phone')),
        ]);
    }
}
