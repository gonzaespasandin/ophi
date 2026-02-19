<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
            foreach ($request->get('ingredient-parent') as $idParent) {
                $parent = Ingredient::findOrFail($idParent);
                $parent->ingredients()->attach($ingredient['id']);
            }
        }

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

        $ingredient = Ingredient::findOrFail($id);
        $ingredient['name'] = $request->get('name');
        $ingredient['aliases'] = $request->get('aliases') ?? '';
        $ingredient->save();

        // Children
        $ingredient->ingredients()->sync($request->input('ingredient-children', []));

        // Parents
        $formParentIds = $request->get('ingredient-parent', []);
        $ingredientParentIds = $ingredient->getParentIds();
        $detachIngredientFrom = array_filter($ingredientParentIds, fn($i) => !in_array($i, $formParentIds));

        Log::info('------------------------------------------------------------------------------------------');
        Log::info('Updating an ingredient');
        Log::info('Form parent IDs: ', ['array' => $formParentIds]);
        Log::info('Ingredient parent IDs: ', ['array' => $ingredientParentIds]);
        Log::info('I have to detach ingredient\'s ID from: ', ['array' => $detachIngredientFrom]);

        // Attaching
        if (count($formParentIds) > 0) {
            $data = array_map(fn ($parentId) => [
                'belongs_to_id' => $parentId,
                'owner_id' => $ingredient['id']
            ], $formParentIds);

            DB::table('ingredient_has_ingredients')->insert($data);
        }

        // Detaching from parents
        if (count($detachIngredientFrom) > 0) {
            DB::table('ingredient_has_ingredients')
                ->whereIn('belongs_to_id', $detachIngredientFrom)
                ->where('owner_id', '=', $ingredient['id'])
                ->delete();
        }
        return to_route('admin.ingredients');
    }

    public function destroy(Request $request) {
        $ingredient = Ingredient::findOrFail($request->get('id'));

        $ingredient->ingredients()->detach();
        DB::table('ingredient_has_ingredients')
            ->where('owner_id', $ingredient['id'])
            ->delete();
        $ingredient->delete();

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
