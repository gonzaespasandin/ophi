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
                'barcode' => '7790011223344',
                'rnpa' => '25010584',
                'brand' => 'Granja del Sol',
                'category' => 'Pescado',
                'created_at' => now(),
                'updated_at' => now(),
                // Ingredientes: Merluza (id 12), Huevo (id 2)
            ],
            [
                'id' => 2,
                'name' => 'Yogur sabor natural',
                'name_normalized' => 'yogursabornatural',
                'img' => 'placeholder.jpg',
                'img_alt' => 'vaso de yogur blanco sabor natural',
                'origin' => 'Buenos Aires',
                'barcode' => '7791234567123',
                'rnpa' => '26010585',
                'brand' => 'La Serenísima',
                'category' => 'Lácteo',
                'created_at' => now(),
                'updated_at' => now(),
                // Ingredientes: Yogur (id 15), Leche (id 13)
            ],
            [
                'id' => 3,
                'name' => 'Galletitas con almendras',
                'name_normalized' => 'galletitasconalmendras',
                'img' => 'placeholder.jpg',
                'img_alt' => 'galletitas dulces con trocitos de almendra',
                'origin' => 'Córdoba',
                'barcode' => '7799988776655',
                'rnpa' => '27010586',
                'brand' => 'Bagley',
                'category' => 'Panadería',
                'created_at' => now(),
                'updated_at' => now(),
                // Ingredientes: Almendra (id 17), Huevo (id 2)
            ],
            [
                'id' => 4,
                'name' => 'Queso cremoso',
                'name_normalized' => 'quesocremoso',
                'img' => 'placeholder.jpg',
                'img_alt' => 'pieza de queso cremoso con textura suave',
                'origin' => 'Santa Fe',
                'barcode' => '7795556667778',
                'rnpa' => '28010587',
                'brand' => 'Ilolay',
                'category' => 'Lácteo',
                'created_at' => now(),
                'updated_at' => now(),
                // Ingredientes: Queso (id 14), Leche (id 13)
            ],
            [
                'id' => 5,
                'name' => 'Barra de cereal con nuez',
                'name_normalized' => 'barradecerealconnuez',
                'img' => 'placeholder.jpg',
                'img_alt' => 'barra de cereal con trozos de nuez',
                'origin' => 'Buenos Aires',
                'barcode' => '7792233445566',
                'rnpa' => '29010588',
                'brand' => 'Granix',
                'category' => 'Snack',
                'created_at' => now(),
                'updated_at' => now(),
                // Ingredientes: Nuez (id 16)
            ],
            [
                'id' => 6,
                'name' => 'Budín de vainilla',
                'name_normalized' => 'budindevainilla',
                'img' => 'placeholder.jpg',
                'img_alt' => 'budín de vainilla esponjoso con corteza dorada',
                'origin' => 'Buenos Aires',
                'barcode' => '7795566778899',
                'rnpa' => '29010601',
                'brand' => 'La Serenísima',
                'category' => 'Panificados',
                'created_at' => now(),
                'updated_at' => now(),
                // Ingredientes: Huevo (id 2), Leche (id 13)
            ],
            [
                'id' => 7,
                'name' => 'Salmón ahumado',
                'name_normalized' => 'salmonahumado',
                'img' => 'placeholder.jpg',
                'img_alt' => 'riquisimo salmon ahumado',
                'origin' => 'Buenos Aires',
                'barcode' => '7795566775532',
                'rnpa' => '29010933',
                'brand' => 'La Serenísima',
                'category' => 'Fiambres',
                'created_at' => now(),
                'updated_at' => now(),
                // Ingredientes: Salmón (10)
            ],
        ]);
    }
}
