<?php

namespace App\Services;

use App\Models\History;
use App\Models\HistoryResult;
use App\Models\Profile;
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
    /**
     * Everything a scan needs to render a row: the brand travels with the
     * product because both the home and the history link to
     * /product/{name}/{brand}.
     */
    private const SCAN_RELATIONS = ['product.brand', 'results.profile'];

   static public function index()
    {
        // ---------- Chequeo de premuium 
        $user = User::with(['subscription'])
        ->find(Auth::id());
        if(!$user->isPremium()) {
            $history = History::with(self::SCAN_RELATIONS)
                ->where('user_id', Auth::user()->id)
                ->orderBy('scanned_at', 'desc')
                ->limit(10)
                ->get();
            return $history;
        }
        // ----------
        $history = History::with(self::SCAN_RELATIONS)
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
            'scan_img' => $data['scan_img'] ?? null,
        ]);

        foreach ($data['results'] as $result) {
            $profile = Profile::find($result['profile_id']);
            $semanticResult = $result['result'] ?? (
                ($result['is_safe'] ?? null) === null
                    ? HistoryResult::RESULT_UNKNOWN
                    : ((bool) $result['is_safe'] ? HistoryResult::RESULT_SAFE : HistoryResult::RESULT_UNSAFE)
            );

            HistoryResult::create([
                'scan_history_id' => $history->id,
                'profile_id' => $result['profile_id'],
                'scanned_at' => $history->scanned_at,
                'profile_name' => $profile?->name,
                'profile_avatar' => $profile?->avatar,
                'result' => $semanticResult,
                'unsafe_ingredients' => $result['unsafe_ingredients'] ?? [],
            ]);
        }

        DB::commit();

        $history->load(['product', 'results.profile']);

        return $history;
    }

    static public function getLatestScans()
    {
        $history = History::with(self::SCAN_RELATIONS)
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
        $history = History::with(self::SCAN_RELATIONS)
                ->where('user_id', Auth::user()->id)
                 ->whereHas('product', function ($query) use ($name) {
                    $query->where('name', 'like', "%$name%");
                })
                ->limit(10)
                ->get();
        return $history;
    }
}
