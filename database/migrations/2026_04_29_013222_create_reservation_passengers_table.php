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
        Schema::create('reservation_passengers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained('reservations')->onDelete('cascade');
            $table->string('seat_number', 10)->nullable();
            
            // Passenger Details
            $table->string('name', 150);
            $table->date('birthdate')->nullable();
            
            // Pricing & Category
            $table->string('passenger_type', 50)->default('Adulto');
            $table->string('benefit_label', 100)->nullable(); // e.g., 'INAPAM', 'Estudiante'
            
            $table->decimal('base_price', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('final_price', 10, 2)->default(0);
            
            // Validation (For special discounts)
            $table->string('validation_status', 20)->default('pending'); // pending, validated, rejected
            $table->text('validation_notes')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservation_passengers');
    }
};
