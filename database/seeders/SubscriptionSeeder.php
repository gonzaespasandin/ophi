<?php

namespace Database\Seeders;

use App\Models\Subscription;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       Subscription::insert([
        [
            'user_id'    => 1,
            'plan'       => 'free',
            'amount'     => 0,
            'expires_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'user_id'    => 2,
            'plan'       => 'premium',
            'amount'     => 4999,
            'expires_at' => now()->addDays(30),
            'created_at' => now(),
            'updated_at' => now(),
        ],
    ]);
    }
}
