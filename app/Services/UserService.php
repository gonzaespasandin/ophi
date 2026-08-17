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

    /**
     * Ingredient ids each profile avoids, keyed by profile id.
     *
     * Unlike getAvoidedIngredients(), which flattens the whole household into a
     * single set, this keeps the profiles apart so a product can be judged
     * profile by profile. A profile with no restrictions maps to an empty set:
     * there is nothing it needs to avoid.
     *
     * @return \Illuminate\Support\Collection<int, array<int, int>>
     */
    static public function getAvoidedIngredientIdsByProfile(): \Illuminate\Support\Collection
    {
        if(!Auth::check()) {
            return collect();
        }

        return auth()->user()->profiles()
            ->with('ingredients')
            ->get()
            ->mapWithKeys(fn ($profile) => [
                $profile->id => self::expandIngredientHierarchy($profile->ingredients->pluck('id')->all()),
            ]);
    }

    /**
     * Adds the parents and children of the given ingredients: avoiding "Lactosa"
     * also means avoiding "leche entera" underneath it.
     *
     * @param  array<int, int>  $ingredientIds
     * @return array<int, int>
     */
    static private function expandIngredientHierarchy(array $ingredientIds): array
    {
        if(count($ingredientIds) === 0) {
            return [];
        }

        $parents = IngredientService::getParentIngredients($ingredientIds);
        $children = IngredientService::getChildrenIngredients($ingredientIds);

        return $parents->merge($children)
            ->pluck('id')
            ->merge($ingredientIds)
            ->unique()
            ->values()
            ->all();
    }
}
