<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request) {
        $query = Product::with(['brand', 'category', 'ingredients']);

        if ($request->has('q')) {
            $query->where('name', 'like', "%{$request->get('q')}%");
        }

        $products = $query->paginate(2)->withQueryString();

        return view('products.index', [
            'products' => $products,
            'query' => $request->get('q')
        ]);
    }

    public function create() {
        return view('products.create', [
            'categories' => Category::all(),
            'brands' => Brand::all(),
            'ingredients' => Ingredient::all()
        ]);
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required|min:2',
            'barcode' => 'required|size:13|unique:products,barcode',
            'rnpa' => 'nullable|size:8',
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

        $product->ingredients()->attach($request->get('ingredients'));

        return to_route('admin.products');
    }

    public function edit(int $id) {
        return view('products.edit', [
            'product' => Product::findOrFail($id),
            'categories' => Category::all(),
            'brands' => Brand::all(),
            'ingredients' => Ingredient::all()
        ]);
    }

    public function update(Request $request, int $id) {
        $request->validate([
            'name' => 'required|min:2',
            'barcode' => 'required|size:13',
            'rnpa' => 'nullable|size:8',
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

        $product->ingredients()->sync($request->input('ingredients', []));

        return to_route('admin.products');
    }

    public function destroy(Request $request) {
        $product = Product::findOrFail($request->input('id'));

        $product->ingredients()->detach();
        $product->delete();

        return to_route('admin.products');
    }
}
