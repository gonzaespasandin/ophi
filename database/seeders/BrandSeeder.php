<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('brands')->insert([
           [
               'id' => 1,
               'name' => 'Granja del Sol'
           ],
            [
                'id' => 2,
                'name' => 'Baguette Artesanal'
            ],
            [
                'id' => 3,
                'name' => 'Granola Feliz'
            ],
            [
                'id' => 4,
                'name' => 'Dulce Tentación'
            ],
            [
                'id' => 5,
                'name' => 'Healthy Life',
            ],
            [
                'id' => 6,
                'name' => 'Biscuit Factory'
            ],
            [
                'id' => 7,
                'name' => 'Terrabusi'
            ]
        ]);
    }
}
