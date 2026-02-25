<?php

namespace App\Classifier\BigGroups\Intolerances;

use App\Classifier\Contracts\ClassifierInterface;
use App\Classifier\Strategies\ExactMatchStrategy;
use App\Classifier\Strategies\KeyWordStrategy;
use App\Models\Ingredient;

class BelongsToSalicylate implements ClassifierInterface {
    public static string $name = "Salicilato";

    public static function classify(Ingredient $ingredient): bool {
        $exact = ExactMatchStrategy::words([
            'salicilato',
            'acido salicilico',
            'salicilico',
            'aspirina',
            'menta',
            'mentol',
            'hierbabuena',
            'romero',
            'tomillo',
            'oregano',
            'curry',
            'curcuma',
            'pimenton',
            'paprika',
            'canela',
            'clavo',
            'anis',
            'comino',
            'vino',
            'cerveza',
            'sidra',
            'miel',
            'almendra',
            'manzana',
            'pera',
            'uva',
            'frutilla',
            'fresa',
            'frambuesa',
            'cereza',
            'naranja',
            'mandarina',
            'limon',
            'tomate'
        ])->against($ingredient->name);
        if ($exact->run()) {
            return true;
        }

        $keyWord = KeyWordStrategy::rules([
            'salicil',
            'acido salicil',
            'aspirin',
            'menta',
            'mentol',
            'hierba',
            'especia',
            'condimento',
            'romero',
            'tomillo',
            'oregano',
            'curry',
            'curcum',
            'piment',
            'paprika',
            'canela',
            'clavo',
            'anis',
            'comino',
            'vino',
            'cervez',
            'sidra',
            'miel',
            'almendr',
            'manzana',
            'pera',
            'uva',
            'frutill',
            'fresa',
            'frambues',
            'cereza',
            'naranj',
            'mandarin',
            'limon',
            'tomate'
        ])->against($ingredient->name);
        if ($keyWord->run()) {
            return true;
        }

        return false;
    }
}
