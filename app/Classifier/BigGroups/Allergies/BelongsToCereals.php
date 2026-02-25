<?php

namespace App\Classifier\BigGroups\Allergies;

use App\Classifier\Contracts\ClassifierInterface;
use App\Classifier\Strategies\ExactMatchStrategy;
use App\Classifier\Strategies\KeyWordStrategy;
use App\Models\Ingredient;

class BelongsToCereals implements ClassifierInterface {
    public static string $name = "Cereales";

    public static function classify(Ingredient $ingredient): bool {
        $exact = ExactMatchStrategy::words([
            'trigo',
            'arroz',
            'maiz',
            'cebada',
            'centeno',
            'avena',
            'espelta',
            'kamut',
            'triticale',
            'mijo',
            'sorgo',
            'quinoa',
            'amaranto',
            'farro',
            'bulgur',
            'cuscus',
            'semola',
            'salvado',
            'germen de trigo',
            'copos de maiz',
            'copos de arroz',
            'copos de avena',
            'harina',
            'harina integral',
            'harina de trigo',
            'harina de maiz',
            'harina de arroz',
            'pasta',
            'fideos',
            'pan',
            'galleta',
            'bizcocho',
            'cereal',
            'granola'
        ])->against($ingredient->name);
        if ($exact->run()) {
            return true;
        }

        $keyWord = KeyWordStrategy::rules([
            'cereal',
            'trig',
            'arroz',
            'maiz',
            'cebada',
            'centen',
            'aven',
            'espelt',
            'kamut',
            'triticale',
            'mijo',
            'sorg',
            'quino',
            'amaran',
            'farro',
            'bulgur',
            'cuscus',
            'semol',
            'salvad',
            'germen',
            'copos',
            'harina',
            'integral',
            'pasta',
            'fideo',
            'pan',
            'gallet',
            'bizcoch',
            'granola'
        ])->against($ingredient->name);
        if ($keyWord->run()) {
            return true;
        }

        return false;
    }
}
