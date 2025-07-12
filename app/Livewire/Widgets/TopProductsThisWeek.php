<?php

namespace App\Livewire\Widgets;

use Livewire\Component;
use App\Models\OrderItem;

class TopProductsThisWeek extends Component
{
    public $from;
    public $to;
    public $userId;

    public $topProducts = [];

    protected $listeners = ['dashboardFilterUpdated' => 'updateFilters'];

    public function mount()
    {
        $this->from   = now()->startOfWeek()->format('Y-m-d');
        $this->to     = now()->endOfWeek()->format('Y-m-d');
        $this->userId = '';

        $this->loadTopProducts();
    }

    public function updateFilters($filters)
    {
        $this->from   = $filters['from'];
        $this->to     = $filters['to'];
        $this->userId = $filters['userId'];

        $this->loadTopProducts();
    }

    public function loadTopProducts()
    {
        $query = OrderItem::with('product')
            ->whereHas('order', function ($query) {
                $query->whereBetween('order_date', [$this->from, $this->to]);

                if ($this->userId) {
                    $query->where('user_id', $this->userId);
                }
            });

        $this->topProducts = $query->selectRaw('product_id, SUM(quantity) as total_sold')
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->with('product')
            ->take(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.widgets.top-products-this-week');
    }
}
