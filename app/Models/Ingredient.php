<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Ingredient extends Model
{
    public const CARE_AVOID = 0;
    public const CARE_WARNING = 1;

    protected $fillable = [
        'name',
            'icon',
            'is_group',
            'aliases',
        'classified',
        'is_part_of_main_groups',
    ];

    protected function casts(): array
    {
        return [
            'is_group' => 'boolean',
            'classified' => 'boolean',
            'is_part_of_main_groups' => 'boolean',
        ];
    }

    protected $hidden = ['pivot', 'parents', 'products'];
    protected $appends = ['parent_ids'];

    public function getParentIdsAttribute(): array
    {
        if ($this->relationLoaded('parents')) {
            return $this->parents->pluck('id')->unique()->values()->toArray();
        }

        return $this->getParentIds();
    }

    public function getChildrenIds() {
        return $this->ingredients()->pluck('id')->toArray();
    }

    public function getParentIds() {
        return DB::table('ingredient_ingredient')
            ->where('child_id', $this->id)
            ->pluck('parent_id')
            ->toArray();
    }

    public function ingredients() {
        return $this->belongsToMany(Ingredient::class, 'ingredient_ingredient', 'parent_id', 'child_id');
    }

    public function children() {
        return $this->ingredients();
    }

    public function parents() {
        return $this->belongsToMany(Ingredient::class, 'ingredient_ingredient', 'child_id', 'parent_id');
    }

    public function products() {
        return $this->belongsToMany(Product::class)->withPivot('is_trace');
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
            $ingredient['is_part_of_main_groups'] = empty($parents);
            $ingredient->save();
        }

        $ingredient->parents()->syncWithoutDetaching($parents);

        return $ingredient;
    }

    public static function normalize(string $name) {
        return trim(strtolower($name));
    }
}
