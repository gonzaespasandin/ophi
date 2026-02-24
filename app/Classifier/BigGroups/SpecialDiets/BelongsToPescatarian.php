<?php

namespace App\Classifier\BigGroups\SpecialDiets;

use App\Classifier\Contracts\ClassifierInterface;
use App\Models\Ingredient;

class BelongsToPescatarian implements ClassifierInterface {
    public static string $name = "Pescetariana";

    public static function classify(Ingredient $ingredient): bool {


        return false;
    }
}
