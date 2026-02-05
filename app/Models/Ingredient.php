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

    public function getAllSubgroups() {
        dd($this->ingredients);
    }

}
