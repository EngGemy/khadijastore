<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages\CreateProduct;
use App\Filament\Resources\ProductResource\Pages\EditProduct;
use App\Filament\Resources\ProductResource\Pages\ListProducts;
use App\Filament\Resources\ProductResource\Pages\ManageProductVariants;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Category;
use App\Models\Product;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationLabel = 'المنتجات';

    protected static ?string $modelLabel = 'منتج';

    protected static ?string $pluralModelLabel = 'المنتجات';

    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('variants');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('الأساسيات')->schema([
                Select::make('brand_id')->label('البراند')
                    ->relationship('brand', 'name')->required()
                    ->visible(fn () => auth()->user()->isSuperAdmin()),
                Select::make('category_id')->label('التصنيف')
                    ->options(function ($get) {
                        $brandId = $get('brand_id') ?? auth()->user()?->brand_id;

                        return Category::hierarchicalOptions($brandId);
                    })
                    ->searchable()
                    ->preload()
                    ->helperText('اختر من الشجرة: قسم ← ماركة ← منتج'),
                TextInput::make('name')->label('اسم المنتج')->required()->columnSpanFull(),
                Textarea::make('short_description')->label('وصف مختصر')->columnSpanFull(),
                RichEditor::make('description')->label('الوصف الكامل')->columnSpanFull(),
            ])->columns(2),

            Section::make('المميزات وطريقة الاستخدام')
                ->description('تظهر في صفحة المنتج تحت «لماذا هذا المنتج؟» و«طريقة الاستخدام».')
                ->schema([
                    Repeater::make('features')
                        ->label('لماذا هذا المنتج؟')
                        ->schema([
                            TextInput::make('title')->label('العنوان')->required(),
                            Textarea::make('description')->label('الوصف')->rows(2),
                        ])
                        ->defaultItems(0)
                        ->collapsible()
                        ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                        ->addActionLabel('إضافة ميزة')
                        ->columnSpanFull(),
                    Repeater::make('usage_steps')
                        ->label('طريقة الاستخدام')
                        ->schema([
                            Textarea::make('text')->label('الخطوة')->required()->rows(2),
                        ])
                        ->defaultItems(0)
                        ->collapsible()
                        ->itemLabel(fn (array $state): ?string => isset($state['text']) ? mb_strimwidth($state['text'], 0, 48, '…') : null)
                        ->addActionLabel('إضافة خطوة')
                        ->columnSpanFull(),
                ])
                ->collapsed(),

            Section::make('التسعير')->schema([
                TextInput::make('price')->label('السعر')
                    ->numeric()->required()->suffix('ج.م'),
                TextInput::make('compare_price')->label('السعر قبل الخصم')
                    ->numeric()->suffix('ج.م'),
                TextInput::make('badge')->label('شارة')
                    ->placeholder('الأكثر مبيعًا / جديد'),
                TextInput::make('video_url')->label('رابط الفيديو')->url(),
            ])->columns(2),

            Section::make('مخزون المنتج (عند غياب الباقات)')->schema([
                TextInput::make('stock')->label('الكمية المتاحة')
                    ->numeric()->default(0)->suffix('قطعة'),
                Toggle::make('track_stock')->label('تتبع المخزون')->default(true),
                TextInput::make('low_stock_threshold')->label('حد التنبيه المنخفض')
                    ->numeric()->default(5)->suffix('قطعة'),
            ])->columns(3)->collapsed(),

            Section::make('الصور')->schema([
                SpatieMediaLibraryFileUpload::make('cover')->label('الصورة الرئيسية')
                    ->collection('cover')->image()->disk('public')->visibility('public')
                    ->panelLayout('integrated')
                    ->imageEditor()
                    ->imageResizeMode('cover')
                    ->imageCropAspectRatio('1:1')
                    ->helperText('الحجم الموصى به: 1000×1000px (نسبة 1:1) · JPG/WebP · مصغّر 400×400'),
                SpatieMediaLibraryFileUpload::make('gallery')->label('معرض الصور')
                    ->collection('gallery')->multiple()->reorderable()->image()->disk('public')->visibility('public')
                    ->panelLayout('grid')
                    ->imageEditor()
                    ->imageResizeMode('cover')
                    ->imageCropAspectRatio('1:1')
                    ->helperText('نفس المقاس الموصى به للصورة الرئيسية: 1000×1000px'),
            ])->columns(2),

            Section::make('الباقات (Variants)')
                ->description('لإدارة احترافية: افتح صفحة «إدارة المتغيرات» من أزرار المنتج — جدول ألوان × أحجام مع الأسعار والمخزون.')
                ->schema([
                    Repeater::make('variants')
                        ->relationship()
                        ->schema([
                            TextInput::make('name')->label('الاسم')->required(),
                            TextInput::make('subtitle')->label('وصف فرعي'),
                            TextInput::make('sku')->label('SKU')->maxLength(64)->nullable(),
                            TextInput::make('price')->label('السعر')->numeric()->required()->suffix('ج.م'),
                            TextInput::make('stock')->label('المخزون')->numeric()->default(0)->suffix('قطعة'),
                            TextInput::make('low_stock_threshold')->label('حد التنبيه')->numeric()->default(5)->suffix('قطعة'),
                            Toggle::make('track_stock')->label('تتبع المخزون')->default(true),
                            Toggle::make('is_popular')->label('الأكثر طلبًا'),
                            Repeater::make('option_values')
                                ->label('قيم الصفات')
                                ->schema([
                                    Select::make('attribute_id')
                                        ->label('الصفة')
                                        ->options(fn () => Attribute::pluck('name', 'id')->toArray())
                                        ->live(),
                                    Select::make('value_id')
                                        ->label('القيمة')
                                        ->options(fn ($get) => $get('attribute_id') ? AttributeValue::where('attribute_id', $get('attribute_id'))->pluck('label', 'id')->toArray() : [])
                                        ->searchable(),
                                ])
                                ->defaultItems(0)
                                ->columns(2)
                                ->collapsible(),
                        ])
                        ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                        ->columns(3)->defaultItems(0)->collapsible()->collapsed(),
                ])->collapsed(),

            Section::make('أسعار الجملة (حسب الكمية)')
                ->schema([
                    Repeater::make('priceTiers')
                        ->relationship()
                        ->schema([
                            TextInput::make('label')
                                ->label('اسم الشريحة')
                                ->nullable(),
                            TextInput::make('min_qty')
                                ->label('الحد الأدنى للكمية')
                                ->numeric()
                                ->required()
                                ->minValue(1)
                                ->helperText('يُفعّل عند هذه الكمية فأكثر'),
                            TextInput::make('price')
                                ->label('السعر')
                                ->numeric()
                                ->required()
                                ->suffix('ج.م'),
                            Toggle::make('is_active')
                                ->label('نشط')
                                ->default(true),
                        ])
                        ->itemLabel(fn (array $state): ?string => ($state['label'] ?? '').' — من '.($state['min_qty'] ?? 0).' قطعة')
                        ->orderColumn('sort')
                        ->columns(2)
                        ->collapsible(),
                ])
                ->collapsed(),

            Section::make('الحالة')->schema([
                Toggle::make('is_active')->label('نشط')->default(true),
                Toggle::make('is_featured')->label('مميّز (يظهر بالرئيسية)'),
                TextInput::make('sort')->label('الترتيب')->numeric()->default(0),
            ])->columns(3),

            Section::make('SEO')->schema([
                TextInput::make('meta_title')
                    ->label('عنوان الصفحة (Meta Title)')
                    ->helperText('اتركه فارغًا ليستخدم اسم المنتج تلقائيًا')
                    ->columnSpanFull(),
                Textarea::make('meta_description')
                    ->label('وصف الصفحة (Meta Description)')
                    ->rows(2)
                    ->helperText('اتركه فارغًا ليستخدم الوصف المختصر تلقائيًا')
                    ->columnSpanFull(),
            ])->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('cover')
                    ->label('')->collection('cover')->size(80)->square()->extraImgAttributes(['class' => 'object-cover']),
                SpatieMediaLibraryImageColumn::make('gallery')
                    ->label('معرض')->collection('gallery')->size(40)->circular()->stacked()->limit(3)->extraImgAttributes(['class' => 'object-cover']),
                TextColumn::make('name')->label('المنتج')->searchable()->weight('bold'),
                TextColumn::make('brand.name')->label('البراند')->badge()
                    ->visible(fn () => auth()->user()->isSuperAdmin()),
                TextColumn::make('price')->label('السعر')->money('EGP')->sortable(),
                TextColumn::make('total_stock')
                    ->label('المخزون')
                    ->badge()
                    ->getStateUsing(fn (Product $record): int => $record->total_stock)
                    ->color(function (Product $record): string {
                        if ($record->variants->isNotEmpty()) {
                            return $record->variants->contains(
                                fn ($v) => $v->track_stock && $v->stock <= $v->low_stock_threshold
                            ) ? 'danger' : 'success';
                        }

                        return $record->track_stock && $record->stock <= $record->low_stock_threshold
                            ? 'danger' : 'success';
                    }),
                TextColumn::make('sales_count')->label('المبيعات')->sortable(),
                IconColumn::make('is_active')->label('نشط')->boolean(),
                IconColumn::make('is_featured')->label('مميّز')->boolean(),
            ])
            ->filters([
                TernaryFilter::make('is_active')->label('نشط'),
                SelectFilter::make('brand_id')->label('البراند')
                    ->relationship('brand', 'name')
                    ->visible(fn () => auth()->user()->isSuperAdmin()),
            ])
            ->recordActions([
                Action::make('variants')
                    ->label('المتغيرات')
                    ->icon('heroicon-o-table-cells')
                    ->color('info')
                    ->url(fn (Product $record) => static::getUrl('variants', ['record' => $record])),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'edit' => EditProduct::route('/{record}/edit'),
            'variants' => ManageProductVariants::route('/{record}/variants'),
        ];
    }
}
