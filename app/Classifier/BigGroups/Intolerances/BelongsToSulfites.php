<?php

namespace App\Classifier\BigGroups\Intolerances;

use App\Classifier\Contracts\ClassifierInterface;
use App\Classifier\Strategies\ExactMatchStrategy;
use App\Classifier\Strategies\KeyWordStrategy;
use App\Models\Ingredient;

class BelongsToSulfites implements ClassifierInterface {
    public static string $name = "Anhídrido sulfuroso y sulfitos";

    public static function classify(Ingredient $ingredient): bool {
        $exact = ExactMatchStrategy::words([
            'sulfito',
            'sulfitos',
            'anhidrido sulfuroso',
            'dioxido de azufre',
            'metabisulfito',
            'bisulfito',
            'sulfito de sodio',
            'sulfito de potasio',
            'metabisulfito de sodio',
            'metabisulfito de potasio',
            'vino',
            'cerveza',
            'sidra',
            'champagne',
            'cava',
            'fruta seca',
            'orejon',
            'pasa',
            'damasco seco',
            'ciruela seca'
        ])->against($ingredient->name);
        if ($exact->run()) {
            return true;
        }

        $keyWord = KeyWordStrategy::rules([
            'sulfit',
            'sulfuro',
            'dioxido de azufre',
            'so2',
            'e220',
            'e221',
            'e222',
            'e223',
            'e224',
            'e225',
            'e226',
            'e227',
            'e228',
            'metabisulfit',
            'bisulfit',
            'vino',
            'cervez',
            'sidra',
            'champagne',
            'cava',
            'fruta seca',
            'desecad',
            'orejon',
            'pasa',
            'damasco seco',
            'ciruela seca'
        ])->against($ingredient->name);
        if ($keyWord->run()) {
            return true;
        }

        return false;
    }
}
