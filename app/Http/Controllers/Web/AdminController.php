<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index() {
        return view('admin', [
            'users' => User::count(),
            'products' => Product::count(),
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

    
}
