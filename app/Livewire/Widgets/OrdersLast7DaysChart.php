<?php

namespace App\Livewire\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Order;
use Carbon\CarbonPeriod;

class OrdersLast7DaysChart extends ChartWidget
{
    protected static ?string $heading = 'Order 7 Hari Terakhir';
    protected static ?string $maxHeight = '240px';
    protected static ?string $pollingInterval = null; // Nonaktifkan auto-refresh

    protected $listeners = ['dashboardFilterUpdated' => 'updateFilters'];

    public $from;
    public $to;
    public $userId;

    public function mount(): void
    {
        $this->from   = now()->startOfWeek()->format('Y-m-d');
        $this->to     = now()->endOfWeek()->format('Y-m-d');
        $this->userId = '';
    }

    public function updateFilters($filters): void
    {
        $this->from   = $filters['from'] ?? $this->from;
        $this->to     = $filters['to'] ?? $this->to;
        $this->userId = $filters['userId'] ?? '';
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $from = $this->from ?? now()->subDays(6)->startOfDay();
        $to   = $this->to ?? now()->endOfDay();

        $query = Order::whereBetween('order_date', [$from, $to]);

        if ($this->userId) {
            $query->where('user_id', $this->userId);
        }

        $orders = $query->selectRaw('DATE(order_date) as date, COUNT(*) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        $period = CarbonPeriod::create($from, $to);
        $labels = [];
        $data   = [];

        foreach ($period as $date) {
            $key = $date->format('Y-m-d');
            $labels[] = $date->format('d M');
            $data[] = $orders[$key] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Order',
                    'data'  => $data,
                    // 'borderColor' => '#3b82f6',
                    // 'backgroundColor' => 'rgba(59, 130, 246, 0.2)',
                    'tension' => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }
}
