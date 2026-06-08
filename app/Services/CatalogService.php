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
     * Busca productos del catálogo con el mismo nombre base (sin el indicador de tamaño)
     * y la misma marca.  Ej: "Crema La Serenísima 330 cc" y "Crema La Serenísima 550 cc"
     * comparten ingredientes, así el admin puede guardarlos de una sola vez.
     *
     * Heurística: elimina el sufijo de cantidad/unidad del nombre (ej: "330 cc", "500 g",
     * "1.5 lt") y busca otros productos de la misma marca cuyo nombre empiece igual.
     *
     * @return array<int, array{ean: string, name: string, has_ingredients: bool}>
     */
    public function findSimilar(string $ean): array
    {
        $product = $this->findByEan($ean);

        if (! $product || ! $product->brand) {
            return [];
        }

        // Eliminar sufijos de cantidad: "330 cc", "500 g", "1.5 lt", "200ml", etc.
        $baseName = preg_replace(
            '/\s+\d+[\.,]?\d*\s*(cc|ml|l|lt|g|gr|kg|oz|unidades?|un)\b.*/i',
            '',
            trim($product->producto)
        );

        // Si el nombre no tiene sufijo de tamaño, no hay similares por esta lógica
        if (rtrim($baseName) === trim($product->producto)) {
            return [];
        }

        return CatalogProduct::where('brand', $product->brand)
            ->where('ean', '!=', $ean)
            ->where('producto', 'like', $baseName . '%')
            ->orderBy('producto')
            ->limit(10)
            ->get(['ean', 'producto', 'ingredientes'])
            ->map(fn ($p) => [
                'ean'             => $p->ean,
                'name'            => $p->producto,
                'has_ingredients' => $p->ingredientes !== null,
            ])
            ->values()
            ->toArray();
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
