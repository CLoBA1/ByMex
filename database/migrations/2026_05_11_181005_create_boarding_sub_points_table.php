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
        Schema::create('boarding_sub_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('boarding_point_id')->constrained('boarding_points')->onDelete('cascade');
            $table->string('name', 150);
            $table->string('reference', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->tinyInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boarding_sub_points');
    }
};
