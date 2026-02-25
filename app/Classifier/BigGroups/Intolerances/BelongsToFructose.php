<?php

namespace App\Classifier\BigGroups\Intolerances;

use App\Classifier\Contracts\ClassifierInterface;
use App\Classifier\Strategies\ExactMatchStrategy;
use App\Classifier\Strategies\KeyWordStrategy;
use App\Models\Ingredient;

class BelongsToFructose implements ClassifierInterface {
    public static string $name = "Fructosa";

    public static function classify(Ingredient $ingredient): bool {
        // Rules
        $exact = ExactMatchStrategy::words([
            'fructosa',
            'fructose',
            'levulosa',
            'sorbitol',
            'miel',
            'agave',
            'jarabe',
            'melaza',
            'azucar',
            'azucar invertido',
            'jarabe de maiz',
            'jarabe de fructosa',
            'almibar',
            'caramelo',
            'panela',
            'mosto',
            'concentrado de fruta'
        ])->against($ingredient->name);
        if ($exact->run()) {
            return true;
        }

        $keyWord = KeyWordStrategy::rules([
            'fruct',
            'sorbit',
            'miel',
            'agave',
            'jarabe',
            'almibar',
            'melaza',
            'azucar',
            'caramelo',
            'panela',
            'mosto',
            'concentrado',
            'jugo concentrado',
            'pure de fruta',
            'extracto de fruta',
            'fruta',
            'manzana',
            'pera',
            'mango',
            'sandia',
            'melon',
            'durazno',
            'ciruela'
        ])->against($ingredient->name);
        if ($keyWord->run()) {
            return true;
        }

        return false;
    }
}
