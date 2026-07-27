<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('source_supermarket', 100)->nullable()->index();
            $table->string('source_product_id', 100)->nullable()->index();
            $table->string('product_type')->nullable();
            $table->longText('description')->nullable();
            $table->json('category_paths')->nullable();
            $table->json('nutrition')->nullable();
            $table->json('labels')->nullable();
            $table->json('source_supermarkets')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'source_supermarket',
                'source_product_id',
                'product_type',
                'description',
                'category_paths',
                'nutrition',
                'labels',
                'source_supermarkets',
            ]);
        });
    }
};
