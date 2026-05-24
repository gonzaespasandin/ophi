<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Model;

/**
 * Representa una fila de la tabla IMAGENES en la SQLite externa del catálogo
 * de productos argentinos (EAN_VALIDOS.db).
 *
 * La lectura es la operación principal. La escritura está limitada a la columna
 * `ingredientes` (y `ingredientes_updated_at`), cargada por el admin vía OCR.
 *
 * @property int         $productoId
 * @property string      $ean
 * @property string      $producto
 * @property string      $brand
 * @property float       $precioReal
 * @property string      $cat1
 * @property string      $cat2
 * @property string      $cat3
 * @property int         $stock
 * @property string      $fechaActualizacion
 * @property string|null $ingredientes
 * @property string|null $ingredientes_updated_at
 */
class CatalogProduct extends Model
{
    protected $connection = 'catalog';
    protected $table = 'IMAGENES';
    // OJO: la columna en SQLite es 'productoId' (con d minúscula).
    // SQLite es case-insensitive en SQL, pero PHP/arrays son case-sensitive,
    // así que el primaryKey TIENE que coincidir exacto con cómo viene en el SELECT.
    protected $primaryKey = 'productoId';
    public $timestamps = false;

    /** Solo se permite escribir las columnas de ingredientes. */
    protected $fillable = ['ingredientes', 'ingredientes_updated_at'];
}
