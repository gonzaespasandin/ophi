<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function ingredients() {
        return $this->belongsToMany(
            Ingredient::class,
            'ingredient_product',
            'product_id',
            'ingredient_id'
        );
    }
}
