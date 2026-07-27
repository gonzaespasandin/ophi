<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class ProductService
{
    static public function getSafeProducts(int $brand_id = 0, int $category_id = 0, int $limit = 7, int $avoidProduct = 0): array | \Illuminate\Database\Eloquent\Collection
    {
        if(!Auth::check()) {
            return [];
        }

        $avoidIngredients = UserService::getAvoidedIngredients(returnOnlyIds: true);

        $query = Product::with(['ingredients.parents', 'brand', 'category', 'categories'])
            ->select('id', 'name', 'brand_id', 'category_id')
            ->where('id', '!=', $avoidProduct);

        if ($brand_id !== 0) {
            $query->where('brand_id', $brand_id);
        }

        if ($category_id !== 0) {
            $query->where(function ($categoryQuery) use ($category_id) {
                $categoryQuery->where('category_id', $category_id)
                    ->orWhereHas('categories', function ($pivotQuery) use ($category_id) {
                        $pivotQuery->where('categories.id', $category_id);
                    });
            });
        }

        $query->whereDoesntHave('ingredients', function($q) use ($avoidIngredients) {
            $q->whereIn('id', $avoidIngredients);
        });

        return $query->limit($limit)->get();
    }

    /**
     * Search products by queries
     */
    static public function search(Request $request) 
    {   
        $queries = $request->query();
        unset($queries['page']);

        $query = Product::with(['ingredients.parents', 'brand', 'category', 'categories']);

        if ($request->query('q')) {
            $name = trim($request->query('q'));

            $query->where(function ($q) use ($name) {
                $q->where('name', 'like', "%{$name}%")
                ->orWhereHas('brand', function ($subQuery) use ($name) {
                    $subQuery->where('name', 'like', "%{$name}%");
                });
            });
        }

        if($request->query('brands')) {
            $brands = explode(',', $queries['brands']);
            $query->whereIn('brand_id', $brands);
        }

        $products = $query->paginate(7);

        return $products;
    }

    /**
     * Look for a specific product
     */
    static public function findByNameAndBrand(string $name, string $brand) 
    {
        $product = Product::with(['ingredients.parents', 'brand'])
            ->where('name', $name)
            ->whereHas('brand', function($query) use ($brand) {
                $query->where('name', $brand);
            })
        ->get();

        $safeProducts = ProductService::getSafeProducts(
            category_id: $product[0]->category_id,
            avoidProduct: $product[0]->id
        );
        $product['safeProducts'] = $safeProducts;

        return $product;
    }

    static public function findMatchByName(string $name) {
        $products = Product::with(['brand', 'ingredients.parents'])->select('id', 'name', 'barcode', 'brand_id')->where('name', 'like', "%$name%")->orwhereHas('brand', function($query) use ($name) {
                $query->where('name', 'like', "%$name%");
            })->limit(4)->get();

        return $products;
    }

    static public function getOrigins(?string $query = null) {
        $origins = Product::query()
            ->select('origin as name')
            ->whereNotNull('origin')
            ->where('origin', '!=', '');

        if ($query !== null && trim($query) !== '') {
            $origins->where('origin', 'like', '%' . trim($query) . '%');
        }

        return $origins
            ->distinct()
            ->orderBy('origin')
            ->limit(80)
            ->get();
    }
}
