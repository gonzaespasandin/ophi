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

    public function find(int $id)
    {
        $product = Product::with(['ingredients', 'brand'])->find($id);

        if (!$product) {
            return response()->isNotFound();
        }

        return response()->json($product);
    }

    public function find_by_barcode(string $barcode) {
        $product = Product::with(['ingredients', 'brand'])->where('barcode', $barcode)->limit(1)->first();

        if (!$product) {
            return response()->isNotFound();
        }

        return response()->json($product);
    }

    public function analyze_compatibility(int $id, int $user_id)
    {
        /* TODO: Authenticate
         * Eventually this method should only be called by
         * authenticated users (previously verified by the
         * respective middleware). For now, I'll keep it simple
         * */

        $profiles = Profile::with(['ingredients'])->where('user_id', $user_id)->get();
        $product = Product::with(['ingredients'])->find($id);

        if (!$product) {
            return response()->isNotFound();
        }

        // TODO: Improve & optimize this code
        foreach ($profiles as $profile) {
            $profile->result = 'success';

            foreach ($profile->ingredients as $ingredient) {

                foreach ($product->ingredients as $ingredient_prod) {

                    if ($ingredient_prod->id == $ingredient->id) {
                        $profile->result = 'danger';
                        break;
                    }
                }
            }
        }

        return response()->json($profiles);
    }

    public function find_by_name(string $name) {
        //---------- Chequeo de premium
        $user = User::with('subscription')
        ->find(Auth::id());
        if(!$user->isPremium()) {
            return view('subscription.index', [
                'message' => 'Desbloqueá el premium para buscar productos!'
            ]);
        }
        //----------

        $name = trim($name);
        // Producto con relación ingredientes donde name = variable name
        $products = Product::with(['ingredients', 'brand'])->where('name', 'like', "$name%")->get();

        if(!$products) {
            return response('Not Found', 404)->header('Content-Type', 'text/plain');
        }

        return response()->json($products);
    }

    public function find_by_name_and_brand(string $name, string $brand) {
        //---------- Chequeo de premium
        $user = User::with('subscription')
        ->find(Auth::id());
        if(!$user->isPremium()) {
            return view('subscription.index', [
                'message' => 'Desbloqueá el premium para buscar productos!'
            ]);
        }
        //----------

        $brand = trim($brand);
        $name = trim($name);

        $products = Product::with(['ingredients', 'brand'])
            ->where('name', $name)
            ->whereHas('brand', function($query) use ($brand) {
                $query->where('name', $brand);
            })
            ->get();

        if ($products->isEmpty()) {
            return response()->json([
                'message' => 'Product not found'
            ], 404);
        }
        
        $safeProducts = ProductService::getSafeProducts(
            category_id: $products[0]->category_id,
            avoidProduct: $products[0]->id
        );
        $products['safeProducts'] = $safeProducts;

        return response()->json($products);
    }

    public function find_match_by_name(string $name) {
        //---------- Chequeo de premium
        $user = User::with('subscription')
        ->find(Auth::id());
        if(!$user->isPremium()) {
            return view('subscription.index', [
                'message' => 'Desbloqueá el premium para buscar productos!'
            ]);
        }
        //----------

        $name = trim($name);
        $products = Product::with(['brand', 'ingredients'])->select('id', 'name', 'barcode', 'brand_id')->where('name', 'like', "$name%")->limit(4)->get();

        if(!$products) {
            return response('Not Found', 404)->header('Content-Type', 'text/plain');
        }

        return response()->json($products);
    }

    public function getRecomendedProducts(Request $request) {
        $maxId = Product::max('id');
        $randomId = rand(1, $maxId);

        $ingredients = collect($request->userI)
            ->pluck('ingredient')
            ->all();
        $products = Product::with(['ingredients', 'brand'])->select('id', 'name', 'brand_id')->where('id', '>=', $randomId)->whereDoesntHave('ingredients', function (Builder $query) use ($ingredients) {
            $query->whereIn('name', $ingredients);
        })->limit(10)->get();
        return response()->json($products);
    }
}
