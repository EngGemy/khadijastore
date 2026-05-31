<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>طلب جديد رقم {{ $order->order_no }}</title>
<style>
  body { margin:0; padding:0; background:#f5f5f5; font-family: 'Segoe UI', Tahoma, Arial, sans-serif; direction: rtl; }
  .wrap { max-width:580px; margin:30px auto; background:#fff; border-radius:12px; overflow:hidden; box-shadow:0 2px 12px rgba(0,0,0,.08); }
  .header { background:#16a34a; padding:28px 32px; color:#fff; }
  .header h1 { margin:0; font-size:20px; font-weight:700; }
  .header p { margin:6px 0 0; opacity:.85; font-size:14px; }
  .body { padding:28px 32px; }
  .label { font-size:12px; color:#888; font-weight:600; text-transform:uppercase; margin-bottom:4px; }
  .value { font-size:15px; color:#222; margin-bottom:16px; font-weight:500; }
  .row { display:flex; gap:20px; flex-wrap:wrap; }
  .col { flex:1; min-width:140px; }
  .divider { border:none; border-top:1px solid #eee; margin:22px 0; }
  .total-row { display:flex; justify-content:space-between; align-items:center; padding:12px 16px; background:#f9fafb; border-radius:8px; }
  .total-label { font-size:15px; color:#555; }
  .total-amount { font-size:20px; font-weight:800; color:#16a34a; }
  .btn { display:inline-block; background:#16a34a; color:#fff !important; text-decoration:none; padding:12px 28px; border-radius:8px; font-weight:700; font-size:15px; margin-top:20px; }
  .footer { background:#f9fafb; padding:16px 32px; text-align:center; font-size:12px; color:#aaa; }
  .badge { display:inline-block; background:#fef3c7; color:#92400e; font-size:12px; font-weight:700; padding:3px 10px; border-radius:20px; }
</style>
</head>
<body>
<div class="wrap">
  <div class="header">
    <h1>طلب جديد رقم {{ $order->order_no }}</h1>
    <p>{{ $order->created_at->format('Y-m-d H:i') }}</p>
  </div>
  <div class="body">
    <div class="row">
      <div class="col">
        <div class="label">اسم العميل</div>
        <div class="value">{{ $order->customer_name }}</div>
      </div>
      <div class="col">
        <div class="label">رقم الموبايل</div>
        <div class="value" dir="ltr">{{ $order->customer_phone }}</div>
      </div>
    </div>
    <div class="row">
      <div class="col">
        <div class="label">المحافظة</div>
        <div class="value">{{ $order->governorate }}</div>
      </div>
      <div class="col">
        <div class="label">طريقة الدفع</div>
        <div class="value">{{ \App\Models\Order::PAYMENT_METHODS[$order->payment_method] ?? $order->payment_method }}</div>
      </div>
    </div>
    <div class="label">العنوان</div>
    <div class="value">{{ $order->address }}</div>

    <hr class="divider">

    <div class="label">المنتجات</div>
    @foreach($order->items as $item)
    <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid #f0f0f0; font-size:14px;">
      <span>{{ $item->product_name }}@if($item->variant_name) · {{ $item->variant_name }}@endif × {{ $item->qty }}</span>
      <span style="font-weight:700;">{{ number_format($item->line_total) }} ج.م</span>
    </div>
    @endforeach

    <hr class="divider">

    <div class="total-row">
      <div>
        <div style="font-size:13px; color:#888; margin-bottom:2px;">الإجمالي النهائي</div>
        <div class="total-amount">{{ number_format($order->total) }} ج.م</div>
      </div>
      <div style="text-align:left;">
        <div style="font-size:13px; color:#888;">الشحن: {{ number_format($order->shipping) }} ج.م</div>
        <div style="font-size:13px; color:#888;">المنتج: {{ number_format($order->subtotal) }} ج.م</div>
      </div>
    </div>

    <div style="text-align:center;">
      <a class="btn" href="{{ url('/admin/orders/'.$order->id) }}">عرض الطلب في لوحة التحكم</a>
    </div>
  </div>
  <div class="footer">متجر العلامات · هذا البريد تلقائي، لا تحتاج للرد عليه.</div>
</div>
</body>
</html>
