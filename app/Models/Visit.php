<?php

namespace App\Models;

use App\Traits\LogsModelActivity;
use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    use LogsModelActivity;

    protected $fillable = [
        'user_id',
        'customer_id',
        'activity_type',
        'note',
        'photo_path',
        'checked_in_at',
        'checked_out_at',
        'latitude',
        'longitude',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
