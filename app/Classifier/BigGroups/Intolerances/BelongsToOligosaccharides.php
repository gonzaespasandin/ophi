<?php

namespace App\Classifier\BigGroups\Intolerances;

use App\Classifier\Contracts\ClassifierInterface;
use App\Models\Ingredient;

class BelongsToOligosaccharides implements ClassifierInterface {
    public static string $name = "Oligosacáridos";

    public static function classify(Ingredient $ingredient): bool {
        // Rules

        return false;
    }
}
