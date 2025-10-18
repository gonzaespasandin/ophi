<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('medical_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->unsignedBigInteger('user_id'); // Primero declarar el campo.
            $table->foreign('user_id') // Foreign() --> nombre del campo que contiene la FK
                  ->references('id') // References() --> nombre del campo al cual referencia ese FK, es decir, la PK de otra tabla.
                  ->on('users'); // On() --> en que tabla está el PK al cual vamos a referenciar.
            $table->string('picture')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_profiles');
    }
};
