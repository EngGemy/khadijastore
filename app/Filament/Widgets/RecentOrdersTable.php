<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentOrdersTable extends BaseWidget
{
    protected static ?string $heading = 'أحدث الطلبات';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 4;

    public function table(Table $table): Table
    {
        $user = auth()->user();
        $isSuper = $user->isSuperAdmin();

        $base = $isSuper
            ? Order::query()->withoutGlobalScopes()
            : Order::query();

        return $table
            ->query(
                (clone $base)
                    ->with('brand')
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('order_no')
                    ->label('رقم الطلب')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('brand.name')
                    ->label('البراند')
                    ->badge()
                    ->visible(fn () => $isSuper),

                TextColumn::make('customer_name')
                    ->label('العميل')
                    ->searchable(),

                TextColumn::make('customer_phone')
                    ->label('الموبايل')
                    ->searchable(),

                TextColumn::make('total')
                    ->label('الإجمالي')
                    ->formatStateUsing(fn ($state) => number_format($state).' ج.م')
                    ->weight('bold'),

                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->formatStateUsing(fn ($state) => Order::STATUSES[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'info',
                        'processing' => 'primary',
                        'shipped' => 'success',
                        'delivered' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->label('التاريخ')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->paginated(false)
            ->emptyStateHeading('لا توجد طلبات حاليًا');
    }
}
