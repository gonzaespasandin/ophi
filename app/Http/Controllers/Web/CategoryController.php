<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class CategoryController extends Controller
{
    public function index()
    {
        $query = Category::query();

        $categories = $query->paginate(10)->withQueryString();

        return view('categories.index', [
            'categories' => $categories
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:categories'
        ]);

        $category = new Category();
        $category->name = $request->input('name');
        $category->save();

        Session::flash('feedback.message', 'Categoría creada correctamente');
        Session::flash('feedback.type', 'success');
        return to_route('admin.categories');
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:categories'
        ]);

        Log::info('[App\Http\Controllers\Web\CategoryController::class update()]');
        Log::info('ID: ' . $request->input('id'));
        Log::info('Name: ' . $request->input('name'));

        $category = Category::findOrFail($request->input('id'));

        $category->name = $request->input('name');
        $category->save();

        Session::flash('feedback.message', 'Categoría actualizada correctamente');
        Session::flash('feedback.type', 'success');
        return to_route('admin.categories');
    }

    public function destroy(Request $request)
    {
        $category = Category::findOrFail($request->input('id'));

        try {
            $category->delete();
        } catch (\Throwable $th) {
            if ($th->getCode() == 23000) {
                Session::flash('feedback.message', 'No se puede eliminar esta categoría porque está siendo utlizada por 1 o más productos');
                Session::flash('feedback.type', 'danger');
                return to_route('admin.categories');
            }

            Session::flash('feedback.message', '¡Ups! Ocurrió un error desconocido, dicsulpá las molestias que esto pueda ocasionar');
            Session::flash('feedback.type', 'danger');
            return to_route('admin.brands');
        }

        Session::flash('feedback.message', 'Categoría eliminada correctamente');
        Session::flash('feedback.type', 'success');
        return to_route('admin.categories');
    }
}
