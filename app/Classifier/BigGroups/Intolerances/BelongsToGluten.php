<?php

namespace App\Classifier\BigGroups\Intolerances;

use App\Classifier\Contracts\ClassifierInterface;
use App\Classifier\Strategies\ExactMatchStrategy;
use App\Classifier\Strategies\KeyWordStrategy;
use App\Models\Ingredient;

class BelongsToGluten implements ClassifierInterface {
    public static string $name = "Gluten";

    public static function classify(Ingredient $ingredient): bool {
        $exact = ExactMatchStrategy::words([
            'gluten',
            'trigo',
            'cebada',
            'centeno',
            'avena',
            'espelta',
            'kamut',
            'triticale',
            'malta',
            'semola',
            'cuscus',
            'bulgur',
            'farro',
            'harina',
            'pan',
            'galleta',
            'bizcocho',
            'pasta',
            'fideo'
        ])->against($ingredient->name);
        if ($exact->run()) {
            return true;
        }

        $keyWord = KeyWordStrategy::rules([
            'gluten',
            'trig',
            'cebada',
            'centen',
            'aven',
            'espelt',
            'kamut',
            'triticale',
            'malta',
            'semol',
            'cuscus',
            'bulgur',
            'farro',
            'harina de trigo',
            'harina integral',
            'harina 000',
            'harina 0000',
            'pan',
            'galleta',
            'bizcoch',
            'pasta',
            'fideo',
            'reboz',
            'empanad',
            'cerveza'
        ])->against($ingredient->name);
        if ($keyWord->run()) {
            return true;
        }

        return false;
    }
}
