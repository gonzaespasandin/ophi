<?php

namespace App\Classifier\BigGroups\SpecialDiets;

use App\Classifier\Contracts\ClassifierInterface;
use App\Models\Ingredient;

class BelongsToVegan implements ClassifierInterface {
    public static string $name = "Vegano";

    public static function classify(Ingredient $ingredient): bool {


        return false;
    }
}
