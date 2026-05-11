<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add a native mp_payment_id column to payments table.
     * This provides proper idempotence for Mercado Pago webhooks
     * without abusing the stripe_payment_intent_id field.
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Native MP payment ID (unique to enforce idempotence at DB level)
            $table->string('mp_payment_id')->nullable()->unique()->after('payment_method');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropUnique(['mp_payment_id']);
            $table->dropColumn('mp_payment_id');
        });
    }
};
