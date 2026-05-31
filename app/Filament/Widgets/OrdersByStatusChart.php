<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;

class OrdersByStatusChart extends ChartWidget
{
    protected ?string $heading = 'توزيع الطلبات حسب الحالة';

    protected static ?int $sort = 3;

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $user = auth()->user();
        $isSuper = $user->isSuperAdmin();

        $base = $isSuper
            ? Order::query()->withoutGlobalScopes()
            : Order::query();

        $statuses = ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'];
        $labels = [];
        $values = [];
        $colors = [
            '#f59e0b', // pending - amber
            '#3b82f6', // confirmed - blue
            '#8b5cf6', // processing - violet
            '#06b6d4', // shipped - cyan
            '#16a34a', // delivered - green
            '#ef4444', // cancelled - red
        ];
        $bgColors = [];

        foreach ($statuses as $i => $status) {
            $count = (clone $base)->where('status', $status)->count();
            if ($count > 0 || $status !== 'cancelled') {
                $labels[] = Order::STATUSES[$status] ?? $status;
                $values[] = $count;
                $bgColors[] = $colors[$i];
            }
        }

        return [
            'datasets' => [
                [
                    'data' => $values,
                    'backgroundColor' => $bgColors,
                    'borderWidth' => 0,
                    'hoverOffset' => 8,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'labels' => [
                        'usePointStyle' => true,
                        'padding' => 16,
                        'font' => ['family' => 'Cairo', 'size' => 12],
                    ],
                ],
            ],
            'cutout' => '65%',
        ];
    }
}
