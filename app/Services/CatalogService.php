<?php

namespace App\Services;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class CatalogService
{
    public function __construct(protected ProductIngredientSyncService $ingredientSyncService) {}

    /**
     * Busca un producto de Ophi por su codigo EAN/barcode.
     */
    public function findByEan(string $ean): ?Product
    {
        return Product::with(['brand', 'category', 'ingredients.parents'])
            ->where('barcode', $ean)
            ->first();
    }

    /**
     * Busca por nombre de producto o marca dentro de la DB principal de Ophi.
     */
    public function search(string $query, int $limit = 10): Collection
    {
        return Product::with(['brand', 'category', 'ingredients.parents'])
            ->where('name', 'like', "%{$query}%")
            ->orWhereHas('brand', function ($brandQuery) use ($query) {
                $brandQuery->where('name', 'like', "%{$query}%");
            })
            ->limit($limit)
            ->get();
    }

    /**
     * Busca productos de Ophi con el mismo nombre base y la misma marca.
     *
     * @return array<int, array{ean: string, name: string, has_ingredients: bool}>
     */
    public function findSimilar(string $ean): array
    {
        $product = $this->findByEan($ean);

        if (! $product || ! $product->brand_id) {
            return [];
        }

        $baseName = preg_replace(
            '/\s+\d+[\.,]?\d*\s*(cc|ml|l|lt|g|gr|kg|oz|unidades?|un)\b.*/i',
            '',
            trim($product->name)
        );

        if (rtrim($baseName) === trim($product->name)) {
            return [];
        }

        return Product::with('ingredients.parents')
            ->where('brand_id', $product->brand_id)
            ->where('id', '!=', $product->id)
            ->whereNotNull('barcode')
            ->where('name', 'like', $baseName . '%')
            ->orderBy('name')
            ->limit(10)
            ->get()
            ->map(fn (Product $p) => [
                'ean'             => $p->barcode,
                'name'            => $p->name,
                'has_ingredients' => $p->ingredients->isNotEmpty(),
            ])
            ->values()
            ->toArray();
    }

    /**
     * Guarda ingredientes OCR en las tablas reales de Ophi.
     */
    public function saveIngredients(string $ean, array $ingredientes): Product
    {
        $product = Product::where('barcode', $ean)->firstOrFail();
        $this->ingredientSyncService->sync($product, $ingredientes);

        return $product->load(['brand', 'category', 'ingredients.parents']);
    }

    /**
     * Crea un producto manualmente desde el scanner admin.
     *
     * @param array{name: string, brand: string, category: string, origin?: string|null, rnpa?: string|null} $data
     */
    public function createFromAdminScanner(string $ean, array $data): Product
    {
        $brand = $this->firstOrCreateBrand($data['brand']);
        $category = $this->firstOrCreateCategory($data['category']);
        $name = trim($data['name']);

        $product = Product::create([
            'name' => $name,
            'name_normalized' => mb_strtolower($name),
            'barcode' => $ean,
            'rnpa' => blank($data['rnpa'] ?? null) ? null : trim($data['rnpa']),
            'origin' => blank($data['origin'] ?? null) ? 'Desconocido' : trim($data['origin']),
            'brand_id' => $brand->id,
            'category_id' => $category->id,
        ]);

        return $product->load(['brand', 'category', 'ingredients.parents']);
    }

    protected function firstOrCreateBrand(string $name): Brand
    {
        $name = $this->normalizeLookupName($name);

        return Brand::whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower($name)])
            ->first()
            ?? Brand::create(['name' => $name]);
    }

    protected function firstOrCreateCategory(string $name): Category
    {
        $name = $this->normalizeLookupName($name);

        return Category::whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower($name)])
            ->first()
            ?? Category::create(['name' => $name]);
    }

    protected function normalizeLookupName(string $name): string
    {
        return preg_replace('/\s+/', ' ', trim($name));
    }

    /**
     * Mantiene la forma de respuesta esperada por el scanner admin.
     *
     * @return array<string, mixed>
     */
    public function toLookupPayload(Product $product): array
    {
        $ingredientNames = $product->ingredients
            ->pluck('name')
            ->filter()
            ->values();

        return [
            'productoId'              => $product->id,
            'product_id'              => $product->id,
            'ean'                     => $product->barcode,
            'producto'                => $product->name,
            'name'                    => $product->name,
            'brand'                   => $product->brand?->name,
            'cat1'                    => $product->category?->name,
            'cat2'                    => null,
            'cat3'                    => null,
            'ingredientes'            => $ingredientNames->isEmpty() ? null : $ingredientNames->implode(', '),
            'ingredientes_updated_at' => $product->updated_at?->toISOString(),
        ];
    }
}
