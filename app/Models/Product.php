<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'sku', 'description', 'price', 'image_path'];

    public function getRouteKeyName()
    {
        return 'sku';
    }
}
