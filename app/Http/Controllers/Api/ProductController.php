<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Profile;
use App\Models\User;
use App\Services\IngredientService;
use App\Services\ProductService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function all()
    {
        return response()->json(Product::with(['brand', 'category', 'categories', 'ingredients.parents'])->get());
    }

    public function find(int $id)
    {
        $product = Product::with(['brand', 'category', 'categories', 'ingredients.parents'])
            ->findOrFail($id);

        $product->setAttribute('compatibility', $this->compatibilityForAuthUser($product));

        return response()->json($product);
    }

    public function find_by_barcode(string $barcode)
    {
        $product = Product::with(['brand', 'category', 'categories', 'ingredients.parents'])
            ->where('barcode', $barcode)
            ->first();

        if (! $product) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        $product->setAttribute('compatibility', $this->compatibilityForAuthUser($product));

        return response()->json($product);
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
        $result = ProductService::getProductsSafeForAnyProfile();

        return response()->json($result);
    }

    public function getRecomendedProducts(Request $request) {
        $items = collect($request->input('userI', []));
        $names = $items
            ->pluck('ingredient')
            ->filter()
            ->map(fn ($name) => trim((string) $name))
            ->filter()
            ->unique()
            ->values();

        $ingredientIds = \App\Models\Ingredient::whereIn('name', $names)->pluck('id')->toArray();
        $avoidIds = IngredientService::getParentIngredients($ingredientIds)
            ->merge(IngredientService::getChildrenIngredients($ingredientIds))
            ->pluck('id')
            ->merge($ingredientIds)
            ->unique()
            ->values()
            ->toArray();

        $query = Product::with(['brand', 'category', 'categories', 'ingredients.parents'])
            ->where('active', true);

        if ($avoidIds !== []) {
            $query->whereDoesntHave('ingredients', function ($ingredientQuery) use ($avoidIds) {
                $ingredientQuery->whereIn('ingredients.id', $avoidIds);
            });
        }

        return response()->json(
            $query->inRandomOrder()->limit(10)->get()
        );
    }

    public function getOrigins(Request $request) {
        $result = ProductService::getOrigins($request->query('q'));

        return response()->json($result);
    }

    private function compatibilityForAuthUser(Product $product): array
    {
        if (! Auth::check()) {
            return [];
        }

        $profiles = Auth::user()
            ->profiles()
            ->with('ingredients')
            ->get();

        $productIngredientIds = $product->ingredients
            ->flatMap(function ($ingredient) {
                return collect([$ingredient->id])
                    ->merge($ingredient->parents->pluck('id'))
                    ->merge($ingredient->parent_ids ?? []);
            })
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->toArray();

        $compatibility = [];

        foreach ($profiles as $profile) {
            $profileIngredientIds = $profile->ingredients->pluck('id')->toArray();
            $avoidIds = IngredientService::getParentIngredients($profileIngredientIds)
                ->merge(IngredientService::getChildrenIngredients($profileIngredientIds))
                ->pluck('id')
                ->merge($profileIngredientIds)
                ->unique()
                ->values()
                ->toArray();

            $matchingIngredients = $product->ingredients
                ->filter(function ($ingredient) use ($avoidIds) {
                    $relatedIds = collect([$ingredient->id])
                        ->merge($ingredient->parents->pluck('id'))
                        ->merge($ingredient->parent_ids ?? [])
                        ->map(fn ($id) => (int) $id)
                        ->unique();

                    return $relatedIds->intersect($avoidIds)->isNotEmpty();
                });

            $matches = $matchingIngredients
                ->filter(fn ($ingredient) => ! (bool) ($ingredient->pivot?->is_trace ?? false))
                ->pluck('name')
                ->values()
                ->toArray();

            $traceMatches = $matchingIngredients
                ->filter(fn ($ingredient) => (bool) ($ingredient->pivot?->is_trace ?? false))
                ->pluck('name')
                ->values()
                ->toArray();

            $isSafe = $matches === [] && $traceMatches === [];

            $compatibility[$profile->id] = [
                'result' => $matches !== [] ? 'No apto' : ($traceMatches !== [] ? 'Advertencia' : 'Apto'),
                'status' => $matches !== [] ? 'unsafe' : ($traceMatches !== [] ? 'warning' : 'safe'),
                'is_safe' => $isSafe,
                'matches' => $matches,
                'trace_matches' => $traceMatches,
            ];
        }

        return $compatibility;
    }
}
