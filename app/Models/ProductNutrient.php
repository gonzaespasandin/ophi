<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductNutrient extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'nutrient_id',
        'value',
        'per_unit',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'float',
        ];
    }
}
