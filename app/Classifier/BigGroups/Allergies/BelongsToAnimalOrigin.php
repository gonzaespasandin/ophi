<?php

namespace App\Classifier\BigGroups\Allergies;

use App\Classifier\Contracts\ClassifierInterface;
use App\Classifier\Strategies\ExactMatchStrategy;
use App\Classifier\Strategies\KeyWordStrategy;
use App\Models\Ingredient;

class BelongsToAnimalOrigin implements ClassifierInterface {
    public static string $name = "Productos de origen animal";

    public static function classify(Ingredient $ingredient): bool {
        $exact = ExactMatchStrategy::words([
            'carne',
            'pollo',
            'gallina',
            'pavo',
            'cerdo',
            'vaca',
            'res',
            'cordero',
            'chivo',
            'conejo',
            'pescado',
            'atun',
            'salmon',
            'sardina',
            'merluza',
            'anchoa',
            'marisco',
            'camaron',
            'langostino',
            'gamba',
            'mejillon',
            'almeja',
            'ostra',
            'calamar',
            'pulpo',
            'huevo',
            'leche',
            'queso',
            'yogur',
            'manteca',
            'mantequilla',
            'crema',
            'nata',
            'gelatina',
            'miel',
            'jamon',
            'salame',
            'salami',
            'chorizo',
            'mortadela',
            'panceta',
            'tocino',
            'grasa animal',
            'suero',
            'caseina'
        ])->against($ingredient->name);
        if ($exact->run()) {
            return true;
        }

        $keyWord = KeyWordStrategy::rules([
            'carn',
            'pollo',
            'gallin',
            'pavo',
            'cerd',
            'vac',
            'res',
            'corder',
            'chiv',
            'conej',
            'pescad',
            'atun',
            'salmon',
            'sardin',
            'merluz',
            'ancho',
            'marisc',
            'camaron',
            'langostin',
            'gamb',
            'mejillon',
            'almej',
            'ostr',
            'calamar',
            'pulpo',
            'huev',
            'leche',
            'ques',
            'yog',
            'mante',
            'crem',
            'nata',
            'gelatin',
            'miel',
            'jamon',
            'salame',
            'salami',
            'choriz',
            'mortadel',
            'pancet',
            'tocin',
            'grasa animal',
            'suero',
            'casein',
            'lacte'
        ])->against($ingredient->name);
        if ($keyWord->run()) {
            return true;
        }

        return false;
    }
}
