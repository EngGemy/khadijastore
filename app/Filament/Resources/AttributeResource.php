<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttributeResource\Pages\CreateAttribute;
use App\Filament\Resources\AttributeResource\Pages\EditAttribute;
use App\Filament\Resources\AttributeResource\Pages\ListAttributes;
use App\Models\Attribute;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AttributeResource extends Resource
{
    protected static ?string $model = Attribute::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-swatch';

    protected static ?string $navigationLabel = 'الصفات (ألوان/أحجام)';

    protected static ?string $modelLabel = 'صفة';

    protected static ?string $pluralModelLabel = 'الصفات (ألوان/أحجام)';

    protected static string | \UnitEnum | null $navigationGroup = 'الكتالوج';

    protected static ?int $navigationSort = 3;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withCount('values');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('بيانات الصفة')->schema([
                TextInput::make('name')
                    ->label('الاسم')
                    ->required(),
                TextInput::make('code')
                    ->label('الكود')
                    ->required()
                    ->maxLength(32)
                    ->helperText('حروف إنجليزية بدون مسافات'),
                Select::make('input_type')
                    ->label('نوع الإدخال')
                    ->options([
                        'select' => 'قائمة منسدلة',
                        'color' => 'اختيار لون',
                    ])
                    ->default('select')
                    ->required(),
                TextInput::make('sort')
                    ->label('الترتيب')
                    ->numeric()
                    ->default(0),
                Select::make('brand_id')
                    ->label('البراند')
                    ->relationship('brand', 'name')
                    ->placeholder('عام')
                    ->helperText('اتركه فارغًا ليكون صفة عامة')
                    ->visible(fn () => auth()->user()?->isSuperAdmin() ?? false),
            ])->columns(2),

            Section::make('القيم')->schema([
                Repeater::make('values')
                    ->relationship()
                    ->schema([
                        TextInput::make('label')
                            ->label('الاسم المعروض')
                            ->required(),
                        TextInput::make('value')
                            ->label('القيمة')
                            ->maxLength(64)
                            ->nullable(),
                        ColorPicker::make('color_hex')
                            ->label('اللون (Hex)')
                            ->nullable(),
                        TextInput::make('sort')
                            ->label('الترتيب')
                            ->numeric()
                            ->default(0),
                    ])
                    ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
                    ->orderColumn('sort')
                    ->columns(2)
                    ->collapsible(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('code')
                    ->label('الكود')
                    ->badge(),
                TextColumn::make('values_count')
                    ->label('عدد القيم')
                    ->counts('values'),
                TextColumn::make('input_type')
                    ->label('نوع الإدخال'),
                TextColumn::make('brand.name')
                    ->label('البراند')
                    ->placeholder('عام'),
                TextColumn::make('sort')
                    ->label('الترتيب')
                    ->sortable(),
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
            'index' => ListAttributes::route('/'),
            'create' => CreateAttribute::route('/create'),
            'edit' => EditAttribute::route('/{record}/edit'),
        ];
    }
}
