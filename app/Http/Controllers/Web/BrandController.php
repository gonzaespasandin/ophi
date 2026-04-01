<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

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

        Session::flash('feedback.message', 'Marca agregada correctamente');
        Session::flash('feedback.type', 'success');
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

        Session::flash('feedback.message', 'Marca actualizada correctamente');
        Session::flash('feedback.type', 'success');
        return to_route('admin.brands');
    }

    public function destroy(Request $request)
    {
        $brand = Brand::findOrFail($request->input('id'));

        try {
            $brand->delete();
        } catch (\Throwable $th) {
            if ($th->getCode() == 23000) {
                Session::flash('feedback.message', 'No se puede eliminar esta marca porque está siendo utlizada');
                Session::flash('feedback.type', 'danger');
                return to_route('admin.brands');
            }

            Session::flash('feedback.message', '¡Ups! Ocurrió un error desconocido, dicsulpá las molestias que esto pueda ocasionar');
            Session::flash('feedback.type', 'danger');
            return to_route('admin.brands');
        }

        Session::flash('feedback.message', 'Marca eliminada correctamente');
        Session::flash('feedback.type', 'success');
        return to_route('admin.brands');
    }
}
