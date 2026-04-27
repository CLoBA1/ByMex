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
        Schema::create('tours', function (Blueprint $table) {
            $table->id();
            $table->string('title', 150);
            $table->string('destination', 100);
            $table->dateTime('departure_date');
            $table->string('boarding_point', 255)->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('total_seats')->default(45);
            $table->integer('expiration_hours')->default(24)->comment('Horas para que caduque un apartado');
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tours');
    }
};
