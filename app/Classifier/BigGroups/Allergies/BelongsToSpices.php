<?php

namespace App\Classifier\BigGroups\Allergies;

use App\Classifier\Contracts\ClassifierInterface;
use App\Classifier\Strategies\ExactMatchStrategy;
use App\Classifier\Strategies\KeyWordStrategy;
use App\Models\Ingredient;

class BelongsToSpices implements ClassifierInterface {
    public static string $name = "Especias";

    public static function classify(Ingredient $ingredient): bool {
        $exact = ExactMatchStrategy::words([
            'sal',
            'pimienta',
            'oregano',
            'albahaca',
            'tomillo',
            'romero',
            'laurel',
            'comino',
            'curcuma',
            'pimenton',
            'paprika',
            'canela',
            'clavo',
            'nuez moscada',
            'cardamomo',
            'anis',
            'anis estrellado',
            'jengibre',
            'ajo en polvo',
            'cebolla en polvo',
            'curry',
            'mostaza',
            'azafran',
            'estragon',
            'eneldo',
            'perejil',
            'menta'
        ])->against($ingredient->name);
        if ($exact->run()) {
            return true;
        }

        $keyWord = KeyWordStrategy::rules([
            'sal',
            'pimient',
            'oreg',
            'albahac',
            'tomill',
            'romer',
            'laurel',
            'comin',
            'curcum',
            'piment',
            'paprik',
            'canel',
            'clav',
            'nuez mosc',
            'cardamom',
            'anis',
            'jengibr',
            'ajo|polvo',
            'ceboll|polvo',
            'curry',
            'mostaz',
            'azafran',
            'estragon',
            'eneld',
            'perej',
            'ment'
        ])->against($ingredient->name);
        if ($keyWord->run()) {
            return true;
        }

        return false;
    }
}
