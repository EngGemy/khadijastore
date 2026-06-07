<x-filament-panels::page>

    <div class="mb-6 rounded-xl border border-yellow-200 bg-yellow-50 px-5 py-4 text-sm text-yellow-800">
        <strong>تنبيه:</strong> إعدادات المساعد الذكي تُقرأ من <code>.env</code>. حدّث المفتاح
        <code>GEMINI_API_KEY</code> وباقي المتغيرات ثم نفّذ
        <code>php artisan config:clear</code> لتفعيل التغييرات.
    </div>

    <form wire:submit="save" class="fi-form gap-y-6 grid">
        {{ $this->form }}

        <div class="fi-ac gap-3 flex flex-wrap items-center justify-between">
            <x-filament::button type="submit" icon="heroicon-m-check">
                حفظ الإعدادات
            </x-filament::button>

            <div class="flex items-center gap-4 text-sm text-gray-500">
                <span>سجلات المحادثات: <strong>{{ $this->getLogsCount() }}</strong></span>
                <x-filament::button wire:click="clearLogs" color="danger" size="sm" icon="heroicon-m-trash">
                    حذف السجلات
                </x-filament::button>
            </div>
        </div>
    </form>

</x-filament-panels::page>
