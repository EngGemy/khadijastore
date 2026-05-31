<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $user = auth()->user();
        $isSuper = $user->isSuperAdmin();

        $orderBase = $isSuper
            ? Order::query()->withoutGlobalScopes()
            : Order::query();

        $productBase = $isSuper
            ? Product::query()->withoutGlobalScopes()
            : Product::query();

        $reviewBase = $isSuper
            ? Review::query()->withoutGlobalScopes()
            : Review::query();

        $totalRevenue = (clone $orderBase)->whereNotIn('status', ['cancelled'])->sum('total');
        $ordersCount = (clone $orderBase)->count();
        $pendingCount = (clone $orderBase)->where('status', 'pending')->count();
        $todayRevenue = (clone $orderBase)
            ->whereNotIn('status', ['cancelled'])
            ->whereDate('created_at', today())
            ->sum('total');

        $productsCount = (clone $productBase)->count();
        $reviewsPending = (clone $reviewBase)->where('is_approved', false)->count();

        $avgOrderValue = $ordersCount > 0
            ? round($totalRevenue / $ordersCount, 0)
            : 0;

        return [
            Stat::make('إجمالي الإيرادات', number_format($totalRevenue).' ج.م')
                ->description('كل الطلبات النشطة')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('عدد الطلبات', number_format($ordersCount))
                ->description($pendingCount.' قيد المراجعة')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('primary'),

            Stat::make('إيرادات اليوم', number_format($todayRevenue).' ج.م')
                ->description(today()->locale('ar')->translatedFormat('l d M'))
                ->descriptionIcon('heroicon-m-calendar')
                ->color($todayRevenue > 0 ? 'warning' : 'gray'),

            Stat::make('متوسط قيمة الطلب', number_format($avgOrderValue).' ج.م')
                ->description('الوسطي الحسابي')
                ->descriptionIcon('heroicon-m-calculator')
                ->color('info'),

            Stat::make('المنتجات', number_format($productsCount))
                ->description('إجمالي المنتجات')
                ->descriptionIcon('heroicon-m-cube')
                ->color('primary'),

            Stat::make('مراجعات جديدة', number_format($reviewsPending))
                ->description('في انتظار الموافقة')
                ->descriptionIcon('heroicon-m-star')
                ->color($reviewsPending > 0 ? 'danger' : 'gray'),
        ];
    }
}
