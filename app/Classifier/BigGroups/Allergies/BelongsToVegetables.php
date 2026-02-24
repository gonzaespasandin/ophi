<?php

namespace App\Classifier\BigGroups\Allergies;

use App\Classifier\Contracts\ClassifierInterface;
use App\Models\Ingredient;

class BelongsToVegetables implements ClassifierInterface {
    public static string $name = "Verduras";

    public static function classify(Ingredient $ingredient): bool {
        // Rules

        return false;
    }
}
