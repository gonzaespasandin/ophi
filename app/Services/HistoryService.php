<?php

namespace App\Services;

use App\Models\History;
use App\Models\HistoryResult;
use App\Models\Subscription;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Error;

class HistoryService
{
   static public function index()
    {
        // ---------- Chequeo de premuium 
        $user = User::with(['subscription'])
        ->find(Auth::id());
        if(!$user->isPremium()) {
            $history = History::with([
                'product', 'results.profile'
                ])
                ->where('user_id', Auth::user()->id)
                ->orderBy('scanned_at', 'desc')
                ->limit(10)
                ->get();
            return $history;
        }
        // ---------- 
        $history = History::with([
            'product', 'results.profile'
            ])
            ->where('user_id', Auth::user()->id)
            ->orderBy('scanned_at', 'desc')
            ->paginate(10);

        return $history;
    }

    static public function store(Array $data) 
    {        
        DB::beginTransaction();

        $history = History::create([
            'user_id' => Auth::user()->id,
            'product_id' => $data['product_id'],
            'scanned_at' => now(),
        ]);

        foreach ($data['results'] as $result) {
            HistoryResult::create([
                'history_id' => $history->id,
                'profile_id' => $result['profile_id'],
                'is_safe' => $result['is_safe'],
                'unsafe_ingredients' => $result['unsafe_ingredients'] ?? [],
            ]);
        }

        DB::commit();

        $history->load(['product', 'results.profile']);
    }

    static public function getLatestScans()
    {
        $history = History::with([
                'product', 'results.profile'
                ])
                ->where('user_id', Auth::user()->id)
                ->orderBy('scanned_at', 'desc')
                ->limit(3) 
                ->get();

        return $history;
    }

    static public function searchByName(string $name) {
        $user = User::with(['subscription'])
        ->find(Auth::id());
        if(!$user->isPremium()) {
            return response()->json([
                'message' => 'Usuario no premium'
            ], 403);
        }
        $history = History::with([
                'product', 'results.profile'
                ])
                ->where('user_id', Auth::user()->id)
                 ->whereHas('product', function ($query) use ($name) {
                    $query->where('name', 'like', "%$name%");
                })
                ->limit(10)
                ->get();
        return $history;
    }
}
