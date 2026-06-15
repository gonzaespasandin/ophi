<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InsCode extends Model
{
    protected $table = 'ins_codes';
    protected $primaryKey = 'code';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = ['code', 'nombre', 'categoria'];
}
