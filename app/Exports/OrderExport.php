<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrderExport implements FromCollection, WithHeadings
{
    public function __construct(public $from = null, public $to = null) {}

    public function collection(): Collection
    {
        return Order::with('customer', 'user')
            ->when($this->from, fn($q) => $q->whereDate('order_date', '>=', $this->from))
            ->when($this->to, fn($q) => $q->whereDate('order_date', '<=', $this->to))
            ->get()
            ->map(function ($order) {
                return [
                    'Order No' => $order->order_number,
                    'Tanggal' => $order->order_date,
                    'Customer' => $order->customer->name ?? '-',
                    'Sales' => $order->user->name ?? '-',
                    'Total' => $order->total_amount,
                    'Catatan' => $order->notes,
                ];
            });
    }

    public function headings(): array
    {
        return ['No Order', 'Tanggal', 'Customer', 'Sales', 'Total', 'Catatan'];
    }
}