<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IngredientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /*
         * Ingredient::create([
         *      'id' => 1,
         *      // etc...
         * ]);
         * */

        DB::table('ingredients')->insert([
            [
               'id' => 1,
               'name' => 'Tomate cherry',
               'icon' => 'cherry',
            ],
            [
                'id' => 2,
                'name' => 'Duraznito',
                'icon' => null,
            ]
        ]);
    }
}
