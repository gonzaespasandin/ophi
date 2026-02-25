<?php

namespace App\Classifier\BigGroups\Intolerances;

use App\Classifier\Contracts\ClassifierInterface;
use App\Classifier\Strategies\ExactMatchStrategy;
use App\Classifier\Strategies\KeyWordStrategy;
use App\Models\Ingredient;

class BelongsToFructans implements ClassifierInterface {
    public static string $name = "Fructanos";

    public static function classify(Ingredient $ingredient): bool {
        $exact = ExactMatchStrategy::words([
            'fructanos',
            'fructano',
            'inulina',
            'ajo',
            'cebolla',
            'puerro',
            'chalota',
            'cebollin',
            'trigo',
            'centeno',
            'cebada',
            'espelta',
            'alcachofa',
            'esparrago',
            'achicoria'
        ])->against($ingredient->name);
        if ($exact->run()) {
            return true;
        }

        $keyWord = KeyWordStrategy::rules([
            'fructan',
            'inulin',
            'ajo',
            'ceboll',
            'puerro',
            'chalot',
            'cebollin',
            'trig',
            'centen',
            'cebada',
            'espelt',
            'alcachof',
            'esparrag',
            'achicor',
            'harina de trigo',
            'extracto de achicoria'
        ])->against($ingredient->name);
        if ($keyWord->run()) {
            return true;
        }

        return false;
    }
}
