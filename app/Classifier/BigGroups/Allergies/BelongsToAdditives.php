<?php

namespace App\Classifier\BigGroups\Allergies;

use App\Classifier\Contracts\ClassifierInterface;
use App\Models\Ingredient;

class BelongsToAdditives implements ClassifierInterface {
    public static string $name = "Aditivos";

    public static function classify(Ingredient $ingredient): bool {
        // Rules

        return false;
    }
}
