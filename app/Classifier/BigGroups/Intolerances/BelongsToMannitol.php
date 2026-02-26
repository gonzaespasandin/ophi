<?php

namespace App\Classifier\BigGroups\Intolerances;

use App\Classifier\Contracts\ClassifierInterface;
use App\Classifier\Strategies\ExactMatchStrategy;
use App\Classifier\Strategies\KeyWordStrategy;
use App\Models\Ingredient;

class BelongsToMannitol implements ClassifierInterface {
    public static string $name = "Manitol";

    public static function classify(Ingredient $ingredient): bool {
        $exact = ExactMatchStrategy::words([
            'manitol',
            'e421',
            'sorbitol',
            'xilitol',
            'maltitol',
            'isomalt',
            'lactitol',
            'eritritol',
            'coliflor',
            'champiñon',
            'seta',
            'apio',
            'calabaza',
            'batata',
            'boniato',
            'pera',
            'manzana',
            'durazno',
            'ciruela'
        ])->against($ingredient->name);
        if ($exact->run()) {
            return true;
        }

        $keyWord = KeyWordStrategy::rules([
            'manitol',
            'e421',
            'sorbit',
            'xilit',
            'maltit',
            'isomalt',
            'lactit',
            'eritrit',
            'polialcohol',
            'alcohol de azucar',
            'coliflor',
            'champi',
            'seta',
            'hongo',
            'apio',
            'calabaza',
            'batata',
            'boniato',
            'pera',
            'manzana',
            'durazno',
            'ciruela',
            'edulcorante'
        ])->against($ingredient->name);
        if ($keyWord->run()) {
            return true;
        }

        return false;
    }
}
