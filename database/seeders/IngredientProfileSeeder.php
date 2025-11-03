<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IngredientProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('ingredient_profile')->insert([
            [
                'ingredient_id' => 1,
                'profile_id' => 1,
            ],
            [
                'ingredient_id' => 2,
                'profile_id' => 2,
            ],
            [
                'ingredient_id' => 1,
                'profile_id' => 3,
            ],
            [
                'ingredient_id' => 2,
                'profile_id' => 3,
            ],
            [
                'ingredient_id' => 1,
                'profile_id' => 4,
            ]
        ]);
    }
}
