<?php

namespace App\Classifier\BigGroups\Allergies;

use App\Classifier\Contracts\ClassifierInterface;
use App\Classifier\Strategies\ExactMatchStrategy;
use App\Classifier\Strategies\KeyWordStrategy;
use App\Models\Ingredient;

class BelongsToVegetables implements ClassifierInterface {
    public static string $name = "Verduras";

    public static function classify(Ingredient $ingredient): bool {
        $exact = ExactMatchStrategy::words([
            'lechuga',
            'tomate',
            'cebolla',
            'ajo',
            'zanahoria',
            'papa',
            'patata',
            'batata',
            'boniato',
            'calabaza',
            'zapallo',
            'zapallito',
            'zucchini',
            'berenjena',
            'pepino',
            'acelga',
            'espinaca',
            'repollo',
            'col',
            'coliflor',
            'brocoli',
            'bruselas',
            'apio',
            'puerro',
            'remolacha',
            'rabanito',
            'rabano',
            'alcachofa',
            'esparrago',
            'choclo',
            'maiz',
            'arveja',
            'guisante',
            'haba',
            'poroto verde',
            'chaucha',
            'pimiento',
            'morrón',
            'aji',
            'hongo',
            'champiñon',
            'seta',
            'verdura',
            'ensalada',
            'pure de papa',
            'sopa de verduras'
        ])->against($ingredient->name);
        if ($exact->run()) {
            return true;
        }

        $keyWord = KeyWordStrategy::rules([
            'verdura',
            'vegetal',
            'hortaliza',
            'lechug',
            'tomat',
            'ceboll',
            'aj',
            'zanahor',
            'pap',
            'patat',
            'batat',
            'boniat',
            'calabaz',
            'zapall',
            'zucchin',
            'berenjen',
            'pepin',
            'acelg',
            'espinac',
            'repollo',
            'coliflor',
            'brocol',
            'brusel',
            'apio',
            'puerro',
            'remolach',
            'raban',
            'alcachof',
            'esparrag',
            'chocl',
            'maiz',
            'arvej',
            'guisant',
            'haba',
            'poroto',
            'chauch',
            'pimient',
            'morron',
            'aji',
            'hong',
            'champi',
            'seta',
            'ensalad',
            'sopa de verdura',
            'pure de papa'
        ])->against($ingredient->name);
        if ($keyWord->run()) {
            return true;
        }

        return false;
    }
}
