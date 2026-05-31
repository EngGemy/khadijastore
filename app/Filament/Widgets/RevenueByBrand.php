<?php

namespace App\Filament\Widgets;

use Filament\Tables\Columns\TextColumn;
use App\Models\Order;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * تفصيل الإيرادات والطلبات لكل براند — للسوبر أدمن فقط.
 */
class RevenueByBrand extends BaseWidget
{
    protected static ?string $heading = 'الإيرادات حسب البراند';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 7;

    public static function canView(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Order::query()
                    ->withoutGlobalScopes()
                    ->selectRaw('brand_id, COUNT(*) as orders_count, SUM(total) as revenue, SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending_count')
                    ->whereNotIn('status', ['cancelled'])
                    ->reorder()
                    ->groupBy('brand_id')
                    ->orderBy('revenue', 'desc')
                    ->with('brand')
            )
            ->defaultSort('revenue', 'desc')
            ->columns([
                TextColumn::make('brand.name')->label('البراند')->weight('bold'),
                TextColumn::make('orders_count')->label('الطلبات'),
                TextColumn::make('pending_count')->label('قيد المراجعة')
                    ->badge()->color('warning'),
                TextColumn::make('revenue')->label('الإيرادات')
                    ->formatStateUsing(fn ($s) => number_format($s).' ج.م')
                    ->weight('bold')->color('success'),
            ])
            ->paginated(false);
    }
}
