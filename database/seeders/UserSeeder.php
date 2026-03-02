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
                'email' => 'testuser3@test.com',
                'name' => 'Test User 3',
                'password' => Hash::make('password'),
                'role' => 'user',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'email' => 'testuser4@test.com',
                'name' => 'Test User 4',
                'password' => Hash::make('password'),
                'role' => 'user',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'email' => 'testuser5@test.com',
                'name' => 'Test User 5',
                'password' => Hash::make('password'),
                'role' => 'user',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'email' => 'testuser6@test.com',
                'name' => 'Test User 6',
                'password' => Hash::make('password'),
                'role' => 'user',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 7,
                'email' => 'testuser7@test.com',
                'name' => 'Test User 7',
                'password' => Hash::make('password'),
                'role' => 'user',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 8,
                'email' => 'testuser8@test.com',
                'name' => 'Test User 8',
                'password' => Hash::make('password'),
                'role' => 'user',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 9,
                'email' => 'testuser9@test.com',
                'name' => 'Test User 9',
                'password' => Hash::make('password'),
                'role' => 'user',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 10,
                'email' => 'testuser10@test.com',
                'name' => 'Test User 10',
                'password' => Hash::make('password'),
                'role' => 'user',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 11,
                'email' => 'testuser11@test.com',
                'name' => 'Test User 11',
                'password' => Hash::make('password'),
                'role' => 'user',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Credenciales docentes
            [
                'id' => 12,
                'email' => 'carina@davinci.edu.ar',
                'name' => 'Carina',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 13,
                'email' => 'cecilia@davinci.edu.ar',
                'name' => 'Cecilia',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 14,
                'email' => 'santiago@davinci.edu.ar',
                'name' => 'Santiago',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 15,
                'email' => 'jorge@davinci.edu.ar',
                'name' => 'Jorge',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 16,
                'email' => 'test1@davinci.edu.ar',
                'name' => 'Test1',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 17,
                'email' => 'test2@davinci.edu.ar',
                'name' => 'Test2',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 18,
                'email' => 'test3@davinci.edu.ar',
                'name' => 'Test3',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 19,
                'email' => 'test4@davinci.edu.ar',
                'name' => 'Test4',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }   
}
