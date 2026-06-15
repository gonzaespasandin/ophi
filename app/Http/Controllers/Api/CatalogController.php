<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CatalogService;

class CatalogController extends Controller
{
    public function __construct(protected CatalogService $catalogService) {}

    /**
     * Busca un producto de Ophi por EAN/barcode.
     */
    public function findByEan(string $ean)
    {
        $product = $this->catalogService->findByEan($ean);

        if (! $product) {
            return response()->json(['message' => 'EAN no encontrado en Ophi'], 404);
        }

        return response()->json($this->catalogService->toLookupPayload($product));
    }
}
