<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('profiles')->insert([
            [
                'id' => 1,
                'name' => 'Administrador',
                'user_id' => 1,
                'owner_id' => 1,
                'is_main' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Cachito',
                'user_id' => 1,
                'owner_id' => 1,
                'is_main' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Pekenga',
                'user_id' => 1,
                'owner_id' => 1,
                'is_main' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'name' => 'Leito',
                'user_id' => 1,
                'owner_id' => 1,
                'is_main' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'name' => 'Usuario',
                'user_id' => 2,
                'owner_id' => 2,
                'is_main' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
