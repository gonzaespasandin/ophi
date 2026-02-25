<?php

namespace App\Classifier\BigGroups\Intolerances;

use App\Classifier\Contracts\ClassifierInterface;
use App\Classifier\Strategies\ExactMatchStrategy;
use App\Classifier\Strategies\KeyWordStrategy;
use App\Models\Ingredient;

class BelongsToLowInFODMAP implements ClassifierInterface {
    public static string $name = "Bajo en FODMAP";

    public static function classify(Ingredient $ingredient): bool {
        $exact = ExactMatchStrategy::words([
            'ajo',
            'cebolla',
            'puerro',
            'chalota',
            'cebollin',
            'trigo',
            'centeno',
            'cebada',
            'lenteja',
            'garbanzo',
            'poroto',
            'frijol',
            'soja',
            'haba',
            'coliflor',
            'champiñon',
            'seta',
            'manzana',
            'pera',
            'mango',
            'sandia',
            'melon',
            'durazno',
            'ciruela',
            'miel',
            'leche',
            'yogur',
            'helado',
            'queso fresco',
            'sorbitol',
            'manitol',
            'xilitol',
            'maltitol',
            'isomalt',
            'lactitol'
        ])->against($ingredient->name);
        if ($exact->run()) {
            return true;
        }

        $keyWord = KeyWordStrategy::rules([
            'fodmap',
            'ajo',
            'ceboll',
            'puerro',
            'chalot',
            'cebollin',
            'trig',
            'centen',
            'cebada',
            'lentej',
            'garban',
            'porot',
            'frijol',
            'soja',
            'haba',
            'legumbr',
            'coliflor',
            'champi',
            'seta',
            'manzana',
            'pera',
            'mango',
            'sandia',
            'melon',
            'durazno',
            'ciruela',
            'miel',
            'lactos',
            'leche',
            'yog',
            'helad',
            'sorbit',
            'manit',
            'xilit',
            'maltit',
            'isomalt',
            'lactit',
            'polialcohol',
            'inulin',
            'fructan',
            'galactan'
        ])->against($ingredient->name);
        if ($keyWord->run()) {
            return true;
        }

        return false;
    }
}
