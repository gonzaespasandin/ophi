<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\History;
use App\Models\HistoryResult;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HistoryController extends Controller
{
    public function index() {
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
            return response()->json($history);
        }
        // ---------- 
        $history = History::with([
            'product', 'results.profile'
            ])
            ->where('user_id', Auth::user()->id)
            ->orderBy('scanned_at', 'desc')
            ->paginate(10);

        return response()->json($history);
    }

    public function store(Request $request) {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'results' => 'required|array',
            'results.*.profile_id' => 'required|exists:profiles,id',
            'results.*.is_safe' => 'required|boolean',
            'results.*.unsafe_ingredients' => 'nullable|array',
        ]);


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

            return response()->json([
                'message' => 'Historial creado correctamente',
                'data' => $history,
            ], 201);
    }

    public function getLatestScans() {
        $history = History::with([
                'product', 'results.profile'
                ])
                ->where('user_id', Auth::user()->id)
                ->orderBy('scanned_at', 'desc')
                ->limit(3) 
                ->get();

        return response()->json($history);
    }

    public function countScans() {
        $historyCount = History::
                  where('user_id', Auth::user()->id)
                ->count();
        
        return response()->json($historyCount);
    }

    public function searchByName(string $name) {
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
        
        return response()->json($history);
    }
}
