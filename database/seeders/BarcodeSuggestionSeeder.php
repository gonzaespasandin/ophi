<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class BarcodeSuggestionSeeder extends Seeder
{
    public function run(): void
    {
        // 9 usuarios de prueba (el admin será el 10mo que confirma y dispara la auto-aprobación)
        $testUsers = [];
        for ($i = 3; $i <= 11; $i++) {
            $testUsers[] = [
                'id'         => $i,
                'email'      => "testuser{$i}@test.com",
                'name'       => "Test User {$i}",
                'password'   => Hash::make('password'),
                'role'       => 'user',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('users')->insert($testUsers);

        // Sugerencia: código 0123456789012 → Coca Cola 500ml (product_id: 9)
        // El primer usuario (id: 3) es quien la sugirió originalmente
        DB::table('barcode_suggestions')->insert([
            'id'                   => 1,
            'barcode'              => '0123456789012',
            'product_id'           => 9,
            'suggested_by_user_id' => 3,
            'status'               => 'pending',
            'created_at'           => now(),
            'updated_at'           => now(),
        ]);

        // 9 confirmaciones (usuarios 3 al 11)
        $confirmations = [];
        for ($i = 3; $i <= 11; $i++) {
            $confirmations[] = [
                'barcode_suggestion_id' => 1,
                'user_id'               => $i,
                'created_at'            => now(),
                'updated_at'            => now(),
            ];
        }
        DB::table('barcode_suggestions_confirmations')->insert($confirmations);
    }
}
