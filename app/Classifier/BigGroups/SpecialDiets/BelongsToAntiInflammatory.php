<?php

namespace App\Classifier\BigGroups\SpecialDiets;

use App\Classifier\Contracts\ClassifierInterface;
use App\Classifier\Strategies\ExactMatchStrategy;
use App\Classifier\Strategies\KeyWordStrategy;
use App\Models\Ingredient;

class BelongsToAntiInflammatory implements ClassifierInterface {
    public static string $name = "Antiinflamatorio";

    public static function classify(Ingredient $ingredient): bool {
        $exact = ExactMatchStrategy::words([
            'azucar',
            'azucar blanca',
            'harina refinada',
            'harina blanca',
            'pan blanco',
            'bolleria',
            'galletitas',
            'gaseosa',
            'refresco',
            'margarina',
            'grasa trans',
            'aceite vegetal refinado',
            'aceite de soja',
            'aceite de maiz',
            'aceite de girasol refinado',
            'fritura',
            'embutido',
            'salchicha',
            'chorizo',
            'salame',
            'tocino',
            'panceta',
            'carne procesada',
            'alcohol'
        ])->against($ingredient->name);
        if ($exact->run()) {
            return true;
        }

        $keyWord = KeyWordStrategy::rules([
            'azucar',
            'jarabe',
            'sirope',
            'harina|refinad',
            'pan|blanc',
            'bolleri',
            'gallet',
            'gaseos',
            'refresc',
            'margarin',
            'grasa|trans',
            'aceite|soja',
            'aceite|maiz',
            'aceite|girasol',
            'refinad',
            'frit',
            'embutid',
            'salchich',
            'choriz',
            'salam',
            'tocin',
            'pancet',
            'procesad',
            'ultraproces',
            'alcohol'
        ])->against($ingredient->name);
        if ($keyWord->run()) {
            return true;
        }

        return false;
    }
}
