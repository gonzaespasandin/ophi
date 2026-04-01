<?php

namespace App\Services;

use App\Models\User;

class PremiumService {
    static public function getPremiumUsers(): \Illuminate\Database\Eloquent\Collection
    {
        return User::with('subscription')
            ->whereHas('subscription', function ($query) {
                $query->where('plan_id', 2);
            })
            ->get();
    }

    static public function getFreeUsers(): \Illuminate\Database\Eloquent\Collection
    {
        return User::with('subscription')
            ->whereHas('subscription', function ($query) {
                $query->where('plan_id', 1);
            })
            ->get();
    }

    static public function cancelUserPlan(User $user) {
        $user->subscription()?->update([
            'plan_id' => 1,
            'amount' => 0,
        ]);
    }

    static public function upgradeUserPlan(User $user, $planId = 2) {
        $user->subscription()?->update([
            'plan_id' => $planId,
            'amount' => 4999,
            'expires_at' => now()->addDays(30),
        ]);
    }
}
