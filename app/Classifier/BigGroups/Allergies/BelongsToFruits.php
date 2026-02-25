<?php

namespace App\Classifier\BigGroups\Allergies;

use App\Classifier\Contracts\ClassifierInterface;
use App\Classifier\Strategies\ExactMatchStrategy;
use App\Classifier\Strategies\KeyWordStrategy;
use App\Models\Ingredient;
use Illuminate\Support\Str;

class BelongsToFruits implements ClassifierInterface {
    public static string $name = "Frutas";

    public static function classify(Ingredient $ingredient): bool {
        $exact = ExactMatchStrategy::words([
            'manzana',
            'pera',
            'banana',
            'platano',
            'naranja',
            'mandarina',
            'limon',
            'lima',
            'pomelo',
            'toronja',
            'mango',
            'papaya',
            'anana',
            'piña',
            'kiwi',
            'frutilla',
            'fresa',
            'frambuesa',
            'mora',
            'arandano',
            'cereza',
            'ciruela',
            'durazno',
            'melocoton',
            'nectarina',
            'damasco',
            'albaricoque',
            'higo',
            'granada',
            'uva',
            'melon',
            'sandia',
            'maracuya',
            'parchita',
            'guayaba',
            'membrillo',
            'caqui',
            'palta',
            'aguacate',
            'coco',
            'datil',
            'tamarindo',
            'carambola',
            'lichi',
            'pitahaya',
            'fruta',
            'jugo',
            'zumo',
            'nectar',
            'pure',
            'mermelada',
            'confitura',
            'almibar',
            'fruta seca',
            'fruta deshidratada',
            'concentrado de fruta',
            'extracto de fruta',
            'pulpa'
        ])->against($ingredient->name);
        if ($exact->run()) {
            return true;
        }

        $keyWord = KeyWordStrategy::rules([
            'fruta',
            'manzan',
            'per',
            'banan',
            'platan',
            'naranj',
            'mandarin',
            'limon',
            'lima',
            'pomel',
            'toronj',
            'mang',
            'papay',
            'anan',
            'piñ',
            'kiw',
            'frutill',
            'fres',
            'frambues',
            'mor',
            'arandan',
            'cerez',
            'ciruel',
            'durazn',
            'melocot',
            'nectarin',
            'damasc',
            'albaricoq',
            'hig',
            'granad',
            'uv',
            'melon',
            'sand',
            'maracuy',
            'guayab',
            'membrill',
            'caqu',
            'palt',
            'aguacat',
            'coc',
            'datil',
            'tamarind',
            'carambol',
            'lich',
            'pitahay',
            'jugo',
            'zumo',
            'nectar',
            'pure',
            'mermelad',
            'confitur',
            'almibar',
            'deshidrat',
            'concentrado',
            'extracto de fruta',
            'pulpa'
        ])->against($ingredient->name);
        if ($keyWord->run()) {
            return true;
        }

        return false;
    }
}
