<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IngredientHasIngredientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('ingredient_ingredient')->insertOrIgnore([
            /** INTOLERANCES (parent_id: 1 && child_id 4-18) */
            [
                'parent_id' => 1,
                'child_id' => 4,
            ],
            [
                'parent_id' => 1,
                'child_id' => 5,
            ],
            [
                'parent_id' => 1,
                'child_id' => 6,
            ],
            [
                'parent_id' => 1,
                'child_id' => 7,
            ],
            [
                'parent_id' => 1,
                'child_id' => 8,
            ],
            [
                'parent_id' => 1,
                'child_id' => 9,
            ],
            [
                'parent_id' => 1,
                'child_id' => 10,
            ],
            [
                'parent_id' => 1,
                'child_id' => 11,
            ],
            [
                'parent_id' => 1,
                'child_id' => 12,
            ],
            [
                'parent_id' => 1,
                'child_id' => 13,
            ],
            [
                'parent_id' => 1,
                'child_id' => 14,
            ],
            [
                'parent_id' => 1,
                'child_id' => 15,
            ],
            [
                'parent_id' => 1,
                'child_id' => 16,
            ],
            [
                'parent_id' => 1,
                'child_id' => 17,
            ],
            [
                'parent_id' => 1,
                'child_id' => 18,
            ],
            /** ALLERGIES (parent_id: 2 && child_id 19-28) */
            [
                'parent_id' => 2,
                'child_id' => 19,
            ],
            [
                'parent_id' => 2,
                'child_id' => 20,
            ],
            [
                'parent_id' => 2,
                'child_id' => 21,
            ],
            [
                'parent_id' => 2,
                'child_id' => 22,
            ],
            [
                'parent_id' => 2,
                'child_id' => 23,
            ],
            [
                'parent_id' => 2,
                'child_id' => 24,
            ],
            [
                'parent_id' => 2,
                'child_id' => 25,
            ],
            [
                'parent_id' => 2,
                'child_id' => 26,
            ],
            [
                'parent_id' => 2,
                'child_id' => 27,
            ],
            [
                'parent_id' => 2,
                'child_id' => 28,
            ],



            /** DIETS (parent_id: 3 && child_id 29-35) */
            [
                'parent_id' => 3,
                'child_id' => 29,
            ],
            [
                'parent_id' => 3,
                'child_id' => 30,
            ],
            [
                'parent_id' => 3,
                'child_id' => 31,
            ],
            [
                'parent_id' => 3,
                'child_id' => 32,
            ],
            [
                'parent_id' => 3,
                'child_id' => 33,
            ],
            [
                'parent_id' => 3,
                'child_id' => 34,
            ],
            [
                'parent_id' => 3,
                'child_id' => 35,
            ],

            /** FRUIT ALLERGY SUB-GROUPS */
            [
                'parent_id' => 19,
                'child_id' => 36,
            ],
            [
                'parent_id' => 19,
                'child_id' => 37,
            ],
            [
                'parent_id' => 19,
                'child_id' => 38,
            ],
        ]);
    }
}
