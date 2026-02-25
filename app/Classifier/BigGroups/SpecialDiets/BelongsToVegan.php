<?php

namespace App\Classifier\BigGroups\SpecialDiets;

use App\Classifier\Contracts\ClassifierInterface;
use App\Classifier\Strategies\ExactMatchStrategy;
use App\Classifier\Strategies\KeyWordStrategy;
use App\Models\Ingredient;

class BelongsToVegan implements ClassifierInterface {
    public static string $name = "Vegano";

    public static function classify(Ingredient $ingredient): bool {
        $exact = ExactMatchStrategy::words([
            'carne',
            'res',
            'cerdo',
            'pollo',
            'pavo',
            'cordero',
            'pescado',
            'atun',
            'salmon',
            'marisco',
            'huevo',
            'huevos',
            'leche',
            'queso',
            'yogur',
            'manteca',
            'mantequilla',
            'crema',
            'nata',
            'gelatina',
            'miel',
            'caseina',
            'lactosa',
            'suero',
            'colageno'
        ])->against($ingredient->name);
        if ($exact->run()) {
            return true;
        }

        $keyWord = KeyWordStrategy::rules([
            'carn',
            'res',
            'cerd',
            'poll',
            'pav',
            'corder',
            'pescad',
            'atun',
            'salmon',
            'marisc',
            'huev',
            'leche',
            'ques',
            'yog',
            'mante',
            'crem',
            'nata',
            'gelatin',
            'miel',
            'casein',
            'lactos',
            'suer',
            'colagen',
            'proteina animal',
            'derivad|animal'
        ])->against($ingredient->name);
        if ($keyWord->run()) {
            return true;
        }

        return false;
    }
}
