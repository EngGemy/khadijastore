<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use App\Filament\Resources\ThemeResource\Pages\ListThemes;
use App\Filament\Resources\ThemeResource\Pages\CreateTheme;
use App\Filament\Resources\ThemeResource\Pages\EditTheme;
use App\Filament\Resources\ThemeResource\Pages;
use App\Models\Theme;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ThemeResource extends Resource
{
    protected static ?string $model = Theme::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-swatch';

    protected static ?string $navigationLabel = 'الثيمات والمناسبات';

    protected static ?string $modelLabel = 'ثيم';

    protected static ?string $pluralModelLabel = 'الثيمات';

    protected static string | \UnitEnum | null $navigationGroup = 'الإدارة';

    protected static ?int $navigationSort = 11;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('الثيم')->schema([
                TextInput::make('name')->label('الاسم')
                    ->required()->placeholder('ثيم العيد'),
                TextInput::make('key')->label('المفتاح')
                    ->required()->placeholder('eid')->unique(ignoreRecord: true),
                Select::make('scope')->label('النطاق')
                    ->options(['global' => 'عام (كل البراندات)', 'brand' => 'براند محدد'])
                    ->default('global')->live()->required(),
                Select::make('brand_id')->label('البراند')
                    ->relationship('brand', 'name')
                    ->visible(fn (Get $get) => $get('scope') === 'brand'),
            ])->columns(2),

            Section::make('ألوان الثيم')->schema([
                ColorPicker::make('tokens.accent')->label('اللون المميز'),
                ColorPicker::make('tokens.ink')->label('لون النص'),
                ColorPicker::make('tokens.paper')->label('الخلفية'),
                TextInput::make('tokens.strip_text')->label('نص الشريط العلوي')
                    ->columnSpanFull(),
            ])->columns(3),

            Section::make('التفعيل والجدولة')->schema([
                Toggle::make('is_active')->label('مفعّل'),
                TextInput::make('priority')->label('الأولوية')
                    ->numeric()->default(0)->helperText('الأعلى يفوز عند التعارض'),
                DateTimePicker::make('starts_at')->label('يبدأ في')
                    ->helperText('اتركه فارغًا للتفعيل الفوري'),
                DateTimePicker::make('ends_at')->label('ينتهي في')
                    ->helperText('للمناسبات المؤقتة مثل العيد'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('الثيم')->weight('bold'),
                TextColumn::make('scope')->label('النطاق')->badge()
                    ->formatStateUsing(fn ($s) => $s === 'global' ? 'عام' : 'براند'),
                TextColumn::make('brand.name')->label('البراند')->placeholder('â€”'),
                ColorColumn::make('tokens.accent')->label('اللون'),
                IconColumn::make('is_active')->label('مفعّل')->boolean(),
                TextColumn::make('starts_at')->label('يبدأ')->dateTime('Y-m-d')->placeholder('â€”'),
                TextColumn::make('ends_at')->label('ينتهي')->dateTime('Y-m-d')->placeholder('â€”'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListThemes::route('/'),
            'create' => CreateTheme::route('/create'),
            'edit' => EditTheme::route('/{record}/edit'),
        ];
    }
}

