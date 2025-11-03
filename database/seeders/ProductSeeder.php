<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('products')->insert([
            [
                /* Producto real: Jugo Verao sabor a Ananá */
                'id' => 1,
                'name' => 'Bandejita de tomatitos cherry',
                'name_normalized' => 'bandejita de tomatitos cherry',
                'img' => 'placeholder.jpg',
                'img_alt' => 'una bandejita de tomatitos cherry muy apetitosos',
                'origin' => 'BS.AS',
                'barcode' => '7622201717506',
                'rnpa' => '19010578',
                'brand' => 'Verao',
                'category' => 'Jugo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                /* Producto real: Cepita sabor a durazno */
                'id' => 2,
                'name' => 'Cepita de durazno',
                'name_normalized' => 'cepita de durazno',
                'img' => 'placeholder.jpg',
                'img_alt' => 'botella de litro y medio',
                'origin' => 'BS.AS',
                'barcode' => '7790895648427',
                'rnpa' => '19010573',
                'brand' => 'Cepita',
                'category' => 'Jugo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                /* Este no es real, me lo inventé :) */
                'id' => 3,
                'name' => 'Coca-cola de durazno y tomate cherry',
                'name_normalized' => 'coca cola de durazno y tomate cherry',
                'img' => 'placeholder.jpg',
                'img_alt' => 'botella grande',
                'origin' => 'BS.AS',
                'barcode' => '5690892358612',
                'rnpa' => '19010571',
                'brand' => 'Coca-cola',
                'category' => 'Jugo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                /* Producto real: Jugo Verao sabor a Ananá */
                'id' => 4,
                'name' => 'Bandejita de acelga',
                'name_normalized' => 'bandejita de acelga',
                'img' => 'placeholder.jpg',
                'img_alt' => 'una bandejita de acelga muy apetitosa',
                'origin' => 'BS.AS',
                'barcode' => '7625205217506',
                'rnpa' => '19050578',
                'brand' => 'Verao',
                'category' => 'Jugo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
