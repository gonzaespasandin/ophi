<?php

namespace App\Classifier\BigGroups\Allergies;

use App\Classifier\Contracts\ClassifierInterface;
use App\Models\Ingredient;

class BelongsToFish implements ClassifierInterface {
    public static string $name = "Pescado";

    public static function classify(Ingredient $ingredient): bool {
        // Rules

        return false;
    }
}
