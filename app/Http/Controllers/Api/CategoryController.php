<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function getCategories(Request $request) 
    {
        $query = Category::select('id', 'name')->orderBy('name');

        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->query('q') . '%');
        }

        $categories = $query->get();
        return response()->json($categories);
    }
}
