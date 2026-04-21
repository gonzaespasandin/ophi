<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;  

class Product extends Model
{
    protected $fillable = ['name', 'name_normalized', 'barcode', 'rnpa', 'brand_id', 'origin', 'category_id'];

    public function getIngredientIds(): array {
        $ids = [];

        Log::info('------------------------------------------------------------------------------------------');
        Log::info('getIngredientIds()');
        Log::info('Ingredients', ['ids' => $this->ingredients]);
        Log::info('Ids?', ['ids' => $this->ingredients->pluck('id')->all()]);

        return $this->ingredients->pluck('id')->all();
    }

    public function ingredients() {
        return $this->belongsToMany(
            Ingredient::class,
            'ingredient_product',
            'product_id',
            'ingredient_id'
        );
    }

    public function category() : \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

}
