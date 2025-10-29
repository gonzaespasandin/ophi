<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ScannerService;

class ScannerController extends Controller
{
    protected $scannerService;

    public function __construct(ScannerService $scannerService)
    {
        $this->scannerService = $scannerService;
    }

    public function index()
    {
        return view('scanner.index');
    }

    public function process(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string|max:15',
        ]);

        $resultado = $this->scannerService->procesarCodigo($request->codigo);

        if (!$resultado) {
            return response()->json(['message' => 'Código no encontrado'], 404);
        }

        return response()->json($resultado);
    }
}
