<?php

namespace App\Filament\Resources;

use App\Models\Order;
use App\Models\OrderItem;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationLabel = 'الطلبات';

    protected static ?string $modelLabel = 'طلب';

    protected static ?string $pluralModelLabel = 'الطلبات';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('status', 'pending')->count();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('بيانات العميل')->schema([
                TextInput::make('customer_name')->label('الاسم')->required(),
                TextInput::make('customer_phone')->label('الموبايل')->tel()->required(),
                TextInput::make('governorate')->label('المحافظة')->required(),
                Textarea::make('address')->label('العنوان')->required()->columnSpanFull(),
            ])->columns(2),

            Section::make('الطلب والدفع')->schema([
                Select::make('status')->label('الحالة')
                    ->options(Order::STATUSES)->required()->native(false),
                Select::make('payment_method')->label('طريقة الدفع')
                    ->options(Order::PAYMENT_METHODS)->disabled(),
                TextInput::make('total')->label('الإجمالي')
                    ->numeric()->suffix('ج.م')->disabled(),
                FileUpload::make('receipt_path')->label('إيصال التحويل')
                    ->image()->disk('public')->directory('receipts')
                    ->visible(fn ($record) => $record?->payment_method === 'transfer'),
            ])->columns(2),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('بيانات العميل')->schema([
                TextEntry::make('order_no')->label('رقم الطلب')->weight('bold'),
                TextEntry::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->formatStateUsing(fn ($state) => Order::STATUSES[$state] ?? $state)
                    ->color(fn ($state): string => match ($state) {
                        'delivered' => 'success',
                        'cancelled' => 'danger',
                        'pending' => 'warning',
                        'shipped' => 'info',
                        default => 'gray',
                    }),
                TextEntry::make('customer_name')->label('الاسم'),
                TextEntry::make('customer_phone')->label('الموبايل'),
                TextEntry::make('governorate')->label('المحافظة'),
                TextEntry::make('payment_method')
                    ->label('طريقة الدفع')
                    ->formatStateUsing(fn ($state) => Order::PAYMENT_METHODS[$state] ?? $state),
                TextEntry::make('address')->label('العنوان')->columnSpanFull(),
                TextEntry::make('notes')->label('ملاحظات')->columnSpanFull()->placeholder('لا يوجد'),
            ])->columns(3),

            Section::make('الملخص المالي')->schema([
                TextEntry::make('subtotal')->label('المنتج')
                    ->formatStateUsing(fn ($state) => number_format($state) . ' ج.م'),
                TextEntry::make('shipping')->label('الشحن')
                    ->formatStateUsing(fn ($state) => number_format($state) . ' ج.م'),
                TextEntry::make('total')->label('الإجمالي')
                    ->formatStateUsing(fn ($state) => number_format($state) . ' ج.م')
                    ->weight('bold')->color('success'),
            ])->columns(3),

            Section::make('المنتجات')->schema([
                RepeatableEntry::make('items')
                    ->label('')
                    ->schema([
                        TextEntry::make('product_name')->label('المنتج'),
                        TextEntry::make('variant_name')->label('الباقة')->placeholder('—'),
                        TextEntry::make('qty')->label('الكمية'),
                        TextEntry::make('price')
                            ->label('السعر')
                            ->formatStateUsing(fn ($state) => number_format($state) . ' ج.م'),
                        TextEntry::make('line_total')
                            ->label('الإجمالي')
                            ->formatStateUsing(fn ($state) => number_format($state) . ' ج.م')
                            ->weight('bold'),
                    ])->columns(5),
            ]),

            Section::make('سجل الحالات')->schema([
                RepeatableEntry::make('statusHistories')
                    ->label('')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('التاريخ')
                            ->dateTime('Y-m-d H:i'),
                        TextEntry::make('from_label')
                            ->label('من')
                            ->placeholder('—'),
                        TextEntry::make('to_label')
                            ->label('إلى')
                            ->badge()
                            ->color(fn ($state): string => match ($state) {
                                'تم التسليم' => 'success',
                                'ملغي' => 'danger',
                                'قيد المراجعة' => 'warning',
                                'تم الشحن' => 'info',
                                default => 'gray',
                            }),
                        TextEntry::make('changer.name')
                            ->label('بواسطة')
                            ->placeholder('النظام'),
                    ])->columns(4),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_no')->label('رقم الطلب')
                    ->searchable()->sortable()->weight('bold'),
                TextColumn::make('brand.name')->label('البراند')
                    ->badge()->visible(fn () => auth()->user()->isSuperAdmin()),
                TextColumn::make('customer_name')->label('العميل')->searchable(),
                TextColumn::make('customer_phone')->label('الموبايل')->searchable(),
                TextColumn::make('governorate')->label('المحافظة'),
                TextColumn::make('total')->label('الإجمالي')
                    ->money('EGP')->sortable(),
                TextColumn::make('payment_method')->label('الدفع')
                    ->badge()->formatStateUsing(fn ($state) => Order::PAYMENT_METHODS[$state] ?? $state),
                SelectColumn::make('status')->label('الحالة')
                    ->options(Order::STATUSES)->selectablePlaceholder(false),
                TextColumn::make('created_at')->label('التاريخ')
                    ->dateTime('Y-m-d H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->label('الحالة')->options(Order::STATUSES),
                SelectFilter::make('payment_method')->label('الدفع')->options(Order::PAYMENT_METHODS),
                SelectFilter::make('brand_id')->label('البراند')
                    ->relationship('brand', 'name')
                    ->visible(fn () => auth()->user()->isSuperAdmin()),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\OrderResource\Pages\ListOrders::route('/'),
            'view' => \App\Filament\Resources\OrderResource\Pages\ViewOrder::route('/{record}'),
            'edit' => \App\Filament\Resources\OrderResource\Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
