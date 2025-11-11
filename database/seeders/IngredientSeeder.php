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
            /** MAIN GROUPS */
            [
                'id' => 1,
                'name' => 'Intolerancias',
                'icon' => null,
                'is_group' => 1
            ],
            [
                'id' => 2,
                'name' => 'Alergias',
                'icon' => null,
                'is_group' => 1
            ],
            [
                'id' => 3,
                'name' => 'Dietas especiales',
                'icon' => null,
                'is_group' => 1
            ],



            /** INTOLERANCES (GROUPS) */
            [
                'id' => 4,
                'name' => 'Histamina',
                'icon' => null,
                'is_group' => 1
            ],
            [
                'id' => 5,
                'name' => 'Lactosa',
                'icon' => null,
                'is_group' => 1
            ],
            [
                'id' => 6,
                'name' => 'Fructosa',
                'icon' => null,
                'is_group' => 1
            ],
            [
                'id' => 7,
                'name' => 'Gluten',
                'icon' => null,
                'is_group' => 1
            ],
            [
                'id' => 8,
                'name' => 'Sorbitol',
                'icon' => null,
                'is_group' => 1
            ],
            [
                'id' => 9,
                'name' => 'Low-FODMAP',
                'icon' => null,
                'is_group' => 1
            ],
            [
                'id' => 10,
                'name' => 'Salicilato',
                'icon' => null,
                'is_group' => 1
            ],
            [
                'id' => 11,
                'name' => 'Tiramina',
                'icon' => null,
                'is_group' => 1
            ],
            [
                'id' => 12,
                'name' => 'Manitol',
                'icon' => null,
                'is_group' => 1
            ],
            [
                'id' => 13,
                'name' => 'Xilitol',
                'icon' => null,
                'is_group' => 1
            ],
            [
                'id' => 14,
                'name' => 'Oligosacáridos',
                'icon' => null,
                'is_group' => 1
            ],
            [
                'id' => 15,
                'name' => 'Fructanos',
                'icon' => null,
                'is_group' => 1
            ],
            [
                'id' => 16,
                'name' => 'Saracosa',
                'icon' => null,
                'is_group' => 1
            ],
            [
                'id' => 17,
                'name' => 'Glucosa',
                'icon' => null,
                'is_group' => 1
            ],
            [
                'id' => 18,
                'name' => 'Anhídrido sulfuroso y sulfitos',
                'icon' => null,
                'is_group' => 1
            ],



            /** ALLERGIES (GROUPS) */
            [
                'id' => 19,
                'name' => 'Frutas',
                'icon' => null,
                'is_group' => 1
            ],
            [
                'id' => 20,
                'name' => 'Verduras',
                'icon' => null,
                'is_group' => 1
            ],
            [
                'id' => 21,
                'name' => 'Cereales',
                'icon' => null,
                'is_group' => 1
            ],
            [
                'id' => 22,
                'name' => 'Nueces y semillas',
                'icon' => null,
                'is_group' => 1
            ],
            [
                'id' => 23,
                'name' => 'Productos de origen animal',
                'icon' => null,
                'is_group' => 1
            ],
            [
                'id' => 24,
                'name' => 'Caseína',
                'icon' => null,
                'is_group' => 1
            ],
            [
                'id' => 25,
                'name' => 'Carne',
                'icon' => null,
                'is_group' => 1
            ],
            [
                'id' => 26,
                'name' => 'Pescado',
                'icon' => null,
                'is_group' => 1
            ],
            [
                'id' => 27,
                'name' => 'Especias',
                'icon' => null,
                'is_group' => 1
            ],
            [
                'id' => 28,
                'name' => 'Aditivos',
                'icon' => null,
                'is_group' => 1
            ],



            /** SPECIAL DIETS (GROUPS) */
            [
                'id' => 29,
                'name' => 'Pescadores',
                'icon' => null,
                'is_group' => 1
            ],
            [
                'id' => 30,
                'name' => 'Vegetariano',
                'icon' => null,
                'is_group' => 1
            ],
            [
                'id' => 31,
                'name' => 'Vegano',
                'icon' => null,
                'is_group' => 1
            ],
            [
                'id' => 32,
                'name' => 'Sin azúcar',
                'icon' => null,
                'is_group' => 1
            ],
            [
                'id' => 33,
                'name' => 'Sin alcohol',
                'icon' => null,
                'is_group' => 1
            ],
            [
                'id' => 34,
                'name' => 'Anti-inflamatorio',
                'icon' => null,
                'is_group' => 1
            ],
            [
                'id' => 35,
                'name' => 'Bajo en purinas',
                'icon' => null,
                'is_group' => 1
            ],



            /** FRUITS INGREDIENTS (GROUPS AND SUB-GROUPS) */
            [
                'id' => 36,
                'name' => 'Fruta de hueso',
                'icon' => null,
                'is_group' => 1
            ],
            [
                'id' => 37,
                'name' => 'Fruta con semilla',
                'icon' => null,
                'is_group' => 1
            ],
            [
                'id' => 38,
                'name' => 'Cítricos',
                'icon' => null,
                'is_group' => 1
            ],
            [
                'id' => 39,
                'name' => 'Manzana',
                'icon' => null,
                'is_group' => 0
            ],
            [
                'id' => 40,
                'name' => 'Pera',
                'icon' => null,
                'is_group' => 0
            ],
            [
                'id' => 41,
                'name' => 'Banana',
                'icon' => null,
                'is_group' => 0
            ]
        ]);
    }
}
