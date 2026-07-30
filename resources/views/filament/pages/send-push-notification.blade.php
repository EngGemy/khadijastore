<x-filament-panels::page>

    <div class="mb-6 rounded-xl border border-gray-200 bg-gray-50 px-5 py-4 text-sm text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200">
        <p class="font-semibold mb-1">Web Push</p>
        <p>المشتركون الحاليون: <strong>{{ $this->getSubscriptionCount() }}</strong></p>
        <p class="mt-2 text-xs opacity-80">
            المفاتيح: <code>VAPID_PUBLIC_KEY</code> · <code>VAPID_PRIVATE_KEY</code> · <code>VAPID_SUBJECT</code>
            — توليد: <code>php artisan webpush:vapid</code>
            — أوامر: <code>php artisan notifications:send-push</code>
        </p>
    </div>

    <form wire:submit="send" class="fi-form gap-y-6 grid">
        {{ $this->form }}

        <div class="fi-ac">
            <x-filament::button type="submit" icon="heroicon-m-paper-airplane" color="warning">
                إرسال الإشعار
            </x-filament::button>
        </div>
    </form>

</x-filament-panels::page>
