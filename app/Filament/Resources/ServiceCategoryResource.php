<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceCategoryResource\Pages\CreateServiceCategory;
use App\Filament\Resources\ServiceCategoryResource\Pages\EditServiceCategory;
use App\Filament\Resources\ServiceCategoryResource\Pages\ListServiceCategories;
use App\Models\Listing;
use App\Models\ServiceCategory;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ServiceCategoryResource extends Resource
{
    protected static ?string $model = ServiceCategory::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'أقسام الدليل';

    protected static ?string $modelLabel = 'قسم';

    protected static ?string $pluralModelLabel = 'أقسام الدليل';

    protected static string|\UnitEnum|null $navigationGroup = 'الدليل';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('بيانات القسم')->schema([
                Select::make('type')
                    ->label('النوع')
                    ->options(Listing::types())
                    ->required(),
                TextInput::make('name')
                    ->label('الاسم بالعربية')
                    ->required(),
                TextInput::make('name_en')
                    ->label('الاسم بالإنجليزية')
                    ->nullable(),
                TextInput::make('slug')
                    ->label('Slug')
                    ->nullable()
                    ->helperText('يُولَّد تلقائيًا من الاسم إن تُرك فارغًا'),
                TextInput::make('sort')
                    ->label('الترتيب')
                    ->numeric()
                    ->default(0),
                Toggle::make('is_active')
                    ->label('نشط')
                    ->default(true),
                Select::make('brand_id')
                    ->label('البراند')
                    ->relationship('brand', 'name')
                    ->placeholder('عام (للكل)')
                    ->helperText('اتركه فارغًا ليكون عامًا')
                    ->visible(fn () => auth()->user()?->isSuperAdmin() ?? false),
            ])->columns(2),
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
                TextColumn::make('type')
                    ->label('النوع')
                    ->badge()
                    ->formatStateUsing(fn ($state) => Listing::types()[$state] ?? $state),
                TextColumn::make('name_en')
                    ->label('English')
                    ->placeholder('—'),
                TextColumn::make('listings_count')
                    ->label('الإدراجات')
                    ->counts('listings'),
                TextColumn::make('sort')
                    ->label('الترتيب')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean(),
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
            'index'  => ListServiceCategories::route('/'),
            'create' => CreateServiceCategory::route('/create'),
            'edit'   => EditServiceCategory::route('/{record}/edit'),
        ];
    }
}
