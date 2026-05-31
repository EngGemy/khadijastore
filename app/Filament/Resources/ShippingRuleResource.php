<?php

namespace App\Filament\Resources;

use App\Models\Governorate;
use App\Models\ShippingRule;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Table;
use App\Filament\Resources\ShippingRuleResource\Pages\ListShippingRules;
use App\Filament\Resources\ShippingRuleResource\Pages\CreateShippingRule;
use App\Filament\Resources\ShippingRuleResource\Pages\EditShippingRule;
use Filament\Resources\Resource;

class ShippingRuleResource extends Resource
{
    protected static ?string $model = ShippingRule::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationLabel = 'قواعد الشحن';

    protected static ?string $modelLabel = 'قاعدة شحن';

    protected static ?string $pluralModelLabel = 'قواعد الشحن';

    protected static string | \UnitEnum | null $navigationGroup = 'الشحن';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('الأساسيات')->schema([
                TextInput::make('name')->label('اسم القاعدة')->required()
                    ->placeholder('مثال: شحن مجاني للعيد'),

                Select::make('type')->label('نوع القاعدة')
                    ->options([
                        'free' => 'مجاني (0)',
                        'flat' => 'سعر ثابت',
                        'percent_off' => 'خصم نسبة %',
                        'amount_off' => 'خصم مبلغ ثابت',
                    ])
                    ->required()
                    ->live(),

                TextInput::make('value')->label('القيمة')
                    ->numeric()
                    ->nullable()
                    ->suffix(fn ($get) => match ($get('type')) {
                        'percent_off' => '%',
                        default => 'ج.م',
                    })
                    ->hidden(fn ($get) => $get('type') === 'free'),

                TextInput::make('priority')->label('الأولوية')
                    ->numeric()->default(0)->helperText('أعلى رقم = أولوية أعلى'),

                Toggle::make('is_active')->label('نشطة')->default(true),
            ])->columns(2),

            Section::make('النطاق والتطبيق')->schema([
                Select::make('scope')->label('نطاق التطبيق')
                    ->options([
                        'all' => 'كل المحافظات',
                        'selected' => 'محافظات محددة',
                    ])
                    ->required()
                    ->live(),

                Select::make('governorate_ids')
                    ->label('المحافظات')
                    ->multiple()
                    ->options(fn () => Governorate::pluck('name', 'name'))
                    ->visible(fn ($get) => $get('scope') === 'selected')
                    ->columnSpanFull(),

                TextInput::make('min_order_total')->label('الحد الأدنى للطلب')
                    ->numeric()->nullable()->suffix('ج.م')
                    ->helperText('تُطبّق فقط إذا كان إجمالي الطلب ≥ هذا المبلغ'),

                Select::make('brand_id')->label('البراند')
                    ->relationship('brand', 'name')
                    ->visible(fn () => auth()->user()->isSuperAdmin())
                    ->nullable()
                    ->helperText('اتركه فارغًا لتطبيق على كل البراندات'),
            ])->columns(2),

            Section::make('الجدولة')->schema([
                DateTimePicker::make('starts_at')->label('يبدأ من')
                    ->nullable()->native(false),
                DateTimePicker::make('ends_at')->label('ينتهي في')
                    ->nullable()->native(false),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('القاعدة')->searchable()->weight('bold'),
                TextColumn::make('type')->label('النوع')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'free' => 'مجاني',
                        'flat' => 'ثابت',
                        'percent_off' => 'خصم %',
                        'amount_off' => 'خصم مبلغ',
                        default => $state,
                    })->badge(),
                TextColumn::make('value')->label('القيمة')
                    ->formatStateUsing(function ($state, ShippingRule $record) {
                        if ($record->type === 'free') return '—';
                        return number_format($state).' '.($record->type === 'percent_off' ? '%' : 'ج.م');
                    }),
                TextColumn::make('scope')->label('النطاق')
                    ->formatStateUsing(fn ($state) => $state === 'all' ? 'الكل' : 'محدد'),
                TextColumn::make('priority')->label('الأولوية')->sortable(),
                TextColumn::make('brand.name')->label('البراند')
                    ->badge()->visible(fn () => auth()->user()->isSuperAdmin()),
                IconColumn::make('is_active')->label('نشطة')->boolean(),
                TextColumn::make('starts_at')->label('يبدأ')->dateTime('Y-m-d H:i'),
                TextColumn::make('ends_at')->label('ينتهي')->dateTime('Y-m-d H:i'),
            ])
            ->filters([
                // can add later
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('priority', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListShippingRules::route('/'),
            'create' => CreateShippingRule::route('/create'),
            'edit' => EditShippingRule::route('/{record}/edit'),
        ];
    }
}
