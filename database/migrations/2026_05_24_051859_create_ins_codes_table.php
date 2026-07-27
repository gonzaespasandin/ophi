<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabla de referencia Codex Alimentarius (INS) en la DB principal de Ophi.
     */
    public function up(): void
    {
        Schema::create('ins_codes', function (Blueprint $table) {
            $table->string('code', 10)->primary();
            $table->string('nombre', 255);
            $table->string('categoria', 100)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ins_codes');
    }
};
