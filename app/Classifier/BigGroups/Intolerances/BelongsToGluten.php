<?php

namespace App\Classifier\BigGroups\Intolerances;

use App\Classifier\Contracts\ClassifierInterface;
use App\Models\Ingredient;

class BelongsToGluten implements ClassifierInterface {
    public static string $name = "Gluten";

    public static function classify(Ingredient $ingredient): bool {
        // Rules

        return false;
    }
}
