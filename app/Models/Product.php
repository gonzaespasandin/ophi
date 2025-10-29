<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'barcode', 'name_normalized', 'img', 'img_alt', 'origin', 'rnpa']; //'global_category_id', 'brand_id'];
}