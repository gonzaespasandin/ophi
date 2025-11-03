<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\ScannerService;

class ScannerController extends Controller
{
    protected $ScannerService;

    public function __construct(ScannerService $ScannerService)
    {
        $this->ScannerService = $ScannerService;
    }
    public function process(Request $request) {
        Log::info('Request recieved to process the scanner: ', ['request' => $request]);

        $request->validate([
            'codigo' => 'required|string|max:15',
        ]);

        $result = $this->ScannerService->process_code($request->codigo);

        if (!$result) {
            return response()->json(['message' => 'Código no encontrado'], 404);
        }

        return response()->json($result);
    }
}
