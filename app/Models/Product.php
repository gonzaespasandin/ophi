<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'name_normalized',
        'barcode',
        'rnpa',
        'brand_id',
        'origin',
        'category_id',
        'slug',
        'embedding',
        'active',
        'source_supermarket',
        'source_product_id',
        'product_type',
        'description',
        'category_paths',
        'nutrition',
        'labels',
        'source_supermarkets',
        'img',
        'img_alt',
    ];

    protected $casts = [
        'category_paths' => 'array',
        'nutrition' => 'array',
        'labels' => 'array',
        'source_supermarkets' => 'array',
        'embedding' => 'array',
        'active' => 'boolean',
    ];

    public function getIngredientIds(): array {
        return $this->ingredients->pluck('id')->all();
    }

    public function ingredients() {
        return $this->belongsToMany(
            Ingredient::class,
            'ingredient_product',
            'product_id',
            'ingredient_id'
        )->withPivot('is_trace');
    }

    public function category() : \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_product');
    }

    public function brand(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

}
