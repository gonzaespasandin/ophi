<?php

namespace App\Http\Controllers;


use App\Models\Profile;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function all()
    {
        return response()->json(Product::all());
    }

    public function find(int $id)
    {
        $product = Product::with(['ingredients'])->find($id);

        if (!$product) {
            return response()->isNotFound();
        }

        return response()->json($product);
    }

    public function find_by_barcode(string $barcode) {
        $product = Product::with(['ingredients'])->where('barcode', $barcode)->limit(1)->first();

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


        // Producto con relación ingredientes donde name = variable name
        $product = Product::with(['ingredients'])->where('name', $name)->get();

        if($product->isEmpty()) {
            $name = trim($name);
            $products = Product::with(['ingredients'])->where('name', 'like', "%$name%")->get();

            if(!$products) {
                return response('Not Found', 404)->header('Content-Type', 'text/plain');
            }

            return response()->json($products);
        }




        return response()->json($product);
    }

    public function find_match_by_name(string $name) {
        $name = trim($name);
        $products = Product::with(['ingredients'])->select('id', 'name')->where('name', 'like', "$name%")->limit(4)->get();
        
        if(!$products) {
            return response('Not Found', 404)->header('Content-Type', 'text/plain');
        }

        return response()->json($products);
    }


}
