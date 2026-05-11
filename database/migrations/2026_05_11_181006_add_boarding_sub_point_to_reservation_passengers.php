<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tolerant to partial execution: safe to run even if column already exists.
     */
    public function up(): void
    {
        // STEP 1: Add column only if it doesn't already exist
        if (!Schema::hasColumn('reservation_passengers', 'boarding_sub_point_id')) {
            Schema::table('reservation_passengers', function (Blueprint $table) {
                $table->unsignedBigInteger('boarding_sub_point_id')->nullable()->after('boarding_point_id');
            });
        }

        // STEP 2: Add FK only if it doesn't already exist
        $fkName = 'reservation_passengers_boarding_sub_point_id_foreign';
        $db = DB::connection()->getDatabaseName();

        $fkExists = DB::select("
            SELECT CONSTRAINT_NAME
            FROM information_schema.TABLE_CONSTRAINTS
            WHERE TABLE_SCHEMA = ?
              AND TABLE_NAME = 'reservation_passengers'
              AND CONSTRAINT_NAME = ?
              AND CONSTRAINT_TYPE = 'FOREIGN KEY'
        ", [$db, $fkName]);

        if (empty($fkExists)) {
            Schema::table('reservation_passengers', function (Blueprint $table) {
                $table->foreign('boarding_sub_point_id', 'reservation_passengers_boarding_sub_point_id_foreign')
                      ->references('id')
                      ->on('boarding_sub_points')
                      ->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     * Also tolerant: skip if column/FK no longer exists.
     */
    public function down(): void
    {
        $fkName = 'reservation_passengers_boarding_sub_point_id_foreign';
        $db = DB::connection()->getDatabaseName();

        $fkExists = DB::select("
            SELECT CONSTRAINT_NAME
            FROM information_schema.TABLE_CONSTRAINTS
            WHERE TABLE_SCHEMA = ?
              AND TABLE_NAME = 'reservation_passengers'
              AND CONSTRAINT_NAME = ?
              AND CONSTRAINT_TYPE = 'FOREIGN KEY'
        ", [$db, $fkName]);

        if (!empty($fkExists)) {
            Schema::table('reservation_passengers', function (Blueprint $table) use ($fkName) {
                $table->dropForeign($fkName);
            });
        }

        if (Schema::hasColumn('reservation_passengers', 'boarding_sub_point_id')) {
            Schema::table('reservation_passengers', function (Blueprint $table) {
                $table->dropColumn('boarding_sub_point_id');
            });
        }
    }
};
