<?php

namespace App\Services;

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

        $result = $parents->merge($children)
            ->unique('id')
            ->values();

        if($returnOnlyIds) {
            return $result->pluck('id')->toArray();
        }

        return $result;
    }
}
