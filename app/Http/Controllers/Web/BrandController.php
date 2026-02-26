<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index()
    {
        $query = Brand::query();

        $brands = $query->paginate(10)->withQueryString();

        return view('brands.index', [
            'brands' => $brands
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:brands'
        ]);

        $brand = new Brand();
        $brand->name = $request->input('name');
        $brand->save();

        return to_route('admin.brands');
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:brands'
        ]);

        $brand = Brand::findOrFail($request->input('id'));

        $brand->name = $request->input('name');
        $brand->save();

        return to_route('admin.brands');
    }

    public function destroy(Request $request)
    {
        $brand = Brand::findOrFail($request->input('id'));

        $brand->delete();

        return to_route('admin.brands');
    }
}
