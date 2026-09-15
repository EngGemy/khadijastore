<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages\CreateCategory;
use App\Filament\Resources\CategoryResource\Pages\EditCategory;
use App\Filament\Resources\CategoryResource\Pages\ListCategories;
use App\Models\Category;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'شجرة التصنيفات';

    protected static ?string $modelLabel = 'تصنيف';

    protected static ?string $pluralModelLabel = 'التصنيفات';

    protected static string|\UnitEnum|null $navigationGroup = 'الكتالوج';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['parent', 'children'])
            ->withCount(['products', 'children']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('بيانات التصنيف')->schema([
                Select::make('brand_id')
                    ->label('البراند (المتجر)')
                    ->relationship('brand', 'name')
                    ->required()
                    ->live()
                    ->visible(fn () => auth()->user()?->isSuperAdmin() ?? false),
                Select::make('parent_id')
                    ->label('التصنيف الأب')
                    ->options(function (Get $get, ?Category $record) {
                        $brandId = $get('brand_id') ?? auth()->user()?->brand_id;

                        return Category::hierarchicalOptions($brandId, $record);
                    })
                    ->searchable()
                    ->nullable()
                    ->helperText('مثال: سماعات ← أنكر / سامسونج'),
                TextInput::make('name')
                    ->label('الاسم')
                    ->required()
                    ->live(onBlur: true),
                TextInput::make('slug')
                    ->label('Slug')
                    ->helperText('يُولَّد تلقائيًا من الاسم'),
                TextInput::make('sort')
                    ->label('الترتيب')
                    ->numeric()
                    ->default(0),
                Toggle::make('is_active')
                    ->label('نشط')
                    ->default(true),
            ])->columns(2),

            Section::make('بنر القسم الدعائي')
                ->description('يظهر في الرئيسية داخل منطقة الأقسام، وفي صفحة المتجر عند تصفّح هذا القسم.')
                ->schema([
                    Toggle::make('is_promo_active')
                        ->label('تفعيل البنر')
                        ->default(false)
                        ->helperText('لن يظهر البنر للزوار حتى يُفعَّل'),
                    SpatieMediaLibraryFileUpload::make('banner')
                        ->label('صورة البنر')
                        ->collection('banner')
                        ->image()
                        ->disk('public')
                        ->visibility('public')
                        ->maxSize(6144)
                        ->helperText('المقاس الموصى به: 1200×420px · JPG/WebP · الحد الأقصى 6 MB')
                        ->columnSpanFull(),
                    TextInput::make('promo_headline')
                        ->label('عنوان البنر')
                        ->maxLength(80)
                        ->placeholder('عروض الزيوت هذا الأسبوع'),
                    TextInput::make('promo_cta_text')
                        ->label('نص الزر')
                        ->maxLength(40)
                        ->placeholder('تسوّق القسم'),
                    TextInput::make('promo_cta_url')
                        ->label('رابط الزر (اختياري)')
                        ->placeholder('اتركه فارغًا للذهاب تلقائيًا إلى منتجات القسم')
                        ->columnSpanFull(),
                ])
                ->columns(2)
                ->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('التصنيف')
                    ->searchable()
                    ->weight('bold')
                    ->formatStateUsing(fn (Category $record) => $record->indented_name),
                TextColumn::make('breadcrumb')
                    ->label('المسار')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('brand.name')
                    ->label('البراند')
                    ->badge()
                    ->visible(fn () => auth()->user()?->isSuperAdmin() ?? false),
                TextColumn::make('children_count')
                    ->label('فرعية')
                    ->counts('children')
                    ->badge()
                    ->color('info'),
                TextColumn::make('products_count')
                    ->label('منتجات')
                    ->counts('products')
                    ->badge()
                    ->color('success'),
                TextColumn::make('sort')
                    ->label('الترتيب')
                    ->sortable(),
                IconColumn::make('is_promo_active')
                    ->label('بنر')
                    ->boolean()
                    ->toggleable(),
                IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('parent_id')
                    ->label('تحت تصنيف')
                    ->relationship('parent', 'name')
                    ->searchable()
                    ->preload(),
                TernaryFilter::make('is_active')
                    ->label('نشط'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('sort');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCategories::route('/'),
            'create' => CreateCategory::route('/create'),
            'edit' => EditCategory::route('/{record}/edit'),
        ];
    }
}
