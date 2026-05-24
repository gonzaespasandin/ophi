<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Model;

/**
 * Tabla de referencia INS (Codex Alimentarius) en la DB del catálogo.
 *
 * @property string      $code      Código INS, ej: "202", "160b"
 * @property string      $nombre    Nombre canónico en español
 * @property string|null $categoria Familia: "colorante", "conservante", etc.
 */
class InsCode extends Model
{
    protected $connection = 'catalog';
    protected $table = 'ins_codes';
    protected $primaryKey = 'code';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = ['code', 'nombre', 'categoria'];
}
