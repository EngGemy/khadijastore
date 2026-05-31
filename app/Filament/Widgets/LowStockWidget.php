<?php

namespace App\Filament\Widgets;

use App\Models\ProductVariant;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class LowStockWidget extends BaseWidget
{
    protected static ?string $heading = 'تنبيه المخزون المنخفض';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 8;

    public function table(Table $table): Table
    {
        $query = ProductVariant::query()
            ->with(['product.brand'])
            ->where('track_stock', true)
            ->where('stock', '>=', 0)
            ->whereColumn('stock', '<=', 'low_stock_threshold');

        if (! auth()->user()?->isSuperAdmin()) {
            $brandId = auth()->user()?->brand_id;
            $query->whereHas('product', fn (Builder $q) => $q->where('brand_id', $brandId));
        }

        return $table
            ->query($query)
            ->columns([
                TextColumn::make('product.name')
                    ->label('المنتج')
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('name')
                    ->label('الباقة'),
                TextColumn::make('product.brand.name')
                    ->label('البراند')
                    ->badge()
                    ->visible(fn () => auth()->user()?->isSuperAdmin()),
                TextColumn::make('stock')
                    ->label('المخزون الحالي')
                    ->badge()
                    ->color(fn (ProductVariant $record): string => $record->stock <= 0 ? 'danger' : 'warning'),
                TextColumn::make('low_stock_threshold')
                    ->label('حد التنبيه'),
            ])
            ->paginated(false)
            ->emptyStateHeading('لا يوجد مخزون منخفض حاليًا')
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}
