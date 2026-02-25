<?php

namespace App\Classifier\BigGroups\Intolerances;

use App\Classifier\Contracts\ClassifierInterface;
use App\Classifier\Strategies\ExactMatchStrategy;
use App\Classifier\Strategies\KeyWordStrategy;
use App\Models\Ingredient;

class BelongsToSaccharose implements ClassifierInterface {
    public static string $name = "Sacarosa";

    public static function classify(Ingredient $ingredient): bool {
        $exact = ExactMatchStrategy::words([
            'sacarosa',
            'azucar',
            'azucar blanco',
            'azucar rubio',
            'azucar moreno',
            'azucar mascabo',
            'azucar invertido',
            'panela',
            'melaza',
            'almibar',
            'caramelo',
            'jarabe',
            'jarabe de azucar',
            'jarabe de maiz',
            'dulce',
            'mermelada'
        ])->against($ingredient->name);
        if ($exact->run()) {
            return true;
        }

        $keyWord = KeyWordStrategy::rules([
            'sacaros',
            'azucar',
            'panela',
            'melaza',
            'almibar',
            'caramel',
            'jarabe',
            'sirope',
            'dulc',
            'mermelad',
            'glasead',
            'confita',
            'endulz'
        ])->against($ingredient->name);
        if ($keyWord->run()) {
            return true;
        }

        return false;
    }
}
