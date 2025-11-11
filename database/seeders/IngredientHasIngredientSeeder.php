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
        DB::table('ingredient_has_ingredients')->insert([
            /** INTOLERANCES (belongs_to_id: 1 && owner_id 4-18) */
            [
                'belongs_to_id' => 1,
                'owner_id' => 4,
            ],
            [
                'belongs_to_id' => 1,
                'owner_id' => 5,
            ],
            [
                'belongs_to_id' => 1,
                'owner_id' => 6,
            ],
            [
                'belongs_to_id' => 1,
                'owner_id' => 7,
            ],
            [
                'belongs_to_id' => 1,
                'owner_id' => 8,
            ],
            [
                'belongs_to_id' => 1,
                'owner_id' => 9,
            ],
            [
                'belongs_to_id' => 1,
                'owner_id' => 10,
            ],
            [
                'belongs_to_id' => 1,
                'owner_id' => 11,
            ],
            [
                'belongs_to_id' => 1,
                'owner_id' => 12,
            ],
            [
                'belongs_to_id' => 1,
                'owner_id' => 13,
            ],
            [
                'belongs_to_id' => 1,
                'owner_id' => 14,
            ],
            [
                'belongs_to_id' => 1,
                'owner_id' => 15,
            ],
            [
                'belongs_to_id' => 1,
                'owner_id' => 16,
            ],
            [
                'belongs_to_id' => 1,
                'owner_id' => 17,
            ],
            [
                'belongs_to_id' => 1,
                'owner_id' => 18,
            ],



            /** ALLERGIES (belongs_to_id: 2 && owner_id 19-28) */
            [
                'belongs_to_id' => 2,
                'owner_id' => 19,
            ],
            [
                'belongs_to_id' => 2,
                'owner_id' => 20,
            ],
            [
                'belongs_to_id' => 2,
                'owner_id' => 21,
            ],
            [
                'belongs_to_id' => 2,
                'owner_id' => 22,
            ],
            [
                'belongs_to_id' => 2,
                'owner_id' => 23,
            ],
            [
                'belongs_to_id' => 2,
                'owner_id' => 24,
            ],
            [
                'belongs_to_id' => 2,
                'owner_id' => 25,
            ],
            [
                'belongs_to_id' => 2,
                'owner_id' => 26,
            ],
            [
                'belongs_to_id' => 2,
                'owner_id' => 27,
            ],
            [
                'belongs_to_id' => 2,
                'owner_id' => 28,
            ],



            /** ALLERGIES (belongs_to_id: 3 && owner_id 29-35) */
            [
                'belongs_to_id' => 3,
                'owner_id' => 29,
            ],
            [
                'belongs_to_id' => 3,
                'owner_id' => 30,
            ],
            [
                'belongs_to_id' => 3,
                'owner_id' => 31,
            ],
            [
                'belongs_to_id' => 3,
                'owner_id' => 32,
            ],
            [
                'belongs_to_id' => 3,
                'owner_id' => 33,
            ],
            [
                'belongs_to_id' => 3,
                'owner_id' => 34,
            ],
            [
                'belongs_to_id' => 3,
                'owner_id' => 35,
            ],

            /** ALLERGIES SUB-GROUPS */
            [
                'belongs_to_id' => 36,
                'owner_id' => 39,
            ],
            [
                'belongs_to_id' => 36,
                'owner_id' => 40,
            ],
            [
                'belongs_to_id' => 37,
                'owner_id' => 39,
            ],
            [
                'belongs_to_id' => 37,
                'owner_id' => 41,
            ],
            [
                'belongs_to_id' => 37,
                'owner_id' => 40,
            ],
            [
                'belongs_to_id' => 19,
                'owner_id' => 36,
            ],
            [
                'belongs_to_id' => 19,
                'owner_id' => 37,
            ],
            [
                'belongs_to_id' => 19,
                'owner_id' => 38,
            ],
            [
                'belongs_to_id' => 19,
                'owner_id' => 39,
            ],
            [
                'belongs_to_id' => 19,
                'owner_id' => 40,
            ],
            [
                'belongs_to_id' => 19,
                'owner_id' => 41,
            ],
        ]);
    }
}
