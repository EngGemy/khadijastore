<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class RevenueTrendChart extends ChartWidget
{
    protected ?string $heading = 'الإيرادات خلال 30 يوم';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 2;

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $user = auth()->user();
        $isSuper = $user->isSuperAdmin();

        $base = $isSuper
            ? Order::query()->withoutGlobalScopes()
            : Order::query();

        $data = (clone $base)
            ->selectRaw('DATE(created_at) as date, SUM(total) as revenue')
            ->whereNotIn('status', ['cancelled'])
            ->whereDate('created_at', '>=', now()->subDays(29))
            ->groupBy('date')
            ->reorder()
            ->orderBy('date')
            ->pluck('revenue', 'date');

        $labels = [];
        $values = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $labels[] = now()->subDays($i)->locale('ar')->translatedFormat('d M');
            $values[] = (int) ($data[$date] ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'الإيرادات (ج.م)',
                    'data' => $values,
                    'borderColor' => '#16a34a',
                    'backgroundColor' => 'rgba(22, 163, 74, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                    'pointBackgroundColor' => '#16a34a',
                    'pointBorderColor' => '#ffffff',
                    'pointBorderWidth' => 2,
                    'pointRadius' => 4,
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
                    'display' => true,
                    'position' => 'top',
                    'align' => 'end',
                    'labels' => [
                        'usePointStyle' => true,
                        'font' => ['family' => 'Cairo'],
                    ],
                ],
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
            'interaction' => [
                'mode' => 'index',
                'intersect' => false,
            ],
        ];
    }
}
