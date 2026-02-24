<?php

namespace App\Classifier\BigGroups\Intolerances;

use App\Classifier\Contracts\ClassifierInterface;
use App\Models\Ingredient;

class BelongsToFructose implements ClassifierInterface {
    public static string $name = "Fructosa";

    public static function classify(Ingredient $ingredient): bool {
        // Rules

        return false;
    }
}
