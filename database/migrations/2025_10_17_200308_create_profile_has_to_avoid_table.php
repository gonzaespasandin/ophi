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
        Schema::create('profile_has_to_avoid', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained('medital_profiles');
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
        Schema::dropIfExists('profile_has_to_avoid');
    }
};
