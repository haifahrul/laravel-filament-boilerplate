<?php

namespace App\Livewire\Widgets;

use Livewire\Component;
use App\Models\Order;

class TopSalesThisWeek extends Component
{
    public $from;
    public $to;
    public $userId;

    public $topSales = [];

    protected $listeners = ['dashboardFilterUpdated' => 'updateFilters'];

    public function mount()
    {
        $this->from   = now()->startOfWeek()->format('Y-m-d');
        $this->to     = now()->endOfWeek()->format('Y-m-d');
        $this->userId = '';

        $this->loadTopSales();
    }

    public function updateFilters($filters)
    {
        $this->from   = $filters['from'];
        $this->to     = $filters['to'];
        $this->userId = $filters['userId'];

        $this->loadTopSales();
    }

    public function loadTopSales()
    {
        $query = Order::with('user')
            ->whereBetween('order_date', [$this->from, $this->to]);

        if ($this->userId) {
            $query->where('user_id', $this->userId);
        }

        $this->topSales = $query->selectRaw('user_id, COUNT(*) as total_orders')
            ->groupBy('user_id')
            ->orderByDesc('total_orders')
            ->with('user')
            ->take(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.widgets.top-sales-this-week');
    }
}
