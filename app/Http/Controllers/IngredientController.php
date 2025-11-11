<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use Illuminate\Http\Request;

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
