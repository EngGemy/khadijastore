<?php

namespace App\Filament\Pages;

use App\Services\SettingsService;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
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
use Illuminate\Support\Facades\Cache;

class ManageHomeSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-home';

    protected static ?string $navigationLabel = 'الصفحة الرئيسية';

    protected static ?string $title = 'إعدادات الصفحة الرئيسية';

    protected static string | \UnitEnum | null $navigationGroup = 'الإعدادات';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.manage-home-settings';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public function mount(): void
    {
        $s = app(SettingsService::class)->all();

        $stats = $s['home.hero.stats'] ?? [
            ['value' => '{brands_count}+', 'label' => 'براندات · Brands'],
            ['value' => '{total_orders}+', 'label' => 'طلب مكتمل'],
            ['value' => '{avg_rating}★',   'label' => 'متوسط التقييم'],
        ];

        $this->form->fill([
            // HERO
            'hero_eyebrow'           => $s['home.hero.eyebrow']            ?? 'منصة البراندات الموثوقة · EST. 2026',
            'hero_title_line1'       => $s['home.hero.title_line1']        ?? 'أفضل',
            'hero_title_highlight'   => $s['home.hero.title_highlight']    ?? 'البراندات',
            'hero_title_line2'       => $s['home.hero.title_line2']        ?? 'في مكان واحد',
            'hero_paragraph'         => $s['home.hero.paragraph']          ?? 'تشكيلة مختارة من علامات موثوقة، مع الدفع عند الاستلام والتوصيل لكل المحافظات.',
            'hero_primary_btn_text'  => $s['home.hero.primary_btn_text']   ?? 'تسوّق الآن',
            'hero_primary_btn_link'  => $s['home.hero.primary_btn_link']   ?? '#products',
            'hero_secondary_btn_text'=> $s['home.hero.secondary_btn_text'] ?? 'تصفّح البراندات',
            'hero_secondary_btn_link'=> $s['home.hero.secondary_btn_link'] ?? '#brands',
            'hero_cards'             => $s['home.hero.cards'] ?? [],

            // STATS
            'hero_stats' => is_array($stats) ? $stats : [],

            // SECTION TITLES
            'categories_title'   => $s['home.categories.title']   ?? 'كل ما تحتاجه، مصنّف بعناية',
            'categories_eyebrow' => $s['home.categories.eyebrow'] ?? 'تسوّق حسب الفئة · CATEGORIES',
            'brands_title'       => $s['home.brands.title']       ?? 'براندات تثق بها',
            'brands_eyebrow'     => $s['home.brands.eyebrow']     ?? 'شركاؤنا · OUR BRANDS',
            'products_title'     => $s['home.products.title']     ?? 'منتجات يحبها عملاؤنا',
            'products_eyebrow'   => $s['home.products.eyebrow']   ?? 'الأكثر طلبًا · BESTSELLERS',
            'products_mode'      => $s['home.products.mode']      ?? 'featured',
            'products_limit'     => $s['home.products.limit']     ?? 8,

            // CTA
            'cta_eyebrow'  => $s['home.cta.eyebrow']   ?? 'جاهز تطلب؟ · READY?',
            'cta_title'    => $s['home.cta.title']      ?? 'اطلب الآن وادفع عند الاستلام',
            'cta_paragraph'=> $s['home.cta.paragraph']  ?? 'توصيل سريع لكل المحافظات.',
            'cta_btn_text' => $s['home.cta.btn_text']   ?? 'ابدأ التسوّق',
            'cta_btn_link' => $s['home.cta.btn_link']   ?? '#products',

            // SHOW/HIDE
            'show_marquee'    => (bool) ($s['home.show_marquee']    ?? true),
            'show_categories' => (bool) ($s['home.show_categories'] ?? true),
            'show_brands'     => (bool) ($s['home.show_brands']     ?? true),
            'show_products'   => (bool) ($s['home.show_products']   ?? true),
            'show_cta'        => (bool) ($s['home.show_cta']        ?? true),

            // SEO
            'seo_title'       => $s['home.seo.title']       ?? 'متجر العلامات · أفضل البراندات في مكان واحد',
            'seo_description' => $s['home.seo.description'] ?? '',
            'seo_image'       => $s['home.seo.image']       ?? '',
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('الإعدادات')->tabs([

                Tabs\Tab::make('الهيرو')->schema([
                    Section::make('نص الهيرو')->schema([
                        TextInput::make('hero_eyebrow')
                            ->label('النص الصغير فوق العنوان')
                            ->helperText('مثال: منصة البراندات الموثوقة · EST. 2026'),
                        TextInput::make('hero_title_line1')
                            ->label('السطر الأول من العنوان')
                            ->helperText('مثال: أفضل'),
                        TextInput::make('hero_title_highlight')
                            ->label('الكلمة المميّزة (بلون أخضر)')
                            ->helperText('مثال: البراندات'),
                        TextInput::make('hero_title_line2')
                            ->label('السطر الثاني من العنوان')
                            ->helperText('مثال: في مكان واحد'),
                        Textarea::make('hero_paragraph')
                            ->label('فقرة الهيرو')
                            ->rows(2)
                            ->columnSpanFull(),
                        Repeater::make('hero_cards')
                            ->label('بطاقات الهيرو (3 كروت)')
                            ->schema([
                                FileUpload::make('image')
                                    ->label('الصورة')
                                    ->disk('public')
                                    ->directory('hero-cards')
                                    ->visibility('public')
                                    ->image()
                                    ->imageEditor()
                                    ->imagePreviewHeight('120')
                                    ->panelLayout('integrated')
                                    ->removeUploadedFileButtonPosition('right')
                                    ->maxSize(2048)
                                    ->required(),
                                TextInput::make('name')
                                    ->label('اسم البطاقة')
                                    ->required(),
                                TextInput::make('label')
                                    ->label('اللابل (مثال: FEATURED)')
                                    ->default('FEATURED'),
                                TextInput::make('link')
                                    ->label('الرابط')
                                    ->default('#'),
                                Select::make('bg_style')
                                    ->label('نمط الخلفية')
                                    ->options([
                                        'dark'  => 'داكن',
                                        'light' => 'فاتح',
                                    ])
                                    ->default('dark'),
                            ])
                            ->columns(3)
                            ->maxItems(3)
                            ->defaultItems(3)
                            ->addActionLabel('إضافة بطاقة')
                            ->columnSpanFull(),
                    ])->columns(2),

                    Section::make('أزرار الهيرو')->schema([
                        TextInput::make('hero_primary_btn_text')->label('نص الزر الرئيسي'),
                        TextInput::make('hero_primary_btn_link')->label('رابط الزر الرئيسي'),
                        TextInput::make('hero_secondary_btn_text')->label('نص الزر الثانوي'),
                        TextInput::make('hero_secondary_btn_link')->label('رابط الزر الثانوي'),
                    ])->columns(2),
                ]),

                Tabs\Tab::make('الإحصائيات')->schema([
                    Section::make('إحصائيات الهيرو (3 أرقام)')->schema([
                        Repeater::make('hero_stats')
                            ->label('الإحصائيات')
                            ->schema([
                                TextInput::make('value')
                                    ->label('القيمة')
                                    ->helperText('استخدم {brands_count} أو {total_orders} أو {avg_rating} للقيم الديناميكية')
                                    ->required(),
                                TextInput::make('label')
                                    ->label('التسمية')
                                    ->required(),
                            ])
                            ->columns(2)
                            ->maxItems(3)
                            ->defaultItems(3)
                            ->addActionLabel('إضافة إحصائية')
                            ->columnSpanFull(),
                    ]),
                ]),

                Tabs\Tab::make('عناوين الأقسام')->schema([
                    Section::make('قسم الفئات')->schema([
                        TextInput::make('categories_eyebrow')->label('النص الصغير'),
                        TextInput::make('categories_title')->label('العنوان الرئيسي'),
                    ])->columns(2),

                    Section::make('قسم البراندات')->schema([
                        TextInput::make('brands_eyebrow')->label('النص الصغير'),
                        TextInput::make('brands_title')->label('العنوان الرئيسي'),
                    ])->columns(2),

                    Section::make('قسم المنتجات')->schema([
                        TextInput::make('products_eyebrow')->label('النص الصغير'),
                        TextInput::make('products_title')->label('العنوان الرئيسي'),
                        Select::make('products_mode')
                            ->label('مصدر المنتجات')
                            ->options([
                                'featured'     => 'المميّزة (is_featured)',
                                'latest'       => 'الأحدث',
                                'best_selling' => 'الأكثر مبيعًا',
                            ])
                            ->default('featured'),
                        TextInput::make('products_limit')
                            ->label('عدد المنتجات')
                            ->numeric()
                            ->default(8)
                            ->minValue(1)
                            ->maxValue(24),
                    ])->columns(2),
                ]),

                Tabs\Tab::make('قسم الحث (CTA)')->schema([
                    Section::make('محتوى بانر الحث')->schema([
                        TextInput::make('cta_eyebrow')->label('النص الصغير'),
                        TextInput::make('cta_title')->label('العنوان الرئيسي'),
                        Textarea::make('cta_paragraph')->label('الفقرة')->rows(2)->columnSpanFull(),
                        TextInput::make('cta_btn_text')->label('نص الزر'),
                        TextInput::make('cta_btn_link')->label('رابط الزر'),
                    ])->columns(2),
                ]),

                Tabs\Tab::make('إظهار/إخفاء الأقسام')->schema([
                    Section::make('تفعيل/إخفاء الأقسام الافتراضية')->schema([
                        Toggle::make('show_marquee')
                            ->label('قسم البراندات المتحركة (Marquee)')
                            ->default(true),
                        Toggle::make('show_categories')
                            ->label('قسم الفئات')
                            ->default(true),
                        Toggle::make('show_brands')
                            ->label('قسم البراندات')
                            ->default(true),
                        Toggle::make('show_products')
                            ->label('قسم المنتجات')
                            ->default(true),
                        Toggle::make('show_cta')
                            ->label('قسم الحث على الشراء (CTA)')
                            ->default(true),
                    ])->columns(2)->description('هذه التوجيهات تتحكم في الأقسام الافتراضية. يمكن تعطيل أي بلوك بشكل مستقل من قائمة "أقسام الصفحة الرئيسية".'),
                ]),

                Tabs\Tab::make('SEO')->schema([
                    Section::make('محركات البحث')->schema([
                        TextInput::make('seo_title')
                            ->label('عنوان الصفحة (Title Tag)')
                            ->helperText('يظهر في نتائج جوجل — 60 حرف كحد أقصى'),
                        Textarea::make('seo_description')
                            ->label('الوصف (Meta Description)')
                            ->rows(2)
                            ->helperText('يظهر تحت العنوان في جوجل — 160 حرف كحد أقصى')
                            ->columnSpanFull(),
                        TextInput::make('seo_image')
                            ->label('رابط صورة المشاركة (OG Image)')
                            ->url()
                            ->helperText('تظهر عند مشاركة الصفحة على السوشيال ميديا')
                            ->columnSpanFull(),
                    ])->columns(2),
                ]),

            ]),
        ])->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $service = app(SettingsService::class);

        $mapping = [
            'hero_eyebrow'           => 'home.hero.eyebrow',
            'hero_title_line1'       => 'home.hero.title_line1',
            'hero_title_highlight'   => 'home.hero.title_highlight',
            'hero_title_line2'       => 'home.hero.title_line2',
            'hero_paragraph'         => 'home.hero.paragraph',
            'hero_primary_btn_text'  => 'home.hero.primary_btn_text',
            'hero_primary_btn_link'  => 'home.hero.primary_btn_link',
            'hero_secondary_btn_text'=> 'home.hero.secondary_btn_text',
            'hero_secondary_btn_link'=> 'home.hero.secondary_btn_link',
            'hero_cards'             => 'home.hero.cards',
            'hero_stats'             => 'home.hero.stats',
            'categories_title'       => 'home.categories.title',
            'categories_eyebrow'     => 'home.categories.eyebrow',
            'brands_title'           => 'home.brands.title',
            'brands_eyebrow'         => 'home.brands.eyebrow',
            'products_title'         => 'home.products.title',
            'products_eyebrow'       => 'home.products.eyebrow',
            'products_mode'          => 'home.products.mode',
            'products_limit'         => 'home.products.limit',
            'cta_eyebrow'            => 'home.cta.eyebrow',
            'cta_title'              => 'home.cta.title',
            'cta_paragraph'          => 'home.cta.paragraph',
            'cta_btn_text'           => 'home.cta.btn_text',
            'cta_btn_link'           => 'home.cta.btn_link',
            'show_marquee'           => 'home.show_marquee',
            'show_categories'        => 'home.show_categories',
            'show_brands'            => 'home.show_brands',
            'show_products'          => 'home.show_products',
            'show_cta'               => 'home.show_cta',
            'seo_title'              => 'home.seo.title',
            'seo_description'        => 'home.seo.description',
            'seo_image'              => 'home.seo.image',
        ];

        foreach ($mapping as $formKey => $settingKey) {
            $service->set($settingKey, $data[$formKey] ?? null);
        }

        Cache::forget('home.blocks.resolved');
        Cache::forget('home.page.data');

        Notification::make()
            ->title('تم الحفظ')
            ->body('تم تحديث إعدادات الصفحة الرئيسية بنجاح.')
            ->success()
            ->send();
    }
}
