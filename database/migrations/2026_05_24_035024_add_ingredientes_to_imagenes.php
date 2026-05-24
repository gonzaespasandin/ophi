<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'catalog';

    /**
     * Agrega las columnas de ingredientes a la tabla IMAGENES del catálogo externo.
     * Se usa ADD COLUMN NULL que en SQLite es O(1) — no reescribe las 170k filas.
     */
    public function up(): void
    {
        Schema::connection('catalog')->table('IMAGENES', function (Blueprint $table) {
            $table->text('ingredientes')->nullable();
            $table->timestamp('ingredientes_updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::connection('catalog')->table('IMAGENES', function (Blueprint $table) {
            $table->dropColumn(['ingredientes', 'ingredientes_updated_at']);
        });
    }
};
