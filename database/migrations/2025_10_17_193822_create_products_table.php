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
            $table->string('name_normalized');
            $table->string('img');
            $table->string('img_alt');
            $table->string('origin');

            // These two fields won’t be used in any numeric operations, so storing it as VARCHAR makes more sense.
            $table->string('barcode', 15)->unique(); // 13 numbers per barcode
            $table->string('rnpa', 10)->unique(); // 8 numbers per RNPA

            $table->foreignId('global_category_id')->constrained('global_categories');
            $table->foreignId('brand_id')->constrained('brands');

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
