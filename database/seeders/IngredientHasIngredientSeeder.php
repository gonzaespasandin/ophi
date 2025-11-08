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
                'owner_id' => 10,
            ],
            [
                'belongs_to_id' => 3,
                'owner_id' => 10,
            ],
            [
                'belongs_to_id' => 3,
                'owner_id' => 11,
            ],
            [
                'belongs_to_id' => 3,
                'owner_id' => 12,
            ],
            [
                'belongs_to_id' => 4,
                'owner_id' => 13,
            ],
            [
                'belongs_to_id' => 4,
                'owner_id' => 14,
            ],
            [
                'belongs_to_id' => 4,
                'owner_id' => 15,
            ],
            [
                'belongs_to_id' => 5,
                'owner_id' => 16,
            ],
            [
                'belongs_to_id' => 5,
                'owner_id' => 17,
            ],
            [
                'belongs_to_id' => 5,
                'owner_id' => 18,
            ],
            
        ]);
    }
}
