<?php

namespace App\Services;

use App\Models\Product;

class ScannerService
{
    public function __construct(protected CatalogService $catalogService) {}

    /**
     * Busca un barcode primero en la DB de Ophi.
     * Si no está, hace fallback al catálogo externo (EAN_VALIDOS.db)
     * y devuelve un payload con forma de Product (ingredients vacíos)
     * marcado con `from_catalog: true` para que el front sepa que
     * todavía no tenemos los ingredientes cargados.
     *
     * Devuelve null si no hay match en ningún lado → dispara el flujo
     * de BarcodeSuggestion existente.
     */
    public function process_code(string $barcode)
    {
        $product = Product::with('ingredients')->where('barcode', $barcode)->first();

        if ($product) {
            return $product;
        }

        $catalogProduct = $this->catalogService->findByEan($barcode);

        if (!$catalogProduct) {
            return null;
        }

        return [
            'from_catalog' => true,
            'name' => $catalogProduct->producto,
            'barcode' => $catalogProduct->ean,
            'brand' => ['name' => $catalogProduct->brand],
            'ingredients' => [],
            'catalog' => [
                'productoId' => $catalogProduct->productoId,
                'cat1' => $catalogProduct->cat1,
                'cat2' => $catalogProduct->cat2,
                'cat3' => $catalogProduct->cat3,
            ],
        ];
    }
}
