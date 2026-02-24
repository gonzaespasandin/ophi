<?php

namespace App\Classifier\BigGroups\Intolerances;

use App\Classifier\Contracts\ClassifierInterface;
use App\Models\Ingredient;

class BelongsToSaccharose implements ClassifierInterface {
    public static string $name = "Sacarosa";

    public static function classify(Ingredient $ingredient): bool {
        // Rules

        return false;
    }
}
