<?php

namespace App\Livewire\Widgets;

use Livewire\Component;

class DashboardStats extends Component
{
    public $from;
    public $to;
    public $userId;

    protected $listeners = [ 'dashboardFilterUpdated' => 'updateFilters' ];

    public function mount()
    {
        $this->from   = now()->startOfWeek()->format('Y-m-d');
        $this->to     = now()->endOfWeek()->format('Y-m-d');
        $this->userId = '';
    }

    public function updateFilters($filters)
    {
        $this->from   = $filters[ 'from' ];
        $this->to     = $filters[ 'to' ];
        $this->userId = $filters[ 'userId' ];
    }

    public function render()
    {
        return view('livewire.widgets.dashboard-stats', [
        ]);
    }
}
