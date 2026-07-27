<?php

namespace App\Classifier\BigGroups\Intolerances;

use App\Classifier\Contracts\ClassifierInterface;
use App\Classifier\Strategies\ExactMatchStrategy;
use App\Classifier\Strategies\KeyWordStrategy;
use App\Models\Ingredient;

class BelongsToTyramine implements ClassifierInterface {
    public static string $name = "Tiramina";

    public static function classify(Ingredient $ingredient): bool {
        $exact = ExactMatchStrategy::words([
            'tiramina',
            'queso',
            'roquefort',
            'brie',
            'camembert',
            'parmesano',
            'cheddar',
            'gorgonzola',
            'salame',
            'salami',
            'jamon',
            'mortadela',
            'chorizo',
            'longaniza',
            'pepperoni',
            'panceta',
            'tocino',
            'atun',
            'sardina',
            'anchoa',
            'arenque',
            'caballa',
            'vino',
            'cerveza',
            'sidra',
            'champagne',
            'cava',
            'soja',
            'miso',
            'tempeh',
            'tofu',
            'chucrut',
            'kimchi',
            'kombucha',
            'levadura',
            'extracto de levadura',
            'higado',
            'palta',
            'aguacate',
            'chocolate',
            'cacao'
        ])->against($ingredient->name);
        if ($exact->run()) {
            return true;
        }

        $keyWord = KeyWordStrategy::rules([
            'tiramin',
            'fermentad',
            'curad',
            'madurad',
            'añejad',
            'embutid',
            'fiambre',
            'ahumad',
            'marinad',
            'escabech',
            'queso|!fresco',
            'pescado|!fresco',
            'vino',
            'cervez',
            'sidra',
            'licor',
            'soja',
            'miso',
            'tempeh',
            'tofu',
            'chucrut',
            'kimchi',
            'kombucha',
            'levadura',
            'higado',
            'palta',
            'aguacate',
            'chocolate',
            'cacao'
        ])->against($ingredient->name);
        if ($keyWord->run()) {
            return true;
        }

        return false;
    }
}
