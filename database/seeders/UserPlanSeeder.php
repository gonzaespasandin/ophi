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
        $premiumUsers = [1, 12, 13, 14, 15, 16, 17, 18, 19];
        $subscriptions = [];

        for ($userId = 1; $userId <= 19; $userId++) {
            $isPremium = in_array($userId, $premiumUsers, true);

            $subscriptions[] = [
                'user_id' => $userId,
                'plan_id' => $isPremium ? 2 : 1,
                'payment_id' => null,
                'subscription_start_timestamp' => now(),
                'subscription_end_timestamp' => $isPremium ? now()->addDays(30) : null,
                'auto_renovate' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        FacadesDB::table('subscriptions')->insert($subscriptions);

        $premiumRoleId = FacadesDB::table('roles')->where('name', 'premium')->value('id');

        if ($premiumRoleId) {
            foreach ($premiumUsers as $userId) {
                FacadesDB::table('role_user')->updateOrInsert([
                    'user_id' => $userId,
                    'role_id' => $premiumRoleId,
                ]);
            }
        }
    }
}
