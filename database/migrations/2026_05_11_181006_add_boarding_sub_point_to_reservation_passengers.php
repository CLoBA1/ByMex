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
            $table->foreignId('boarding_sub_point_id')->nullable()->after('boarding_point_id')
                  ->constrained('boarding_sub_points')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservation_passengers', function (Blueprint $table) {
            $table->dropForeign(['boarding_sub_point_id']);
            $table->dropColumn('boarding_sub_point_id');
        });
    }
};
