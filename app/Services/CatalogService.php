<?php

namespace App\Services;

use App\Models\Catalog\CatalogProduct;
use Illuminate\Database\Eloquent\Collection;

class CatalogService
{
    /**
     * Busca un producto en el catálogo externo por su código EAN/barcode.
     * Devuelve null si no existe.
     */
    public function findByEan(string $ean): ?CatalogProduct
    {
        return CatalogProduct::where('ean', $ean)->first();
    }

    /**
     * Búsqueda por nombre de producto o marca.
     * Útil para futuras funciones de importación masiva o admin.
     */
    public function search(string $query, int $limit = 10): Collection
    {
        return CatalogProduct::where('producto', 'like', "%{$query}%")
            ->orWhere('brand', 'like', "%{$query}%")
            ->limit($limit)
            ->get();
    }

    /**
     * Guarda la lista de ingredientes normalizados en IMAGENES.ingredientes.
     * Recibe el array ya limpio (producido por OcrService o editado por el admin),
     * lo une con coma y lo persiste junto con el timestamp.
     */
    public function saveIngredients(string $ean, array $ingredientes): CatalogProduct
    {
        $product = CatalogProduct::where('ean', $ean)->firstOrFail();
        $product->ingredientes = implode(', ', array_map('trim', $ingredientes));
        $product->ingredientes_updated_at = now();
        $product->save();
        return $product;
    }
}
