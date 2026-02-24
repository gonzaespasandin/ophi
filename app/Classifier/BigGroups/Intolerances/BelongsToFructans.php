<?php

namespace App\Classifier\BigGroups\Intolerances;

use App\Classifier\Contracts\ClassifierInterface;
use App\Models\Ingredient;

class BelongsToFructans implements ClassifierInterface {
    public static string $name = "Fructanos";

    public static function classify(Ingredient $ingredient): bool {
        // Rules

        return false;
    }
}
