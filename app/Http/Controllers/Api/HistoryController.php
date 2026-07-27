<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\History;
use App\Models\HistoryResult;
use App\Models\User;
use App\Services\HistoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HistoryController extends Controller
{
    public function index() {
       $history = HistoryService::index();

        return response()->json($history);
    }

    public function store(Request $request) {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'scan_img' => 'nullable|string|max:255',
            'results' => 'required|array',
            'results.*.profile_id' => 'required|exists:profiles,id',
            'results.*.is_safe' => 'nullable|boolean',
            'results.*.result' => 'nullable|integer|in:0,1,2,3',
            'results.*.unsafe_ingredients' => 'nullable|array',
        ]);


        $history = HistoryService::store($data);

        return response()->json([
            'message' => 'Historial creado correctamente',
            'data' => $history,
        ], 201);
    }

    public function getLatestScans() {
        $history = HistoryService::getLatestScans();
        
        return response()->json($history);
    }

    public function countScans() {
        $historyCount = History::
                  where('user_id', Auth::user()->id)
                ->count();
        
        return response()->json($historyCount);
    }

    public function searchByName(string $name) {
        $history = HistoryService::searchByName($name);
        
        return response()->json($history);
    }
}
