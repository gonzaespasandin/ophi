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
               'name' => 'Crustáceos',
               'icon' => null,
               'is_group' => 1
            ],
            [
                'id' => 2,
                'name' => 'Huevo',
                'icon' => null,
                'is_group' => 0
            ],
            [
                'id' => 3,
                'name' => 'Pescado',
                'icon' => null,
                'is_group' => 1
            ],
            [
                'id' => 4,
                'name' => 'Lácteos',
                'icon' => null,
                'is_group' => 1
            ],
            [
                'id' => 5,
                'name' => 'Frutos secos',
                'icon' => null,
                'is_group' => 1
            ],
            [
                'id' => 7,
                'name' => 'Cangrejo',
                'icon' => null,
                'is_group' => 0
            ],
            [
                'id' => 8,
                'name' => 'Langosta',
                'icon' => null,
                'is_group' => 0
            ],
            [
                'id' => 9,
                'name' => 'Camarón',
                'icon' => null,
                'is_group' => 0
            ],
            [
                'id' => 10,
                'name' => 'Salmón',
                'icon' => null,
                'is_group' => 0
            ],
            [
                'id' => 11,
                'name' => 'Atún',
                'icon' => null,
                'is_group' => 0
            ],
            [
                'id' => 12,
                'name' => 'Merluza',
                'icon' => null,
                'is_group' => 0
            ],
            [
                'id' => 13,
                'name' => 'Leche',
                'icon' => null,
                'is_group' => 0
            ],
            [
                'id' => 14,
                'name' => 'Queso',
                'icon' => null,
                'is_group' => 0
            ],
            [
                'id' => 15,
                'name' => 'Yogur',
                'icon' => null,
                'is_group' => 0
            ],
            [
                'id' => 16,
                'name' => 'Nuez',
                'icon' => null,
                'is_group' => 0
            ],
            [
                'id' => 17,
                'name' => 'Almendra',
                'icon' => null,
                'is_group' => 0
            ],
            [
                'id' => 18,
                'name' => 'Avellana',
                'icon' => null,
                'is_group' => 0
            ],
        ]);
    }
}
