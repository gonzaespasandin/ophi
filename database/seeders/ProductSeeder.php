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
                'name' => 'Pañales PAMPERS',
                'name_normalized' => 'Producto 1',
                'img' => 'img/producto1.jpg',
                'img_alt' => 'Producto 1',
                'origin' => 'Argentina',
                'barcode' => '4070071967072',
                'rnpa' => '12345678',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
