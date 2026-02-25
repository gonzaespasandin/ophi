<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use Illuminate\Support\Facades\Log;

class IngredientController extends Controller
{
    public function all() {
        return response()->json(Ingredient::all());
    }

    public function intolerances() {
        $id = Ingredient::getRegisterFormIngredientIds()['Intolerancias'];
        return response()->json(Ingredient::with('ingredients')->where('id', $id)->first()->ingredients);
    }

    public function allergies() {
        $id = Ingredient::getRegisterFormIngredientIds()['Alergias'];
        return response()->json(Ingredient::with('ingredients.ingredients')->where('id', $id)->first()->ingredients);
    }

    public function special_diets() {
        $id = Ingredient::getRegisterFormIngredientIds()['Dietas especiales'];
        return response()->json(Ingredient::with('ingredients')->where('id', $id)->first()->ingredients);
    }
}
