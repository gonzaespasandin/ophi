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
                'id' => 1,
                'name' => 'Milanesa de merluza congelada',
                'name_normalized' => 'milanesademerluzacongelada',
                'img' => 'placeholder.jpg',
                'img_alt' => 'milanesa de merluza rebozada lista para freír',
                'origin' => 'Mar del Plata',
                'barcode' => '5901234123457',
                'rnpa' => '25010584',
                'brand' => 'Granja del Sol',
                'category' => 'Pescado',
                'created_at' => now(),
                'updated_at' => now(),
                // Ingredientes: Merluza (id 12), Huevo (id 2)
            ],
            [
                'id' => 2,
                'name' => 'Pan integral con avena y semillas',
                'name_normalized' => 'panintegralconavenaysemillas',
                'img' => 'placeholder.jpg',
                'img_alt' => 'rebanadas de pan integral con avena y semillas',
                'origin' => 'Buenos Aires',
                'barcode' => '7791234567001',
                'rnpa' => '26010586',
                'brand' => 'Baguette Artesanal',
                'category' => 'Panadería',
                'created_at' => now(),
                'updated_at' => now(),
                // Ingredientes: Trigo (39), Avena (40), Sésamo (48)
            ],
            [
                'id' => 3,
                'name' => 'Muesli con leche y almendras',
                'name_normalized' => 'muesliconlecheyalmendras',
                'img' => 'placeholder.jpg',
                'img_alt' => 'bol con muesli, leche y almendras',
                'origin' => 'Buenos Aires',
                'barcode' => '7791234567002',
                'rnpa' => '26010587',
                'brand' => 'Granola Feliz',
                'category' => 'Cereales',
                'created_at' => now(),
                'updated_at' => now(),
                // Ingredientes: Avena (40), Almendras (44), Leches (50)
            ],
           [
                'id' => 4,
                'name' => 'Torta de chocolate con huevo y cacahuete',
                'name_normalized' => 'tortadechocolateconhuevoycacahuete',
                'img' => 'placeholder.jpg',
                'img_alt' => 'porción de torta de chocolate con huevo y cacahuete',
                'origin' => 'Buenos Aires',
                'barcode' => '7791234567003',
                'rnpa' => '26010588',
                'brand' => 'Dulce Tentación',
                'category' => 'Repostería',
                'created_at' => now(),
                'updated_at' => now(),
                // Ingredientes: Trigo (39), Huevos (49), Cacahuete (45)
            ],
            [
                'id' => 5,
                'name' => 'Granola con frutos secos y anacardo',
                'name_normalized' => 'granolaconfrutossecosyanacardo',
                'img' => 'placeholder.jpg',
                'img_alt' => 'bol de granola con frutos secos y anacardo',
                'origin' => 'Buenos Aires',
                'barcode' => '7791234567004',
                'rnpa' => '26010589',
                'brand' => 'Healthy Life',
                'category' => 'Cereales',
                'created_at' => now(),
                'updated_at' => now(),
                // Ingredientes: Avena (40), Anacardo (47), Avellana (46)
            ],
            [
                'id' => 6,
                'name' => 'Galletas integrales con avena y sésamo',
                'name_normalized' => 'galletasintegralesconavenaysesamo',
                'img' => 'placeholder.jpg',
                'img_alt' => 'galletas integrales con avena y sésamo',
                'origin' => 'Buenos Aires',
                'barcode' => '7791234567005',
                'rnpa' => '26010590',
                'brand' => 'Biscuit Factory',
                'category' => 'Galletas',
                'created_at' => now(),
                'updated_at' => now(),
                // Ingredientes: Trigo (39), Avena (40), Sésamo (48)
            ],
        ]);
    }
}
