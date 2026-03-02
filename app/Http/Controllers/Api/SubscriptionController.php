<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    public function getSubscription() {
        return Subscription::with('plan')->where('user_id', Auth::id())->first();
    }

    public function givePremium() {
        $user = User::with(['subscription'])
        ->find(Auth::id());
        if($user->isPremium()) {
            // Si ya es premium, no vamos a renovarle el plan gratis.
            return;
        }
        $userId = Auth::id();
        if($userId) {
            DB::transaction(function () use ($userId) {
                $userPlan = Subscription::where('user_id', $userId)->firstOrFail();
                $premiumPlan = Plan::findOrFail(2);
                $userPlan->update([
                    'plan_id' => $premiumPlan->id,
                    'amount' => $premiumPlan->price,
                    'expires_at' => now()->addDays(30),
                ]);
            });
        }
    }

    public function giveFree() {
        $user = User::with(['subscription'])
        ->find(Auth::id());
        if(!$user->isPremium()) {
            // Si NO es premium, no vamos a cancelar un plan que no existe.
            return;
        }
        $userId = Auth::id();
        if($userId) {
            DB::transaction(function () use ($userId) {
                $userPlan = Subscription::where('user_id', $userId)->firstOrFail();
                $premiumPlan = Plan::findOrFail(1);
                $userPlan->update([
                    'plan_id' => $premiumPlan->id,
                    'amount' => $premiumPlan->price,
                ]);
            });
        }
    }
}
