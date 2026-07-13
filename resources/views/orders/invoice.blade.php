<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>فاتورة {{ $order->order_no }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Cairo', 'Segoe UI', Tahoma, Arial, sans-serif;
            color: #1f2937;
            background: #f3f4f6;
            line-height: 1.6;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .sheet {
            width: 210mm;
            min-height: 297mm;
            margin: 16px auto;
            background: #fff;
            padding: 18mm 16mm;
            box-shadow: 0 10px 40px rgba(0, 0, 0, .12);
        }

        .toolbar {
            max-width: 210mm;
            margin: 16px auto 0;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        .toolbar button {
            font-family: inherit;
            font-size: 14px;
            font-weight: 700;
            border: none;
            border-radius: 10px;
            padding: 10px 22px;
            cursor: pointer;
        }
        .btn-print { background: #16a34a; color: #fff; }
        .btn-close { background: #e5e7eb; color: #374151; }

        .inv-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
            border-bottom: 3px solid #16a34a;
            padding-bottom: 18px;
            margin-bottom: 22px;
        }
        .inv-brand { font-size: 26px; font-weight: 900; color: #111827; }
        .inv-brand small { display: block; font-size: 13px; font-weight: 600; color: #6b7280; margin-top: 4px; }
        .inv-tag {
            display: inline-block;
            margin-top: 10px;
            background: #ecfdf5;
            color: #047857;
            font-size: 14px;
            font-weight: 800;
            padding: 6px 14px;
            border-radius: 999px;
        }
        .inv-logo { max-height: 64px; max-width: 160px; object-fit: contain; }

        .inv-meta {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 24px;
        }
        .inv-box {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 14px 16px;
            background: #fafafa;
        }
        .inv-box h4 {
            font-size: 13px;
            font-weight: 800;
            color: #16a34a;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: .03em;
        }
        .inv-box .row { display: flex; gap: 8px; font-size: 14px; margin-bottom: 5px; }
        .inv-box .row .k { color: #6b7280; min-width: 78px; }
        .inv-box .row .v { font-weight: 700; color: #111827; }

        table.items {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
            margin-bottom: 20px;
        }
        table.items thead th {
            background: #16a34a;
            color: #fff;
            padding: 11px 12px;
            font-weight: 800;
            text-align: right;
        }
        table.items thead th:nth-child(4),
        table.items thead th:nth-child(5),
        table.items thead th:nth-child(6) { text-align: center; }
        table.items tbody td {
            padding: 11px 12px;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: middle;
        }
        table.items tbody td:nth-child(4),
        table.items tbody td:nth-child(5),
        table.items tbody td:nth-child(6) { text-align: center; }
        table.items tbody tr:nth-child(even) { background: #f9fafb; }
        .prod { display: flex; align-items: center; gap: 10px; }
        .prod img { width: 40px; height: 40px; border-radius: 8px; object-fit: cover; background: #f3f4f6; }
        .prod span.ph { width: 40px; height: 40px; border-radius: 8px; background: #f3f4f6; display: inline-grid; place-items: center; }

        .totals {
            display: flex;
            justify-content: flex-start;
            margin-bottom: 28px;
        }
        .totals table {
            width: 300px;
            border-collapse: collapse;
            font-size: 14px;
        }
        .totals td { padding: 8px 12px; }
        .totals td.k { color: #6b7280; }
        .totals td.v { text-align: left; font-weight: 700; }
        .totals tr.grand td {
            border-top: 2px solid #16a34a;
            font-size: 18px;
            font-weight: 900;
            color: #16a34a;
            padding-top: 12px;
        }

        .inv-foot {
            border-top: 1px dashed #d1d5db;
            padding-top: 16px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
        }
        .inv-note {
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 10px;
            padding: 12px 14px;
            font-size: 13px;
            margin-bottom: 20px;
        }

        @media print {
            body { background: #fff; }
            .toolbar { display: none !important; }
            .sheet { margin: 0; box-shadow: none; width: auto; min-height: auto; padding: 0; }
            @page { size: A4 portrait; margin: 12mm; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <button type="button" class="btn-print" onclick="window.print()">طباعة</button>
        <button type="button" class="btn-close" onclick="window.close()">إغلاق</button>
    </div>

    <div class="sheet">
        <div class="inv-head">
            <div>
                <div class="inv-brand">
                    {{ $storeName }}
                    @if($order->brand)<small>{{ $order->brand->name }}</small>@endif
                </div>
                <span class="inv-tag">فاتورة طلب · {{ $order->order_no }}</span>
            </div>
            @if($storeLogo)
                <img src="{{ $storeLogo }}" alt="{{ $storeName }}" class="inv-logo">
            @endif
        </div>

        <div class="inv-meta">
            <div class="inv-box">
                <h4>بيانات الطلب</h4>
                <div class="row"><span class="k">رقم الطلب</span><span class="v">{{ $order->order_no }}</span></div>
                <div class="row"><span class="k">التاريخ</span><span class="v">{{ $order->created_at?->format('Y-m-d H:i') }}</span></div>
                <div class="row"><span class="k">الحالة</span><span class="v">{{ \App\Models\Order::STATUSES[$order->status] ?? $order->status }}</span></div>
                <div class="row"><span class="k">الدفع</span><span class="v">{{ \App\Models\Order::PAYMENT_METHODS[$order->payment_method] ?? $order->payment_method }}</span></div>
            </div>
            <div class="inv-box">
                <h4>بيانات العميل</h4>
                <div class="row"><span class="k">الاسم</span><span class="v">{{ $order->customer_name }}</span></div>
                <div class="row"><span class="k">الموبايل</span><span class="v" dir="ltr">{{ $order->customer_phone }}</span></div>
                <div class="row"><span class="k">المحافظة</span><span class="v">{{ $order->governorate }}</span></div>
                <div class="row"><span class="k">العنوان</span><span class="v">{{ $order->address }}</span></div>
            </div>
        </div>

        <table class="items">
            <thead>
                <tr>
                    <th style="width:36px">#</th>
                    <th>المنتج</th>
                    <th>الباقة</th>
                    <th style="width:64px">الكمية</th>
                    <th style="width:96px">السعر</th>
                    <th style="width:104px">الإجمالي</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    @php $thumb = $item->product?->getFirstMediaUrl('cover', 'thumb'); @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <div class="prod">
                                @if($thumb)
                                    <img src="{{ $thumb }}" alt="">
                                @else
                                    <span class="ph">📦</span>
                                @endif
                                <span>{{ $item->product_name }}</span>
                            </div>
                        </td>
                        <td>{{ $item->variant_name ?: '—' }}</td>
                        <td>{{ $item->qty }}</td>
                        <td>{{ number_format($item->price) }} ج.م</td>
                        <td>{{ number_format($item->line_total) }} ج.م</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <table>
                <tr><td class="k">إجمالي المنتجات</td><td class="v">{{ number_format($order->subtotal) }} ج.م</td></tr>
                <tr><td class="k">الشحن</td><td class="v">{{ number_format($order->shipping) }} ج.م</td></tr>
                <tr class="grand"><td class="k">الإجمالي المستحق</td><td class="v">{{ number_format($order->total) }} ج.م</td></tr>
            </table>
        </div>

        @if($order->notes)
            <div class="inv-note"><strong>ملاحظة العميل:</strong> {{ $order->notes }}</div>
        @endif

        <div class="inv-foot">
            شكراً لتعاملكم مع {{ $storeName }}@if($storePhone) · للتواصل: <span dir="ltr">{{ $storePhone }}</span>@endif
            <br>
            تم إصدار الفاتورة في {{ now()->format('Y-m-d H:i') }}
        </div>
    </div>

    <script>
        window.addEventListener('load', function () {
            setTimeout(function () { window.print(); }, 350);
        });
    </script>
</body>
</html>
