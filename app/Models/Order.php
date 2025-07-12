<?php

namespace App\Models;

use App\Traits\LogsModelActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use LogsModelActivity, SoftDeletes;

    protected $fillable = [
        'user_id',
        'customer_id',
        'order_number',
        'order_date',
        'total_amount',
        'notes',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    protected $casts = [
        'order_date' => 'date',
    ];

    public static function generateOrderNumber(): string
    {
        $prefix = 'ORD-' . now()->format('Ymd');

        return \DB::transaction(function () use ($prefix) {
            // Lock the table to prevent concurrent inserts
            $lastOrder = Order::where('order_number', 'LIKE', $prefix . '%')
                ->lockForUpdate()
                ->orderBy('order_number', 'desc')
                ->first();

            $lastNumber = 0;

            if ($lastOrder) {
                $lastParts  = explode('-', $lastOrder->order_number);
                $lastNumber = (int) end($lastParts);
            }

            $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);

            return $prefix . '-' . $nextNumber;
        });
    }

}
