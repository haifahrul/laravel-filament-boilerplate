<?php

namespace App\Models;

use App\Traits\LogsModelActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory, LogsModelActivity;

    protected $fillable = ['name', 'address', 'contact', 'business_type', 'latitude', 'longitude', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function visits()
    {
        return $this->hasMany(Visit::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function latestVisit()
    {
        return $this->hasOne(\App\Models\Visit::class)->latest('checked_in_at');
    }
}
