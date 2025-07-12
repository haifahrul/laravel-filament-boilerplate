<?php

namespace App\Livewire\Widgets;

use App\Models\Order;
use App\Models\User;
use App\Models\Visit;
use Livewire\Component;

class DashboardStats extends Component
{
    public $from;
    public $to;
    public $userId;

    protected $listeners = ['dashboardFilterUpdated' => 'updateFilters'];

    public function mount()
    {
        $this->from = now()->startOfWeek()->format('Y-m-d');
        $this->to = now()->endOfWeek()->format('Y-m-d');
        $this->userId = '';
    }

    public function updateFilters($filters)
    {
        $this->from = $filters['from'];
        $this->to = $filters['to'];
        $this->userId = $filters['userId'];
    }

    public function render()
    {
        $orderQuery = Order::whereBetween('order_date', [$this->from, $this->to]);
        $visitQuery = Visit::whereBetween('checked_in_at', [$this->from, $this->to]);
        $userQuery = User::role('sales')->whereHas('orders', function ($q) {
            $q->whereBetween('order_date', [$this->from, $this->to]);
        });

        if ($this->userId) {
            $orderQuery->where('user_id', $this->userId);
            $visitQuery->where('user_id', $this->userId);
            $userQuery->where('id', $this->userId);
        }

        return view('livewire.widgets.dashboard-stats', [
            'totalOrders' => $orderQuery->count(),
            'totalVisits' => $visitQuery->count(),
            'activeSales' => $userQuery->count(),
        ]);
    }
}
