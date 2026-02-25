<?php

namespace App\Classifier\BigGroups\Intolerances;

use App\Classifier\Contracts\ClassifierInterface;
use App\Classifier\Strategies\ExactMatchStrategy;
use App\Classifier\Strategies\KeyWordStrategy;
use App\Models\Ingredient;

class BelongsToXylitol implements ClassifierInterface {
    public static string $name = "Xilitol";

    public static function classify(Ingredient $ingredient): bool {
        $exact = ExactMatchStrategy::words([
            'xilitol',
            'e967',
            'sorbitol',
            'manitol',
            'maltitol',
            'isomalt',
            'lactitol',
            'eritritol',
            'jarabe sin azucar',
            'caramelo sin azucar',
            'chicle sin azucar',
            'goma de mascar'
        ])->against($ingredient->name);
        if ($exact->run()) {
            return true;
        }

        $keyWord = KeyWordStrategy::rules([
            'xilit',
            'e967',
            'sorbit',
            'manit',
            'maltit',
            'isomalt',
            'lactit',
            'eritrit',
            'polialcohol',
            'alcohol de azucar',
            'edulcorante',
            'sin azucar'
        ])->against($ingredient->name);
        if ($keyWord->run()) {
            return true;
        }

        return false;
    }
}
