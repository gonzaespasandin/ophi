<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProductService
{
    static public function getSafeProducts(int $brand_id = 0, int $category_id = 0, int $limit = 7, int $avoidProduct = 0): array | \Illuminate\Database\Eloquent\Collection
    {
        if(!Auth::check()) {
            return [];
        }

        $avoidIngredients = UserService::getAvoidedIngredients(returnOnlyIds: true);

        $query = Product::with(['ingredients', 'brand', 'category'])
            ->select('id', 'name', 'brand_id', 'category_id')
            ->where('id', '!=', $avoidProduct);

        if ($brand_id !== 0) {
            $query->where('brand_id', $brand_id);
        }

        if ($category_id !== 0) {
            $query->where('category_id', $category_id);
        }

        $query->whereDoesntHave('ingredients', function($q) use ($avoidIngredients) {
            $q->whereIn('id', $avoidIngredients);
        });

        return $query->limit($limit)->get();
    }
}
