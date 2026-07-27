<?php

namespace App\Classifier\BigGroups\SpecialDiets;

use App\Classifier\Contracts\ClassifierInterface;
use App\Classifier\Strategies\ExactMatchStrategy;
use App\Classifier\Strategies\KeyWordStrategy;
use App\Models\Ingredient;

class BelongsToSugarFree implements ClassifierInterface {
    public static string $name = "Sin azúcar";

    public static function classify(Ingredient $ingredient): bool {
        $exact = ExactMatchStrategy::words([
            'azucar',
            'azucar blanca',
            'azucar morena',
            'azucar integral',
            'azucar mascabo',
            'azucar invertido',
            'sacarosa',
            'glucosa',
            'fructosa',
            'jarabe de glucosa',
            'jarabe de fructosa',
            'jarabe de maiz',
            'jarabe de maiz alta fructosa',
            'miel',
            'melaza',
            'panela',
            'dextrosa',
            'maltosa',
            'lactosa',
            'galactosa',
            'caramelo'
        ])->against($ingredient->name);
        if ($exact->run()) {
            return true;
        }

        $keyWord = KeyWordStrategy::rules([
            'azucar',
            'sacaros',
            'glucos',
            'fructos',
            'jarabe',
            'sirope',
            'miel',
            'melaz',
            'panel',
            'dextros',
            'maltos',
            'lactos',
            'galactos',
            'caramel',
            'nectar',
            'concentrado de fruta',
            'jugo concentrado'
        ])->against($ingredient->name);
        if ($keyWord->run()) {
            return true;
        }

        return false;
    }
}
