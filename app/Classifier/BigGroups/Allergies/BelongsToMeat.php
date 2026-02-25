<?php

namespace App\Classifier\BigGroups\Allergies;

use App\Classifier\Contracts\ClassifierInterface;
use App\Classifier\Strategies\ExactMatchStrategy;
use App\Classifier\Strategies\KeyWordStrategy;
use App\Models\Ingredient;

class BelongsToMeat implements ClassifierInterface {
    public static string $name = "Carne";

    public static function classify(Ingredient $ingredient): bool {
        $exact = ExactMatchStrategy::words([
            'carne',
            'res',
            'vaca',
            'ternera',
            'novillo',
            'cerdo',
            'chancho',
            'pollo',
            'gallina',
            'pavo',
            'cordero',
            'oveja',
            'chivo',
            'cabra',
            'conejo',
            'ciervo',
            'venado',
            'jabali',
            'pato',
            'oca',
            'codorniz',
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
            'menudo'
        ])->against($ingredient->name);
        if ($exact->run()) {
            return true;
        }

        $keyWord = KeyWordStrategy::rules([
            'carn',
            'res',
            'vac',
            'terner',
            'novill',
            'cerd',
            'chanch',
            'poll',
            'gallin',
            'pav',
            'corder',
            'ovej',
            'chiv',
            'cabr',
            'conej',
            'cierv',
            'venad',
            'jabal',
            'pat',
            'oc',
            'codorniz',
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
            'menud'
        ])->against($ingredient->name);
        if ($keyWord->run()) {
            return true;
        }

        return false;
    }
}
