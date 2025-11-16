<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Ingredient extends Model
{
    public function ingredients() {
        return $this->belongsToMany(Ingredient::class, 'ingredient_has_ingredients', 'belongs_to_id', 'owner_id');
    }

    public function getAllSubgroups() {
        dd($this->ingredients);
    }

}
