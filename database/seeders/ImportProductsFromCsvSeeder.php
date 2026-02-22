<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImportProductsFromCsvSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/products.csv');

        if (!file_exists($path)) {
            $this->command->error("No se encontró el archivo: $path");
            return;
        }

        $handle = fopen($path, 'r');

        $header = fgetcsv($handle); // Primera fila: encabezados
        $headerCount = count($header);

        $line = 1;
        while (($row = fgetcsv($handle)) !== false) {
            $line++;

            if (count($row) !== $headerCount) {
                // Log opcional para debug
                logger()->warning("Fila CSV inválida en línea {$line}, columnas: " . count($row));
                continue; // saltamos filas rotas
            }

            $data = array_combine($header, $row);

            // 1) Insertar / obtener marca
            $brandName = trim($data['brand']);

            DB::table('brands')->updateOrInsert(
                ['name' => $brandName],
                ['created_at' => now(), 'updated_at' => now()]
            );

            $brand = DB::table('brands')->where('name', $brandName)->first();

            // 2) Insertar / obtener categoría
            $categoryName = trim($data['product_category']);

            DB::table('categories')->updateOrInsert(
                ['name' => $categoryName],
                ['created_at' => now(), 'updated_at' => now()]
            );

            $categoryRow = DB::table('categories')
                ->where('name', $categoryName)
                ->first();


            // --- Normalizar barcode ---
            $barcodeRaw = $data['barcode'] ?? null;

            $barcode = null;
            if ($barcodeRaw !== null) {
                $barcode = trim((string) $barcodeRaw);

                // Si viene como "1234567890123.0" desde Excel / Sheets
                $barcode = preg_replace('/\.0$/', '', $barcode);

                // Si queda vacío, lo pasamos a null (CLAVE para el UNIQUE)
                if ($barcode === '' || !preg_match('/\d+/', $barcode)) {
                    $barcode = null;
                }
            }

            // 3) Insertar producto
            $productId = DB::table('products')->insertGetId([
                'name' => trim($data['product_name']),
                'name_normalized' => strtolower(trim($data['product_name'])),
                'img' => '',
                'img_alt' => trim($data['product_name']),
                'origin' => $data['origin_country'] ?: 'Desconocido',
                'barcode' => $barcode,
                'rnpa' => $data['rnpa'] !== '' ? trim($data['rnpa']) : null,

                // 🔽 CLAVES FORÁNEAS OBLIGATORIAS
                'brand_id' => $brand->id,
                'category_id' => $categoryRow->id,

                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 4) Parsear ingredientes
            $this->importIngredients($productId, $data['ingredients_text']);
        }

        fclose($handle);
    }

    private function importIngredients(int $productId, string $rawText): void
    {
        // Normalización básica
        $text = strtolower($rawText);
        $text = str_replace(["\r\n", "\n", ";"], ",", $text);
        $text = preg_replace('/\([^)]*\)/', '', $text);
        $text = preg_replace('/\s+/', ' ', $text);
        $text = preg_replace('/,+/', ',', $text);
        $text = trim($text, " ,");

        $additiveLabels = [
            'acidulante',
            'conservante',
            'conservantes',
            'estabilizante',
            'colorante',
            'antioxidante',
            'antioxidantes',
            'secuestrante',
            'aromatizante',
            'aromatizantes',
            'resaltador de sabor',
        ];

        $ingredientsPart = $text;
        $additivesPart = '';

        foreach ($additiveLabels as $label) {
            if (str_contains($ingredientsPart, $label . ':')) {
                [$before, $after] = explode($label . ':', $ingredientsPart, 2);
                $ingredientsPart = $before;
                $additivesPart .= ',' . $after;
            }
        }

        $ingredientsList = $this->splitItems($ingredientsPart);
        $additivesList   = $this->splitItems($additivesPart);

       // Guardar ingredientes normales
        foreach ($ingredientsList as $name) {
            $ingredientName = trim($name);

            $ingredient = DB::table('ingredients')->where('name', $ingredientName)->first();

            if (!$ingredient) {
                $id = DB::table('ingredients')->insertGetId([
                    'name' => $ingredientName,
                    'icon' => 'default.svg',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $id = $ingredient->id;
            }

            DB::table('ingredient_product')->insertOrIgnore([
                'product_id' => $productId,
                'ingredient_id' => $id,
            ]);
        }
        // Guardar aditivos
        foreach ($additivesList as $name) {
            $ingredient = DB::table('ingredients')->where('ingredient', $name)->first();

            if (!$ingredient) {
                $id = DB::table('ingredients')->insertGetId([
                    'ingredient' => $name,
                    'icon' => 'default.svg',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $id = $ingredient->id;
            }

            DB::table('product_has_ingredients')->insert([
                'product_id' => $productId,
                'ingredient_id' => $id,
                'group_id' => null,
                'relation' => 'additive',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function splitItems(string $text): array
    {
        $parts = explode(',', $text);
        $clean = [];

        foreach ($parts as $p) {
            $p = trim($p);
            $p = trim($p, " .-");

            if ($p === '' || $p === 'y' || $p === 'e') {
                continue;
            }

            $clean[] = $p;
        }

        return $clean;
    }
}