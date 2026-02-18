<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barcode_suggestions_confirmations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barcode_suggestion_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unique(['barcode_suggestion_id', 'user_id'], 'bsc_suggestion_user_unique');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barcode_suggestions_confirmations');
    }
};
