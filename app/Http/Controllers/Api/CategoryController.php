<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function getCategories() {
        $categories = Category::select('id', 'name')->get();
        return response()->json($categories);
    }
}
