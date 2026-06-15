<?php

namespace App\Console\Commands;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Services\ProductIngredientSyncService;
use Generator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use JsonException;
use RuntimeException;

class ImportNormalizedProductsCommand extends Command
{
    protected $signature = 'products:import-normalized-json
        {path? : Path to productos_normalizados.json}
        {--limit= : Stop after importing this many products}
        {--chunk=500 : Commit after this many imported products}
        {--dry-run : Parse and report without writing}
        {--without-ingredients : Do not import Ingredientes}
        {--preserve-existing-ingredients : Do not replace ingredients on products that already have any}';

    protected $description = 'Importa productos_normalizados.json a products, brands, categories, ingredients e ingredient_product.';

    public function __construct(private ProductIngredientSyncService $ingredientSyncService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $path = $this->argument('path')
            ?: env('PRODUCT_IMPORT_JSON_PATH', storage_path('app/import/productos_normalizados.json'));

        if (! is_string($path) || ! is_file($path)) {
            $this->error("No se encontro el JSON: {$path}");
            return self::FAILURE;
        }

        $limit = $this->option('limit') !== null ? (int) $this->option('limit') : null;
        $chunk = max(1, (int) $this->option('chunk'));
        $dryRun = (bool) $this->option('dry-run');

        $stats = [
            'records' => 0,
            'products' => 0,
            'skipped_without_ean' => 0,
            'ingredients_synced' => 0,
        ];

        $pending = 0;

        if (! $dryRun) {
            DB::beginTransaction();
        }

        try {
            foreach ($this->readTopLevelObjects($path) as $record) {
                $stats['records']++;

                foreach ($this->productPayloadsFromRecord($record) as $payload) {
                    if ($limit !== null && $stats['products'] >= $limit) {
                        break 2;
                    }

                    if ($payload['barcode'] === null) {
                        $stats['skipped_without_ean']++;
                        continue;
                    }

                    if ($dryRun) {
                        $stats['products']++;
                        continue;
                    }

                    $product = $this->upsertProduct($payload);

                    if (! $this->option('without-ingredients') && $payload['ingredients'] !== []) {
                        if (
                            ! $this->option('preserve-existing-ingredients')
                            || ! $product->ingredients()->exists()
                        ) {
                            $ids = $this->ingredientSyncService->sync($product, $payload['ingredients']);
                            $stats['ingredients_synced'] += count($ids);
                        }
                    }

                    $stats['products']++;
                    $pending++;

                    if ($pending >= $chunk) {
                        DB::commit();
                        DB::beginTransaction();
                        $pending = 0;
                        $this->line("Importados: {$stats['products']}");
                    }
                }
            }

            if (! $dryRun) {
                DB::commit();
            }
        } catch (\Throwable $exception) {
            if (! $dryRun) {
                DB::rollBack();
            }

            $this->error($exception->getMessage());
            return self::FAILURE;
        }

        $this->info(sprintf(
            'Listo. Registros JSON: %d. Productos importados: %d. Sin EAN: %d. Ingredientes vinculados: %d.',
            $stats['records'],
            $stats['products'],
            $stats['skipped_without_ean'],
            $stats['ingredients_synced'],
        ));

        return self::SUCCESS;
    }

    /**
     * @return Generator<int, array<string, mixed>>
     */
    private function readTopLevelObjects(string $path): Generator
    {
        $handle = fopen($path, 'rb');

        if ($handle === false) {
            throw new RuntimeException("No se pudo abrir el JSON: {$path}");
        }

        $buffer = '';
        $collecting = false;
        $depth = 0;
        $inString = false;
        $escaped = false;

        try {
            while (! feof($handle)) {
                $chunk = fread($handle, 1024 * 1024);

                if ($chunk === false) {
                    throw new RuntimeException("Error leyendo el JSON: {$path}");
                }

                $length = strlen($chunk);

                for ($i = 0; $i < $length; $i++) {
                    $char = $chunk[$i];

                    if (! $collecting) {
                        if ($char === '{') {
                            $collecting = true;
                            $depth = 1;
                            $buffer = '{';
                        }

                        continue;
                    }

                    $buffer .= $char;

                    if ($inString) {
                        if ($escaped) {
                            $escaped = false;
                            continue;
                        }

                        if ($char === '\\') {
                            $escaped = true;
                            continue;
                        }

                        if ($char === '"') {
                            $inString = false;
                        }

                        continue;
                    }

                    if ($char === '"') {
                        $inString = true;
                        continue;
                    }

                    if ($char === '{') {
                        $depth++;
                        continue;
                    }

                    if ($char === '}') {
                        $depth--;

                        if ($depth === 0) {
                            try {
                                $decoded = json_decode($buffer, true, 512, JSON_THROW_ON_ERROR);
                            } catch (JsonException $exception) {
                                throw new RuntimeException("JSON invalido cerca del producto actual: {$exception->getMessage()}");
                            }

                            if (is_array($decoded)) {
                                yield $decoded;
                            }

                            $buffer = '';
                            $collecting = false;
                        }
                    }
                }
            }
        } finally {
            fclose($handle);
        }
    }

