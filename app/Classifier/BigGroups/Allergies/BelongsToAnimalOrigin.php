<?php

namespace App\Classifier\BigGroups\Allergies;

use App\Classifier\Contracts\ClassifierInterface;
use App\Models\Ingredient;

class BelongsToAnimalOrigin implements ClassifierInterface {
    public static string $name = "Productos de origen animal";

    public static function classify(Ingredient $ingredient): bool {
        // Rules

        return false;
    }
}
