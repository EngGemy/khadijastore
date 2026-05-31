<x-filament-panels::page>
    <form wire:submit="save" class="fi-form gap-y-6 grid">
        {{ $this->form }}

        <div class="fi-ac gap-3 flex flex-wrap items-center justify-start">
            <x-filament::button type="submit" icon="heroicon-m-check">
                حفظ الإعدادات
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
