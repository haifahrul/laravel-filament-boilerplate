<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ppdb extends Model
{
    protected $fillable = [
        'full_name',
        'place_of_birth',
        'date_of_birth',
        'address',
        'city',
        'phone_number',
        'email',
        'origin_school',
        'current_class',
        'school_year',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];
}