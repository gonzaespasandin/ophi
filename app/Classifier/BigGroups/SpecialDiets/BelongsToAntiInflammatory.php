<?php

namespace App\Classifier\BigGroups\SpecialDiets;

use App\Classifier\Contracts\ClassifierInterface;
use App\Models\Ingredient;

class BelongsToAntiInflammatory implements ClassifierInterface {
    public static string $name = "Antiinflamatorio";

    public static function classify(Ingredient $ingredient): bool {


        return false;
    }
}
