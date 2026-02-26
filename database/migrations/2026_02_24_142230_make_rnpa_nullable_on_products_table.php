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
        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique('products_rnpa_unique');
            $table->string('rnpa', 8)->nullable()->change();
            $table->unique('rnpa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique('products_rnpa_unique');
            $table->string('rnpa', 8)->nullable(false)->change();
            $table->unique('rnpa');
        });
    }
};
