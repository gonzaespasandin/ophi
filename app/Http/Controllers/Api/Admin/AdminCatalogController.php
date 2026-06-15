<?php

namespace App\Http\Controllers\Api\Admin;

use App\Exceptions\OcrConfigurationException;
use App\Http\Controllers\Controller;
use App\Services\CatalogService;
use App\Services\OcrService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Throwable;

class AdminCatalogController extends Controller
{
    public function __construct(
        protected CatalogService $catalogService,
        protected OcrService $ocrService,
    ) {}

    /**
     * Busca un producto de Ophi por EAN/barcode.
     */
    public function lookup(string $ean)
    {
        $product = $this->catalogService->findByEan($ean);

        if (! $product) {
            return response()->json(['message' => 'Producto no encontrado en Ophi'], 404);
        }

        return response()->json($this->catalogService->toLookupPayload($product));
    }

    /**
     * Crea un producto manualmente cuando el scanner admin lee un EAN no encontrado.
     */
    public function create(string $ean, Request $request)
    {
        $request->merge(['barcode' => $ean]);

        $data = $request->validate([
            'barcode' => ['required', 'digits_between:8,15', Rule::unique('products', 'barcode')],
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'brand' => ['required', 'string', 'min:1', 'max:255'],
            'category' => ['required', 'string', 'min:1', 'max:255'],
            'origin' => ['nullable', 'string', 'max:500'],
            'rnpa' => ['nullable', 'string', 'max:32', Rule::unique('products', 'rnpa')],
        ]);

        $product = $this->catalogService->createFromAdminScanner($ean, $data);

        return response()->json($this->catalogService->toLookupPayload($product), 201);
    }

    /**
     * Recibe una foto del listado de ingredientes y devuelve el array normalizado.
     */
    public function extractIngredients(string $ean, Request $request)
    {
        $request->validate([
            'image' => 'required|file|mimes:jpeg,jpg,png,webp|max:5120',
        ]);

        try {
            $ingredientes = $this->ocrService->extractIngredientsFromImage($request->file('image'));
        } catch (OcrConfigurationException $exception) {
            return response()->json(['message' => $exception->getMessage()], 503);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => 'No se pudo extraer ingredientes con OCR. Revisá la configuración y los logs del backend.',
            ], 502);
        }

        return response()->json(['ingredientes' => $ingredientes]);
    }

    /**
     * Devuelve productos de Ophi con el mismo nombre base y misma marca.
     */
    public function similar(string $ean)
    {
        $similar = $this->catalogService->findSimilar($ean);

        return response()->json(['similar' => $similar]);
    }

    /**
     * Guarda la lista de ingredientes OCR en `ingredients` e `ingredient_product`.
     */
    public function saveIngredients(string $ean, Request $request)
    {
        $data = $request->validate([
            'ingredientes'   => 'required|array|min:1',
            'ingredientes.*' => 'required|string|max:255',
        ]);

        $product = $this->catalogService->saveIngredients($ean, $data['ingredientes']);

        return response()->json([
            'message' => 'Ingredientes guardados correctamente',
            ...$this->catalogService->toLookupPayload($product),
        ]);
    }
}
