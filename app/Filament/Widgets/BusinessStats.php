<?php

namespace App\Filament\Widgets;

use App\Enums\InvitationStatus;
use App\Enums\OrderStatus;
use App\Models\Invitation;
use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BusinessStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $paid = Order::query()->where('status', OrderStatus::Paid);
        $rp = fn (int $n) => 'Rp '.number_format($n, 0, ',', '.');

        $today = (int) (clone $paid)->where('paid_at', '>=', today())->sum('total_amount');
        $month = (int) (clone $paid)->where('paid_at', '>=', now()->startOfMonth())->sum('total_amount');
        $ordersMonth = (clone $paid)->where('paid_at', '>=', now()->startOfMonth())->count();

        $counts = Invitation::query()->selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status');

        return [
            Stat::make('Pendapatan hari ini', $rp($today)),
            Stat::make('Pendapatan bulan ini', $rp($month))->description($ordersMonth.' order lunas'),
            Stat::make('Undangan aktif', (string) ($counts[InvitationStatus::Active->value] ?? 0))
                ->description(($counts[InvitationStatus::Expired->value] ?? 0).' berakhir'),
            Stat::make('Undangan draft', (string) ($counts[InvitationStatus::Draft->value] ?? 0))
                ->description('Calon pembeli'),
        ];
    }
}
