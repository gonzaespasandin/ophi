<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function getBrands() {
       $brands = Brand::select('id', 'name')->limit(2)->get();
        return response()->json($brands);
    }

    public function getBrandsByName(string $name) {
        $brands = Brand::select('id', 'name')->where('name', 'like', "%$name%")->limit(50)->get();
        return response()->json($brands);
    }
}
