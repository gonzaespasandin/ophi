<?php

namespace App\Classifier\BigGroups\Allergies;

use App\Classifier\Contracts\ClassifierInterface;
use App\Models\Ingredient;

class BelongsToCereals implements ClassifierInterface {
    public static string $name = "Cereales";

    public static function classify(Ingredient $ingredient): bool {
        // Rules

        return false;
    }
}
