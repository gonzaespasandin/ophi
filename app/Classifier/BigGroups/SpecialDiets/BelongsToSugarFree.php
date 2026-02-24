<?php

namespace App\Classifier\BigGroups\SpecialDiets;

use App\Classifier\Contracts\ClassifierInterface;
use App\Models\Ingredient;

class BelongsToSugarFree implements ClassifierInterface {
    public static string $name = "Sin azúcar";

    public static function classify(Ingredient $ingredient): bool {


        return false;
    }
}
