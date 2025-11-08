<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IngredientProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('ingredient_product')->insert([
            [
                'ingredient_id' => 12,
                'product_id' => 1,
            ],
            [
                'ingredient_id' => 2,
                'product_id' => 1,
            ],
            [
                'ingredient_id' => 15,
                'product_id' => 2,
            ],
            [
                'ingredient_id' => 13,
                'product_id' => 2,
            ],
            [
                'ingredient_id' => 17,
                'product_id' => 3,
            ],
            [
                'ingredient_id' => 2,
                'product_id' => 3,
            ],
            [
                'ingredient_id' => 13,
                'product_id' => 4,
            ],
            [
                'ingredient_id' => 14,
                'product_id' => 4,
            ],
            [
                'ingredient_id' => 16,
                'product_id' => 5,
            ],
            [
                'ingredient_id' => 2,
                'product_id' => 6,
            ],
            [
                'ingredient_id' => 13,
                'product_id' => 6,
            ],
            [
                'ingredient_id' => 10,
                'product_id' => 7,
            ],
        ]);
    }
}
