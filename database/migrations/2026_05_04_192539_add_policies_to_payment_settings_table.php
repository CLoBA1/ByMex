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
        Schema::table('payment_settings', function (Blueprint $table) {
            $table->string('whatsapp_number')->nullable()->after('phones');
            $table->text('reservation_policies')->nullable()->after('final_note');
            $table->text('cancellation_policies')->nullable()->after('reservation_policies');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_settings', function (Blueprint $table) {
            $table->dropColumn(['whatsapp_number', 'reservation_policies', 'cancellation_policies']);
        });
    }
};
