<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class BestSellingProductsTable extends BaseWidget
{
    protected static ?string $heading = 'المنتجات الأكثر مبيعًا';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 6;

    public function table(Table $table): Table
    {
        $user = auth()->user();
        $isSuper = $user->isSuperAdmin();

        $base = $isSuper
            ? Product::query()->withoutGlobalScopes()
            : Product::query();

        return $table
            ->query(
                (clone $base)
                    ->with('brand')
                    ->where('is_active', true)
                    ->orderByDesc('sales_count')
                    ->limit(8)
            )
            ->columns([
                SpatieMediaLibraryImageColumn::make('cover')
                    ->label('')
                    ->collection('cover')
                    ->circular()
                    ->size(40),

                TextColumn::make('name')
                    ->label('المنتج')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('brand.name')
                    ->label('البراند')
                    ->badge()
                    ->visible(fn () => $isSuper),

                TextColumn::make('price')
                    ->label('السعر')
                    ->formatStateUsing(fn ($state) => number_format($state).' ج.م'),

                TextColumn::make('sales_count')
                    ->label('المبيعات')
                    ->sortable()
                    ->weight('bold')
                    ->color('success'),

                TextColumn::make('rating')
                    ->label('التقييم')
                    ->formatStateUsing(fn ($state) => number_format($state, 1).' ★'),
            ])
            ->paginated(false)
            ->emptyStateHeading('لا توجد منتجات مبيعة حاليًا');
    }
}
