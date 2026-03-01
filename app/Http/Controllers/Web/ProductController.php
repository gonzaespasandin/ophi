<?php

namespace App\Http\Controllers\Web;

use App\Classifier\IngredientClassifier;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['brand', 'category', 'ingredients']);

        if ($request->has('q')) {
            $query->where('name', 'like', "%{$request->get('q')}%");
        }

        $products = $query->paginate(10)->withQueryString();

        return view('products.index', [
            'products' => $products,
            'query' => $request->get('q')
        ]);
    }

    public function create()
    {
        return view('products.create', [
            'categories' => Category::all(),
            'brands' => Brand::all(),
            'ingredients' => Ingredient::all()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|min:2',
            'barcode' => 'nullable|unique:products,barcode',
            'rnpa' => 'nullable|unique:products,rnpa',
            'brand' => 'required',
            'origin' => 'required',
            'category' => 'required',
            'ingredients' => 'required',
        ]);

        $data = $request->only(['name', 'barcode', 'rnpa', 'origin']);
        $data['brand_id'] = $request->get('brand');
        $data['category_id'] = $request->get('category');

        $product = new Product($data);
        $product->save();

        $ids = $this->getIngredientIdsFromNames($request->get('ingredients'));
        $product->ingredients()->attach($ids['total']);

        Session::flash('feedback.message', 'Producto agregado correctamente');
        Session::flash('feedback.type', 'success');
        return to_route('admin.products');
    }

    public function edit(int $id)
    {
        return view('products.edit', [
            'product' => Product::findOrFail($id),
            'categories' => Category::all(),
            'brands' => Brand::all(),
            'ingredients' => Ingredient::all()
        ]);
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'name' => 'required|min:2',
            'barcode' => 'nullable|unique:products,barcode,' . $id,
            'rnpa' => 'nullable|unique:products,rnpa,' . $id,
            'brand' => 'required',
            'origin' => 'required',
            'category' => 'required',
            'ingredients' => 'required',
        ]);

        $data = $request->only(['name', 'barcode', 'rnpa', 'origin']);
        $data['brand_id'] = $request->get('brand');
        $data['category_id'] = $request->get('category');

        $product = Product::findOrFail($id);
        $product->update($data);

        $ids = $this->getIngredientIdsFromNames($request->get('ingredients'));
        $product->ingredients()->sync($ids['total']);

        Session::flash('feedback.message', 'Producto actualizado correctamente');
        Session::flash('feedback.type', 'success');
        return to_route('admin.products');
    }

    public function destroy(Request $request)
    {
        $product = Product::findOrFail($request->input('id'));

        $product->ingredients()->detach();
        $product->delete();

        Session::flash('feedback.message', 'Producto eliminado correctamente');
        Session::flash('feedback.type', 'success');
        return to_route('admin.products');
    }

    /**
     * @param string $names A string with the names separated by coma's. EJ: "Coco, melon, apple"
     * @return array Array with the respectives IDs
     */
    public function getIngredientIdsFromNames(string $names) {
        $result = [];
        $newIngredients = [];
        $names = explode(',', $names);
        Log::info('Names: ', [$names]);

        foreach ($names as $name) {
            $name = Ingredient::normalize($name);
            $ingredient = Ingredient::where('name', $name)->first();

            if ($ingredient && !in_array($ingredient->id, $result)) {
                $result[] = $ingredient->id;
            } else {
                $ingredient = new Ingredient();
                $ingredient['name'] = $name;
                $ingredient->save();

                IngredientClassifier::runOne($ingredient);

                $result[] = $ingredient->id;
                $newIngredients[] = $ingredient->id;
            }
        }

        return [
            'total' => $result,
            'new' => $newIngredients
        ];
    }
}
