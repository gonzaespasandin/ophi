<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('plans')->insert([
            [
                'name' => 'free',
                'price_per_month' => 0,
                'price_per_year' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'premium',
                'price_per_month' => 4999,
                'price_per_year' => 4999 * 12,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
