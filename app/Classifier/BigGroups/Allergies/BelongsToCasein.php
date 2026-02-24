<?php

namespace App\Classifier\BigGroups\Allergies;

use App\Classifier\Contracts\ClassifierInterface;
use App\Models\Ingredient;

class BelongsToCasein implements ClassifierInterface {
    public static string $name = "Caseína";

    public static function classify(Ingredient $ingredient): bool {
        // Rules

        return false;
    }
}
