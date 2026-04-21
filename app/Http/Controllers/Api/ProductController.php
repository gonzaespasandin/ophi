<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Profile;
use App\Models\User;
use App\Services\ProductService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function all()
    {
        return response()->json(Product::all());
    }

    /**
     * Search products by queries
     */
    public function search(Request $request) 
    {
        $products = ProductService::search($request);
        return response()->json($products);
    }

    /**
     * Look for a specific product
     */
    public function find_by_name_and_brand(string $name, string $brand) {
        // //---------- Chequeo de premium
        // $user = User::with('subscription')
        // ->find(Auth::id());
        // if(!$user->isPremium()) {
        //     return response()->json([
        //         'message' => 'Usuario no premium'
        //     ], 403);
        // }
        // //----------

        $brand = trim($brand);
        $name = trim($name);

        $product = ProductService::findByNameAndBrand($name, $brand);


        if($product->isEmpty()) {
            return response()->json([
                'message' => 'Product not found'
            ], 404);
        }
        return response()->json($product);
    }

    public function find_match_by_name(string $name) {
        // //---------- Chequeo de premium
        // $user = User::with('subscription')
        // ->find(Auth::id());
        // if(!$user->isPremium()) {
        //     return response()->json([
        //         'message' => 'Usuario no premium'
        //     ], 403);
        // }
        // //----------

        $name = trim($name);
        
        // IMPORTANT: $name can be a name or a brand.
       $products = ProductService::findMatchByName($name);

        return response()->json($products);
    }

    public function getSafeProducts() {
        $result = ProductService::getSafeProducts();

        return response()->json($result);
    }

    public function getOrigins() {
        $result = ProductService::getOrigins();

        return response()->json($result);
    }
}
