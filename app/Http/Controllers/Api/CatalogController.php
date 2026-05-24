<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CatalogService;

class CatalogController extends Controller
{
    public function __construct(protected CatalogService $catalogService) {}

    /**
     * Busca un producto en el catálogo externo por EAN.
     *
     * GET /api/catalog/{ean}
     *
     * 200 → { productoId, ean, producto, brand, cat1, cat2, cat3 }
     * 404 → { message: 'EAN no encontrado en catálogo' }
     */
    public function findByEan(string $ean)
    {
        $product = $this->catalogService->findByEan($ean);

        if (!$product) {
            return response()->json(['message' => 'EAN no encontrado en catálogo'], 404);
        }

        return response()->json($product->only([
            'productoId',
            'ean',
            'producto',
            'brand',
            'cat1',
            'cat2',
            'cat3',
        ]));
    }
}
