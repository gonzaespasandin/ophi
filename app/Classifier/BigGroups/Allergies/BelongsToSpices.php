<?php

namespace App\Classifier\BigGroups\Allergies;

use App\Classifier\Contracts\ClassifierInterface;
use App\Models\Ingredient;

class BelongsToSpices implements ClassifierInterface {
    public static string $name = "Especias";

    public static function classify(Ingredient $ingredient): bool {
        // Rules

        return false;
    }
}
