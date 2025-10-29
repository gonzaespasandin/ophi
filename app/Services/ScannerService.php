<?php

namespace App\Services;

use App\Models\Product;

class ScannerService
{
    public function procesarCodigo(string $codigo)
    {
        return Product::where('barcode', $codigo)->first();
    }
}