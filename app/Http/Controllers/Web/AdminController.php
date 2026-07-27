<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\History;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index() {
        return view('admin', [
            'users' => User::count(),
            'products' => Product::count(),
            'usersLastMonth' => self::usersLastMonth(),
            'effectiveScans' => History::count(),
            'premiumUsers' => self::premiumUsers(),
            'totalMoney' => self::totalMoney(),

        ]);
    }

    public function usersPerMonth()
    {
        $data = User::selectRaw('
                COUNT(*) as total,
                DATE_FORMAT(created_at, "%Y-%m") as period
            ')
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        return response()->json([
            'labels' => $data->pluck('period')->map(function ($p) {
                return \Carbon\Carbon::createFromFormat('Y-m', $p)->format('M Y');
            }),
            'data' => $data->pluck('total'),
        ]);
    }

    public function usersLastMonth() {
        $data = User::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();
        return $data;
    }    

    public function premiumUsers() {
        $data = Subscription::where('plan_id', 2)->count();
        return $data;
    }

    public function totalMoney() {
        $data = Payment::sum('amount');
        return $data;
    }
}
