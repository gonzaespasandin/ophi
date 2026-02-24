<?php

namespace App\Classifier\BigGroups\Intolerances;

use App\Classifier\Contracts\ClassifierInterface;
use App\Models\Ingredient;

class BelongsToLowInFODMAP implements ClassifierInterface {
    public static string $name = "Bajo en FODMAP";

    public static function classify(Ingredient $ingredient): bool {
        // Rules

        return false;
    }
}
