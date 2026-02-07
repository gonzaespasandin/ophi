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
        return response()->json(Ingredient::with('ingredients')->where('id', 1)->first()->ingredients);
    }

    public function allergies() {
        return response()->json(Ingredient::with('ingredients.ingredients')->where('id', 2)->first()->ingredients);
    }

    public function special_diets() {
        return response()->json(Ingredient::with('ingredients')->where('id', 3)->first()->ingredients);
    }
}
