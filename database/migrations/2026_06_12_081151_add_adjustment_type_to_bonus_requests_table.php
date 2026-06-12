<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bonus_requests', function (Blueprint $table) {
            $table->enum('adjustment_type', ['add', 'subtract'])
                  ->default('add')
                  ->after('requested_bonus_count');
        });
    }

    public function down(): void
    {
        Schema::table('bonus_requests', function (Blueprint $table) {
            $table->dropColumn('adjustment_type');
        });
    }
};
