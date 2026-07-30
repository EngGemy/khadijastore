<?php

namespace App\Filament\Pages;

use App\Models\Brand;
use App\Models\PushSubscription;
use App\Services\WebPushService;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SendPushNotification extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-bell-alert';

    protected static ?string $navigationLabel = 'إشعارات الدفع';

    protected static ?string $title = 'إرسال إشعار Push';

    protected static string | \UnitEnum | null $navigationGroup = 'الإعدادات';

    protected static ?int $navigationSort = 12;

    protected string $view = 'filament.pages.send-push-notification';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public function mount(): void
    {
        $this->form->fill([
            'title' => 'عرض جديد من سند',
            'body' => 'تصفّح أحدث العروض والمنتجات الآن',
            'url' => '/',
            'brand_id' => null,
            'customer_phone' => null,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('محتوى الإشعار')->schema([
                TextInput::make('title')->label('العنوان')->required()->maxLength(120),
                Textarea::make('body')->label('النص')->rows(3)->maxLength(500)->columnSpanFull(),
                TextInput::make('url')->label('رابط عند الضغط')->required()->maxLength(500)->default('/'),
                Select::make('brand_id')
                    ->label('تصفية حسب البراند (اختياري)')
                    ->options(fn () => Brand::query()->orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->nullable(),
                TextInput::make('customer_phone')
                    ->label('رقم عميل محدد (اختياري)')
                    ->tel()
                    ->helperText('إن تُرك فارغًا يُرسل البث لكل المشتركين (مع تصفية البراند إن وُجدت)'),
            ])->columns(2),
        ])->statePath('data');
    }

    public function getSubscriptionCount(): int
    {
        return PushSubscription::query()->count();
    }

    public function send(WebPushService $webPush): void
    {
        if (! $webPush->isConfigured()) {
            Notification::make()
                ->title('VAPID غير مُعد')
                ->body('شغّل php artisan webpush:vapid وأضف المفاتيح إلى .env')
                ->danger()
                ->send();

            return;
        }

        $data = $this->form->getState();
        $payload = [
            'title' => $data['title'],
            'body' => $data['body'] ?? '',
            'url' => $data['url'] ?? '/',
            'tag' => 'alamat-admin-'.now()->timestamp,
        ];
        $brandId = isset($data['brand_id']) ? (int) $data['brand_id'] : null;

        if (! empty($data['customer_phone'])) {
            $result = $webPush->sendToPhone($data['customer_phone'], $payload, $brandId);
        } else {
            $result = $webPush->broadcast($payload, $brandId);
        }

        Notification::make()
            ->title('تم الإرسال')
            ->body("نجاح: {$result['sent']} · فشل: {$result['failed']} · منتهي: {$result['expired']}")
            ->success()
            ->send();
    }
}
