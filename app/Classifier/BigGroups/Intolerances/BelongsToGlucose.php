<?php

namespace App\Classifier\BigGroups\Intolerances;

use App\Classifier\Contracts\ClassifierInterface;
use App\Models\Ingredient;

class BelongsToGlucose implements ClassifierInterface {
    public static string $name = "Glucosa";

    public static function classify(Ingredient $ingredient): bool {
        // Rules

        return false;
    }
}
