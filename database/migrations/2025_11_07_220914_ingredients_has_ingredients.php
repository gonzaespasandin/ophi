<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ingredient_has_ingredients', function (Blueprint $table) {
            $table->foreignId('belongs_to_id')->constrained(table: 'ingredients', column: 'id');
            $table->foreignId('owner_id')->constrained(table: 'ingredients', column: 'id');
            $table->primary(['belongs_to_id', 'owner_id']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingredient_has_ingredients');
    }
};
