<?php

namespace App\Classifier\BigGroups\SpecialDiets;

use App\Classifier\Contracts\ClassifierInterface;
use App\Classifier\Strategies\ExactMatchStrategy;
use App\Classifier\Strategies\KeyWordStrategy;
use App\Models\Ingredient;

class BelongsToAlcoholFree implements ClassifierInterface {
    public static string $name = "Sin alcohol";

    public static function classify(Ingredient $ingredient): bool {
        $exact = ExactMatchStrategy::words([
            'alcohol',
            'etanol',
            'vino',
            'cerveza',
            'sidra',
            'champagne',
            'ron',
            'vodka',
            'whisky',
            'ginebra',
            'tequila',
            'licor',
            'coñac',
            'brandy',
            'anisado',
            'vermut',
            'aperitivo',
            'digestivo'
        ])->against($ingredient->name);
        if ($exact->run()) {
            return true;
        }

        $keyWord = KeyWordStrategy::rules([
            'alcohol',
            'etanol',
            'vino',
            'cervez',
            'sidr',
            'champ',
            'ron',
            'vodk',
            'whisk',
            'ginebr',
            'tequil',
            'licor',
            'conac',
            'brandy',
            'anis',
            'vermut',
            'aperitiv',
            'digestiv',
            'bebida alcohol',
            'fermentad|alcohol'
        ])->against($ingredient->name);
        if ($keyWord->run()) {
            return true;
        }

        return false;
    }
}
