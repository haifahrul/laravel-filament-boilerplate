<?php

namespace App\Exports;

use App\Models\OrderItem;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrderItemExport implements FromCollection, WithHeadings
{
    public function __construct(public $from = null, public $to = null) {}

    public function collection(): Collection
    {
        return OrderItem::with(['order.customer', 'order.user', 'product'])
            ->whereHas('order', function ($q) {
                if ($this->from) $q->whereDate('order_date', '>=', $this->from);
                if ($this->to) $q->whereDate('order_date', '<=', $this->to);
            })
            ->get()
            ->map(function ($item) {
                return [
                    'Order No'   => $item->order->order_number ?? '-',
                    'Tanggal'    => $item->order->order_date,
                    'Customer'   => $item->order->customer->name ?? '-',
                    'Sales'      => $item->order->user->name ?? '-',
                    'Produk'     => $item->product->name ?? '-',
                    'SKU'        => $item->product->sku ?? '-',
                    'Qty'        => $item->quantity,
                    'Harga'      => $item->price,
                    'Subtotal'   => $item->subtotal,
                ];
            });
    }

    public function headings(): array
    {
        return ['No Order', 'Tanggal', 'Customer', 'Sales', 'Produk', 'SKU', 'Qty', 'Harga', 'Subtotal'];
    }
}