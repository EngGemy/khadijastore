<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;

class TopBrandsChart extends ChartWidget
{
    protected ?string $heading = 'أفضل البراندات (إيرادات)';

    protected static ?int $sort = 3;

    public static function canView(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $brands = Order::query()
            ->withoutGlobalScopes()
            ->selectRaw('brand_id, SUM(total) as revenue')
            ->whereNotIn('status', ['cancelled'])
            ->groupBy('brand_id')
            ->reorder()
            ->orderByDesc('revenue')
            ->limit(6)
            ->with('brand')
            ->get();

        $labels = [];
        $values = [];

        foreach ($brands as $b) {
            $labels[] = $b->brand?->name ?? 'غير معروف';
            $values[] = (int) $b->revenue;
        }

        return [
            'datasets' => [
                [
                    'label' => 'الإيرادات (ج.م)',
                    'data' => $values,
                    'backgroundColor' => [
                        '#16a34a',
                        '#22c55e',
                        '#4ade80',
                        '#86efac',
                        '#bbf7d0',
                        '#dcfce7',
                    ],
                    'borderRadius' => 6,
                    'barThickness' => 32,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => ['display' => false],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'grid' => ['color' => 'rgba(0,0,0,0.05)'],
                    'ticks' => ['font' => ['family' => 'Cairo']],
                ],
                'x' => [
                    'grid' => ['display' => false],
                    'ticks' => ['font' => ['family' => 'Cairo']],
                ],
            ],
        ];
    }
}
