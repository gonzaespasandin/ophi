<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BarcodeSuggestionService;
use App\Services\ScannerService;
use Illuminate\Http\Request;

class ScannerController extends Controller
{
    protected $ScannerService;
    protected $BarcodeSuggestionService;

    public function __construct(ScannerService $ScannerService, BarcodeSuggestionService $BarcodeSuggestionService)
    {
        $this->ScannerService = $ScannerService;
        $this->BarcodeSuggestionService = $BarcodeSuggestionService;
    }

    public function process(Request $request) {
        $request->validate([
            'codigo' => 'required|string|max:15',
        ]);

        $result = $this->ScannerService->process_code($request->codigo);

        if (!$result) {
            $this->BarcodeSuggestionService->savePendingBarcode($request->codigo, $request->user()->id);
            return response()->json(['message' => 'Código no encontrado'], 404);
        }

        return response()->json($result);
    }

    public function canSuggest(Request $request){
        $canSuggest = $this->BarcodeSuggestionService->canSuggest(
            $request->user()->id,
            $request->query('barcode'),
            $request->query('product_id') ? (int) $request->query('product_id') : null
        );
        return response()->json(['can_suggest' => $canSuggest]);
    }

    public function suggest(Request $request){
        $request->validate([
            'barcode' => 'required|string|max:15',
            'product_id' => 'required|integer',
        ]);

        $result = $this->BarcodeSuggestionService->suggest($request->barcode, $request->product_id, $request->user()->id);
        
        $status = $result['success'] ? 200 : 422;
        return response()->json($result, $status);
    }

    public function getPendingBarcode(Request $request){
        $barcode = $this->BarcodeSuggestionService->getPendingBarcode($request->user()->id);
        return response()->json(['barcode' => $barcode]);
    }
    
    public function clearPendingBarcode(Request $request){
        $this->BarcodeSuggestionService->clearPendingBarcode($request->user()->id);
        return response()->json(['message' => 'Código pendiente eliminado']);
    }
}
