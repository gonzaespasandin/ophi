<?php

namespace App\Services;

use App\Classifier\IngredientClassifier;
use App\Models\Ingredient;
use App\Models\Product;

class ProductIngredientSyncService
{
    /**
     * @param array<int, string> $ingredientNames
     * @return array<int, int>
     */
    public function sync(Product $product, array $ingredientNames): array
    {
        $ids = [];

        foreach ($this->normalizeNames($ingredientNames) as $name) {
            $ingredient = Ingredient::where('name', $name)->first();

            if (! $ingredient) {
                $ingredient = new Ingredient();
                $ingredient->name = $name;
                $ingredient->save();

                IngredientClassifier::runOne($ingredient);
            }

            $ids[] = $ingredient->id;
        }

        $ids = array_values(array_unique($ids));
        $product->ingredients()->sync($ids);

        return $ids;
    }

    /**
     * @param array<int, string> $names
     * @return array<int, string>
     */
    private function normalizeNames(array $names): array
    {
        $normalized = [];

        foreach ($names as $name) {
            foreach ($this->splitIngredientValue($name) as $candidate) {
                $name = Ingredient::normalize($candidate);
                $name = trim($name, " \t\n\r\0\x0B'\"[]");
                $name = preg_replace('/\s+/', ' ', $name);

                if ($name === '' || $name === 'y' || $name === 'e') {
                    continue;
                }

                if (mb_strlen($name) > 100) {
                    $name = trim(mb_substr($name, 0, 100));
                }

                $normalized[] = $name;
            }
        }

        return array_values(array_unique($normalized));
    }

    /**
     * @return array<int, string>
     */
    private function splitIngredientValue(string $value): array
    {
        $value = trim($value);
        $value = trim($value, " \t\n\r\0\x0B[]");

        if ($value === '') {
            return [];
        }

        if (str_contains($value, "','") || str_contains($value, "', '")) {
            return preg_split("/'\\s*,\\s*'/", trim($value, "'")) ?: [];
        }

        if (str_contains($value, '","') || str_contains($value, '", "')) {
            return preg_split('/"\\s*,\\s*"/', trim($value, '"')) ?: [];
        }

        if (mb_strlen($value) > 100 && str_contains($value, ',')) {
            return explode(',', $value);
        }

        return [$value];
    }
}
