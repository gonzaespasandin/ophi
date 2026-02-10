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
                'plan' => 'free',
                'price' => 0,     
                'duration' => 0,    
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'plan' => 'premium',
                'price' => 4999,    
                'duration' => 30,   
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
