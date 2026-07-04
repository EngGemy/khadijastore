<x-filament-panels::page>
    @php
        $product = $this->getRecord();
        $attributes = $this->catalogAttributes;
    @endphp

    <div class="space-y-6">
        {{-- ملخص المنتج --}}
        <div class="fi-section rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h2 class="text-lg font-bold text-gray-950 dark:text-white">{{ $product->name }}</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        السعر الأساسي: <strong>{{ number_format($product->price) }} ج.م</strong>
                        @if($product->category)
                            · التصنيف: <strong>{{ $product->category->breadcrumb }}</strong>
                        @endif
                    </p>
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    {{ count($matrixRows) }} توليفة · {{ $product->variants->count() }} محفوظة
                </div>
            </div>
        </div>

        {{-- اختيار الصفات --}}
        <div class="fi-section rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <h3 class="mb-1 text-base font-semibold text-gray-950 dark:text-white">الصفات المستخدمة</h3>
            <p class="mb-4 text-sm text-gray-500 dark:text-gray-400">
                اختر الصفات (لون، حجم، ...) لتوليد جدول التوليفات تلقائيًا — مثل أمازون ونون.
            </p>

            @if($attributes->isEmpty())
                <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-200">
                    لا توجد صفات بعد.
                    <a href="{{ \App\Filament\Resources\AttributeResource::getUrl('index') }}" class="font-semibold underline">أضف صفات (ألوان/أحجام)</a>
                    ثم عد هنا.
                </div>
            @else
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($attributes as $attribute)
                        <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-gray-200 p-3 transition hover:border-primary-500 dark:border-white/10">
                            <input
                                type="checkbox"
                                wire:model.live="selectedAttributeIds"
                                value="{{ $attribute->id }}"
                                class="mt-1 rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                            />
                            <span>
                                <span class="block font-medium text-gray-950 dark:text-white">{{ $attribute->name }}</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $attribute->values->count() }} قيم · {{ $attribute->code }}
                                </span>
                            </span>
                        </label>
                    @endforeach
                </div>

                <div class="mt-4 flex flex-wrap gap-2">
                    <x-filament::button wire:click="regenerateMatrix" icon="heroicon-m-arrow-path" color="gray">
                        تحديث الجدول
                    </x-filament::button>
                </div>
            @endif
        </div>

        {{-- تعبئة جماعية --}}
        @if(count($matrixRows) > 0)
            <div class="fi-section rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <h3 class="mb-4 text-base font-semibold text-gray-950 dark:text-white">تعبئة جماعية</h3>
                <div class="flex flex-wrap items-end gap-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">سعر موحد (ج.م)</label>
                        <input type="number" wire:model="bulkPrice" min="0"
                            class="fi-input block w-40 rounded-lg border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-white/10 dark:bg-white/5" />
                    </div>
                    <x-filament::button wire:click="applyBulkPrice" color="gray" size="sm">تطبيق على الكل</x-filament::button>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">كمية موحدة</label>
                        <input type="number" wire:model="bulkStock" min="0"
                            class="fi-input block w-40 rounded-lg border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-white/10 dark:bg-white/5" />
                    </div>
                    <x-filament::button wire:click="applyBulkStock" color="gray" size="sm">تطبيق على الكل</x-filament::button>
                </div>
            </div>
        @endif

        {{-- جدول المتغيرات --}}
        @if(count($matrixRows) > 0)
            <form wire:submit="save">
                <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[900px] text-sm">
                            <thead class="border-b border-gray-200 bg-gray-50 dark:border-white/10 dark:bg-white/5">
                                <tr>
                                    @foreach($attributes->whereIn('id', $selectedAttributeIds) as $attribute)
                                        <th class="px-4 py-3 text-start font-semibold text-gray-700 dark:text-gray-200">{{ $attribute->name }}</th>
                                    @endforeach
                                    <th class="px-4 py-3 text-start font-semibold text-gray-700 dark:text-gray-200">الاسم</th>
                                    <th class="px-4 py-3 text-start font-semibold text-gray-700 dark:text-gray-200">السعر</th>
                                    <th class="px-4 py-3 text-start font-semibold text-gray-700 dark:text-gray-200">المخزون</th>
                                    <th class="px-4 py-3 text-start font-semibold text-gray-700 dark:text-gray-200">SKU</th>
                                    <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">تتبع</th>
                                    <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">شائع</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                                @foreach($matrixRows as $index => $row)
                                    <tr wire:key="variant-row-{{ $row['key'] }}" class="hover:bg-gray-50/80 dark:hover:bg-white/5">
                                        @foreach($row['option_values'] as $option)
                                            <td class="px-4 py-2 text-gray-800 dark:text-gray-200">
                                                {{ $option['value_label'] }}
                                            </td>
                                        @endforeach
                                        <td class="px-4 py-2">
                                            <input type="text"
                                                wire:model="matrixRows.{{ $index }}.name"
                                                class="fi-input w-full min-w-[120px] rounded-lg border-gray-300 text-sm dark:border-white/10 dark:bg-white/5" />
                                        </td>
                                        <td class="px-4 py-2">
                                            <input type="number" min="0"
                                                wire:model="matrixRows.{{ $index }}.price"
                                                class="fi-input w-24 rounded-lg border-gray-300 text-sm dark:border-white/10 dark:bg-white/5" />
                                        </td>
                                        <td class="px-4 py-2">
                                            <input type="number" min="0"
                                                wire:model="matrixRows.{{ $index }}.stock"
                                                class="fi-input w-24 rounded-lg border-gray-300 text-sm dark:border-white/10 dark:bg-white/5" />
                                        </td>
                                        <td class="px-4 py-2">
                                            <input type="text"
                                                wire:model="matrixRows.{{ $index }}.sku"
                                                class="fi-input w-full min-w-[100px] rounded-lg border-gray-300 text-sm dark:border-white/10 dark:bg-white/5" />
                                        </td>
                                        <td class="px-4 py-2 text-center">
                                            <input type="checkbox"
                                                wire:model="matrixRows.{{ $index }}.track_stock"
                                                class="rounded border-gray-300 text-primary-600 focus:ring-primary-500" />
                                        </td>
                                        <td class="px-4 py-2 text-center">
                                            <input type="checkbox"
                                                wire:model="matrixRows.{{ $index }}.is_popular"
                                                class="rounded border-gray-300 text-primary-600 focus:ring-primary-500" />
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-4 flex justify-start">
                    <x-filament::button type="submit" icon="heroicon-m-check">
                        حفظ المتغيرات
                    </x-filament::button>
                </div>
            </form>
        @elseif($attributes->isNotEmpty() && count($selectedAttributeIds) === 0)
            <div class="rounded-xl border border-dashed border-gray-300 p-8 text-center text-sm text-gray-500 dark:border-white/20 dark:text-gray-400">
                اختر صفة واحدة على الأقل (مثل اللون والحجم) لعرض جدول التوليفات.
            </div>
        @endif
    </div>
</x-filament-panels::page>
