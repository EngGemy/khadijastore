<x-filament-panels::page>
    @php
        $order = $this->record;
        $statusFlow = \App\Models\Order::STATUS_FLOW;
        $currentIdx = array_search($order->status, $statusFlow, true);
        $isCancelled = $order->status === 'cancelled';
        $isDelivered = $order->status === 'delivered';
        $nextStatuses = $this->nextStatuses;
        $itemQty = $order->items->sum('qty');

        $statusMeta = [
            'pending'    => ['label' => 'قيد المراجعة', 'icon' => 'heroicon-m-clock', 'dot' => '#f59e0b'],
            'confirmed'  => ['label' => 'مؤكد', 'icon' => 'heroicon-m-check-badge', 'dot' => '#3b82f6'],
            'processing' => ['label' => 'قيد التجهيز', 'icon' => 'heroicon-m-cog-6-tooth', 'dot' => '#8b5cf6'],
            'shipped'    => ['label' => 'تم الشحن', 'icon' => 'heroicon-m-truck', 'dot' => '#0ea5e9'],
            'delivered'  => ['label' => 'تم التسليم', 'icon' => 'heroicon-m-check-circle', 'dot' => '#10b981'],
            'cancelled'  => ['label' => 'ملغي', 'icon' => 'heroicon-m-x-circle', 'dot' => '#f43f5e'],
        ];
        $cur = $statusMeta[$order->status] ?? $statusMeta['pending'];
        $btnColors = ['confirmed' => 'primary', 'processing' => 'info', 'shipped' => 'info', 'delivered' => 'success', 'cancelled' => 'danger'];
        $btnIcons = ['confirmed' => 'heroicon-m-check-badge', 'processing' => 'heroicon-m-cog-6-tooth', 'shipped' => 'heroicon-m-truck', 'delivered' => 'heroicon-m-check-circle', 'cancelled' => 'heroicon-m-x-mark'];
        $btnLabels = ['confirmed' => 'تأكيد الطلب', 'processing' => 'بدء التجهيز', 'shipped' => 'تم الشحن', 'delivered' => 'تم التسليم', 'cancelled' => 'إلغاء الطلب'];

        $digits = preg_replace('/\D+/', '', (string) $order->customer_phone);
        $waPhone = str_starts_with($digits, '0') ? '2'.$digits : (str_starts_with($digits, '20') ? $digits : '20'.ltrim($digits, '0'));
        $waText = rawurlencode("مرحباً {$order->customer_name}، بخصوص طلبك رقم {$order->order_no}");
    @endphp

    <style>
        .ov { width: 100%; max-width: 100%; direction: rtl; }
        .ov-kpis { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; margin-bottom: 20px; }
        .ov-layout { display: grid; grid-template-columns: minmax(0, 1fr) 340px; gap: 20px; align-items: start; }
        .ov-card { background: var(--fi-bg, #fff); border: 1px solid rgba(0,0,0,.08); border-radius: 16px; padding: 20px; margin-bottom: 20px; }
        .dark .ov-card { border-color: rgba(255,255,255,.1); }
        .ov-kpi-label { font-size: 12px; color: #6b7280; margin: 0 0 4px; }
        .ov-kpi-value { font-size: 15px; font-weight: 800; color: var(--fi-text, #111); margin: 0; }
        .ov-kpi-value--lg { font-size: 20px; color: #059669; }
        .ov-title { font-size: 16px; font-weight: 800; margin: 0 0 4px; color: var(--fi-text, #111); }
        .ov-sub { font-size: 12px; color: #6b7280; margin: 0 0 16px; }
        .ov-steps { display: flex; flex-wrap: wrap; gap: 8px; margin: 16px 0; }
        .ov-step { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 999px; font-size: 12px; font-weight: 700; border: 1px solid #e5e7eb; color: #6b7280; background: #f9fafb; }
        .ov-step.is-done { background: #ecfdf5; color: #047857; border-color: #a7f3d0; }
        .ov-step.is-active { background: #eff6ff; color: #1d4ed8; border-color: #93c5fd; }
        .ov-table-wrap { overflow-x: auto; margin: 0 -4px; }
        .ov-table { width: 100%; border-collapse: collapse; font-size: 14px; min-width: 560px; }
        .ov-table th { text-align: right; font-size: 11px; text-transform: uppercase; letter-spacing: .04em; color: #6b7280; padding: 10px 12px; background: #f9fafb; border-bottom: 1px solid #e5e7eb; }
        .ov-table td { padding: 12px; border-bottom: 1px solid #f3f4f6; vertical-align: middle; }
        .ov-table tfoot td { background: #f9fafb; font-weight: 600; }
        .ov-product { display: flex; align-items: center; gap: 10px; }
        .ov-thumb { width: 44px; height: 44px; border-radius: 10px; object-fit: cover; background: #f3f4f6; flex-shrink: 0; }
        .ov-side dl { margin: 0; font-size: 14px; }
        .ov-side dt { font-size: 11px; color: #6b7280; margin-bottom: 2px; }
        .ov-side dd { margin: 0 0 12px; font-weight: 700; color: var(--fi-text, #111); }
        .ov-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-top: 12px; }
        .ov-btn { display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 10px 12px; border-radius: 10px; font-size: 12px; font-weight: 800; text-decoration: none; border: none; cursor: pointer; }
        .ov-btn--wa { background: #25D366; color: #fff; }
        .ov-btn--call { background: #111; color: #fff; }
        .ov-btn--muted { background: #f3f4f6; color: #374151; grid-column: span 2; }
        .ov-tabs { display: flex; border-bottom: 1px solid #e5e7eb; margin: -4px -4px 16px; }
        .ov-tab { padding: 12px 16px; font-size: 13px; font-weight: 800; border: none; background: none; cursor: pointer; color: #6b7280; border-bottom: 2px solid transparent; margin-bottom: -1px; }
        .ov-tab.is-on { color: #16a34a; border-bottom-color: #16a34a; }
        .ov-note-box { background: #fffbeb; border-radius: 12px; padding: 12px; margin-top: 12px; font-size: 13px; }
        .ov-alert { padding: 12px 14px; border-radius: 12px; font-size: 13px; font-weight: 700; margin-top: 12px; }
        .ov-alert--danger { background: #fff1f2; color: #be123c; border: 1px solid #fecdd3; }
        .ov-alert--ok { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
        .ov-status-form { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 12px; padding: 16px; margin-top: 16px; }
        .ov-status-form textarea { width: 100%; border-radius: 10px; border: 1px solid #d1d5db; padding: 10px; font-size: 13px; margin-bottom: 12px; min-height: 64px; }
        .ov-timeline-item { display: flex; gap: 12px; padding-bottom: 16px; border-bottom: 1px solid #f3f4f6; margin-bottom: 16px; }
        .ov-timeline-item:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
        .ov-dot { width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; flex-shrink: 0; }
        @media (max-width: 1100px) {
            .ov-layout { grid-template-columns: 1fr; }
            .ov-kpis { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
    </style>

    <div class="ov" x-data="{ tab: @entangle('activePanel'), copied: '' }">

        <div class="ov-kpis">
            <div class="ov-card" style="margin-bottom:0">
                <p class="ov-kpi-label">الحالة</p>
                <p class="ov-kpi-value"><span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:{{ $cur['dot'] }};margin-left:6px"></span>{{ $cur['label'] }}</p>
            </div>
            <div class="ov-card" style="margin-bottom:0">
                <p class="ov-kpi-label">الإجمالي</p>
                <p class="ov-kpi-value ov-kpi-value--lg">{{ number_format($order->total) }} ج.م</p>
            </div>
            <div class="ov-card" style="margin-bottom:0">
                <p class="ov-kpi-label">المنتجات</p>
                <p class="ov-kpi-value">{{ $order->items->count() }} <span style="font-weight:600;color:#6b7280;font-size:12px">({{ $itemQty }} قطعة)</span></p>
            </div>
            <div class="ov-card" style="margin-bottom:0">
                <p class="ov-kpi-label">تاريخ الطلب</p>
                <p class="ov-kpi-value">{{ $order->created_at?->format('d/m/Y') }}</p>
                <p class="ov-kpi-label" style="margin-top:2px">{{ $order->created_at?->format('H:i') }}</p>
            </div>
        </div>

        <div class="ov-layout">
            <div>
                <div class="ov-card">
                    <h3 class="ov-title">مسار الطلب</h3>
                    <p class="ov-sub">حدّث الحالة — تُسجّل في السجل مع اسمك.@if($order->handler) <strong>المسؤول: {{ $order->handler->name }}</strong>@endif</p>

                    @if($isCancelled)
                        <div class="ov-alert ov-alert--danger">الطلب ملغي — لا يمكن متابعة التسليم.</div>
                    @else
                        <div class="ov-steps">
                            @foreach($statusFlow as $idx => $step)
                                @php
                                    $done = $currentIdx !== false && $idx < $currentIdx;
                                    $active = $order->status === $step;
                                    $meta = $statusMeta[$step];
                                @endphp
                                <span @class(['ov-step', 'is-done' => $done, 'is-active' => $active])>
                                    <x-filament::icon :icon="$meta['icon']" style="width:14px;height:14px" />
                                    {{ $meta['label'] }}
                                </span>
                            @endforeach
                        </div>
                    @endif

                    @if($nextStatuses !== [])
                        <div class="ov-status-form ov-no-print">
                            <label class="ov-kpi-label">ملاحظة مع التحديث (اختياري)</label>
                            <textarea wire:model="statusNote" placeholder="مثال: تم التواصل مع العميل"></textarea>
                            <div style="display:flex;flex-wrap:wrap;gap:8px">
                                @foreach($nextStatuses as $next)
                                    @if($next === 'cancelled')
                                        <x-filament::button color="danger" outlined :icon="$btnIcons[$next] ?? null"
                                            wire:click="updateStatus('{{ $next }}')"
                                            wire:confirm="هل أنت متأكد من إلغاء هذا الطلب؟ سيتم استرجاع المخزون."
                                            wire:loading.attr="disabled">{{ $btnLabels[$next] }}</x-filament::button>
                                    @else
                                        <x-filament::button :color="$btnColors[$next] ?? 'gray'" :icon="$btnIcons[$next] ?? null"
                                            wire:click="updateStatus('{{ $next }}')"
                                            wire:loading.attr="disabled">{{ $btnLabels[$next] }}</x-filament::button>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @elseif($isDelivered)
                        <div class="ov-alert ov-alert--ok">تم تسليم الطلب بنجاح.</div>
                    @endif
                </div>

                <div class="ov-card">
                    <h3 class="ov-title">تفاصيل المنتجات <span style="font-size:12px;font-weight:600;color:#6b7280">({{ $order->items->count() }} بند)</span></h3>
                    <div class="ov-table-wrap">
                        <table class="ov-table">
                            <thead>
                                <tr>
                                    <th>المنتج</th>
                                    <th>الباقة</th>
                                    <th style="text-align:center">الكمية</th>
                                    <th style="text-align:left">السعر</th>
                                    <th style="text-align:left">الإجمالي</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                    @php $thumb = $item->product?->getFirstMediaUrl('cover', 'thumb'); @endphp
                                    <tr>
                                        <td>
                                            <div class="ov-product">
                                                @if($thumb)
                                                    <img src="{{ $thumb }}" alt="" class="ov-thumb">
                                                @else
                                                    <span class="ov-thumb" style="display:grid;place-items:center">📦</span>
                                                @endif
                                                <strong>{{ $item->product_name }}</strong>
                                            </div>
                                        </td>
                                        <td>{{ $item->variant_name ?: '—' }}</td>
                                        <td style="text-align:center;font-weight:800">{{ $item->qty }}</td>
                                        <td style="text-align:left">{{ number_format($item->price) }} ج.م</td>
                                        <td style="text-align:left;font-weight:800">{{ number_format($item->line_total) }} ج.م</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr><td colspan="4" style="text-align:left">المنتجات</td><td style="text-align:left">{{ number_format($order->subtotal) }} ج.م</td></tr>
                                <tr><td colspan="4" style="text-align:left">الشحن</td><td style="text-align:left">{{ number_format($order->shipping) }} ج.م</td></tr>
                                <tr><td colspan="4" style="text-align:left;font-size:16px">الإجمالي</td><td style="text-align:left;font-size:18px;color:#059669">{{ number_format($order->total) }} ج.م</td></tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="ov-card" style="margin-bottom:0">
                    <div class="ov-tabs ov-no-print">
                        <button type="button" class="ov-tab" :class="tab==='timeline' && 'is-on'" @click="tab='timeline'">سجل الحالات ({{ $order->statusHistories->count() }})</button>
                        <button type="button" class="ov-tab" :class="tab==='notes' && 'is-on'" @click="tab='notes'">ملاحظات الفريق ({{ $order->staffNotes->count() }})</button>
                    </div>

                    <div x-show="tab==='timeline'">
                        @forelse($order->statusHistories->sortByDesc('created_at') as $history)
                            @php $hMeta = $statusMeta[$history->to_status] ?? $statusMeta['pending']; @endphp
                            <div class="ov-timeline-item">
                                <span class="ov-dot" style="background:{{ $hMeta['dot'] }}">
                                    <x-filament::icon :icon="$hMeta['icon']" style="width:16px;height:16px" />
                                </span>
                                <div style="flex:1;min-width:0">
                                    <strong style="font-size:14px">{{ $history->from_status ? $history->from_label.' → '.$history->to_label : $history->to_label }}</strong>
                                    <p class="ov-kpi-label" style="margin-top:4px">{{ $history->created_at?->format('Y-m-d H:i') }} · {{ $history->changer?->name ?? 'النظام' }}</p>
                                    @if($history->note)<p style="margin:8px 0 0;font-size:13px;background:#f9fafb;padding:8px 10px;border-radius:8px">{{ $history->note }}</p>@endif
                                </div>
                            </div>
                        @empty
                            <p style="text-align:center;color:#9ca3af;padding:24px 0">لا يوجد سجل حالات.</p>
                        @endforelse
                    </div>

                    <div x-show="tab==='notes'" x-cloak>
                        <div class="ov-no-print" style="display:flex;gap:10px;margin-bottom:16px;flex-wrap:wrap">
                            <textarea wire:model="newNote" rows="2" placeholder="ملاحظة داخلية للفريق…" style="flex:1;min-width:200px;border-radius:10px;border:1px solid #d1d5db;padding:10px;font-size:13px"></textarea>
                            <x-filament::button wire:click="addNote" wire:loading.attr="disabled" icon="heroicon-m-paper-airplane">إضافة</x-filament::button>
                        </div>
                        @forelse($order->staffNotes as $note)
                            <div style="display:flex;gap:10px;margin-bottom:12px;padding:12px;background:#f9fafb;border-radius:12px">
                                <span style="width:36px;height:36px;border-radius:50%;background:#dcfce7;color:#166534;display:grid;place-items:center;font-weight:800;flex-shrink:0">{{ mb_substr($note->author?->name ?? 'ف', 0, 1) }}</span>
                                <div style="flex:1">
                                    <div style="display:flex;justify-content:space-between;gap:8px;font-size:11px;color:#6b7280">
                                        <strong style="color:#374151">{{ $note->author?->name ?? 'فريق العمل' }}</strong>
                                        <span>{{ $note->created_at?->format('Y-m-d H:i') }}</span>
                                    </div>
                                    <p style="margin:6px 0 0;font-size:13px;line-height:1.5">{{ $note->body }}</p>
                                </div>
                            </div>
                        @empty
                            <p style="text-align:center;color:#9ca3af;padding:16px 0">لا توجد ملاحظات بعد.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <aside class="ov-side">
                <div class="ov-card">
                    <h3 class="ov-title">العميل</h3>
                    <dl>
                        <dt>الاسم</dt><dd>{{ $order->customer_name }}</dd>
                        <dt>الموبايل</dt><dd dir="ltr" style="text-align:right">{{ $order->customer_phone }}</dd>
                        <dt>المحافظة</dt><dd>{{ $order->governorate }}</dd>
                        <dt>العنوان</dt><dd style="font-weight:600;line-height:1.5">{{ $order->address }}</dd>
                    </dl>
                    <div class="ov-actions ov-no-print">
                        <a href="https://wa.me/{{ $waPhone }}?text={{ $waText }}" target="_blank" class="ov-btn ov-btn--wa">واتساب</a>
                        <a href="tel:{{ $order->customer_phone }}" class="ov-btn ov-btn--call">اتصال</a>
                        <button type="button" class="ov-btn ov-btn--muted"
                                x-on:click="navigator.clipboard.writeText(@js($order->address)); copied='addr'; setTimeout(()=>copied='',1500)">
                            <span x-text="copied==='addr' ? 'تم نسخ العنوان ✓' : 'نسخ العنوان'">نسخ العنوان</span>
                        </button>
                    </div>
                    @if($order->notes)
                        <div class="ov-note-box"><strong>ملاحظة العميل</strong><br>{{ $order->notes }}</div>
                    @endif
                </div>

                <div class="ov-card">
                    <h3 class="ov-title">الدفع</h3>
                    <dl>
                        <dt>الطريقة</dt><dd>{{ \App\Models\Order::PAYMENT_METHODS[$order->payment_method] ?? $order->payment_method }}</dd>
                        <dt>المنتجات</dt><dd>{{ number_format($order->subtotal) }} ج.م</dd>
                        <dt>الشحن</dt><dd>{{ number_format($order->shipping) }} ج.م</dd>
                        <dt>الإجمالي</dt><dd style="font-size:20px;color:#059669">{{ number_format($order->total) }} ج.م</dd>
                    </dl>
                    @if($order->receipt_path)
                        <a href="{{ asset('storage/'.$order->receipt_path) }}" target="_blank" class="ov-btn ov-btn--muted" style="display:flex;margin-top:8px">عرض إيصال التحويل</a>
                    @endif
                </div>

                <div class="ov-card" style="margin-bottom:0">
                    <h3 class="ov-title">معلومات الطلب</h3>
                    <dl>
                        <dt>رقم الطلب</dt><dd>{{ $order->order_no }}</dd>
                        @if($order->brand)<dt>البراند</dt><dd>{{ $order->brand->name }}</dd>@endif
                        @if($order->confirmed_at)<dt>تاريخ التأكيد</dt><dd>{{ $order->confirmed_at->format('Y-m-d H:i') }}</dd>@endif
                        <dt>آخر تحديث</dt><dd>{{ $order->updated_at?->format('Y-m-d H:i') }}</dd>
                    </dl>
                    <button type="button" class="ov-btn ov-btn--muted ov-no-print" style="display:flex;margin-top:8px"
                            x-on:click="navigator.clipboard.writeText(@js($order->order_no)); copied='no'; setTimeout(()=>copied='',1500)">
                        <span x-text="copied==='no' ? 'تم نسخ رقم الطلب ✓' : 'نسخ رقم الطلب'">نسخ رقم الطلب</span>
                    </button>
                </div>
            </aside>
        </div>
    </div>
</x-filament-panels::page>
