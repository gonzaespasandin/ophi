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
        Schema::create('product_has_ingredients', function (Blueprint $table) {
            $table->id();
            $table->enum('relation', ['ingredient', 'group', 'trace', 'additive']);

            $table->foreignId('product_id')->constrained();
            $table->foreignId('ingredient_id')->nullable()->constrained();
            $table->foreignId('group_id')->nullable()->constrained();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_has_ingredients');
    }
};
