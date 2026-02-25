<?php

namespace Database\Seeders;


use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB as FacadesDB;

class UserPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FacadesDB::table('user_has_plan')->insert([
            [
                'user_id'    => 1,
                'plan_id'       => 2,
                'amount'     => 4999,
                'expires_at' => now()->addDays(30),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id'    => 2,
                'plan_id'       => 1,
                'amount'     => 0,
                'expires_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id'    => 3,
                'plan_id'       => 1,
                'amount'     => 0,
                'expires_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id'    => 4,
                'plan_id'       => 1,
                'amount'     => 0,
                'expires_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id'    => 5,
                'plan_id'       => 1,
                'amount'     => 0,
                'expires_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id'    => 6,
                'plan_id'       => 1,
                'amount'     => 0,
                'expires_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id'    => 7,
                'plan_id'       => 1,
                'amount'     => 0,
                'expires_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id'    => 8,
                'plan_id'       => 1,
                'amount'     => 0,
                'expires_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id'    => 9,
                'plan_id'       => 1,
                'amount'     => 0,
                'expires_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id'    => 10,
                'plan_id'       => 1,
                'amount'     => 0,
                'expires_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ]);
    }
}
