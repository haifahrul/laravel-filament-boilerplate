<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;

class DashboardFilter extends Component
{
    public $from;
    public $to;
    public $userId;

    public function mount()
    {
        $this->from   = now()->startOfWeek()->format('Y-m-d');
        $this->to     = now()->endOfWeek()->format('Y-m-d');
        $this->userId = '';
    }

    public function applyFilter()
    {
        $this->dispatch('dashboardFilterUpdated', [
            'from'   => $this->from,
            'to'     => $this->to,
            'userId' => $this->userId,
        ]);
    }

    public function resetFilter()
    {
        $this->from   = now()->startOfWeek()->format('Y-m-d');
        $this->to     = now()->endOfWeek()->format('Y-m-d');
        $this->userId = '';

        $this->dispatch('dashboardFilterUpdated', [
            'from'   => $this->from,
            'to'     => $this->to,
            'userId' => $this->userId,
        ]);
    }

    public function render()
    {
        return view('livewire.dashboard-filter', [
            'salesList' => User::role('sales')->pluck('name', 'id'),
        ]);
    }
}