    /**
     * @param array<string, mixed> $record
     * @return array<int, array<string, mixed>>
     */
    private function productPayloadsFromRecord(array $record): array
    {
        $items = is_array($record['items'] ?? null) ? $record['items'] : [];
        $payloads = [];

        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }

            $image = $this->firstImage($item);
            $name = $this->cleanString($item['nameComplete'] ?? null)
                ?: $this->cleanString($item['name'] ?? null)
                ?: $this->cleanString($record['productName'] ?? null)
                ?: 'Producto sin nombre';

            $payloads[] = [
                'barcode' => $this->normalizeBarcode($item['ean'] ?? null),
                'name' => $name,
                'brand' => $this->cleanString($record['brand'] ?? null) ?: 'Desconocida',
                'category' => $this->primaryCategory($record),
                'category_paths' => $this->stringArray($record['categories'] ?? []),
                'origin' => $this->firstValueFromRecordKeyContaining($record, 'origen') ?: 'Desconocido',
                'source_supermarket' => $this->cleanString($record['supermarket'] ?? null),
                'source_product_id' => $this->cleanString($record['productId'] ?? null),
                'product_type' => $this->firstValueFromRecordKeyContaining($record, 'tipo'),
                'description' => $this->cleanString($record['description'] ?? null),
                'nutrition' => $this->arrayFromRecordKeyContaining($record, 'tabla'),
                'labels' => $this->stringArray($record['Sellos'] ?? []),
                'source_supermarkets' => $this->sourceSupermarkets($record),
                'ingredients' => $this->stringArray($record['Ingredientes'] ?? []),
                'img' => $image['imageUrl'] ?? null,
                'img_alt' => $this->cleanString($image['imageText'] ?? null) ?: $name,
            ];
        }

        return $payloads;
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function upsertProduct(array $payload): Product
    {
        $brand = Brand::firstOrCreate(['name' => $payload['brand']]);
        $category = Category::firstOrCreate(['name' => $payload['category']]);

        return Product::updateOrCreate(
            ['barcode' => $payload['barcode']],
            [
                'name' => $payload['name'],
                'name_normalized' => mb_strtolower($payload['name']),
                'img' => $payload['img'],
                'img_alt' => $payload['img_alt'],
                'origin' => $payload['origin'],
                'brand_id' => $brand->id,
                'category_id' => $category->id,
                'source_supermarket' => $payload['source_supermarket'],
                'source_product_id' => $payload['source_product_id'],
                'product_type' => $payload['product_type'],
                'description' => $payload['description'],
                'category_paths' => $payload['category_paths'],
                'nutrition' => $payload['nutrition'],
                'labels' => $payload['labels'],
                'source_supermarkets' => $payload['source_supermarkets'],
            ],
        );
    }

    /**
     * @param array<string, mixed> $record
     */
    private function primaryCategory(array $record): string
    {
        $categories = $this->stringArray($record['categories'] ?? []);
        $first = $categories[0] ?? null;

        if ($first === null) {
            return 'Desconocido';
        }

        $parts = array_values(array_filter(array_map('trim', explode('/', $first))));

        return end($parts) ?: 'Desconocido';
    }

    /**
     * @param array<string, mixed> $item
     * @return array<string, mixed>
     */
    private function firstImage(array $item): array
    {
        $images = is_array($item['images'] ?? null) ? $item['images'] : [];
        $image = $images[0] ?? [];

        return is_array($image) ? $image : [];
    }

    /**
     * @param array<string, mixed> $record
     * @return array<int, string>
     */
    private function sourceSupermarkets(array $record): array
    {
        $values = $this->stringArray($record['supermarkets'] ?? []);
        $main = $this->cleanString($record['supermarket'] ?? null);

        if ($main !== null) {
            $values[] = $main;
        }

        return array_values(array_unique($values));
    }

    private function normalizeBarcode(mixed $value): ?string
    {
        $barcode = $this->cleanString($value);

        if ($barcode === null) {
            return null;
        }

        $barcode = preg_replace('/\.0$/', '', $barcode);
        $barcode = preg_replace('/\D+/', '', $barcode);

        return $barcode === '' ? null : $barcode;
    }

    private function cleanString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    /**
     * @return array<int, string>
     */
    private function stringArray(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        $strings = [];

        foreach ($value as $item) {
            $clean = $this->cleanString($item);

            if ($clean !== null) {
                $strings[] = $clean;
            }
        }

        return array_values(array_unique($strings));
    }

    /**
     * @param array<string, mixed> $record
     */
    private function firstValueFromRecordKeyContaining(array $record, string $needle): ?string
    {
        foreach ($record as $key => $value) {
            if (! str_contains(mb_strtolower((string) $key), $needle)) {
                continue;
            }

            if (is_array($value)) {
                return $this->cleanString($value[0] ?? null);
            }

            return $this->cleanString($value);
        }

        return null;
    }

    /**
     * @param array<string, mixed> $record
     * @return array<string, mixed>|null
     */
    private function arrayFromRecordKeyContaining(array $record, string $needle): ?array
    {
        foreach ($record as $key => $value) {
            if (str_contains(mb_strtolower((string) $key), $needle) && is_array($value)) {
                return $value;
            }
        }

        return null;
    }
}
