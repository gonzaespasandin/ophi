<?php
// Hardcodeo para testeos <----------------
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
                'brand_id' => 1, // Granja del sol
                'category_id' => 1, // Pescado
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
                'brand_id' => 2, // Baguette Artesanal
                'category_id' => 2, // Panadería
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
                'brand_id' => 3, // 'Granola Feliz'
                'category_id' => 3,
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
                'brand_id' => 4, // 'Dulce Tentación'
                'category_id' => 4, // Repostería
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
                'brand_id' => 5, // 'Healthy Life'
                'category_id' => 3, // Cereales
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
                'brand_id' => 6,
                'category_id' => 5, // Galletas
                'created_at' => now(),
                'updated_at' => now(),
                // Ingredientes: Trigo (39), Avena (40), Sésamo (48)
            ],
            [
                'id' => 7,
                'name' => 'Galletas integrales con avena y sésamo',
                'name_normalized' => 'galletasintegralesconavenaysesamo',
                'img' => 'placeholder.jpg',
                'img_alt' => 'galletas integrales con avena y sésamo',
                'origin' => 'Buenos Aires',
                'barcode' => '7791234567055',
                'rnpa' => '26010595',
                'brand_id' => 7, // Terrabusi
                'category_id' => 5, // Galletas
                'created_at' => now(),
                'updated_at' => now(),
                // Ingredientes: Trigo (39), Avena (40), Sésamo (48)
            ],
            [
                'id' => 8,
                'name' => 'Tortitas sabor chocolate',
                'name_normalized' => 'tortitassaborchocolate',
                'img' => 'placeholder.jpg',
                'img_alt' => 'tortitas sabor chocolate',
                'origin' => 'Buenos Aires',
                'barcode' => '7790040677005',
                'rnpa' => '26010591',
                'brand_id' => 6,
                'category_id' => 5, // Galletas
                'created_at' => now(),
                'updated_at' => now(),
                // Ingredientes: Trigo (39), Azúcar (53), Cacao (54), Aceite de Girasol (55)
            ],
            [
                'id' => 9,
                'name' => 'Coca Cola 500ml',
                'name_normalized' => 'cocacola500ml',
                'img' => 'placeholder.jpg',
                'img_alt' => 'botella de Coca Cola 500ml',
                'origin' => 'Buenos Aires',
                'barcode' => null,
                'rnpa' => '26010600',
                'brand_id' => 1,
                'category_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]

        ]);
    }
}
