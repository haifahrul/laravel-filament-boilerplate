<?php

namespace App\Models;

use App\Traits\LogsModelActivity;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use LogsModelActivity;
    // protected array $logAttributes = ['name', 'price']; // jika ingin log beberapa field saja

    const STATUS_ACTIVE = 1;

    protected $fillable = ['name', 'sku', 'description', 'price', 'image_path', 'status'];

    public function getRouteKeyName()
    {
        return 'sku';
    }

    // Di dalam app/Models/Product.php
    public function getPhotoUrlAttribute()
    {
        return $this->image_path
            ? asset('storage/' . $this->image_path)
            : null;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }
}
