<?php

namespace App\Filament\Widgets;

use App\Enums\OrderStatus;
use App\Models\Order;
use Filament\Widgets\ChartWidget;

class SalesChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Penjualan 30 hari terakhir';

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '280px';

    protected function getData(): array
    {
        $start = today()->subDays(29);

        $rows = Order::query()
            ->where('status', OrderStatus::Paid)
            ->where('paid_at', '>=', $start)
            ->get(['paid_at', 'total_amount'])
            ->groupBy(fn (Order $o) => $o->paid_at->toDateString());

        $labels = [];
        $revenue = [];
        $orders = [];
        for ($i = 0; $i < 30; $i++) {
            $day = $start->copy()->addDays($i);
            $labels[] = $day->translatedFormat('d M');
            $revenue[] = (int) ($rows->get($day->toDateString())?->sum('total_amount') ?? 0);
            $orders[] = $rows->get($day->toDateString())?->count() ?? 0;
        }

        return [
            'datasets' => [
                ['label' => 'Pendapatan (Rp)', 'data' => $revenue, 'borderColor' => '#b76e79', 'backgroundColor' => 'rgba(183,110,121,0.15)', 'fill' => true, 'yAxisID' => 'y'],
                ['label' => 'Order', 'data' => $orders, 'borderColor' => '#2f3e46', 'yAxisID' => 'y1'],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => ['beginAtZero' => true, 'position' => 'left'],
                'y1' => ['beginAtZero' => true, 'position' => 'right', 'grid' => ['drawOnChartArea' => false], 'ticks' => ['precision' => 0]],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
