<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barcode_suggestions', function (Blueprint $table) {
            $table->id();
            $table->string('barcode', 15);
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('suggested_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['pending', 'approved', 'blocked'])->default('pending');
            $table->unique(['barcode', 'product_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barcode_suggestions');
    }
};
