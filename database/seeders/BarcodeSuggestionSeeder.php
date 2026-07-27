<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class BarcodeSuggestionSeeder extends Seeder
{
    public function run(): void
    {
        // Sugerencia: código 0123456789012 → Coca Cola 500ml (product_id: 9)
        DB::table('barcode_suggestions')->insert([
            'id'                   => 1,
            'barcode'              => '7792390620700',
            'product_id'           => 1886,
            'suggested_by_user_id' => 3,
            'status'               => 'pending',
            'created_at'           => now(),
            'updated_at'           => now(),
        ]);

        $confirmations = [];
        for ($i = 3; $i <= 11; $i++) {
            $confirmations[] = [
                'barcode_suggestion_id' => 1,
                'user_id'               => $i,
                'created_at'            => now(),
                'updated_at'            => now(),
            ];
        }
        DB::table('barcode_suggestion_scans')->insert($confirmations);
    }
}
