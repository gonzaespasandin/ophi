<?php

namespace App\Classifier\BigGroups\Allergies;

use App\Classifier\Contracts\ClassifierInterface;
use App\Models\Ingredient;

class BelongsToNutsAndSeeds implements ClassifierInterface {
    public static string $name = "Nueces y semillas";

    public static function classify(Ingredient $ingredient): bool {
        // Rules

        return false;
    }
}
