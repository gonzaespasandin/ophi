<?php

namespace App\Classifier\BigGroups\Allergies;

use App\Classifier\Contracts\ClassifierInterface;
use App\Classifier\Strategies\ExactMatchStrategy;
use App\Classifier\Strategies\KeyWordStrategy;
use App\Models\Ingredient;

class BelongsToCasein implements ClassifierInterface {
    public static string $name = "Caseína";

    public static function classify(Ingredient $ingredient): bool {
        $exact = ExactMatchStrategy::words([
            'caseina',
            'caseinato',
            'caseinato de sodio',
            'caseinato de calcio',
            'caseinato de potasio',
            'leche',
            'queso',
            'yogur',
            'manteca',
            'mantequilla',
            'crema',
            'nata',
            'suero',
            'proteina lactea',
            'proteina de leche',
            'cuajada',
            'requeson',
            'ricota',
            'kefir',
            'helado'
        ])->against($ingredient->name);
        if ($exact->run()) {
            return true;
        }

        $keyWord = KeyWordStrategy::rules([
            'casein',
            'caseinat',
            'proteina lact',
            'proteina de leche',
            'derivado lact',
            'solidos lact',
            'leche',
            'ques',
            'yog',
            'mante',
            'crem',
            'nata',
            'suero',
            'cuajad',
            'reques',
            'ricot',
            'kefir',
            'helad',
            'lacte'
        ])->against($ingredient->name);
        if ($keyWord->run()) {
            return true;
        }

        return false;
    }
}
