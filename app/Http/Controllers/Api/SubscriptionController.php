<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Payment;
use App\Models\Profile;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    public function getSubscription() {
        return Subscription::with(['plan', 'payment'])->where('user_id', Auth::id())->first();
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
                $payment = Payment::create([
                    'user_id' => $userId,
                    'amount' => $premiumPlan->price_per_month,
                    'type' => 'transaction',
                    'method' => 'manual',
                    'metadata' => ['source' => 'give_premium'],
                ]);

                $userPlan->update([
                    'plan_id' => $premiumPlan->id,
                    'payment_id' => $payment->id,
                    'subscription_start_timestamp' => now(),
                    'subscription_end_timestamp' => now()->addDays(30),
                    'auto_renovate' => false,
                ]);

                $premiumRoleId = DB::table('roles')->where('name', 'premium')->value('id');
                if ($premiumRoleId) {
                    DB::table('role_user')->updateOrInsert([
                        'user_id' => $userId,
                        'role_id' => $premiumRoleId,
                    ]);
                }
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
            DB::transaction(function () use ($userId, $user) {
                $userPlan = Subscription::where('user_id', $userId)->firstOrFail();
                $premiumPlan = Plan::findOrFail(1);
                $userPlan->update([
                    'plan_id' => $premiumPlan->id,
                    'payment_id' => null,
                    'subscription_end_timestamp' => null,
                    'auto_renovate' => false,
                ]);

                $premiumRoleId = DB::table('roles')->where('name', 'premium')->value('id');
                if ($premiumRoleId) {
                    DB::table('role_user')
                        ->where('user_id', $userId)
                        ->where('role_id', $premiumRoleId)
                        ->delete();
                }

                $firstProfile = Profile::where('owner_id', $userId)
                    ->orWhere('user_id', $userId)
                    ->first();

                $profiles = $user->profiles()->where('id', '!=', $firstProfile?->id)->get();
                foreach ($profiles as $profile) {
                    $profile->ingredients()->detach(); 
                    $profile->delete();
                }
                if ($firstProfile) {
                    $user->profiles()->where('id', '!=', $firstProfile->id)->delete();
                }
            });
        }
        
    }
}
