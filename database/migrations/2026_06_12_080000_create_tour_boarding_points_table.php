<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tour_boarding_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')
                  ->constrained('tours')
                  ->cascadeOnDelete();
            $table->foreignId('boarding_point_id')
                  ->constrained('boarding_points')
                  ->cascadeOnDelete();
            $table->time('departure_time')->nullable();
            $table->tinyInteger('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['tour_id', 'boarding_point_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_boarding_points');
    }
};
