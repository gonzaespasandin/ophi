<?php

namespace App\Classifier\BigGroups\Intolerances;

use App\Classifier\Contracts\ClassifierInterface;
use App\Classifier\Strategies\ExactMatchStrategy;
use App\Classifier\Strategies\KeyWordStrategy;
use App\Models\Ingredient;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BelongsToHistamine implements ClassifierInterface {
    public static string $name = "Histamina";

    public static function classify(Ingredient $ingredient): bool {
        $name = $ingredient->name;

        $exact = ExactMatchStrategy::words([
            'histamina',
            'vino',
            'cerveza',
            'sidra',
            'champagne',
            'cava',
            'queso',
            'roquefort',
            'brie',
            'camembert',
            'parmesano',
            'cheddar',
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
            'caballa',
            'anchoa',
            'arenque',
            'marisco',
            'mejillon',
            'almeja',
            'ostra',
            'langostino',
            'camaron',
            'gamba',
            'tomate',
            'berenjena',
            'espinaca',
            'palta',
            'aguacate',
            'chocolate',
            'cacao',
            'vinagre',
            'soja',
            'miso',
            'tempeh',
            'chucrut',
            'kombucha'
        ])->against($name);
        if ($exact->run()) {
            return true;
        }

        $keyword = KeyWordStrategy::rules([
            'fermentad',
            'curad',
            'madurad',
            'añejad',
            'ahumad',
            'embutid',
            'fiambre',
            'conserva',
            'enlatad',
            'marinad',
            'escabech',
            'vinag',
            'extracto de levadura',
            'levadura',
            'salsa de soja',
            'bebida alcohol',
            'licor',
            'cerveza',
            'vino',
            'marisco',
            'pescado|!fresco',
            'queso|!fresco',
            'tomate',
            'berenjena',
            'espinaca',
            'palta',
            'aguacate',
            'chocolate',
            'cacao'
        ])->against($name);
        if ($keyword->run()) {
            return true;
        }

        return false;
    }
}
