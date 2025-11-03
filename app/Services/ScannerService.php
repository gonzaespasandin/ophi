<?php

namespace App\Services;

use App\Models\Product;

class ScannerService
{
    public function process_code(string $barcode)
    {
        return Product::where('barcode', $barcode)->first();
    }
}
