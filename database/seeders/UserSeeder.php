<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'id' => 1,
                'email' => 'admin@asd.asd',
                'name' => 'Administrador',
                'password' => Hash::make('asd.asd'),
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'email' => 'user@asd.asd',
                'name' => 'Usuario',
                'password' => Hash::make('asd.asd'),
                'role' => 'user',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'email' => 'user2@asd.asd',
                'name' => 'Usuario2',
                'password' => Hash::make('asd.asd'),
                'role' => 'user',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'email' => 'user3@asd.asd',
                'name' => 'Usuario3',
                'password' => Hash::make('asd.asd'),
                'role' => 'user',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'email' => 'user4@asd.asd',
                'name' => 'Usuario4',
                'password' => Hash::make('asd.asd'),
                'role' => 'user',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'email' => 'user5@asd.asd',
                'name' => 'Usuario5',
                'password' => Hash::make('asd.asd'),
                'role' => 'user',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 7,
                'email' => 'user6@asd.asd',
                'name' => 'Usuario6',
                'password' => Hash::make('asd.asd'),
                'role' => 'user',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 8,
                'email' => 'user7@asd.asd',
                'name' => 'Usuario7',
                'password' => Hash::make('asd.asd'),
                'role' => 'user',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 9,
                'email' => 'user8@asd.asd',
                'name' => 'Usuario8',
                'password' => Hash::make('asd.asd'),
                'role' => 'user',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 10,
                'email' => 'user9@asd.asd',
                'name' => 'Usuario9',
                'password' => Hash::make('asd.asd'),
                'role' => 'user',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
