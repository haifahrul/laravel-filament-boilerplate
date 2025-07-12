<?php

namespace App\Models;

use App\Traits\LogsModelActivity;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use LogsModelActivity;
    // protected array $logAttributes = ['name', 'price']; // jika ingin log beberapa field saja

    protected $fillable = ['name', 'sku', 'description', 'price', 'image_path'];

    public function getRouteKeyName()
    {
        return 'sku';
    }
}
