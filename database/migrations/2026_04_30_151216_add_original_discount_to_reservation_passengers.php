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
        Schema::table('reservation_passengers', function (Blueprint $table) {
            $table->decimal('original_discount_amount', 10, 2)->default(0)->after('discount_amount');
        });

        // Backfill: for all existing passengers, original_discount equals their current discount
        \DB::statement('UPDATE reservation_passengers SET original_discount_amount = discount_amount');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservation_passengers', function (Blueprint $table) {
            $table->dropColumn('original_discount_amount');
        });
    }
};
