<?php

namespace App\Filament\Pages;

use App\Services\PublicStoragePublisher;
use App\Services\SettingsService;
use Filament\Forms\Components\FileUpload;
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

class ManageSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'الإعدادات العامة';

    protected static ?string $title = 'الإعدادات العامة';

    protected static string|\UnitEnum|null $navigationGroup = 'الإعدادات';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.manage-settings';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public function mount(): void
    {
        $settings = app(SettingsService::class)->all();

        $this->form->fill([
            'store_name' => $settings['store.name'] ?? 'متجر العلامات',
            'store_tagline' => $settings['store.tagline'] ?? '',
            'store_logo' => filled($settings['store.logo'] ?? null)
                ? (is_array($settings['store.logo']) ? $settings['store.logo'] : [$settings['store.logo']])
                : [],
            'store_currency' => $settings['store.currency'] ?? 'EGP',
            'store_support_phone' => $settings['store.support_phone'] ?? '',
            'store_support_whatsapp' => $settings['store.support_whatsapp'] ?? '',
            'store_email' => $settings['store.email'] ?? '',
            'store_address' => $settings['store.address'] ?? '',
            'store_social' => $settings['store.social'] ?? [],
            'store_maintenance_mode' => $settings['store.maintenance_mode'] ?? false,
            'checkout_cod_enabled' => $settings['checkout.cod_enabled'] ?? true,
            'checkout_whatsapp_enabled' => $settings['checkout.whatsapp_enabled'] ?? true,
            'checkout_transfer_enabled' => $settings['checkout.transfer_enabled'] ?? true,
            'checkout_min_order_total' => $settings['checkout.min_order_total'] ?? 0,
            'checkout_terms_text' => $settings['checkout.terms_text'] ?? '',
            'shipping_free_over' => $settings['shipping.free_over'] ?? null,
            'shipping_flat_fallback' => $settings['shipping.flat_fallback'] ?? 60,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('الإعدادات')->tabs([
                Tabs\Tab::make('عام')->schema([
                    Section::make('هوية المتجر')->schema([
                        FileUpload::make('store_logo')
                            ->label('لوجو المتجر (صورة)')
                            ->disk('public')
                            ->directory('store')
                            ->visibility('public')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatioOptions([
                                '3:1' => '3:1 — أفقي (موصى به)',
                                '1:1' => '1:1 — مربع',
                            ])
                            ->imagePreviewHeight('100')
                            ->maxSize(512)
                            ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/webp', 'image/svg+xml'])
                            ->helperText('المقاس الموصى به: 480×160 px. PNG/WebP بخلفية شفافة. يظهر في هيدر الموقع ولوحة التحكم.')
                            ->columnSpanFull(),
                    ]),
                    Section::make('بيانات المتجر')->schema([
                        TextInput::make('store_name')->label('اسم المتجر')->required(),
                        TextInput::make('store_tagline')->label('الشعار النصي (Tagline)'),
                        TextInput::make('store_currency')->label('العملة (رمز)')->default('EGP'),
                    ])->columns(2),
                ]),

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
                    Section::make('إعدادات الشحن العامة')->schema([
                        TextInput::make('shipping_free_over')
                            ->label('شحن مجاني للطلبات الأعلى من')
                            ->numeric()->nullable()->suffix('ج.م')
                            ->helperText('اتركه فارغًا إذا لا تريد شحن مجاني تلقائي'),
                        TextInput::make('shipping_flat_fallback')
                            ->label('سعر الشحن الافتراضي (لو المحافظة ليس لها سعر)')
                            ->numeric()->required()->suffix('ج.م')->default(60),
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

                Tabs\Tab::make('الصيانة')->schema([
                    Section::make('وضع الصيانة')->schema([
                        Toggle::make('store_maintenance_mode')
                            ->label('تفعيل وضع الصيانة')
                            ->helperText('عند التفعيل، واجهة المتجر تظهر صفحة صيانة فقط. لوحة التحكم تبقى شغالة.')
                            ->default(false),
                    ]),
                ]),
            ]),
        ])->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $service = app(SettingsService::class);

        $mapping = [
            'store_name' => 'store.name',
            'store_tagline' => 'store.tagline',
            'store_currency' => 'store.currency',
            'store_support_phone' => 'store.support_phone',
            'store_support_whatsapp' => 'store.support_whatsapp',
            'store_email' => 'store.email',
            'store_address' => 'store.address',
            'store_social' => 'store.social',
            'store_maintenance_mode' => 'store.maintenance_mode',
            'checkout_cod_enabled' => 'checkout.cod_enabled',
            'checkout_whatsapp_enabled' => 'checkout.whatsapp_enabled',
            'checkout_transfer_enabled' => 'checkout.transfer_enabled',
            'checkout_min_order_total' => 'checkout.min_order_total',
            'checkout_terms_text' => 'checkout.terms_text',
            'shipping_free_over' => 'shipping.free_over',
            'shipping_flat_fallback' => 'shipping.flat_fallback',
        ];

        foreach ($mapping as $formKey => $settingKey) {
            $value = $data[$formKey] ?? null;
            if ($value === '' && in_array($formKey, ['shipping_free_over'], true)) {
                $value = null;
            }
            $service->set($settingKey, $value);
        }

        $logo = $data['store_logo'] ?? null;
        if (is_array($logo)) {
            $logo = $logo[array_key_first($logo)] ?? null;
        }
        $service->set('store.logo', $logo ?: null);

        if (is_string($logo) && $logo !== '') {
            PublicStoragePublisher::publishPath($logo);
        }

        Notification::make()
            ->title('تم الحفظ')
            ->body('تم تحديث الإعدادات العامة بنجاح.')
            ->success()
            ->send();
    }
}
