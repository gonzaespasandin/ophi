<?php

namespace App\Classifier\BigGroups\Intolerances;

use App\Classifier\Contracts\ClassifierInterface;
use App\Classifier\Strategies\ExactMatchStrategy;
use App\Classifier\Strategies\KeyWordStrategy;
use App\Models\Ingredient;

class BelongsToSorbitol implements ClassifierInterface {
    public static string $name = "Sorbitol";

    public static function classify(Ingredient $ingredient): bool {
        $exact = ExactMatchStrategy::words([
            'sorbitol',
            'sorbit',
            'e420',
            'manitol',
            'xilitol',
            'maltitol',
            'isomalt',
            'lactitol',
            'eritritol',
            'ciruela',
            'durazno',
            'pera',
            'manzana',
            'damasco',
            'albaricoque'
        ])->against($ingredient->name);
        if ($exact->run()) {
            return true;
        }

        $keyWord = KeyWordStrategy::rules([
            'sorbit',
            'e420',
            'manit',
            'xilit',
            'maltit',
            'isomalt',
            'lactit',
            'eritrit',
            'polialcohol',
            'alcohol de azucar',
            'ciruela',
            'durazno',
            'pera',
            'manzana',
            'damasco',
            'albaricoque',
            'edulcorante'
        ])->against($ingredient->name);
        if ($keyWord->run()) {
            return true;
        }

        return false;
    }
}
