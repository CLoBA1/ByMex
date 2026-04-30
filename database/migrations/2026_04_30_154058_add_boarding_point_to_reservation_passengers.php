<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservation_passengers', function (Blueprint $table) {
            $table->foreignId('boarding_point_id')->nullable()->after('benefit_label')
                  ->constrained('boarding_points')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('reservation_passengers', function (Blueprint $table) {
            $table->dropForeign(['boarding_point_id']);
            $table->dropColumn('boarding_point_id');
        });
    }
};
