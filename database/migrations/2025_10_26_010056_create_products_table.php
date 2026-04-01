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
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('name_normalized')->default('');
            $table->string('img')->nullable();
            $table->string('img_alt')->nullable();
            $table->text('origin')->nullable();

            // Barcodes y RNPA pueden faltar o venir rotos en datos reales
            $table->string('barcode', 15)->nullable()->unique();
            $table->string('rnpa', 32)->nullable()->unique();

            $table->foreignId('brand_id')->cascadeOnUpdate()->restrictOnDelete()->constrained();
            $table->foreignId('category_id')->cascadeOnUpdate()->restrictOnDelete()->constrained();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
