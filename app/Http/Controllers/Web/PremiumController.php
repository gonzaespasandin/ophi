<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class PremiumController extends Controller
{
    public function index() {
        return view('premium.index', [
            'premiumUsers' => User::with('subscription')
                            ->whereHas('subscription', function ($query) {
                                $query->where('plan_id', 2);
                            })
                            ->get(),
        ]);
    }
}
