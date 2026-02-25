<?php

namespace App\Classifier\BigGroups\SpecialDiets;

use App\Classifier\Contracts\ClassifierInterface;
use App\Classifier\Strategies\ExactMatchStrategy;
use App\Classifier\Strategies\KeyWordStrategy;
use App\Models\Ingredient;

class BelongsToLowInPurines implements ClassifierInterface {
    public static string $name = "Bajo en purinas";

    public static function classify(Ingredient $ingredient): bool {
        $exact = ExactMatchStrategy::words([
            'higado',
            'rinon',
            'molleja',
            'tripa',
            'menudo',
            'anchoa',
            'sardina',
            'arenque',
            'caballa',
            'atun',
            'salmon',
            'marisco',
            'camaron',
            'langostino',
            'mejillon',
            'almeja',
            'calamar',
            'pulpo',
            'extracto de carne',
            'caldo de carne',
            'levadura',
            'extracto de levadura',
            'cerveza'
        ])->against($ingredient->name);
        if ($exact->run()) {
            return true;
        }

        $keyWord = KeyWordStrategy::rules([
            'higad',
            'rinon',
            'mollej',
            'trip',
            'menud',
            'ancho',
            'sardin',
            'arenqu',
            'cabal',
            'atun',
            'salmon',
            'marisc',
            'camaron',
            'langostin',
            'mejillon',
            'almej',
            'calamar',
            'pulp',
            'extracto|carn',
            'caldo|carn',
            'levadur',
            'cervez',
            'viscer'
        ])->against($ingredient->name);
        if ($keyWord->run()) {
            return true;
        }

        return false;
    }
}
