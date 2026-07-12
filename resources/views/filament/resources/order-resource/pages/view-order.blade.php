<x-filament-panels::page>
    @php
        /** @var \App\Models\Order $order */
        $order = $this->record;
        $statusFlow = \App\Models\Order::STATUS_FLOW;
        $currentIdx = array_search($order->status, $statusFlow, true);
        $isCancelled = $order->status === 'cancelled';
        $isDelivered = $order->status === 'delivered';
        $nextStatuses = $this->nextStatuses;

        // نسبة تقدّم مسار التسليم
        $progress = $isCancelled
            ? 0
            : ($currentIdx !== false ? (int) round(($currentIdx / (count($statusFlow) - 1)) * 100) : 0);

        // تطبيع رقم الموبايل لرابط واتساب (مصر)
        $digits = preg_replace('/\D+/', '', (string) $order->customer_phone);
        if (str_starts_with($digits, '0')) {
            $waPhone = '2' . $digits;
        } elseif (str_starts_with($digits, '20')) {
            $waPhone = $digits;
        } else {
            $waPhone = '20' . ltrim($digits, '0');
        }
        $waText = rawurlencode("مرحباً {$order->customer_name}، بخصوص طلبك رقم {$order->order_no}");

        $statusMeta = [
            'pending'    => ['label' => 'قيد المراجعة', 'icon' => 'heroicon-m-clock',              'grad' => 'from-amber-500 to-orange-500',   'soft' => 'bg-amber-50 text-amber-700 ring-amber-200 dark:bg-amber-500/10 dark:text-amber-300 dark:ring-amber-500/30'],
            'confirmed'  => ['label' => 'مؤكد',        'icon' => 'heroicon-m-check-badge',         'grad' => 'from-blue-500 to-indigo-500',    'soft' => 'bg-blue-50 text-blue-700 ring-blue-200 dark:bg-blue-500/10 dark:text-blue-300 dark:ring-blue-500/30'],
            'processing' => ['label' => 'قيد التجهيز', 'icon' => 'heroicon-m-cog-6-tooth',         'grad' => 'from-violet-500 to-purple-500',  'soft' => 'bg-violet-50 text-violet-700 ring-violet-200 dark:bg-violet-500/10 dark:text-violet-300 dark:ring-violet-500/30'],
            'shipped'    => ['label' => 'تم الشحن',    'icon' => 'heroicon-m-truck',               'grad' => 'from-sky-500 to-cyan-500',       'soft' => 'bg-sky-50 text-sky-700 ring-sky-200 dark:bg-sky-500/10 dark:text-sky-300 dark:ring-sky-500/30'],
            'delivered'  => ['label' => 'تم التسليم',  'icon' => 'heroicon-m-check-circle',        'grad' => 'from-emerald-500 to-green-500',  'soft' => 'bg-emerald-50 text-emerald-700 ring-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-300 dark:ring-emerald-500/30'],
            'cancelled'  => ['label' => 'ملغي',        'icon' => 'heroicon-m-x-circle',            'grad' => 'from-rose-500 to-red-500',       'soft' => 'bg-rose-50 text-rose-700 ring-rose-200 dark:bg-rose-500/10 dark:text-rose-300 dark:ring-rose-500/30'],
        ];
        $cur = $statusMeta[$order->status] ?? $statusMeta['pending'];

        $btnColors = [
            'confirmed' => 'primary', 'processing' => 'info', 'shipped' => 'info',
            'delivered' => 'success', 'cancelled' => 'danger',
        ];
        $btnIcons = [
            'confirmed' => 'heroicon-m-check-badge', 'processing' => 'heroicon-m-cog-6-tooth',
            'shipped' => 'heroicon-m-truck', 'delivered' => 'heroicon-m-check-circle',
            'cancelled' => 'heroicon-m-x-mark',
        ];
    @endphp

    <div class="mx-auto max-w-6xl space-y-5 print:space-y-3" x-data="{ copied: '' }">

        {{-- ═══════════ Hero header ═══════════ --}}
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br {{ $cur['grad'] }} p-6 text-white shadow-xl print:shadow-none sm:p-8">
            <div class="pointer-events-none absolute -right-8 -top-8 h-40 w-40 rounded-full bg-white/10 blur-2xl"></div>
            <div class="pointer-events-none absolute -bottom-10 -left-6 h-48 w-48 rounded-full bg-black/10 blur-2xl"></div>

            <div class="relative flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="space-y-3">
                    <div class="flex flex-wrap items-center gap-3">
                        <span class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-white/20 ring-1 ring-white/30 backdrop-blur">
                            <x-filament::icon :icon="$cur['icon']" class="h-6 w-6" />
                        </span>
                        <div>
                            <p class="text-xs font-medium text-white/70">رقم الطلب</p>
                            <h2 class="text-2xl font-black tracking-tight">{{ $order->order_no }}</h2>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 text-sm text-white/85">
                        <span class="inline-flex items-center gap-1 rounded-full bg-white/15 px-3 py-1 font-bold ring-1 ring-white/25">
                            <x-filament::icon :icon="$cur['icon']" class="h-4 w-4" /> {{ $cur['label'] }}
                        </span>
                        <span class="inline-flex items-center gap-1">
                            <x-filament::icon icon="heroicon-m-calendar-days" class="h-4 w-4 opacity-70" />
                            {{ $order->created_at?->format('Y-m-d · H:i') }}
                        </span>
                        @if($order->brand)
                            <span class="inline-flex items-center gap-1">
                                <x-filament::icon icon="heroicon-m-building-storefront" class="h-4 w-4 opacity-70" /> {{ $order->brand->name }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="flex flex-col items-start gap-3 sm:flex-row sm:items-center">
                    <div class="rounded-2xl bg-white/15 px-5 py-3 ring-1 ring-white/25 backdrop-blur">
                        <p class="text-xs font-medium text-white/70">إجمالي الطلب</p>
                        <p class="text-3xl font-black leading-tight">{{ number_format($order->total) }} <span class="text-base font-bold">ج.م</span></p>
                        <p class="text-xs text-white/70">{{ \App\Models\Order::PAYMENT_METHODS[$order->payment_method] ?? $order->payment_method }}</p>
                    </div>
                </div>
            </div>

            {{-- Progress bar --}}
            @unless($isCancelled)
                <div class="relative mt-6">
                    <div class="mb-1.5 flex items-center justify-between text-xs font-semibold text-white/80">
                        <span>تقدّم الطلب</span>
                        <span>{{ $progress }}%</span>
                    </div>
                    <div class="h-2 overflow-hidden rounded-full bg-black/20">
                        <div class="h-full rounded-full bg-white transition-all duration-500" style="width: {{ max($progress, 4) }}%"></div>
                    </div>
                </div>
            @endunless
        </div>

        {{-- ═══════════ Quick actions ═══════════ --}}
        <div class="flex flex-wrap gap-2 print:hidden">
            <a href="https://wa.me/{{ $waPhone }}?text={{ $waText }}" target="_blank"
               class="inline-flex items-center gap-2 rounded-xl bg-[#25D366] px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:brightness-95">
                <x-filament::icon icon="heroicon-m-chat-bubble-oval-left-ellipsis" class="h-5 w-5" /> واتساب العميل
            </a>
            <a href="tel:{{ $order->customer_phone }}"
               class="inline-flex items-center gap-2 rounded-xl bg-gray-900 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-gray-800 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100">
                <x-filament::icon icon="heroicon-m-phone" class="h-5 w-5" /> اتصال
            </a>
            <button type="button"
                    x-on:click="navigator.clipboard.writeText(@js($order->address)); copied='addr'; setTimeout(()=>copied='',1500)"
                    class="inline-flex items-center gap-2 rounded-xl bg-gray-100 px-4 py-2.5 text-sm font-bold text-gray-700 ring-1 ring-gray-200 transition hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-200 dark:ring-white/10 dark:hover:bg-gray-700">
                <x-filament::icon icon="heroicon-m-map-pin" class="h-5 w-5" />
                <span x-text="copied==='addr' ? 'تم النسخ ✓' : 'نسخ العنوان'">نسخ العنوان</span>
            </button>
        </div>

        {{-- ═══════════ Status management ═══════════ --}}
        <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 sm:p-6">
            <div class="flex items-center gap-3">
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-primary-50 text-primary-600 dark:bg-primary-500/10 dark:text-primary-400">
                    <x-filament::icon icon="heroicon-m-arrow-path-rounded-square" class="h-5 w-5" />
                </span>
                <div>
                    <h3 class="text-base font-bold text-gray-950 dark:text-white">إدارة حالة الطلب</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">حدّث الحالة مباشرة مع ملاحظة اختيارية تُسجّل في السجل.</p>
                </div>
            </div>

            {{-- Stepper --}}
            @if($isCancelled)
                <div class="mt-5 flex items-center gap-3 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3.5 text-sm font-bold text-rose-800 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-200">
                    <x-filament::icon icon="heroicon-m-x-circle" class="h-6 w-6 shrink-0" />
                    هذا الطلب ملغي — لا يمكن متابعة مسار التسليم.
                </div>
            @else
                <div class="mt-6 overflow-x-auto pb-2">
                    <div class="flex min-w-[620px] items-start gap-0">
                        @foreach($statusFlow as $idx => $step)
                            @php
                                $done = $currentIdx !== false && $idx < $currentIdx;
                                $active = $order->status === $step;
                                $upcoming = $currentIdx !== false && $idx > $currentIdx;
                                $sMeta = $statusMeta[$step];
                            @endphp
                            <div class="flex flex-1 flex-col items-center">
                                <div class="flex w-full items-center">
                                    <div @class([
                                        'h-1 flex-1 rounded-full',
                                        'bg-emerald-500' => $done || $active,
                                        'bg-gray-200 dark:bg-gray-700' => $upcoming,
                                        'opacity-0' => $loop->first,
                                    ])></div>
                                    <div @class([
                                        'flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl ring-2 transition-all',
                                        'bg-emerald-500 text-white ring-emerald-500 shadow-lg shadow-emerald-500/30' => $done,
                                        'bg-gradient-to-br '.$sMeta['grad'].' text-white ring-transparent scale-110 shadow-xl' => $active,
                                        'bg-gray-100 text-gray-400 ring-gray-200 dark:bg-gray-800 dark:text-gray-500 dark:ring-gray-700' => $upcoming,
                                    ])>
                                        @if($done)
                                            <x-filament::icon icon="heroicon-m-check" class="h-6 w-6" />
                                        @else
                                            <x-filament::icon :icon="$sMeta['icon']" class="h-6 w-6" />
                                        @endif
                                    </div>
                                    <div @class([
                                        'h-1 flex-1 rounded-full',
                                        'bg-emerald-500' => $done,
                                        'bg-gray-200 dark:bg-gray-700' => $active || $upcoming,
                                        'opacity-0' => $loop->last,
                                    ])></div>
                                </div>
                                <span @class([
                                    'mt-2 text-[11px] font-bold whitespace-nowrap',
                                    'text-emerald-600 dark:text-emerald-400' => $done,
                                    'text-gray-950 dark:text-white' => $active,
                                    'text-gray-400 dark:text-gray-500' => $upcoming,
                                ])>{{ $sMeta['label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Actions --}}
            @if($nextStatuses !== [])
                <div class="mt-6 rounded-2xl bg-gray-50 p-4 dark:bg-gray-800/40 print:hidden">
                    <label class="mb-2 flex items-center gap-1.5 text-sm font-semibold text-gray-700 dark:text-gray-300">
                        <x-filament::icon icon="heroicon-m-pencil-square" class="h-4 w-4" /> ملاحظة مع التحديث (اختياري)
                    </label>
                    <textarea wire:model="statusNote" rows="2" placeholder="مثال: تم التواصل مع العميل — الشحنة تخرج غداً"
                              class="mb-4 w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-900 dark:text-white"></textarea>

                    <div class="flex flex-wrap gap-2">
                        @foreach($nextStatuses as $next)
                            @php
                                $buttonLabel = match ($next) {
                                    'confirmed' => 'تأكيد الطلب',
                                    'processing' => 'بدء التجهيز',
                                    'shipped' => 'تم الشحن',
                                    'delivered' => 'تم التسليم',
                                    'cancelled' => 'إلغاء الطلب',
                                    default => \App\Models\Order::STATUSES[$next] ?? $next,
                                };
                            @endphp

                            @if($next === 'cancelled')
                                <x-filament::button
                                    color="danger" outlined
                                    :icon="$btnIcons[$next] ?? null"
                                    wire:click="updateStatus('{{ $next }}')"
                                    wire:confirm="هل أنت متأكد من إلغاء هذا الطلب؟ سيتم استرجاع المخزون."
                                >{{ $buttonLabel }}</x-filament::button>
                            @else
                                <x-filament::button
                                    :color="$btnColors[$next] ?? 'gray'"
                                    :icon="$btnIcons[$next] ?? null"
                                    wire:click="updateStatus('{{ $next }}')"
                                >{{ $buttonLabel }}</x-filament::button>
                            @endif
                        @endforeach
                    </div>
                </div>
            @elseif($isDelivered)
                <div class="mt-5 flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3.5 text-sm font-bold text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-200">
                    <x-filament::icon icon="heroicon-m-check-circle" class="h-6 w-6 shrink-0" />
                    تم تسليم الطلب بنجاح 🎉
                </div>
            @endif
        </div>

        {{-- ═══════════ Main grid ═══════════ --}}
        <div class="grid gap-5 lg:grid-cols-3">

            {{-- Customer --}}
            <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 sm:p-6 lg:col-span-1">
                <div class="flex items-center gap-2">
                    <x-filament::icon icon="heroicon-m-user-circle" class="h-5 w-5 text-gray-400" />
                    <h3 class="text-base font-bold text-gray-950 dark:text-white">بيانات العميل</h3>
                </div>
                <dl class="mt-4 space-y-3.5 text-sm">
                    <div class="flex items-start justify-between gap-3">
                        <dt class="text-gray-500 dark:text-gray-400">الاسم</dt>
                        <dd class="text-end font-bold text-gray-950 dark:text-white">{{ $order->customer_name }}</dd>
                    </div>
                    <div class="flex items-start justify-between gap-3">
                        <dt class="text-gray-500 dark:text-gray-400">الموبايل</dt>
                        <dd class="dir-ltr text-end font-bold text-gray-950 dark:text-white">{{ $order->customer_phone }}</dd>
                    </div>
                    <div class="flex items-start justify-between gap-3">
                        <dt class="text-gray-500 dark:text-gray-400">المحافظة</dt>
                        <dd class="text-end font-semibold text-gray-800 dark:text-gray-200">{{ $order->governorate }}</dd>
                    </div>
                    <div class="border-t border-gray-100 pt-3 dark:border-white/10">
                        <dt class="mb-1 text-gray-500 dark:text-gray-400">العنوان</dt>
                        <dd class="font-semibold leading-relaxed text-gray-800 dark:text-gray-200">{{ $order->address }}</dd>
                    </div>
                    @if($order->notes)
                        <div class="rounded-xl bg-amber-50 p-3 dark:bg-amber-500/10">
                            <dt class="flex items-center gap-1 text-xs font-bold text-amber-700 dark:text-amber-300">
                                <x-filament::icon icon="heroicon-m-chat-bubble-bottom-center-text" class="h-4 w-4" /> ملاحظة العميل
                            </dt>
                            <dd class="mt-1 text-sm text-amber-900 dark:text-amber-100">{{ $order->notes }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            {{-- Products --}}
            <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 sm:p-6 lg:col-span-2">
                <div class="flex items-center gap-2">
                    <x-filament::icon icon="heroicon-m-shopping-bag" class="h-5 w-5 text-gray-400" />
                    <h3 class="text-base font-bold text-gray-950 dark:text-white">المنتجات</h3>
                    <span class="ms-auto rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-bold text-gray-600 dark:bg-gray-800 dark:text-gray-300">{{ $order->items->count() }} منتج</span>
                </div>

                <div class="mt-4 space-y-2.5">
                    @foreach($order->items as $item)
                        <div class="flex items-center gap-3 rounded-2xl bg-gray-50 p-3 dark:bg-gray-800/40">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-sm font-bold text-gray-500 ring-1 ring-gray-200 dark:bg-gray-900 dark:text-gray-400 dark:ring-white/10">×{{ $item->qty }}</span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate font-bold text-gray-950 dark:text-white">{{ $item->product_name }}</p>
                                @if($item->variant_name)
                                    <p class="truncate text-xs text-gray-500 dark:text-gray-400">{{ $item->variant_name }}</p>
                                @endif
                            </div>
                            <div class="text-end">
                                <p class="font-bold text-gray-950 dark:text-white">{{ number_format($item->line_total) }} ج.م</p>
                                <p class="text-xs text-gray-400">{{ number_format($item->price) }} × {{ $item->qty }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Totals --}}
                <div class="mt-4 space-y-2 border-t border-gray-100 pt-4 text-sm dark:border-white/10">
                    <div class="flex justify-between text-gray-600 dark:text-gray-400">
                        <span>المنتجات</span><span class="font-semibold text-gray-800 dark:text-gray-200">{{ number_format($order->subtotal) }} ج.م</span>
                    </div>
                    <div class="flex justify-between text-gray-600 dark:text-gray-400">
                        <span>الشحن</span><span class="font-semibold text-gray-800 dark:text-gray-200">{{ number_format($order->shipping) }} ج.م</span>
                    </div>
                    <div class="flex items-center justify-between border-t border-dashed border-gray-200 pt-2.5 dark:border-white/10">
                        <span class="font-bold text-gray-950 dark:text-white">الإجمالي</span>
                        <span class="text-xl font-black text-emerald-600 dark:text-emerald-400">{{ number_format($order->total) }} ج.م</span>
                    </div>
                </div>

                @if($order->receipt_path)
                    <a href="{{ asset('storage/'.$order->receipt_path) }}" target="_blank"
                       class="mt-4 inline-flex items-center gap-1.5 rounded-xl bg-primary-50 px-3.5 py-2 text-sm font-bold text-primary-700 ring-1 ring-primary-200 transition hover:bg-primary-100 dark:bg-primary-500/10 dark:text-primary-300 dark:ring-primary-500/30">
                        <x-filament::icon icon="heroicon-m-receipt-percent" class="h-4 w-4" /> عرض إيصال التحويل
                    </a>
                @endif
            </div>
        </div>

        {{-- ═══════════ Staff notes ═══════════ --}}
        <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 sm:p-6 print:hidden">
            <div class="flex items-center gap-2">
                <x-filament::icon icon="heroicon-m-chat-bubble-left-right" class="h-5 w-5 text-gray-400" />
                <h3 class="text-base font-bold text-gray-950 dark:text-white">ملاحظات الفريق</h3>
                <span class="ms-auto rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-bold text-gray-600 dark:bg-gray-800 dark:text-gray-300">{{ $order->staffNotes->count() }}</span>
            </div>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">ملاحظات داخلية تظهر لك وللفريق فقط.</p>

            <div class="mt-4 flex flex-col gap-3 sm:flex-row">
                <textarea wire:model="newNote" rows="2" placeholder="اكتب ملاحظة للفريق…"
                          class="flex-1 rounded-xl border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white"></textarea>
                <x-filament::button wire:click="addNote" wire:loading.attr="disabled" icon="heroicon-m-paper-airplane" class="self-start sm:self-end">
                    إرسال
                </x-filament::button>
            </div>

            @if($order->staffNotes->isNotEmpty())
                <div class="mt-5 space-y-3">
                    @foreach($order->staffNotes as $note)
                        <div class="flex gap-3">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-primary-100 text-sm font-bold text-primary-700 dark:bg-primary-500/15 dark:text-primary-300">
                                {{ mb_substr($note->author?->name ?? 'ف', 0, 1) }}
                            </span>
                            <div class="min-w-0 flex-1 rounded-2xl rounded-ss-sm bg-gray-50 px-4 py-3 dark:bg-gray-800/60">
                                <div class="flex flex-wrap items-center justify-between gap-2 text-xs text-gray-500 dark:text-gray-400">
                                    <span class="font-bold text-gray-700 dark:text-gray-300">{{ $note->author?->name ?? 'فريق العمل' }}</span>
                                    <span>{{ $note->created_at?->diffForHumans() }}</span>
                                </div>
                                <p class="mt-1.5 text-sm leading-relaxed text-gray-800 dark:text-gray-200">{{ $note->body }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="mt-4 rounded-2xl border border-dashed border-gray-200 py-6 text-center text-sm text-gray-400 dark:border-gray-700">
                    لا توجد ملاحظات بعد.
                </div>
            @endif
        </div>

        {{-- ═══════════ Timeline ═══════════ --}}
        <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 sm:p-6">
            <div class="flex items-center gap-2">
                <x-filament::icon icon="heroicon-m-clock" class="h-5 w-5 text-gray-400" />
                <h3 class="text-base font-bold text-gray-950 dark:text-white">سجل الحالات</h3>
            </div>
            <div class="mt-5">
                @forelse($order->statusHistories->sortByDesc('created_at') as $history)
                    @php $hMeta = $statusMeta[$history->to_status] ?? $statusMeta['pending']; @endphp
                    <div class="relative flex gap-4 pb-6 last:pb-0">
                        <div class="flex flex-col items-center">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br {{ $hMeta['grad'] }} text-white shadow-sm">
                                <x-filament::icon :icon="$hMeta['icon']" class="h-4 w-4" />
                            </span>
                            @if(!$loop->last)
                                <div class="mt-1 w-px flex-1 bg-gray-200 dark:bg-gray-700"></div>
                            @endif
                        </div>
                        <div class="min-w-0 flex-1 pb-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-sm font-bold text-gray-950 dark:text-white">
                                    {{ $history->from_status ? $history->from_label.' ← '.$history->to_label : $history->to_label }}
                                </span>
                                <span class="rounded-full px-2 py-0.5 text-[11px] font-bold ring-1 ring-inset {{ $hMeta['soft'] }}">{{ $history->to_label }}</span>
                            </div>
                            <p class="mt-0.5 flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                                <span>{{ $history->created_at?->format('Y-m-d H:i') }}</span>
                                <span>·</span>
                                <span>بواسطة {{ $history->changer?->name ?? 'النظام' }}</span>
                            </p>
                            @if($history->note)
                                <p class="mt-2 rounded-xl bg-gray-50 px-3 py-2 text-sm text-gray-700 dark:bg-gray-800 dark:text-gray-300">{{ $history->note }}</p>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-gray-200 py-6 text-center text-sm text-gray-400 dark:border-gray-700">
                        لا يوجد سجل بعد.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-filament-panels::page>
