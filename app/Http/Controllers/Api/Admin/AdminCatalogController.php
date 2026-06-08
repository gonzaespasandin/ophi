<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\CatalogService;
use App\Services\OcrService;
use Illuminate\Http\Request;

class AdminCatalogController extends Controller
{
    public function __construct(
        protected CatalogService $catalogService,
        protected OcrService $ocrService,
    ) {}

    /**
     * Busca un producto en el catálogo por EAN.
     * Si ya tiene ingredientes cargados, los incluye en la respuesta.
     */
    public function lookup(string $ean)
    {
        $product = $this->catalogService->findByEan($ean);

        if (! $product) {
            return response()->json(['message' => 'Producto no encontrado en el catálogo'], 404);
        }

        return response()->json([
            'productoId'              => $product->productoId,
            'ean'                     => $product->ean,
            'name'                    => $product->producto,
            'brand'                   => $product->brand,
            'cat1'                    => $product->cat1,
            'cat2'                    => $product->cat2,
            'cat3'                    => $product->cat3,
            'ingredientes'            => $product->ingredientes,
            'ingredientes_updated_at' => $product->ingredientes_updated_at,
        ]);
    }

    /**
     * Recibe una foto del listado de ingredientes y devuelve el array normalizado.
     * NO guarda nada — solo extrae. El admin revisa y confirma antes de guardar.
     */
    public function extractIngredients(string $ean, Request $request)
    {
        $request->validate([
            'image' => 'required|file|mimes:jpeg,jpg,png,webp|max:5120',
        ]);

        $ingredientes = $this->ocrService->extractIngredientsFromImage($request->file('image'));

        return response()->json(['ingredientes' => $ingredientes]);
    }

    /**
     * Devuelve productos del catálogo con el mismo nombre base pero distinto tamaño.
     * Permite al admin aplicar los mismos ingredientes a varias presentaciones del mismo producto.
     */
    public function similar(string $ean)
    {
        $similar = $this->catalogService->findSimilar($ean);

        return response()->json(['similar' => $similar]);
    }

    /**
     * Guarda la lista de ingredientes (ya revisada/editada por el admin) en el catálogo.
     */
    public function saveIngredients(string $ean, Request $request)
    {
        $data = $request->validate([
            'ingredientes'   => 'required|array|min:1',
            'ingredientes.*' => 'required|string|max:255',
        ]);

        $product = $this->catalogService->saveIngredients($ean, $data['ingredientes']);

        return response()->json([
            'message'                 => 'Ingredientes guardados correctamente',
            'ean'                     => $product->ean,
            'ingredientes'            => $product->ingredientes,
            'ingredientes_updated_at' => $product->ingredientes_updated_at,
        ]);
    }
}
