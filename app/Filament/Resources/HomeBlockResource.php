<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HomeBlockResource\Pages\CreateHomeBlock;
use App\Filament\Resources\HomeBlockResource\Pages\EditHomeBlock;
use App\Filament\Resources\HomeBlockResource\Pages\ListHomeBlocks;
use App\Models\HomeBlock;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Cache;

class HomeBlockResource extends Resource
{
    protected static ?string $model = HomeBlock::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-view-columns';

    protected static ?string $navigationLabel = 'أقسام الصفحة الرئيسية';

    protected static ?string $modelLabel = 'قسم';

    protected static ?string $pluralModelLabel = 'أقسام الصفحة الرئيسية';

    protected static string | \UnitEnum | null $navigationGroup = 'الإعدادات';

    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('النوع والترتيب')->schema([
                Select::make('type')
                    ->label('نوع القسم')
                    ->options(HomeBlock::typeLabels())
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn ($set) => $set('data', [])),
                TextInput::make('title')
                    ->label('العنوان (اختياري — إذا تُرك فارغًا يستخدم الإعداد العام)')
                    ->nullable(),
                TextInput::make('subtitle')
                    ->label('العنوان الفرعي / Eyebrow (اختياري)')
                    ->nullable(),
                TextInput::make('sort')
                    ->label('الترتيب (أصغر = أعلى)')
                    ->numeric()
                    ->default(0),
                Toggle::make('is_active')
                    ->label('مفعّل')
                    ->default(true),
            ])->columns(2),

            // ─── CATEGORIES ──────────────────────────────────────────────
            Section::make('بطاقات الفئات')
                ->schema([
                    Repeater::make('data.items')
                        ->label('الفئات')
                        ->schema([
                            TextInput::make('label')->label('الاسم')->required(),
                            TextInput::make('sublabel')->label('الاسم الإنجليزي'),
                            TextInput::make('icon')->label('الأيقونة (Emoji)')->default('📦'),
                            TextInput::make('link')->label('الرابط')->required(),
                            Select::make('bg_style')
                                ->label('خلفية البطاقة')
                                ->options([
                                    'bg-ink'         => 'أسود صلب',
                                    'gradient-dark'  => 'تدرج داكن 1',
                                    'gradient-darker'=> 'تدرج داكن 2',
                                    'gradient-mixed' => 'تدرج مختلط',
                                ])
                                ->default('bg-ink'),
                        ])
                        ->columns(3)
                        ->addActionLabel('إضافة فئة')
                        ->defaultItems(1)
                        ->columnSpanFull(),
                ])
                ->visible(fn (Get $get) => $get('type') === 'categories'),

            // ─── BANNER ──────────────────────────────────────────────────
            Section::make('محتوى البانر')
                ->schema([
                    TextInput::make('data.eyebrow')->label('النص الصغير'),
                    TextInput::make('data.title')->label('العنوان'),
                    Textarea::make('data.paragraph')->label('الفقرة')->rows(2)->columnSpanFull(),
                    TextInput::make('data.btn_text')->label('نص الزر'),
                    TextInput::make('data.btn_link')->label('رابط الزر'),
                ])
                ->columns(2)
                ->visible(fn (Get $get) => $get('type') === 'banner'),

            // ─── IMAGE CTA ───────────────────────────────────────────────
            Section::make('محتوى البانر بصورة')
                ->schema([
                    TextInput::make('data.eyebrow')->label('النص الصغير'),
                    TextInput::make('data.title')->label('العنوان'),
                    Textarea::make('data.paragraph')->label('الفقرة')->rows(2)->columnSpanFull(),
                    TextInput::make('data.btn_text')->label('نص الزر'),
                    TextInput::make('data.btn_link')->label('رابط الزر'),
                    TextInput::make('data.image')->label('رابط الصورة (URL)')->url()->columnSpanFull(),
                ])
                ->columns(2)
                ->visible(fn (Get $get) => $get('type') === 'image_cta'),

            // ─── RICH TEXT ───────────────────────────────────────────────
            Section::make('المحتوى (HTML)')
                ->schema([
                    RichEditor::make('data.html')
                        ->label('المحتوى')
                        ->columnSpanFull(),
                ])
                ->visible(fn (Get $get) => $get('type') === 'rich_text'),

            // ─── PRODUCTS GRID ───────────────────────────────────────────
            Section::make('إعدادات شبكة المنتجات')
                ->schema([
                    Select::make('data.source')
                        ->label('مصدر المنتجات')
                        ->options([
                            'featured'     => 'المميّزة',
                            'latest'       => 'الأحدث',
                            'best_selling' => 'الأكثر مبيعًا',
                        ])
                        ->default('featured'),
                    TextInput::make('data.limit')
                        ->label('الحد الأقصى')
                        ->numeric()
                        ->default(8)
                        ->minValue(1)
                        ->maxValue(24),
                ])
                ->columns(2)
                ->visible(fn (Get $get) => $get('type') === 'products_grid'),

            // ─── BRANDS MARQUEE / BRANDS GRID ────────────────────────────
            Section::make('إعدادات البراندات')
                ->schema([
                    TextInput::make('data.limit')
                        ->label('الحد الأقصى (فارغ = الكل)')
                        ->numeric()
                        ->nullable(),
                ])
                ->visible(fn (Get $get) => in_array($get('type'), ['brands_marquee', 'brands_grid'])),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort')
            ->defaultSort('sort')
            ->columns([
                TextColumn::make('sort')->label('#')->width(40),
                TextColumn::make('type')
                    ->label('النوع')
                    ->badge()
                    ->formatStateUsing(fn ($state) => HomeBlock::typeLabels()[$state] ?? $state),
                TextColumn::make('title')->label('العنوان')->placeholder('(من الإعدادات)'),
                IconColumn::make('is_active')->label('مفعّل')->boolean(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()->after(fn () => Cache::forget('home.blocks.resolved')),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListHomeBlocks::route('/'),
            'create' => CreateHomeBlock::route('/create'),
            'edit'   => EditHomeBlock::route('/{record}/edit'),
        ];
    }
}
