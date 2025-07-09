<?php

namespace App\Exports;

use App\Models\Visit;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VisitExport implements FromCollection, WithHeadings
{
    public function __construct(public $from = null, public $to = null) {}

    public function collection(): Collection
    {
        return Visit::with(['user', 'customer'])
            ->when($this->from, fn($q) => $q->whereDate('created_at', '>=', $this->from))
            ->when($this->to, fn($q) => $q->whereDate('created_at', '<=', $this->to))
            ->get()
            ->map(fn ($visit) => [
                'Sales'     => $visit->user->name ?? '-',
                'Customer'  => $visit->customer->name ?? '-',
                'Aktivitas' => ucfirst($visit->activity_type),
                'Check-in'  => $visit->checked_in_at,
                'Check-out' => $visit->checked_out_at,
                'Catatan'   => $visit->note,
            ]);
    }

    public function headings(): array
    {
        return ['Sales', 'Customer', 'Aktivitas', 'Check-in', 'Check-out', 'Catatan'];
    }
}