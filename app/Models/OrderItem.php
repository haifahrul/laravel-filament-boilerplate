<?php

namespace App\Models;

use App\Traits\LogsModelActivity;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use LogsModelActivity;
    protected $fillable = ['order_id', 'product_id', 'quantity', 'price', 'subtotal'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
