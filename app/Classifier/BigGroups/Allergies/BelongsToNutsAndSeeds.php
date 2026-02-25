<?php

namespace App\Classifier\BigGroups\Allergies;

use App\Classifier\Contracts\ClassifierInterface;
use App\Classifier\Strategies\ExactMatchStrategy;
use App\Classifier\Strategies\KeyWordStrategy;
use App\Models\Ingredient;

class BelongsToNutsAndSeeds implements ClassifierInterface {
    public static string $name = "Nueces y semillas";

    public static function classify(Ingredient $ingredient): bool {
        $exact = ExactMatchStrategy::words([
            'nuez',
            'nueces',
            'almendra',
            'almendras',
            'mani',
            'cacahuate',
            'cacahuete',
            'avellana',
            'avellanas',
            'castana',
            'castanas',
            'castana de caju',
            'anacardo',
            'pistacho',
            'pistachos',
            'nuez pecan',
            'nuez de brasil',
            'macadamia',
            'semilla',
            'semillas',
            'chia',
            'lino',
            'linaza',
            'sesamo',
            'ajonjoli',
            'girasol',
            'calabaza',
            'zapallo',
            'amapola'
        ])->against($ingredient->name);
        if ($exact->run()) {
            return true;
        }

        $keyWord = KeyWordStrategy::rules([
            'nuez',
            'almendr',
            'mani',
            'cacahu',
            'avellan',
            'castan',
            'caju',
            'anacard',
            'pistach',
            'pecan',
            'brasil',
            'macadami',
            'semill',
            'chia',
            'lin',
            'sesam',
            'ajonjol',
            'girasol',
            'calabaz',
            'zapall',
            'amapol'
        ])->against($ingredient->name);
        if ($keyWord->run()) {
            return true;
        }

        return false;
    }
}
