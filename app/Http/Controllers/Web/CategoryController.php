<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    public function index() {
        $categories = Category::all();

        return view('categories.index', [
            'categories' => $categories
        ]);
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required|unique:categories'
        ]);

        $category = new Category();
        $category->name = $request->input('name');
        $category->save();

        return to_route('admin.categories');
    }

    public function update(Request $request) {
        $request->validate([
            'name' => 'required|unique:categories'
        ]);

        Log::info('[App\Http\Controllers\Web\CategoryController::class update()]');
        Log::info('ID: '. $request->input('id'));
        Log::info('Name: '. $request->input('name'));

        $category = Category::findOrFail($request->input('id'));

        $category->name = $request->input('name');
        $category->save();

        return to_route('admin.categories');
    }

    public function destroy(Request $request) {
        $category = Category::findOrFail($request->input('id'));

        $category->delete();

        return to_route('admin.categories');
    }
}
