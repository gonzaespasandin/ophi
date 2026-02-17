<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\PremiumService;
use Illuminate\Http\Request;

class PremiumController extends Controller
{
    public function index() {
        return view('premium.index', [
            'premiumUsers' => PremiumService::getPremiumUsers(),
        ]);
    }

    public function cancel(User $user) {
        PremiumService::cancelUserPlan($user);

        return to_route('admin.premium.index');
    }

    public function give() {
        return view('premium.give', [
            'users' => PremiumService::getFreeUsers(),
        ]);
    }

    public function upgrade(Request $request) {
        $user = User::with('subscription')->findOrFail($request->get('user'));
        PremiumService::upgradeUserPlan($user);

        return to_route('admin.premium.index');
    }
}
