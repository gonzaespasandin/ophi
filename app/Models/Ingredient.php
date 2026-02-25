<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Ingredient extends Model
{
    public function getChildrenIds() {
        return $this->ingredients()->pluck('id')->toArray();
    }

    public function getParentIds() {
        return DB::table('ingredient_has_ingredients')
            ->where('owner_id', $this->id)
            ->pluck('belongs_to_id')
            ->toArray();
    }

    public function ingredients() {
        return $this->belongsToMany(Ingredient::class, 'ingredient_has_ingredients', 'belongs_to_id', 'owner_id');
    }

    public function parents() {
        return $this->belongsToMany(Ingredient::class, 'ingredient_has_ingredients', 'owner_id', 'belongs_to_id');
    }

    public function getAllSubgroups() {
        dd($this->ingredients);
    }

    public static function getRegisterFormIngredientIds() {
        $bigIngredients = ["Intolerancias", "Alergias", "Dietas especiales"];
        $subGroups = [
            "Intolerancias" => ["Histamina", "Lactosa", "Fructosa", "Gluten", "Sorbitol", "Bajo en FODMAP", "Salicilato", "Tiramina", "Manitol", "Xilitol", "Oligosacáridos", "Fructanos", "Sacarosa", "Glucosa", "Anhídrido sulfuroso y sulfitos"],
            "Alergias" => ["Frutas", "Verduras", "Cereales", "Nueces y semillas", "Productos de origen animal", "Caseína", "Carne", "Pescado", "Especias", "Aditivos"],
            "Dietas especiales" => ["Pescetariana", "Vegetariano", "Vegano", "Sin azúcar", "Sin alcohol", "Antiinflamatorio", "Bajo en purinas"]
        ];
        $result = [];

        foreach ($bigIngredients as $bigIngredient) {
            $obtainedIngredient = self::getOrCreateByName($bigIngredient);
            $result[$bigIngredient] = $obtainedIngredient->id;

            foreach ($subGroups[$bigIngredient] as $subGroup) {
                $subGroupObtainedIngredient = self::getOrCreateByName($subGroup, [$obtainedIngredient->id]);
                $result[$subGroup] = $subGroupObtainedIngredient->id;
            }
        }

        return $result;
    }

    public static function getOrCreateByName(string $name, array $parents = []): Ingredient {
        $ingredient = Ingredient::where('name', $name)->where('is_group', 1)->first();

        if (!$ingredient) {
            $ingredient = new Ingredient();
            $ingredient['name'] = $name;
            $ingredient['is_group'] = 1;
            $ingredient->save();
        }

        $ingredient->parents()->syncWithoutDetaching($parents);

        return $ingredient;
    }
}
