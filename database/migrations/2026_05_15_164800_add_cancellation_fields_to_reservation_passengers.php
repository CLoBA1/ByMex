<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservation_passengers', function (Blueprint $table) {
            $table->timestamp('cancelled_at')->nullable()->after('action_notes');
            $table->text('cancellation_reason')->nullable()->after('cancelled_at');
            $table->decimal('cancellation_retained_amount', 10, 2)->default(0)->after('cancellation_reason');
        });
    }

    public function down(): void
    {
        Schema::table('reservation_passengers', function (Blueprint $table) {
            $table->dropColumn(['cancelled_at', 'cancellation_reason', 'cancellation_retained_amount']);
        });
    }
};
