<?php

namespace App\Classifier\BigGroups\SpecialDiets;

use App\Classifier\Contracts\ClassifierInterface;
use App\Models\Ingredient;

class BelongsToAlcoholFree implements ClassifierInterface {
    public static string $name = "Sin alcohol";

    public static function classify(Ingredient $ingredient): bool {


        return false;
    }
}
