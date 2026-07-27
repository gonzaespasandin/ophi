<?php

namespace App\Services;

use App\Models\User;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class PremiumService {
    static public function getPremiumUsers(): \Illuminate\Database\Eloquent\Collection
    {
        return User::with('subscription.plan')
            ->whereHas('subscription', function ($query) {
                $query->where('plan_id', 2);
            })
            ->get();
    }

    static public function getFreeUsers(): \Illuminate\Database\Eloquent\Collection
    {
        return User::with('subscription.plan')
            ->whereHas('subscription', function ($query) {
                $query->where('plan_id', 1);
            })
            ->get();
    }

    static public function cancelUserPlan(User $user) {
        $user->subscription()?->update([
            'plan_id' => 1,
            'payment_id' => null,
            'subscription_end_timestamp' => null,
            'auto_renovate' => false,
        ]);

        $premiumRoleId = DB::table('roles')->where('name', 'premium')->value('id');
        if ($premiumRoleId) {
            DB::table('role_user')
                ->where('user_id', $user->id)
                ->where('role_id', $premiumRoleId)
                ->delete();
        }
    }

    static public function upgradeUserPlan(User $user, $planId = 2) {
        $payment = Payment::create([
            'user_id' => $user->id,
            'amount' => 4999,
            'type' => 'transaction',
            'method' => 'manual',
            'metadata' => ['source' => 'premium_service'],
        ]);

        $user->subscription()?->update([
            'plan_id' => $planId,
            'payment_id' => $payment->id,
            'subscription_start_timestamp' => now(),
            'subscription_end_timestamp' => now()->addDays(30),
            'auto_renovate' => false,
        ]);

        $premiumRoleId = DB::table('roles')->where('name', 'premium')->value('id');
        if ($premiumRoleId) {
            DB::table('role_user')->updateOrInsert([
                'user_id' => $user->id,
                'role_id' => $premiumRoleId,
            ]);
        }
    }
}
