<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nutrient extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'unit',
        'category',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_nutrients')
            ->withPivot(['value', 'per_unit']);
    }
}
