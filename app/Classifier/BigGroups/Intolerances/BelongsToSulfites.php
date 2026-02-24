<?php

namespace App\Classifier\BigGroups\Intolerances;

use App\Classifier\Contracts\ClassifierInterface;
use App\Models\Ingredient;

class BelongsToSulfites implements ClassifierInterface {
    public static string $name = "Anhídrido sulfuroso y sulfitos";

    public static function classify(Ingredient $ingredient): bool {
        // Rules

        return false;
    }
}
