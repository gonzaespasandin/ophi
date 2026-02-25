<?php

namespace App\Classifier\BigGroups\Intolerances;

use App\Classifier\Contracts\ClassifierInterface;
use App\Classifier\Strategies\ExactMatchStrategy;
use App\Classifier\Strategies\KeyWordStrategy;
use App\Models\Ingredient;

class BelongsToGlucose implements ClassifierInterface {
    public static string $name = "Glucosa";

    public static function classify(Ingredient $ingredient): bool {
        $exact = ExactMatchStrategy::words([
            'glucosa',
            'dextrosa',
            'jarabe de glucosa',
            'jarabe de maiz',
            'jarabe de maiz de alta fructosa',
            'azucar',
            'azucar invertido',
            'maltosa',
            'maltodextrina',
            'melaza',
            'caramelo',
            'panela',
            'almibar'
        ])->against($ingredient->name);
        if ($exact->run()) {
            return true;
        }

        $keyWord = KeyWordStrategy::rules([
            'glucos',
            'dextros',
            'jarabe',
            'maiz',
            'azucar',
            'maltos',
            'maltodextr',
            'melaza',
            'caramel',
            'panela',
            'almibar',
            'sirope'
        ])->against($ingredient->name);
        if ($keyWord->run()) {
            return true;
        }

        return false;
    }
}
