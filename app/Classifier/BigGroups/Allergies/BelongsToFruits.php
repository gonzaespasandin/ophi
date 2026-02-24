<?php

namespace App\Classifier\BigGroups\Allergies;

use App\Classifier\Contracts\ClassifierInterface;
use App\Models\Ingredient;
use Illuminate\Support\Str;

class BelongsToFruits implements ClassifierInterface {
    public static string $name = "Frutas";

    public static function classify(Ingredient $ingredient): bool {
        $name = Str::trim(Str::lower($ingredient['name']));

        if (Str::contains($name, 'manzana')) {
            return true;
        }

        return false;
    }
}
