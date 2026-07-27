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
                'ingredient_id' => 39,
                'product_id' => 1
            ],
            // Pan integral con avena y semillas (product_id 1)
            ['ingredient_id' => 39, 'product_id' => 2], // Trigo
            ['ingredient_id' => 40, 'product_id' => 2], // Avena
            ['ingredient_id' => 48, 'product_id' => 2], // Sésamo

            // Muesli con leche y almendras (product_id 2)
             // Avena
            ['ingredient_id' => 44, 'product_id' => 3], // Almendras
            ['ingredient_id' => 50, 'product_id' => 3], // Leches

            // Torta de chocolate con huevo y cacahuete (product_id 3)
            ['ingredient_id' => 39, 'product_id' => 4], // Trigo
            ['ingredient_id' => 49, 'product_id' => 4], // Huevos
            ['ingredient_id' => 45, 'product_id' => 4], // Cacahuete

            // Granola con frutos secos y anacardo (product_id 4)
            ['ingredient_id' => 40, 'product_id' => 5], // Avena
            ['ingredient_id' => 47, 'product_id' => 5], // Anacardo
            ['ingredient_id' => 46, 'product_id' => 5], // Avellana

            // Galletas integrales con avena y sésamo (product_id 5)
            ['ingredient_id' => 39, 'product_id' => 6], // Trigo
            ['ingredient_id' => 40, 'product_id' => 6], // Avena
            ['ingredient_id' => 48, 'product_id' => 6], // Sésamo

            // Galletas integrales con avena y sésamo 2 (product_id 5)
            ['ingredient_id' => 39, 'product_id' => 7], // Trigo
            ['ingredient_id' => 40, 'product_id' => 7], // Avena
            ['ingredient_id' => 48, 'product_id' => 7], // Sésamo

            // Galletas integrales con avena y sésamo (product_id 5)
            ['ingredient_id' => 39, 'product_id' => 8], // Trigo
            ['ingredient_id' => 53, 'product_id' => 8], // Azúcar
            ['ingredient_id' => 54, 'product_id' => 8], // Cacao
            ['ingredient_id' => 55, 'product_id' => 8], // Aceite de Griasol
            
        ]);
    }
}
