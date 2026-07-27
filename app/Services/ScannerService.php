<?php

namespace App\Services;

use App\Models\Product;

class ScannerService
{
    /**
     * Busca un barcode en la DB principal de Ophi.
     */
    public function process_code(string $barcode)
    {
        return Product::with(['ingredients.parents', 'brand', 'category', 'categories'])
            ->where('barcode', $barcode)
            ->first();
    }
}
