<?php

namespace App\Classifier\BigGroups\Intolerances;

use App\Classifier\Contracts\ClassifierInterface;
use App\Classifier\Strategies\ExactMatchStrategy;
use App\Classifier\Strategies\KeyWordStrategy;
use App\Models\Ingredient;

class BelongsToOligosaccharides implements ClassifierInterface {
    public static string $name = "Oligosacáridos";

    public static function classify(Ingredient $ingredient): bool {
        $exact = ExactMatchStrategy::words([
            'oligosacaridos',
            'fructanos',
            'galactanos',
            'inulina',
            'ajo',
            'cebolla',
            'puerro',
            'chalota',
            'cebollin',
            'trigo',
            'centeno',
            'cebada',
            'garbanzo',
            'lenteja',
            'poroto',
            'frijol',
            'soja',
            'habas'
        ])->against($ingredient->name);
        if ($exact->run()) {
            return true;
        }

        $keyWord = KeyWordStrategy::rules([
            'oligosacar',
            'fructan',
            'galactan',
            'inulin',
            'ajo',
            'ceboll',
            'puerro',
            'chalot',
            'cebollin',
            'trig',
            'centen',
            'cebada',
            'garban',
            'lentej',
            'porot',
            'frijol',
            'soja',
            'haba',
            'legumbr'
        ])->against($ingredient->name);
        if ($keyWord->run()) {
            return true;
        }

        return false;
    }
}
