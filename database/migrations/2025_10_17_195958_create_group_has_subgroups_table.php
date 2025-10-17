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
        Schema::create('group_has_subgroups', function (Blueprint $table) {
            $table->id();

            $table->foreignId('parent_id')->constrained('groups');
            $table->foreignId('children_id')->constrained('groups');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_has_subgroups');
    }
};
