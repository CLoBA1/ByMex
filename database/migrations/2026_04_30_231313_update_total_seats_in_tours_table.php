<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Cambiar el valor por defecto de la columna
        Schema::table('tours', function (Blueprint $table) {
            $table->integer('total_seats')->default(49)->change();
        });

        // 2. Actualizar todos los tours existentes a 49 (ya que en hostinger están en 45 o el valor anterior)
        DB::table('tours')->update(['total_seats' => 49]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            $table->integer('total_seats')->default(45)->change();
        });
    }
};
