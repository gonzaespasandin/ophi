<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categories')->insert([
            [
                'id' => 1,
                'name' => 'Pescado',
            ],
            [
                'id' => 2,
                'name' => 'Panadería',
            ],
            [
                'id' => 3,
                'name' => 'Cereales',
            ],
            [
                'id' => 4,
                'name' => 'Repostería',
            ],
            [
                'id' => 5,
                'name' => 'Galletas',
            ]
        ]);
    }
}
