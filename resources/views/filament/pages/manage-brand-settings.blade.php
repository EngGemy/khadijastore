<x-filament-panels::page>
    @if($this->storeUrl)
        <div class="mb-6 rounded-xl border border-primary-200 bg-primary-50 p-4 dark:border-primary-800 dark:bg-primary-950/40"
             x-data="{
               url: @js($this->storeUrl),
               copied: false,
               async copyLink() {
                 try {
                   await navigator.clipboard.writeText(this.url);
                   this.copied = true;
                   setTimeout(() => this.copied = false, 2200);
                 } catch (e) {
                   prompt('انسخ رابط متجرك:', this.url);
                 }
               }
             }">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="min-w-0">
                    <p class="text-sm font-bold text-primary-700 dark:text-primary-300">رابط متجرك للمشاركة</p>
                    <p class="mt-1 text-sm font-semibold text-gray-700 dark:text-gray-200 break-all" dir="ltr">{{ $this->storeUrl }}</p>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">شارك هذا الرابط على واتساب، فيسبوك، أو في بيو حسابك.</p>
                </div>
                <div class="flex flex-wrap gap-2 shrink-0">
                    <x-filament::button color="gray" type="button" x-on:click="copyLink()">
                        <span x-show="!copied">نسخ الرابط</span>
                        <span x-show="copied" x-cloak>تم النسخ ✓</span>
                    </x-filament::button>
                    <x-filament::button tag="a" href="{{ $this->storeUrl }}" target="_blank" icon="heroicon-m-arrow-top-right-on-square">
                        فتح المتجر
                    </x-filament::button>
                </div>
            </div>
        </div>
    @endif

    <form wire:submit="save" class="fi-form gap-y-6 grid">
        {{ $this->form }}

        <div class="fi-ac gap-3 flex flex-wrap items-center justify-start">
            <x-filament::button type="submit" icon="heroicon-m-check">
                حفظ الإعدادات
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
