<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    //
    public function user() {
        return $this->belongsTo(User::class);
    }

    public function ingredients() {
        return $this->belongsToMany(
            Ingredient::class,
            'ingredient_profile',
            'profile_id',
            'ingredient_id'
        );
    }
}
