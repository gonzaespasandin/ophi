<?php

namespace App\Classifier\BigGroups\SpecialDiets;

use App\Classifier\Contracts\ClassifierInterface;
use App\Classifier\Strategies\ExactMatchStrategy;
use App\Classifier\Strategies\KeyWordStrategy;
use App\Models\Ingredient;

class BelongsToPescatarian implements ClassifierInterface {
    public static string $name = "Pescetariana";

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
            'conejo',
            'ciervo',
            'venado',
            'jabali',
            'embutido',
            'jamon',
            'salame',
            'salami',
            'chorizo',
            'longaniza',
            'mortadela',
            'panceta',
            'tocino',
            'bondiola',
            'salchicha',
            'hamburguesa',
            'carne picada',
            'higado',
            'rinon',
            'molleja',
            'tripa',
            'menudo',
            'gelatina',
            'grasa animal',
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
            'conej',
            'cierv',
            'venad',
            'jabal',
            'embutid',
            'jamon',
            'salam',
            'choriz',
            'longaniz',
            'mortadel',
            'pancet',
            'tocin',
            'bondiol',
            'salchich',
            'hamburgues',
            'picad',
            'higad',
            'rinon',
            'mollej',
            'trip',
            'menud',
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
