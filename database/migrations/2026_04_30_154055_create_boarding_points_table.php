<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('boarding_points', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('color_label', 50);  // ej. "azul", "verde"
            $table->string('color_hex', 7)->default('#6b7280'); // ej. "#2563eb"
            $table->boolean('is_active')->default(true);
            $table->string('notes', 255)->nullable();
            $table->timestamps();
        });

        // Seed inicial con los 4 puntos operativos del negocio
        \DB::table('boarding_points')->insert([
            ['name' => 'Iguala',                'color_label' => 'Azul',     'color_hex' => '#2563eb', 'is_active' => true, 'notes' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Chilpancingo',          'color_label' => 'Verde',    'color_hex' => '#16a34a', 'is_active' => true, 'notes' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Cuernavaca / Morelos',  'color_label' => 'Café',     'color_hex' => '#92400e', 'is_active' => true, 'notes' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Acapulco',              'color_label' => 'Amarillo', 'color_hex' => '#ca8a04', 'is_active' => true, 'notes' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('boarding_points');
    }
};
