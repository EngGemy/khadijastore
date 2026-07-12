<?php

namespace App\Filament\Pages;

use App\Models\Brand;
use App\Services\SettingsService;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
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
use Illuminate\Database\Eloquent\Model;

class ManageBrandSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-adjustments-horizontal';

    protected static ?string $navigationLabel = 'إعدادات البراند';

    protected static ?string $title = 'إعدادات البراند';

    protected static string | \UnitEnum | null $navigationGroup = 'الإعدادات';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.manage-brand-settings';

    public ?array $data = [];

    public ?Brand $brand = null;

    public function getStoreUrlProperty(): ?string
    {
        return $this->brand ? brand_page_url($this->brand->slug) : null;
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user && ($user->isSuperAdmin() || $user->hasRole('brand_admin'));
    }

    /**
     * Spatie Media Library file upload requires a bound Eloquent record.
     */
    public function getRecord(): ?Model
    {
        return $this->brand;
    }

    public function mount(): void
    {
        $this->brand = auth()->user()?->brand;
        $brandId = auth()->user()?->brand_id;
        $settings = app(SettingsService::class)->all($brandId);

        $this->form->fill(array_merge([
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
        ], $this->brand ? [
            'brand_name' => $this->brand->name,
            'brand_category_label' => $this->brand->category_label,
            'brand_description' => $this->brand->description,
            'brand_mark' => $this->brand->mark,
            'brand_whatsapp' => $this->brand->whatsapp,
            'brand_vodafone_cash' => $this->brand->vodafone_cash,
            'brand_instapay' => $this->brand->instapay,
            'brand_meta_title' => $this->brand->meta_title,
            'brand_meta_description' => $this->brand->meta_description,
        ] : []));
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('إعدادات البراند')->tabs([
                Tabs\Tab::make('الهوية والصفحة')
                    ->icon('heroicon-o-building-storefront')
                    ->visible(fn () => $this->brand !== null)
                    ->schema([
                        Section::make('مظهر صفحة البراند')
                            ->description('الاسم، الوصف، اللوجو، والتصنيف يظهرون في رأس صفحة متجرك (كما يراه الزائر).')
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('logo')
                                    ->label('لوجو البراند')
                                    ->collection('logo')
                                    ->image()
                                    ->disk('public')
                                    ->visibility('public')
                                    ->maxSize(6144)
                                    ->helperText('PNG/WebP بخلفية شفافة. يظهر في رأس صفحة البراند. الحد الأقصى 6 MB.')
                                    ->columnSpanFull(),
                                TextInput::make('brand_name')
                                    ->label('اسم البراند')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('brand_mark')
                                    ->label('حرف بديل (بدون لوجو)')
                                    ->maxLength(8)
                                    ->helperText('يظهر داخل دائرة اللوجو إذا لم ترفع صورة.'),
                                TextInput::make('brand_category_label')
                                    ->label('تصنيف البراند')
                                    ->placeholder('مواد عطارة · HERBS')
                                    ->helperText('الشارة الصغيرة فوق اسم البراند.'),
                                Textarea::make('brand_description')
                                    ->label('وصف البراند')
                                    ->rows(3)
                                    ->helperText('يظهر تحت اسم البراند في صفحة المتجر.')
                                    ->columnSpanFull(),
                            ])->columns(2),

                        Section::make('تواصل البراند (زر واتساب في الصفحة)')->schema([
                            TextInput::make('brand_whatsapp')
                                ->label('واتساب البراند')
                                ->placeholder('201001234567')
                                ->helperText('بصيغة دولية بدون + — يظهر زر «واتساب» في رأس صفحة البراند.'),
                            TextInput::make('brand_vodafone_cash')
                                ->label('فودافون كاش'),
                            TextInput::make('brand_instapay')
                                ->label('إنستاباي'),
                        ])->columns(3),

                        Section::make('SEO (اختياري)')->collapsed()->schema([
                            TextInput::make('brand_meta_title')
                                ->label('عنوان الصفحة (Meta Title)')
                                ->helperText('اتركه فارغًا ليستخدم اسم البراند.')
                                ->columnSpanFull(),
                            Textarea::make('brand_meta_description')
                                ->label('وصف الصفحة (Meta Description)')
                                ->rows(2)
                                ->helperText('اتركه فارغًا ليستخدم وصف البراند.')
                                ->columnSpanFull(),
                        ]),
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

        if ($this->brand) {
            $this->brand->update([
                'name' => $data['brand_name'],
                'mark' => $data['brand_mark'] ?: mb_substr($data['brand_name'], 0, 1),
                'category_label' => $data['brand_category_label'] ?: null,
                'description' => $data['brand_description'] ?: null,
                'whatsapp' => $data['brand_whatsapp'] ?: null,
                'vodafone_cash' => $data['brand_vodafone_cash'] ?: null,
                'instapay' => $data['brand_instapay'] ?: null,
                'meta_title' => $data['brand_meta_title'] ?: null,
                'meta_description' => $data['brand_meta_description'] ?: null,
            ]);

            forget_home_blocks_cache();
        }

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
