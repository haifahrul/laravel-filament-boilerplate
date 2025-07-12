<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Visit;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget;

class DashboardStatsV2 extends StatsOverviewWidget
{
    protected function getCards(): array
    {
        return [
            Card::make('Total Order Hari Ini', Order::whereDate('order_date', today())->count())
                ->description('Hari ini')
                ->color('success'),

            Card::make('Kunjungan Hari Ini', Visit::whereDate('checked_in_at', today())->count())
                ->description('Sales Visit')
                ->color('primary'),

            Card::make('Sales Aktif', User::role('sales')->whereHas('orders', fn ($q) => $q->whereDate('order_date', today()))->count())
                ->description('Dengan aktivitas hari ini')
                ->color('info'),
        ];
    }
}
