<?php

namespace App\Classifier\BigGroups\Intolerances;

use App\Classifier\Contracts\ClassifierInterface;
use App\Classifier\Strategies\ExactMatchStrategy;
use App\Classifier\Strategies\KeyWordStrategy;
use App\Models\Ingredient;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BelongsToHistamine implements ClassifierInterface {
    public static string $name = "Histamina";

    public static function classify(Ingredient $ingredient): bool {
        // By Key Words
        $keyWord = KeyWordStrategy::rules([
            'pescado'
        ])->against($ingredient->name);
        if ($keyWord->run()) {
            return true;
        }

        // By Exact Matches (example: word "sal" against "salsa" will be false
        $exact = ExactMatchStrategy::words(['sal'])->against($ingredient->name);
        if ($exact->run()) {
            return true;
        }

        return false;
    }
}
