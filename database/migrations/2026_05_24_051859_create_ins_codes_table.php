<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'catalog';

    /**
     * Tabla de referencia Codex Alimentarius (INS) en la DB del catálogo.
     * Permite resolver códigos como "160b" o "202" a nombres canónicos
     * durante el post-procesamiento del OCR.
     *
     * code     → código oficial INS (PK), ej: "202", "160b", "471"
     * nombre   → nombre canónico en español, ej: "sorbato de potasio"
     * categoria → familia, ej: "conservante", "colorante", "emulsionante"
     */
    public function up(): void
    {
        Schema::connection('catalog')->create('ins_codes', function (Blueprint $table) {
            $table->string('code', 10)->primary();
            $table->string('nombre', 255);
            $table->string('categoria', 100)->nullable();
        });
    }

    public function down(): void
    {
        Schema::connection('catalog')->dropIfExists('ins_codes');
    }
};
