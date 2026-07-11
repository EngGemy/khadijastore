<x-filament-panels::page>
    @php
        /** @var \App\Models\Order $order */
        $order = $this->record;
        $statusFlow = \App\Models\Order::STATUS_FLOW;
        $currentIdx = array_search($order->status, $statusFlow, true);
        $isCancelled = $order->status === 'cancelled';
        $statusColors = [
            'pending' => 'bg-amber-100 text-amber-800 ring-amber-200 dark:bg-amber-500/15 dark:text-amber-300 dark:ring-amber-500/30',
            'confirmed' => 'bg-blue-100 text-blue-800 ring-blue-200 dark:bg-blue-500/15 dark:text-blue-300 dark:ring-blue-500/30',
            'processing' => 'bg-violet-100 text-violet-800 ring-violet-200 dark:bg-violet-500/15 dark:text-violet-300 dark:ring-violet-500/30',
            'shipped' => 'bg-sky-100 text-sky-800 ring-sky-200 dark:bg-sky-500/15 dark:text-sky-300 dark:ring-sky-500/30',
            'delivered' => 'bg-emerald-100 text-emerald-800 ring-emerald-200 dark:bg-emerald-500/15 dark:text-emerald-300 dark:ring-emerald-500/30',
            'cancelled' => 'bg-rose-100 text-rose-800 ring-rose-200 dark:bg-rose-500/15 dark:text-rose-300 dark:ring-rose-500/30',
        ];
        $btnColors = [
            'confirmed' => 'primary',
            'processing' => 'info',
            'shipped' => 'info',
            'delivered' => 'success',
            'cancelled' => 'danger',
        ];
        $nextStatuses = $this->nextStatuses;
    @endphp

    <div class="space-y-6 print:space-y-4">

        {{-- Header --}}
        <div class="fi-section rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 sm:p-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-xl font-extrabold text-gray-950 dark:text-white">{{ $order->order_no }}</h2>
                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold ring-1 ring-inset {{ $statusColors[$order->status] ?? $statusColors['pending'] }}">
                            {{ $order->status_label }}
                        </span>
                    </div>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        {{ $order->created_at?->format('Y-m-d H:i') }}
                        @if($order->brand)
                            · {{ $order->brand->name }}
                        @endif
                        @if($order->handler)
                            · المعالج: <strong>{{ $order->handler->name }}</strong>
                        @endif
                    </p>
                </div>
                <div class="rounded-xl bg-emerald-50 px-4 py-3 text-start dark:bg-emerald-500/10">
                    <p class="text-xs font-bold uppercase tracking-wide text-emerald-700 dark:text-emerald-300">الإجمالي</p>
                    <p class="text-2xl font-extrabold text-emerald-800 dark:text-emerald-200">{{ number_format($order->total) }} <span class="text-sm">ج.م</span></p>
                    <p class="text-xs text-emerald-700/70 dark:text-emerald-300/70">{{ \App\Models\Order::PAYMENT_METHODS[$order->payment_method] ?? $order->payment_method }}</p>
                </div>
            </div>
        </div>

        {{-- Status pipeline --}}
        <div class="fi-section rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 sm:p-6">
            <h3 class="text-base font-bold text-gray-950 dark:text-white">تتبّع حالة الطلب</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">حدّث الحالة مباشرة من هنا مع إمكانية إضافة ملاحظة للفريق أو العميل.</p>

            @if($isCancelled)
                <div class="mt-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-800 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-200">
                    هذا الطلب ملغي ولا يمكن متابعة مسار التسليم.
                </div>
            @else
                <div class="mt-6 overflow-x-auto pb-2">
                    <div class="flex min-w-[640px] items-center gap-0">
                        @foreach($statusFlow as $idx => $step)
                            @php
                                $done = $currentIdx !== false && $idx < $currentIdx;
                                $active = $order->status === $step;
                                $upcoming = $currentIdx !== false && $idx > $currentIdx;
                            @endphp
                            <div class="flex flex-1 items-center">
                                <div class="flex flex-col items-center gap-2 text-center">
                                    <div @class([
                                        'flex h-11 w-11 items-center justify-center rounded-full text-sm font-extrabold ring-2 transition-all',
                                        'bg-emerald-600 text-white ring-emerald-600 shadow-lg shadow-emerald-600/25' => $done,
                                        'bg-gray-950 text-white ring-gray-950 scale-110 shadow-lg dark:bg-white dark:text-gray-950 dark:ring-white' => $active,
                                        'bg-gray-100 text-gray-400 ring-gray-200 dark:bg-gray-800 dark:text-gray-500 dark:ring-gray-700' => $upcoming,
                                    ])>{{ $idx + 1 }}</div>
                                    <span @class([
                                        'text-[11px] font-bold whitespace-nowrap',
                                        'text-emerald-700 dark:text-emerald-300' => $done,
                                        'text-gray-950 dark:text-white' => $active,
                                        'text-gray-400 dark:text-gray-500' => $upcoming,
                                    ])>{{ \App\Models\Order::STATUSES[$step] }}</span>
                                </div>
                                @if(!$loop->last)
                                    <div @class([
                                        'mx-2 h-0.5 flex-1 rounded-full',
                                        $done ? 'bg-emerald-500' : 'bg-gray-200 dark:bg-gray-700',
                                    ])></div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($nextStatuses !== [])
                <div class="mt-6 border-t border-gray-100 pt-5 dark:border-white/10">
                    <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">ملاحظة مع التحديث (اختياري)</label>
                    <textarea wire:model="statusNote" rows="2" placeholder="مثال: تم التواصل مع العميل — الشحنة تخرج غداً"
                              class="mb-4 w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white"></textarea>

                    <div class="flex flex-wrap gap-2">
                        @foreach($nextStatuses as $next)
                            @php
                                $buttonLabel = match ($next) {
                                    'confirmed' => '✓ تأكيد الطلب',
                                    'processing' => '⚙ بدء التجهيز',
                                    'shipped' => '🚚 تم الشحن',
                                    'delivered' => '✅ تم التسليم',
                                    'cancelled' => '✕ إلغاء الطلب',
                                    default => \App\Models\Order::STATUSES[$next] ?? $next,
                                };
                            @endphp

                            @if($next === 'cancelled')
                                <x-filament::button
                                    :color="$btnColors[$next] ?? 'gray'"
                                    wire:click="updateStatus('{{ $next }}')"
                                    wire:confirm="هل أنت متأكد من إلغاء هذا الطلب؟ سيتم استرجاع المخزون."
                                >
                                    {{ $buttonLabel }}
                                </x-filament::button>
                            @else
                                <x-filament::button
                                    :color="$btnColors[$next] ?? 'gray'"
                                    wire:click="updateStatus('{{ $next }}')"
                                >
                                    {{ $buttonLabel }}
                                </x-filament::button>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <div class="grid gap-6 xl:grid-cols-3">

            {{-- Customer --}}
            <div class="fi-section rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 sm:p-6 xl:col-span-1">
                <h3 class="text-base font-bold text-gray-950 dark:text-white">بيانات العميل</h3>
                <dl class="mt-4 space-y-3 text-sm">
                    <div><dt class="text-gray-500 dark:text-gray-400">الاسم</dt><dd class="font-bold text-gray-950 dark:text-white">{{ $order->customer_name }}</dd></div>
                    <div><dt class="text-gray-500 dark:text-gray-400">الموبايل</dt><dd class="font-bold dir-ltr text-end">{{ $order->customer_phone }}</dd></div>
                    <div><dt class="text-gray-500 dark:text-gray-400">المحافظة</dt><dd class="font-semibold">{{ $order->governorate }}</dd></div>
                    <div><dt class="text-gray-500 dark:text-gray-400">العنوان</dt><dd class="font-semibold leading-relaxed">{{ $order->address }}</dd></div>
                    @if($order->notes)
                        <div class="rounded-xl bg-amber-50 p-3 dark:bg-amber-500/10">
                            <dt class="text-xs font-bold text-amber-700 dark:text-amber-300">ملاحظة العميل</dt>
                            <dd class="mt-1 text-sm text-amber-900 dark:text-amber-100">{{ $order->notes }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            {{-- Products + financial --}}
            <div class="space-y-6 xl:col-span-2">
                <div class="fi-section rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 sm:p-6">
                    <h3 class="text-base font-bold text-gray-950 dark:text-white">المنتجات</h3>
                    <div class="mt-4 overflow-x-auto">
                        <table class="w-full min-w-[520px] text-sm">
                            <thead>
                                <tr class="border-b border-gray-100 text-gray-500 dark:border-white/10 dark:text-gray-400">
                                    <th class="pb-3 text-start font-bold">المنتج</th>
                                    <th class="pb-3 text-start font-bold">الباقة</th>
                                    <th class="pb-3 text-center font-bold">الكمية</th>
                                    <th class="pb-3 text-end font-bold">السعر</th>
                                    <th class="pb-3 text-end font-bold">الإجمالي</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-white/10">
                                @foreach($order->items as $item)
                                    <tr>
                                        <td class="py-3 font-semibold text-gray-950 dark:text-white">{{ $item->product_name }}</td>
                                        <td class="py-3 text-gray-500">{{ $item->variant_name ?: '—' }}</td>
                                        <td class="py-3 text-center font-bold">{{ $item->qty }}</td>
                                        <td class="py-3 text-end">{{ number_format($item->price) }} ج.م</td>
                                        <td class="py-3 text-end font-bold">{{ number_format($item->line_total) }} ج.م</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 flex flex-wrap justify-end gap-4 border-t border-gray-100 pt-4 text-sm dark:border-white/10">
                        <div><span class="text-gray-500">المنتجات:</span> <strong>{{ number_format($order->subtotal) }} ج.م</strong></div>
                        <div><span class="text-gray-500">الشحن:</span> <strong>{{ number_format($order->shipping) }} ج.م</strong></div>
                        <div class="text-base"><span class="text-gray-500">الإجمالي:</span> <strong class="text-emerald-600 dark:text-emerald-400">{{ number_format($order->total) }} ج.م</strong></div>
                    </div>
                    @if($order->receipt_path)
                        <div class="mt-4">
                            <a href="{{ asset('storage/'.$order->receipt_path) }}" target="_blank" class="text-sm font-bold text-primary-600 hover:underline">عرض إيصال التحويل ↗</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Staff notes --}}
        <div class="fi-section rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 sm:p-6">
            <h3 class="text-base font-bold text-gray-950 dark:text-white">ملاحظات الفريق</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">أضف ملاحظات داخلية — تظهر لك وللفريق فقط.</p>

            <div class="mt-4 flex flex-col gap-3 sm:flex-row">
                <textarea wire:model="newNote" rows="2" placeholder="اكتب ملاحظة للفريق…"
                          class="flex-1 rounded-xl border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white"></textarea>
                <x-filament::button wire:click="addNote" icon="heroicon-m-paper-airplane" class="self-start sm:self-end">
                    إرسال ملاحظة
                </x-filament::button>
            </div>

            @if($order->staffNotes->isNotEmpty())
                <div class="mt-5 space-y-3">
                    @foreach($order->staffNotes as $note)
                        <div class="rounded-xl bg-gray-50 px-4 py-3 dark:bg-gray-800/60">
                            <div class="flex flex-wrap items-center justify-between gap-2 text-xs text-gray-500 dark:text-gray-400">
                                <span class="font-bold text-gray-700 dark:text-gray-300">{{ $note->author?->name ?? 'فريق العمل' }}</span>
                                <span>{{ $note->created_at?->format('Y-m-d H:i') }}</span>
                            </div>
                            <p class="mt-2 text-sm leading-relaxed text-gray-800 dark:text-gray-200">{{ $note->body }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="mt-4 text-sm text-gray-400">لا توجد ملاحظات بعد.</p>
            @endif
        </div>

        {{-- Timeline --}}
        <div class="fi-section rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 sm:p-6">
            <h3 class="text-base font-bold text-gray-950 dark:text-white">سجل الحالات</h3>
            <div class="mt-5 space-y-0">
                @forelse($order->statusHistories->sortByDesc('created_at') as $history)
                    <div class="relative flex gap-4 pb-6 last:pb-0">
                        <div class="flex flex-col items-center">
                            <div class="h-3 w-3 rounded-full bg-primary-500 ring-4 ring-primary-100 dark:ring-primary-500/20"></div>
                            @if(!$loop->last)
                                <div class="mt-1 w-px flex-1 bg-gray-200 dark:bg-gray-700"></div>
                            @endif
                        </div>
                        <div class="min-w-0 flex-1 -mt-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-sm font-bold text-gray-950 dark:text-white">
                                    {{ $history->from_status ? $history->from_label.' → '.$history->to_label : $history->to_label }}
                                </span>
                                <span class="text-xs text-gray-400">{{ $history->created_at?->format('Y-m-d H:i') }}</span>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">بواسطة: {{ $history->changer?->name ?? 'النظام' }}</p>
                            @if($history->note)
                                <p class="mt-2 rounded-lg bg-gray-50 px-3 py-2 text-sm text-gray-700 dark:bg-gray-800 dark:text-gray-300">{{ $history->note }}</p>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-400">لا يوجد سجل بعد.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-filament-panels::page>
