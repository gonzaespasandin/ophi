<?php

namespace App\Services;

use App\Models\Ingredient;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserService {
    static public function getAvoidedIngredients($returnOnlyIds = false): array | \Illuminate\Support\Collection
    {
        if(!Auth::check()) {
            return [];
        }

        $ingredientIds = auth()->user()->profiles()
            ->with('ingredients')
            ->get()
            ->pluck('ingredients')
            ->flatten()
            ->pluck('id')
            ->unique()
            ->values()
            ->toArray();

        $parents = IngredientService::getParentIngredients($ingredientIds);
        $children = IngredientService::getChildrenIngredients($ingredientIds);

        $direct = Ingredient::whereIn('id', $ingredientIds)->get();

        $result = $parents->merge($children)->merge($direct)
            ->unique('id')
            ->values();

        if($returnOnlyIds) {
            return $result->pluck('id')->toArray();
        }

        return $result;
    }
}
