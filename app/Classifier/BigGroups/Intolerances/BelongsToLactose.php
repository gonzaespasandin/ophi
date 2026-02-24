<?php

namespace App\Classifier\BigGroups\Intolerances;

use App\Classifier\Contracts\ClassifierInterface;
use App\Models\Ingredient;
use Illuminate\Support\Str;

class BelongsToLactose implements ClassifierInterface {
    public static string $name = "Lactosa";

    public static function classify(Ingredient $ingredient): bool {
        $name = Str::trim(Str::lower($ingredient['name']));

        if (Str::contains($name, 'leche')) {
            return true;
        }

        return false;
    }
}
