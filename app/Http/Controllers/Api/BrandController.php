<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function getBrands(Request $request) 
    {
        $query = Brand::select('id', 'name')->orderBy('name')->limit(80);

        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->query('q') . '%');
        }

        $brands = $query->get();
        return response()->json($brands);
    }

    public function getBrandsByName(string $name) 
    {
        $brands = Brand::select('id', 'name')
            ->where('name', 'like', "%$name%")
            ->orderBy('name')
            ->limit(80)
            ->get();

        return response()->json($brands);
    }
}
