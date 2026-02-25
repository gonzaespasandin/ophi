<?php

namespace App\Classifier\Contracts;

use App\Models\Ingredient;

interface ClassifierInterface {
    public static function classify(Ingredient $ingredient): bool;
}
