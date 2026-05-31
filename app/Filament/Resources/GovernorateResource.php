<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Table;
use App\Filament\Resources\GovernorateResource\Pages\ListGovernorates;
use App\Filament\Resources\GovernorateResource\Pages\EditGovernorate;
use App\Models\Governorate;
use Filament\Resources\Resource;

class GovernorateResource extends Resource
{
    protected static ?string $model = Governorate::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-map';

    protected static ?string $navigationLabel = 'المحافظات';

    protected static ?string $modelLabel = 'محافظة';

    protected static ?string $pluralModelLabel = 'المحافظات';

    protected static string | \UnitEnum | null $navigationGroup = 'الشحن';

    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('بيانات المحافظة')->schema([
                TextInput::make('name')->label('اسم المحافظة')->required(),
                TextInput::make('shipping_fee')->label('رسوم الشحن')
                    ->numeric()->required()->suffix('ج.م'),
                TextInput::make('free_over')->label('شحن مجاني إذا تجاوز الطلب')
                    ->numeric()->nullable()->suffix('ج.م')
                    ->helperText('اتركه فارغًا لتطبيق الإعداد العام'),
                TextInput::make('sort')->label('الترتيب')->numeric()->default(0),
                Toggle::make('is_active')->label('نشطة')->default(true),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('المحافظة')->searchable()->weight('bold'),
                TextColumn::make('shipping_fee')->label('الشحن')->suffix(' ج.م'),
                TextColumn::make('free_over')->label('مجاني فوق')
                    ->formatStateUsing(fn ($state) => $state ? number_format($state).' ج.م' : '—'),
                TextColumn::make('sort')->label('الترتيب')->sortable(),
                IconColumn::make('is_active')->label('نشطة')->boolean(),
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
            'index' => ListGovernorates::route('/'),
            'edit' => EditGovernorate::route('/{record}/edit'),
        ];
    }
}
