<?php

namespace App\Classifier\BigGroups\Intolerances;

use App\Classifier\Contracts\ClassifierInterface;
use App\Models\Ingredient;

class BelongsToXylitol implements ClassifierInterface {
    public static string $name = "Xilitol";

    public static function classify(Ingredient $ingredient): bool {
        // Rules

        return false;
    }
}
