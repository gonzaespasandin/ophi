<?php

namespace App\Classifier\BigGroups\Allergies;

use App\Classifier\Contracts\ClassifierInterface;
use App\Models\Ingredient;

class BelongsToMeat implements ClassifierInterface {
    public static string $name = "Carne";

    public static function classify(Ingredient $ingredient): bool {
        // Rules

        return false;
    }
}
