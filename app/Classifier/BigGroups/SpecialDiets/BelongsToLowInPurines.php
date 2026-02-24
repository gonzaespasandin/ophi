<?php

namespace App\Classifier\BigGroups\SpecialDiets;

use App\Classifier\Contracts\ClassifierInterface;
use App\Models\Ingredient;

class BelongsToLowInPurines implements ClassifierInterface {
    public static string $name = "Bajo en purinas";

    public static function classify(Ingredient $ingredient): bool {


        return false;
    }
}
