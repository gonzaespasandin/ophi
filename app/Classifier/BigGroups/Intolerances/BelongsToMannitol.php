<?php

namespace App\Classifier\BigGroups\Intolerances;

use App\Classifier\Contracts\ClassifierInterface;
use App\Models\Ingredient;

class BelongsToMannitol implements ClassifierInterface {
    public static string $name = "Manitol";

    public static function classify(Ingredient $ingredient): bool {
        // Rules

        return false;
    }
}
