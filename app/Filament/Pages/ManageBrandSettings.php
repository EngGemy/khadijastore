<?php

namespace App\Filament\Pages;

use App\Services\SettingsService;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;

class ManageBrandSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-adjustments-horizontal';

    protected static ?string $navigationLabel = 'إعدادات البراند';

    protected static ?string $title = 'إعدادات البراند';

    protected static string | \UnitEnum | null $navigationGroup = 'الإعدادات';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.manage-settings';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user && ($user->isSuperAdmin() || $user->hasRole('brand_admin'));
    }

    public function mount(): void
    {
        $brandId = auth()->user()?->brand_id;
        $settings = app(SettingsService::class)->all($brandId);

        $this->form->fill([
            'store_support_phone' => $settings['store.support_phone'] ?? '',
            'store_support_whatsapp' => $settings['store.support_whatsapp'] ?? '',
            'store_email' => $settings['store.email'] ?? '',
            'store_address' => $settings['store.address'] ?? '',
            'store_social' => $settings['store.social'] ?? [],
            'checkout_cod_enabled' => $settings['checkout.cod_enabled'] ?? true,
            'checkout_whatsapp_enabled' => $settings['checkout.whatsapp_enabled'] ?? true,
            'checkout_transfer_enabled' => $settings['checkout.transfer_enabled'] ?? true,
            'checkout_min_order_total' => $settings['checkout.min_order_total'] ?? 0,
            'checkout_terms_text' => $settings['checkout.terms_text'] ?? '',
            'shipping_free_over' => $settings['shipping.free_over'] ?? null,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('إعدادات البراند')->tabs([
                Tabs\Tab::make('الدفع والطلبات')->schema([
                    Section::make('طرق الدفع المتاحة')->schema([
                        Toggle::make('checkout_cod_enabled')->label('الدفع عند الاستلام (COD)')->default(true),
                        Toggle::make('checkout_whatsapp_enabled')->label('الطلب عبر واتساب')->default(true),
                        Toggle::make('checkout_transfer_enabled')->label('التحويل البنكي')->default(true),
                    ])->columns(3),
                    Section::make('شروط الطلب')->schema([
                        TextInput::make('checkout_min_order_total')->label('الحد الأدنى للطلب (ج.م)')
                            ->numeric()->default(0)->suffix('ج.م'),
                        Textarea::make('checkout_terms_text')->label('نص الشروط عند الطلب')
                            ->rows(3)->columnSpanFull(),
                    ]),
                ]),

                Tabs\Tab::make('الشحن')->schema([
                    Section::make('إعدادات الشحن')->schema([
                        TextInput::make('shipping_free_over')
                            ->label('شحن مجاني للطلبات الأعلى من')
                            ->numeric()->nullable()->suffix('ج.م')
                            ->helperText('اتركه فارغًا لتطبيق الإعداد العام'),
                    ]),
                ]),

                Tabs\Tab::make('التواصل ووسائل التواصل')->schema([
                    Section::make('بيانات التواصل')->schema([
                        TextInput::make('store_support_phone')->label('رقم الدعم'),
                        TextInput::make('store_support_whatsapp')->label('واتساب الدعم'),
                        TextInput::make('store_email')->label('البريد الإلكتروني')->email(),
                        Textarea::make('store_address')->label('العنوان')->rows(2),
                    ])->columns(2),
                    Section::make('وسائل التواصل الاجتماعي')->schema([
                        KeyValue::make('store_social')
                            ->label('روابط السوشيال ميديا')
                            ->keyLabel('المنصة')
                            ->valueLabel('الرابط')
                            ->columnSpanFull(),
                    ]),
                ]),
            ]),
        ])->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $service = app(SettingsService::class);
        $brandId = auth()->user()?->brand_id;

        $mapping = [
            'store_support_phone' => 'store.support_phone',
            'store_support_whatsapp' => 'store.support_whatsapp',
            'store_email' => 'store.email',
            'store_address' => 'store.address',
            'store_social' => 'store.social',
            'checkout_cod_enabled' => 'checkout.cod_enabled',
            'checkout_whatsapp_enabled' => 'checkout.whatsapp_enabled',
            'checkout_transfer_enabled' => 'checkout.transfer_enabled',
            'checkout_min_order_total' => 'checkout.min_order_total',
            'checkout_terms_text' => 'checkout.terms_text',
            'shipping_free_over' => 'shipping.free_over',
        ];

        foreach ($mapping as $formKey => $settingKey) {
            $value = $data[$formKey] ?? null;
            if ($value === '' && in_array($formKey, ['shipping_free_over'], true)) {
                $value = null;
            }
            $service->set($settingKey, $value, $brandId);
        }

        Notification::make()
            ->title('تم الحفظ')
            ->body('تم تحديث إعدادات البراند بنجاح.')
            ->success()
            ->send();
    }
}
