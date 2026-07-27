<?php

namespace App\Classifier\BigGroups\Allergies;

use App\Classifier\Contracts\ClassifierInterface;
use App\Classifier\Strategies\ExactMatchStrategy;
use App\Classifier\Strategies\KeyWordStrategy;
use App\Models\Ingredient;

class BelongsToFish implements ClassifierInterface {
    public static string $name = "Pescado";

    public static function classify(Ingredient $ingredient): bool {
        $exact = ExactMatchStrategy::words([
            'pescado',
            'atun',
            'salmon',
            'merluza',
            'bacalao',
            'sardina',
            'anchoa',
            'caballa',
            'trucha',
            'lenguado',
            'abadejo',
            'corvina',
            'brótola',
            'besugo',
            'pez espada',
            'mero',
            'tilapia',
            'arenque',
            'palometa',
            'filete de pescado'
        ])->against($ingredient->name);
        if ($exact->run()) {
            return true;
        }

        $keyWord = KeyWordStrategy::rules([
            'pescad',
            'atun',
            'salmon',
            'merluz',
            'bacala',
            'sardin',
            'ancho',
            'cabal',
            'truch',
            'lenguad',
            'abadej',
            'corvin',
            'brotol',
            'besug',
            'espada',
            'mer',
            'tilapi',
            'arenqu',
            'palomet',
            'filet|pescad'
        ])->against($ingredient->name);
        if ($keyWord->run()) {
            return true;
        }

        return false;
    }
}
