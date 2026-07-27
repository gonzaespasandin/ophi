<?php

namespace App\Http\Controllers\Web;

use App\Classifier\IngredientClassifier;
use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class IngredientController extends Controller
{
    public function index(Request $request) {
        $query = Ingredient::with('ingredients');

        if ($request->has('q')) {
            $query->where('name', 'LIKE', "%{$request->get('q')}%")
                ->orWhere('aliases', 'LIKE', "%{$request->get('q')}%");
        }

        $ingredients = $query->paginate(10)->withQueryString();

        return view('ingredients.index', [
            'ingredients' => $ingredients,
            'query' => $request->get('q')
        ]);
    }

    public function create() {
        return view('ingredients.create', [
            'ingredients' => Ingredient::all()
        ]);
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'aliases' => 'nullable|string|max:255',
        ]);

        try {
            $ingredient = new Ingredient();
            $ingredient['name'] = $request->get('name');
            $ingredient['aliases'] = $request->get('aliases') ?? '';
            if (!$request->isNotFilled('ingredient-children')) {
                $ingredient['is_group'] = true;
            }
            $ingredient->save();

            if (!$request->isNotFilled('ingredient-children')) {
                $ingredient->ingredients()->attach($request->get('ingredient-children'));
            }

            if (!$request->isNotFilled('ingredient-parent')) {
                $ingredient->parents()->attach($request->get('ingredient-parent'));
            }

            IngredientClassifier::runOne($ingredient);

//            if (!$request->isNotFilled('ingredient-parent')) {
//                foreach ($request->get('ingredient-parent') as $idParent) {
//                    $parent = Ingredient::findOrFail($idParent);
//                    $parent->ingredients()->attach($ingredient['id']);
//                }
//            }
        } catch (\Exception $e) {
            Session::flash('feedback.message', '¡Ups! Ocurrió un error desconocido');
            Session::flash('feedback.type', 'danger');
            return to_route('admin.ingredients');
        }

        Session::flash('feedback.message', 'Ingrediente creado correctamente');
        Session::flash('feedback.type', 'success');
        return to_route('admin.ingredients');
    }

    public function edit(int $id) {
        $ingredient = Ingredient::findOrFail($id);

        return view('ingredients.edit', [
            'ingredients' => Ingredient::where('id', '!=', $id)->get(),
            'ingredient' => $ingredient,
            'childrenIds' => $ingredient->getChildrenIds(),
            'parentIds' => $ingredient->getParentIds(),
        ]);
    }

    public function update(Request $request, int $id) {
        $request->validate([
            'name' => 'required|string|max:255',
            'aliases' => 'nullable|string|max:255',
        ]);

        try {
            $ingredient = Ingredient::findOrFail($id);
            $ingredient['name'] = $request->get('name');
            $ingredient['aliases'] = $request->get('aliases') ?? '';
            $ingredient->save();

            // Children
            $ingredient->ingredients()->sync($request->input('ingredient-children', []));

            // Parents
            $ingredient->parents()->sync($request->input('ingredient-parent', []));
//            $formParentIds = $request->get('ingredient-parent', []);
//            $ingredientParentIds = $ingredient->getParentIds();
//            $detachIngredientFrom = array_filter($ingredientParentIds, fn($i) => !in_array($i, $formParentIds));
//
//            Log::info('------------------------------------------------------------------------------------------');
//            Log::info('Updating an ingredient');
//            Log::info('Form parent IDs: ', ['array' => $formParentIds]);
//            Log::info('Ingredient parent IDs: ', ['array' => $ingredientParentIds]);
//            Log::info('I have to detach ingredient\'s ID from: ', ['array' => $detachIngredientFrom]);
//
//            // Attaching
//            if (count($formParentIds) > 0) {
//                $data = array_map(fn ($parentId) => [
//                    'belongs_to_id' => $parentId,
//                    'owner_id' => $ingredient['id']
//                ], $formParentIds);
//
//                DB::table('ingredient_has_ingredients')->insert($data);
//            }
//
//            // Detaching from parents
//            if (count($detachIngredientFrom) > 0) {
//                DB::table('ingredient_has_ingredients')
//                    ->whereIn('belongs_to_id', $detachIngredientFrom)
//                    ->where('owner_id', '=', $ingredient['id'])
//                    ->delete();
//            }
        } catch (\Exception $e) {
            Session::flash('feedback.message', '¡Ups! Ocurrió un error desconocido');
            Session::flash('feedback.type', 'danger');
            return to_route('admin.ingredients');
        }

        Session::flash('feedback.message', 'Ingrediente actualizado correctamente');
        Session::flash('feedback.type', 'success');
        return to_route('admin.ingredients');
    }

    public function destroy(Request $request) {
        $ingredient = Ingredient::findOrFail($request->get('id'));

        try {
            DB::transaction(function () use ($ingredient) {
                if ($ingredient->products()->count() > 0) {
                    throw new \Exception(
                        'No se puede eliminar este ingrediente porque tiene productos asociados',
                        23000
                    );
                }
                $ingredient->ingredients()->detach();

                DB::table('ingredient_ingredient')
                    ->where('child_id', $ingredient->id)
                    ->delete();

                $ingredient->delete();
            });
        } catch (\Exception $e) {
            if ($e->getCode() == 23000) {
                Session::flash('feedback.message', 'No se puede eliminar este ingrediente porque está siendo usado por 1 o más productos');
            } else {
                Session::flash('feedback.message', '¡Ups! Ocurrió un error desconocido');
            }

            Session::flash('feedback.type', 'danger');
            return to_route('admin.ingredients');
        }

        Session::flash('feedback.message', 'Ingrediente eliminado correctamente');
        Session::flash('feedback.type', 'success');
        return to_route('admin.ingredients');
    }

    public function storeAjax(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'aliases' => 'nullable|string|max:255',
        ]);

        $ingredient = new Ingredient();
        $ingredient['name'] = $request->get('name');
        $ingredient['aliases'] = $request->get('aliases') ?? '';
        $ingredient->save();

        return response()->json([
            'id' => $ingredient->id,
            'name' => $ingredient->name,
        ]);
    }
}
