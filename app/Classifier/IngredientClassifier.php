<?php

namespace App\Classifier;

use App\Classifier\BigGroups\Allergies\BelongsToAdditives;
use App\Classifier\BigGroups\Allergies\BelongsToAnimalOrigin;
use App\Classifier\BigGroups\Allergies\BelongsToCasein;
use App\Classifier\BigGroups\Allergies\BelongsToCereals;
use App\Classifier\BigGroups\Allergies\BelongsToFish;
use App\Classifier\BigGroups\Allergies\BelongsToFruits;
use App\Classifier\BigGroups\Allergies\BelongsToMeat;
use App\Classifier\BigGroups\Allergies\BelongsToNutsAndSeeds;
use App\Classifier\BigGroups\Allergies\BelongsToSpices;
use App\Classifier\BigGroups\Allergies\BelongsToVegetables;
use App\Classifier\BigGroups\Intolerances\BelongsToFructans;
use App\Classifier\BigGroups\Intolerances\BelongsToFructose;
use App\Classifier\BigGroups\Intolerances\BelongsToGlucose;
use App\Classifier\BigGroups\Intolerances\BelongsToGluten;
use App\Classifier\BigGroups\Intolerances\BelongsToHistamine;
use App\Classifier\BigGroups\Intolerances\BelongsToLactose;
use App\Classifier\BigGroups\Intolerances\BelongsToLowInFODMAP;
use App\Classifier\BigGroups\Intolerances\BelongsToMannitol;
use App\Classifier\BigGroups\Intolerances\BelongsToOligosaccharides;
use App\Classifier\BigGroups\Intolerances\BelongsToSaccharose;
use App\Classifier\BigGroups\Intolerances\BelongsToSalicylate;
use App\Classifier\BigGroups\Intolerances\BelongsToSorbitol;
use App\Classifier\BigGroups\Intolerances\BelongsToSulfites;
use App\Classifier\BigGroups\Intolerances\BelongsToTyramine;
use App\Classifier\BigGroups\Intolerances\BelongsToXylitol;
use App\Classifier\BigGroups\SpecialDiets\BelongsToAlcoholFree;
use App\Classifier\BigGroups\SpecialDiets\BelongsToAntiInflammatory;
use App\Classifier\BigGroups\SpecialDiets\BelongsToLowInPurines;
use App\Classifier\BigGroups\SpecialDiets\BelongsToPescatarian;
use App\Classifier\BigGroups\SpecialDiets\BelongsToSugarFree;
use App\Classifier\BigGroups\SpecialDiets\BelongsToVegan;
use App\Classifier\BigGroups\SpecialDiets\BelongsToVegetarian;
use App\Models\Ingredient;
use Illuminate\Support\Facades\Log;

class IngredientClassifier {
    public static function run($console) {
        $ingredients = Ingredient::where('is_group', 0)
            ->whereDoesntHave('parents')
            ->get();

        $console->withProgressBar($ingredients, function (Ingredient $ingredient) use ($console) {
            $belongs_to_array = self::searchMatches($ingredient);

            $ingredient->parents()->syncWithoutDetaching($belongs_to_array);
        });
    }

    public static function runOne($ingredient) {
        $belongs_to_array = self::searchMatches($ingredient);
        $ingredient->parents()->syncWithoutDetaching($belongs_to_array);
    }

    public static function searchMatches(Ingredient $ingredient): array {
        $result = [];
        $idsDictionary = Ingredient::getRegisterFormIngredientIds();
        $possibleMatches = [
            // Intolerances
            BelongsToHistamine::class, BelongsToLactose::class, BelongsToFructose::class, BelongsToGluten::class, BelongsToSorbitol::class, BelongsToLowInFODMAP::class, BelongsToSalicylate::class, BelongsToTyramine::class, BelongsToMannitol::class, BelongsToXylitol::class, BelongsToOligosaccharides::class, BelongsToFructans::class, BelongsToSaccharose::class, BelongsToGlucose::class, BelongsToSulfites::class,
            // Allergies
            BelongsToFruits::class, BelongsToVegetables::class, BelongsToCereals::class, BelongsToNutsAndSeeds::class, BelongsToAnimalOrigin::class, BelongsToCasein::class, BelongsToMeat::class, BelongsToFish::class, BelongsToSpices::class, BelongsToAdditives::class,
            // Special Diets
            BelongsToPescatarian::class, BelongsToVegetarian::class, BelongsToVegan::class, BelongsToSugarFree::class, BelongsToAlcoholFree::class, BelongsToAntiInflammatory::class, BelongsToLowInPurines::class,
        ];

        foreach ($possibleMatches as $match) {
            if ($match::classify($ingredient)) {
                $result[] = $idsDictionary[$match::$name];
            }
        }

        return $result;
    }
}
