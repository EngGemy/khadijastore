<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use App\Filament\Resources\BrandResource\Pages\ListBrands;
use App\Filament\Resources\BrandResource\Pages\CreateBrand;
use App\Filament\Resources\BrandResource\Pages\EditBrand;
use App\Filament\Resources\BrandResource\Pages;
use App\Models\Brand;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BrandResource extends Resource
{
    protected static ?string $model = Brand::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationLabel = 'البراندات';

    protected static ?string $modelLabel = 'براند';

    protected static ?string $pluralModelLabel = 'البراندات';

    protected static string | \UnitEnum | null $navigationGroup = 'الإدارة';

    protected static ?int $navigationSort = 10;

    // البراندات للسوبر أدمن فقط
    public static function canAccess(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('الهوية')->schema([
                TextInput::make('name')->label('اسم البراند')->required(),
                TextInput::make('slug')
                    ->label('الرابط المختصر (Slug)')
                    ->unique(ignoreRecord: true)
                    ->regex('/^[a-z0-9]+(?:-[a-z0-9]+)*$/')
                    ->helperText('يُستخدم في عنوان URL (مثال: mobile-store). اتركه فارغًا ليتولد تلقائيًا من الاسم.')
                    ->columnSpanFull(),
                TextInput::make('mark')->label('حرف اللوجو')->maxLength(8),
                TextInput::make('category_label')->label('وصف الفئة'),
                Textarea::make('description')->label('الوصف')->columnSpanFull(),
                SpatieMediaLibraryFileUpload::make('logo')->label('اللوجو')
                    ->collection('logo')
                    ->image()
                    ->disk('public')
                    ->visibility('public')
                    ->maxSize(6144)
                    ->helperText('الحجم الموصى به: 512×512px · PNG/WebP بخلفية شفافة · الحد الأقصى 6 MB · يُعرض مصغّراً 200×200.'),
            ])->columns(2),

            Section::make('التواصل والدفع (منفصل لكل براند)')->schema([
                TextInput::make('whatsapp')->label('واتساب')
                    ->placeholder('201001234567')->helperText('بصيغة دولية بدون +'),
                TextInput::make('vodafone_cash')->label('فودافون كاش'),
                TextInput::make('instapay')->label('إنستاباي'),
            ])->columns(3),

            Section::make('المواعيد')->schema([
                KeyValue::make('working_hours')->label('ساعات العمل')
                    ->keyLabel('اليوم')->valueLabel('المواعيد')->columnSpanFull(),
                TextInput::make('timezone')->label('المنطقة الزمنية')
                    ->default('Africa/Cairo'),
            ])->columns(2),

            Section::make('صفحة المتجر العامة')->schema([
                TextInput::make('_store_url')
                    ->label('رابط المتجر للمشاركة')
                    ->default(fn (?Brand $record) => $record ? brand_page_url($record->slug) : '')
                    ->disabled()
                    ->dehydrated(false)
                    ->helperText('شارك هذا الرابط على واتساب، فيسبوك، أو في بيو حسابك.')
                    ->columnSpanFull(),
            ])->visibleOn('edit'),

            Section::make('SEO')->schema([
                TextInput::make('meta_title')
                    ->label('عنوان الصفحة (Meta Title)')
                    ->helperText('اتركه فارغًا ليستخدم اسم البراند تلقائيًا')
                    ->columnSpanFull(),
                Textarea::make('meta_description')
                    ->label('وصف الصفحة (Meta Description)')
                    ->rows(2)
                    ->helperText('اتركه فارغًا ليستخدم وصف البراند تلقائيًا')
                    ->columnSpanFull(),
            ])->collapsed(),

            Toggle::make('is_active')->label('نشط')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('logo')
                    ->label('')->collection('logo')->circular(),
                TextColumn::make('name')->label('البراند')->searchable()->weight('bold'),
                TextColumn::make('category_label')->label('الفئة')->badge(),
                TextColumn::make('products_count')->label('المنتجات')->counts('products'),
                TextColumn::make('users_count')->label('المستخدمون')->counts('users'),
                TextColumn::make('whatsapp')->label('واتساب'),
                IconColumn::make('is_active')->label('نشط')->boolean(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBrands::route('/'),
            'create' => CreateBrand::route('/create'),
            'edit' => EditBrand::route('/{record}/edit'),
        ];
    }
}
