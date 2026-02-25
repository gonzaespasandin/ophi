<?php

namespace App\Classifier\BigGroups\Allergies;

use App\Classifier\Contracts\ClassifierInterface;
use App\Classifier\Strategies\ExactMatchStrategy;
use App\Classifier\Strategies\KeyWordStrategy;
use App\Models\Ingredient;

class BelongsToAdditives implements ClassifierInterface {
    public static string $name = "Aditivos";

    public static function classify(Ingredient $ingredient): bool {
        $exact = ExactMatchStrategy::words([
            'colorante',
            'conservante',
            'antioxidante',
            'emulsionante',
            'estabilizante',
            'espesante',
            'gelificante',
            'edulcorante',
            'aromatizante',
            'potenciador del sabor',
            'acidulante',
            'regulador de acidez',
            'antiaglomerante',
            'humectante',
            'gasificante',
            'propulsor',
            'lecitina',
            'glutamato monosodico',
            'aspartamo',
            'sacarina',
            'sucralosa',
            'acesulfame k',
            'benzoato de sodio',
            'sorbato de potasio',
            'nitrito de sodio',
            'nitrato de sodio',
            'bht',
            'bha'
        ])->against($ingredient->name);
        if ($exact->run()) {
            return true;
        }

        $keyWord = KeyWordStrategy::rules([
            'colorant',
            'conserv',
            'antioxid',
            'emulsion',
            'estabiliz',
            'espes',
            'gelific',
            'edulcor',
            'aromatiz',
            'potenci',
            'acidul',
            'regulador',
            'antiaglomer',
            'humect',
            'gasific',
            'propuls',
            'lecitin',
            'glutamat',
            'aspart',
            'sacarin',
            'sucralos',
            'acesulfam',
            'benzoat',
            'sorbat',
            'nitrit',
            'nitrat',
            'bht',
            'bha',
            'e1',
            'e2',
            'e3',
            'e4',
            'e5'
        ])->against($ingredient->name);
        if ($keyWord->run()) {
            return true;
        }

        return false;
    }
}
