<x-filament-panels::page>
    @php
        /** @var \App\Models\Order $order */
        $order = $this->record;
        $statusFlow = \App\Models\Order::STATUS_FLOW;
        $currentIdx = array_search($order->status, $statusFlow, true);
        $isCancelled = $order->status === 'cancelled';
        $isDelivered = $order->status === 'delivered';
        $nextStatuses = $this->nextStatuses;
        $itemQty = $order->items->sum('qty');

        $statusMeta = [
            'pending'    => ['label' => 'قيد المراجعة', 'icon' => 'heroicon-m-clock',              'color' => 'amber',   'ring' => 'ring-amber-500/30',   'bg' => 'bg-amber-500',   'soft' => 'bg-amber-50 text-amber-800 dark:bg-amber-500/10 dark:text-amber-200'],
            'confirmed'  => ['label' => 'مؤكد',        'icon' => 'heroicon-m-check-badge',         'color' => 'blue',    'ring' => 'ring-blue-500/30',    'bg' => 'bg-blue-500',    'soft' => 'bg-blue-50 text-blue-800 dark:bg-blue-500/10 dark:text-blue-200'],
            'processing' => ['label' => 'قيد التجهيز', 'icon' => 'heroicon-m-cog-6-tooth',         'color' => 'violet',  'ring' => 'ring-violet-500/30',  'bg' => 'bg-violet-500',  'soft' => 'bg-violet-50 text-violet-800 dark:bg-violet-500/10 dark:text-violet-200'],
            'shipped'    => ['label' => 'تم الشحن',    'icon' => 'heroicon-m-truck',               'color' => 'sky',     'ring' => 'ring-sky-500/30',     'bg' => 'bg-sky-500',     'soft' => 'bg-sky-50 text-sky-800 dark:bg-sky-500/10 dark:text-sky-200'],
            'delivered'  => ['label' => 'تم التسليم',  'icon' => 'heroicon-m-check-circle',        'color' => 'emerald', 'ring' => 'ring-emerald-500/30', 'bg' => 'bg-emerald-500', 'soft' => 'bg-emerald-50 text-emerald-800 dark:bg-emerald-500/10 dark:text-emerald-200'],
            'cancelled'  => ['label' => 'ملغي',        'icon' => 'heroicon-m-x-circle',            'color' => 'rose',    'ring' => 'ring-rose-500/30',    'bg' => 'bg-rose-500',    'soft' => 'bg-rose-50 text-rose-800 dark:bg-rose-500/10 dark:text-rose-200'],
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
        @media print {
            .order-no-print { display: none !important; }
            .fi-header, .fi-topbar, .fi-sidebar { display: none !important; }
        }
    </style>

    <div class="mx-auto max-w-[1400px] space-y-5" x-data="{ tab: @entangle('activePanel'), copied: '' }">

        {{-- KPI strip --}}
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
            <div class="rounded-2xl bg-white p-4 ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">الحالة</p>
                <p class="mt-1 flex items-center gap-1.5 text-sm font-bold text-gray-950 dark:text-white">
                    <span class="inline-block h-2 w-2 rounded-full {{ $cur['bg'] }}"></span>
                    {{ $cur['label'] }}
                </p>
            </div>
            <div class="rounded-2xl bg-white p-4 ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">الإجمالي</p>
                <p class="mt-1 text-lg font-black text-emerald-600 dark:text-emerald-400">{{ number_format($order->total) }} <span class="text-xs font-bold">ج.م</span></p>
            </div>
            <div class="rounded-2xl bg-white p-4 ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">المنتجات</p>
                <p class="mt-1 text-lg font-black text-gray-950 dark:text-white">{{ $order->items->count() }} <span class="text-xs font-semibold text-gray-500">({{ $itemQty }} قطعة)</span></p>
            </div>
            <div class="rounded-2xl bg-white p-4 ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">تاريخ الطلب</p>
                <p class="mt-1 text-sm font-bold text-gray-950 dark:text-white">{{ $order->created_at?->format('d/m/Y') }}</p>
                <p class="text-xs text-gray-500">{{ $order->created_at?->format('H:i') }}</p>
            </div>
        </div>

        <div class="grid gap-5 xl:grid-cols-12">

            {{-- ═══ Main column ═══ --}}
            <div class="space-y-5 xl:col-span-8">

                {{-- Status workflow --}}
                <section class="rounded-2xl bg-white p-5 ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 sm:p-6">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <h3 class="text-base font-bold text-gray-950 dark:text-white">مسار الطلب</h3>
                            <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">حدّث الحالة — تُسجّل تلقائياً في السجل مع اسمك.</p>
                        </div>
                        @if($order->handler)
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                <x-filament::icon icon="heroicon-m-user" class="h-3.5 w-3.5" />
                                {{ $order->handler->name }}
                            </span>
                        @endif
                    </div>

                    @if($isCancelled)
                        <div class="mt-4 flex items-center gap-3 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-800 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-200">
                            <x-filament::icon icon="heroicon-m-x-circle" class="h-5 w-5 shrink-0" />
                            الطلب ملغي — لا يمكن متابعة التسليم.
                        </div>
                    @else
                        <div class="mt-5 flex flex-wrap gap-2">
                            @foreach($statusFlow as $idx => $step)
                                @php
                                    $done = $currentIdx !== false && $idx < $currentIdx;
                                    $active = $order->status === $step;
                                    $meta = $statusMeta[$step];
                                @endphp
                                <div @class([
                                    'inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-bold ring-1 ring-inset',
                                    $meta['soft'], $meta['ring'] => $active,
                                    'bg-gray-100 text-gray-500 ring-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700' => ! $active && ! $done,
                                    'bg-emerald-50 text-emerald-700 ring-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-300 dark:ring-emerald-500/30' => $done,
                                ])>
                                    @if($done)
                                        <x-filament::icon icon="heroicon-m-check" class="h-3.5 w-3.5" />
                                    @else
                                        <x-filament::icon :icon="$meta['icon']" class="h-3.5 w-3.5" />
                                    @endif
                                    {{ $meta['label'] }}
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if($nextStatuses !== [])
                        <div class="order-no-print mt-5 rounded-xl border border-gray-200 bg-gray-50/80 p-4 dark:border-white/10 dark:bg-gray-800/40">
                            <label class="mb-2 block text-xs font-bold text-gray-600 dark:text-gray-300">ملاحظة مع التحديث (اختياري)</label>
                            <textarea wire:model="statusNote" rows="2" placeholder="مثال: تم التواصل مع العميل — الشحنة تخرج غداً"
                                      class="mb-3 w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-900 dark:text-white"></textarea>
                            <div class="flex flex-wrap gap-2">
                                @foreach($nextStatuses as $next)
                                    @if($next === 'cancelled')
                                        <x-filament::button color="danger" outlined :icon="$btnIcons[$next] ?? null"
                                            wire:click="updateStatus('{{ $next }}')"
                                            wire:confirm="هل أنت متأكد من إلغاء هذا الطلب؟ سيتم استرجاع المخزون."
                                            wire:loading.attr="disabled"
                                        >{{ $btnLabels[$next] ?? $next }}</x-filament::button>
                                    @else
                                        <x-filament::button :color="$btnColors[$next] ?? 'gray'" :icon="$btnIcons[$next] ?? null"
                                            wire:click="updateStatus('{{ $next }}')"
                                            wire:loading.attr="disabled"
                                        >{{ $btnLabels[$next] ?? $next }}</x-filament::button>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @elseif($isDelivered)
                        <div class="mt-4 flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-200">
                            <x-filament::icon icon="heroicon-m-check-circle" class="h-5 w-5" /> تم التسليم بنجاح
                        </div>
                    @endif
                </section>

                {{-- Products table --}}
                <section class="overflow-hidden rounded-2xl bg-white ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                    <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4 dark:border-white/10 sm:px-6">
                        <h3 class="text-base font-bold text-gray-950 dark:text-white">تفاصيل المنتجات</h3>
                        <span class="rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-bold text-gray-600 dark:bg-gray-800 dark:text-gray-300">{{ $order->items->count() }} بند</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[640px] text-sm">
                            <thead class="bg-gray-50 text-xs font-bold uppercase tracking-wide text-gray-500 dark:bg-gray-800/60 dark:text-gray-400">
                                <tr>
                                    <th class="px-5 py-3 text-start">المنتج</th>
                                    <th class="px-3 py-3 text-start">الباقة</th>
                                    <th class="px-3 py-3 text-center">الكمية</th>
                                    <th class="px-3 py-3 text-end">السعر</th>
                                    <th class="px-5 py-3 text-end">الإجمالي</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-white/10">
                                @foreach($order->items as $item)
                                    @php $thumb = $item->product?->getFirstMediaUrl('cover', 'thumb'); @endphp
                                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30">
                                        <td class="px-5 py-3.5">
                                            <div class="flex items-center gap-3">
                                                <span class="flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-lg bg-gray-100 ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-white/10">
                                                    @if($thumb)
                                                        <img src="{{ $thumb }}" alt="" class="h-full w-full object-cover">
                                                    @else
                                                        <x-filament::icon icon="heroicon-m-cube" class="h-5 w-5 text-gray-400" />
                                                    @endif
                                                </span>
                                                <span class="font-semibold text-gray-950 dark:text-white">{{ $item->product_name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-3 py-3.5 text-gray-600 dark:text-gray-300">{{ $item->variant_name ?: '—' }}</td>
                                        <td class="px-3 py-3.5 text-center font-bold text-gray-950 dark:text-white">{{ $item->qty }}</td>
                                        <td class="px-3 py-3.5 text-end text-gray-600 dark:text-gray-300">{{ number_format($item->price) }} ج.م</td>
                                        <td class="px-5 py-3.5 text-end font-bold text-gray-950 dark:text-white">{{ number_format($item->line_total) }} ج.م</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="border-t border-gray-200 bg-gray-50/80 dark:border-white/10 dark:bg-gray-800/40">
                                <tr>
                                    <td colspan="4" class="px-5 py-2.5 text-end text-gray-600 dark:text-gray-400">المنتجات</td>
                                    <td class="px-5 py-2.5 text-end font-semibold">{{ number_format($order->subtotal) }} ج.م</td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="px-5 py-2.5 text-end text-gray-600 dark:text-gray-400">الشحن</td>
                                    <td class="px-5 py-2.5 text-end font-semibold">{{ number_format($order->shipping) }} ج.م</td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="px-5 py-3 text-end text-base font-bold text-gray-950 dark:text-white">الإجمالي</td>
                                    <td class="px-5 py-3 text-end text-lg font-black text-emerald-600 dark:text-emerald-400">{{ number_format($order->total) }} ج.م</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </section>

                {{-- Activity tabs --}}
                <section class="rounded-2xl bg-white ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                    <div class="flex border-b border-gray-100 dark:border-white/10">
                        <button type="button" @click="tab='timeline'"
                                :class="tab==='timeline' ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400'"
                                class="border-b-2 px-5 py-3.5 text-sm font-bold transition">
                            سجل الحالات ({{ $order->statusHistories->count() }})
                        </button>
                        <button type="button" @click="tab='notes'"
                                :class="tab==='notes' ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400'"
                                class="border-b-2 px-5 py-3.5 text-sm font-bold transition">
                            ملاحظات الفريق ({{ $order->staffNotes->count() }})
                        </button>
                    </div>

                    <div x-show="tab==='timeline'" class="p-5 sm:p-6">
                        @forelse($order->statusHistories->sortByDesc('created_at') as $history)
                            @php $hMeta = $statusMeta[$history->to_status] ?? $statusMeta['pending']; @endphp
                            <div class="relative flex gap-3 pb-5 last:pb-0">
                                <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full {{ $hMeta['bg'] }} text-white">
                                    <x-filament::icon :icon="$hMeta['icon']" class="h-4 w-4" />
                                </span>
                                <div class="min-w-0 flex-1 border-b border-gray-100 pb-5 last:border-0 dark:border-white/10">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="text-sm font-bold text-gray-950 dark:text-white">
                                            {{ $history->from_status ? $history->from_label.' → '.$history->to_label : $history->to_label }}
                                        </span>
                                        <span class="rounded-full px-2 py-0.5 text-[10px] font-bold {{ $hMeta['soft'] }}">{{ $history->to_label }}</span>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        {{ $history->created_at?->format('Y-m-d H:i') }} · {{ $history->changer?->name ?? 'النظام' }}
                                    </p>
                                    @if($history->note)
                                        <p class="mt-2 rounded-lg bg-gray-50 px-3 py-2 text-sm text-gray-700 dark:bg-gray-800 dark:text-gray-300">{{ $history->note }}</p>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="py-8 text-center text-sm text-gray-400">لا يوجد سجل حالات بعد.</p>
                        @endforelse
                    </div>

                    <div x-show="tab==='notes'" x-cloak class="p-5 sm:p-6">
                        <div class="order-no-print mb-4 flex flex-col gap-3 sm:flex-row">
                            <textarea wire:model="newNote" rows="2" placeholder="ملاحظة داخلية للفريق…"
                                      class="flex-1 rounded-lg border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white"></textarea>
                            <x-filament::button wire:click="addNote" wire:loading.attr="disabled" icon="heroicon-m-paper-airplane" class="self-start sm:self-end">
                                إضافة
                            </x-filament::button>
                        </div>
                        @forelse($order->staffNotes as $note)
                            <div class="mb-3 flex gap-3 rounded-xl bg-gray-50 p-3 dark:bg-gray-800/50">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-primary-100 text-sm font-bold text-primary-700 dark:bg-primary-500/15 dark:text-primary-300">
                                    {{ mb_substr($note->author?->name ?? 'ف', 0, 1) }}
                                </span>
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center justify-between gap-2 text-xs text-gray-500">
                                        <span class="font-bold text-gray-700 dark:text-gray-300">{{ $note->author?->name ?? 'فريق العمل' }}</span>
                                        <span>{{ $note->created_at?->format('Y-m-d H:i') }}</span>
                                    </div>
                                    <p class="mt-1 text-sm leading-relaxed text-gray-800 dark:text-gray-200">{{ $note->body }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="py-6 text-center text-sm text-gray-400">لا توجد ملاحظات — أضف أول ملاحظة للفريق.</p>
                        @endforelse
                    </div>
                </section>
            </div>

            {{-- ═══ Sidebar ═══ --}}
            <aside class="space-y-4 xl:col-span-4 xl:sticky xl:top-4 xl:self-start">

                {{-- Customer --}}
                <div class="rounded-2xl bg-white p-5 ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                    <h3 class="flex items-center gap-2 text-sm font-bold text-gray-950 dark:text-white">
                        <x-filament::icon icon="heroicon-m-user-circle" class="h-5 w-5 text-gray-400" /> العميل
                    </h3>
                    <dl class="mt-4 space-y-3 text-sm">
                        <div><dt class="text-xs text-gray-500">الاسم</dt><dd class="font-bold text-gray-950 dark:text-white">{{ $order->customer_name }}</dd></div>
                        <div><dt class="text-xs text-gray-500">الموبايل</dt><dd class="dir-ltr font-bold text-gray-950 dark:text-white">{{ $order->customer_phone }}</dd></div>
                        <div><dt class="text-xs text-gray-500">المحافظة</dt><dd class="font-semibold text-gray-800 dark:text-gray-200">{{ $order->governorate }}</dd></div>
                        <div>
                            <dt class="text-xs text-gray-500">العنوان</dt>
                            <dd class="mt-1 leading-relaxed font-semibold text-gray-800 dark:text-gray-200">{{ $order->address }}</dd>
                        </div>
                    </dl>
                    <div class="order-no-print mt-4 grid grid-cols-2 gap-2">
                        <a href="https://wa.me/{{ $waPhone }}?text={{ $waText }}" target="_blank"
                           class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-[#25D366] px-3 py-2 text-xs font-bold text-white hover:brightness-95">
                            <x-filament::icon icon="heroicon-m-chat-bubble-oval-left-ellipsis" class="h-4 w-4" /> واتساب
                        </a>
                        <a href="tel:{{ $order->customer_phone }}"
                           class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-gray-900 px-3 py-2 text-xs font-bold text-white dark:bg-white dark:text-gray-900">
                            <x-filament::icon icon="heroicon-m-phone" class="h-4 w-4" /> اتصال
                        </a>
                        <button type="button" class="col-span-2 inline-flex items-center justify-center gap-1.5 rounded-lg bg-gray-100 px-3 py-2 text-xs font-bold text-gray-700 ring-1 ring-gray-200 dark:bg-gray-800 dark:text-gray-200 dark:ring-white/10"
                                x-on:click="navigator.clipboard.writeText(@js($order->address)); copied='addr'; setTimeout(()=>copied='',1500)">
                            <x-filament::icon icon="heroicon-m-map-pin" class="h-4 w-4" />
                            <span x-text="copied==='addr' ? 'تم نسخ العنوان ✓' : 'نسخ العنوان'">نسخ العنوان</span>
                        </button>
                    </div>
                    @if($order->notes)
                        <div class="mt-4 rounded-xl bg-amber-50 p-3 dark:bg-amber-500/10">
                            <p class="text-xs font-bold text-amber-700 dark:text-amber-300">ملاحظة العميل</p>
                            <p class="mt-1 text-sm text-amber-900 dark:text-amber-100">{{ $order->notes }}</p>
                        </div>
                    @endif
                </div>

                {{-- Payment --}}
                <div class="rounded-2xl bg-white p-5 ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                    <h3 class="flex items-center gap-2 text-sm font-bold text-gray-950 dark:text-white">
                        <x-filament::icon icon="heroicon-m-banknotes" class="h-5 w-5 text-gray-400" /> الدفع
                    </h3>
                    <dl class="mt-4 space-y-2.5 text-sm">
                        <div class="flex justify-between"><dt class="text-gray-500">الطريقة</dt><dd class="font-bold">{{ \App\Models\Order::PAYMENT_METHODS[$order->payment_method] ?? $order->payment_method }}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">المنتجات</dt><dd class="font-semibold">{{ number_format($order->subtotal) }} ج.م</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">الشحن</dt><dd class="font-semibold">{{ number_format($order->shipping) }} ج.م</dd></div>
                        <div class="flex justify-between border-t border-dashed border-gray-200 pt-2 dark:border-white/10">
                            <dt class="font-bold text-gray-950 dark:text-white">الإجمالي</dt>
                            <dd class="text-lg font-black text-emerald-600 dark:text-emerald-400">{{ number_format($order->total) }} ج.م</dd>
                        </div>
                    </dl>
                    @if($order->receipt_path)
                        <a href="{{ asset('storage/'.$order->receipt_path) }}" target="_blank"
                           class="mt-4 inline-flex w-full items-center justify-center gap-1.5 rounded-lg bg-primary-50 px-3 py-2 text-xs font-bold text-primary-700 ring-1 ring-primary-200 dark:bg-primary-500/10 dark:text-primary-300">
                            <x-filament::icon icon="heroicon-m-document-text" class="h-4 w-4" /> عرض إيصال التحويل
                        </a>
                    @endif
                </div>

                {{-- Meta --}}
                <div class="rounded-2xl bg-white p-5 ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                    <h3 class="text-sm font-bold text-gray-950 dark:text-white">معلومات الطلب</h3>
                    <dl class="mt-4 space-y-2.5 text-sm">
                        <div class="flex justify-between gap-3">
                            <dt class="text-gray-500">رقم الطلب</dt>
                            <dd class="font-bold text-gray-950 dark:text-white">{{ $order->order_no }}</dd>
                        </div>
                        @if($order->brand)
                            <div class="flex justify-between gap-3">
                                <dt class="text-gray-500">البراند</dt>
                                <dd class="font-semibold">{{ $order->brand->name }}</dd>
                            </div>
                        @endif
                        @if($order->confirmed_at)
                            <div class="flex justify-between gap-3">
                                <dt class="text-gray-500">تاريخ التأكيد</dt>
                                <dd class="font-semibold">{{ $order->confirmed_at->format('Y-m-d H:i') }}</dd>
                            </div>
                        @endif
                        <div class="flex justify-between gap-3">
                            <dt class="text-gray-500">آخر تحديث</dt>
                            <dd class="font-semibold">{{ $order->updated_at?->format('Y-m-d H:i') }}</dd>
                        </div>
                    </dl>
                    <button type="button" class="order-no-print mt-4 w-full rounded-lg bg-gray-100 px-3 py-2 text-xs font-bold text-gray-700 dark:bg-gray-800 dark:text-gray-200"
                            x-on:click="navigator.clipboard.writeText(@js($order->order_no)); copied='no'; setTimeout(()=>copied='',1500)">
                        <span x-text="copied==='no' ? 'تم نسخ رقم الطلب ✓' : 'نسخ رقم الطلب'">نسخ رقم الطلب</span>
                    </button>
                </div>
            </aside>
        </div>
    </div>
</x-filament-panels::page>
