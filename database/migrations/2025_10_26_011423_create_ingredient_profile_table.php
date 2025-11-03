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
        Schema::create('ingredient_profile', function (Blueprint $table) {
            $table->foreignId('ingredient_id')->constrained();
            $table->foreignId('profile_id')->constrained();

            $table->primary(['ingredient_id', 'profile_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingredient_profile');
    }
};
