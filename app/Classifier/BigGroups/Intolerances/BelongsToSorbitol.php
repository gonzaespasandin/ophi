<?php

namespace App\Classifier\BigGroups\Intolerances;

use App\Classifier\Contracts\ClassifierInterface;
use App\Models\Ingredient;

class BelongsToSorbitol implements ClassifierInterface {
    public static string $name = "Sorbitol";

    public static function classify(Ingredient $ingredient): bool {
        // Rules

        return false;
    }
}
