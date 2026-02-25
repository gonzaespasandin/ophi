<?php

namespace App\Classifier\BigGroups\SpecialDiets;

use App\Classifier\Contracts\ClassifierInterface;
use App\Classifier\Strategies\ExactMatchStrategy;
use App\Classifier\Strategies\KeyWordStrategy;
use App\Models\Ingredient;

class BelongsToVegetarian implements ClassifierInterface {
    public static string $name = "Vegetariano";

    public static function classify(Ingredient $ingredient): bool {
        $exact = ExactMatchStrategy::words([
            'carne',
            'res',
            'vaca',
            'ternera',
            'cerdo',
            'chancho',
            'pollo',
            'pavo',
            'cordero',
            'chivo',
            'pescado',
            'atun',
            'salmon',
            'marisco',
            'camaron',
            'langostino',
            'mejillon',
            'almeja',
            'calamar',
            'pulpo',
            'gelatina',
            'grasa animal',
            'manteca animal',
            'caldo de carne',
            'caldo de pollo'
        ])->against($ingredient->name);
        if ($exact->run()) {
            return true;
        }

        $keyWord = KeyWordStrategy::rules([
            'carn',
            'res',
            'vac',
            'terner',
            'cerd',
            'chanch',
            'poll',
            'pav',
            'corder',
            'chiv',
            'pescad',
            'atun',
            'salmon',
            'marisc',
            'camaron',
            'langostin',
            'mejillon',
            'almej',
            'calamar',
            'pulp',
            'gelatin',
            'grasa|animal',
            'caldo|carn',
            'caldo|poll'
        ])->against($ingredient->name);
        if ($keyWord->run()) {
            return true;
        }

        return false;
    }
}
