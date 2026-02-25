<?php

namespace App\Classifier\BigGroups\Intolerances;

use App\Classifier\Contracts\ClassifierInterface;
use App\Classifier\Strategies\ExactMatchStrategy;
use App\Classifier\Strategies\KeyWordStrategy;
use App\Models\Ingredient;
use Illuminate\Support\Str;

class BelongsToLactose implements ClassifierInterface {
    public static string $name = "Lactosa";

    public static function classify(Ingredient $ingredient): bool {
        $name = $ingredient->name;

        $exact = ExactMatchStrategy::words([
            'lactosa',
            'leche',
            'suero',
            'caseina',
            'requeson',
            'ricota',
            'crema',
            'nata',
            'manteca',
            'mantequilla',
            'margarina',
            'yogur',
            'yoghurt',
            'kefir',
            'queso',
            'helado',
            'dulce de leche',
            'cuajada',
            'flan',
            'natilla',
            'leche condensada',
            'leche en polvo'
        ])->against($name);
        if ($exact->run()) {
            return true;
        }

        $keyword = KeyWordStrategy::rules([
            'leche',
            'lactosa',
            'lactosuero',
            'suero lact',
            'proteina lactea',
            'solidos lacteos',
            'derivado lacteo',
            'grasa lactea',
            'azucar lactea',
            'casein',
            'queso',
            'yog',
            'kefir',
            'crema',
            'nata',
            'manteca',
            'mantequilla',
            'dulce de leche',
            'leche condensada',
            'leche en polvo'
        ])->against($name);
        if ($keyword->run()) {
            return true;
        }

        return false;
    }
}
