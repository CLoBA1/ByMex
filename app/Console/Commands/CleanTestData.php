<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;

class CleanTestData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vbymex:clean-test-data {--force : Ejecutar el borrado real} {--dry-run : Mostrar conteos sin borrar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Limpia datos transaccionales (reservaciones, clientes, pagos, tours, etc.) para dejar la BD lista para producción.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isForce = $this->option('force');

        $this->info('====================================================');
        $this->info('   LIMPIEZA DE DATOS DE PRUEBA (MODO OPERATIVO)     ');
        $this->info('====================================================');

        if (!$isForce) {
            $this->warn('ATENCIÓN: Corriendo en modo DRY-RUN. No se borrará nada.');
            $this->line('Para ejecutar el borrado real, usa: php artisan vbymex:clean-test-data --force');
        } else {
            $this->error('ATENCIÓN: Estás a punto de ELIMINAR datos transaccionales de la base de datos.');
            if (app()->environment('production')) {
                $this->error('¡ESTÁS EN PRODUCCIÓN!');
                $this->warn('Confirma que ya existe un backup real desde hPanel/phpMyAdmin o mysqldump antes de continuar.');
            }
            if (!$this->confirm('¿Deseas continuar con el borrado definitivo?')) {
                $this->info('Operación cancelada por el usuario.');
                return;
            }
        }

        // Definir las tablas transaccionales a limpiar, EN ORDEN DE DEPENDENCIAS (Hijos primero, Padres después)
        // tours va AL FINAL porque reservations/reservation_seats tienen FK hacia tours
        $tablesToClean = [
            'passenger_documents',
            'reservation_adjustments',
            'payments',
            'reservation_seats',
            'reservation_passengers',
            'reservations',
            'bonus_requests',
            'clients',
            'admin_notifications',
            'notifications',
            'tours',           // ← se limpia al final, después de todos sus hijos
        ];

        // Obtener conteos iniciales
        $counts = [];
        $this->info("\nConteos actuales:");
        foreach ($tablesToClean as $table) {
            if (Schema::hasTable($table)) {
                $counts[$table] = DB::table($table)->count();
                $this->line("- $table: " . $counts[$table] . " registros");
            } else {
                $this->warn("- $table: [NO EXISTE]");
            }
        }

        if (!$isForce) {
            $this->info("\n[DRY RUN] Finalizado. Si ejecutas con --force, los registros anteriores serán eliminados.");
            return;
        }

        // Recopilar rutas de imágenes de tours ANTES de truncar
        $tourImages = [];
        if (Schema::hasTable('tours')) {
            $tourImages = DB::table('tours')
                ->whereNotNull('image')
                ->pluck('image')
                ->toArray();
        }

        // Ejecutar borrado
        $this->info("\nIniciando limpieza...");
        try {
            // Deshabilitar constraints momentáneamente
            Schema::disableForeignKeyConstraints();

            foreach ($tablesToClean as $table) {
                if (Schema::hasTable($table)) {
                    // Truncate borra los registros y resetea el auto-increment a 1
                    DB::table($table)->truncate();
                    $this->info("Tabla limpiada y auto-increment reseteado: $table");
                }
            }

            Schema::enableForeignKeyConstraints();
            $this->info("\n¡Limpieza ejecutada con éxito!");
            
            // Eliminar imágenes de tours del disco
            if (!empty($tourImages)) {
                $this->warn("\nEliminando imágenes de tours del storage...");
                $deleted = 0;
                foreach ($tourImages as $imagePath) {
                    $fullPath = storage_path('app/public/' . ltrim($imagePath, 'public/'));
                    // También intentar la ruta tal cual (en caso de que ya venga con 'tours/...')
                    $altPath  = storage_path('app/' . ltrim($imagePath, '/'));
                    if (file_exists($fullPath)) {
                        unlink($fullPath);
                        $deleted++;
                    } elseif (file_exists($altPath)) {
                        unlink($altPath);
                        $deleted++;
                    }
                }
                $this->info("Imágenes de tours eliminadas del disco: $deleted de " . count($tourImages));
                if ($deleted < count($tourImages)) {
                    $this->warn("Algunas imágenes no se encontraron en disco (posiblemente ya estaban eliminadas).");
                }
            } else {
                $this->line("No había imágenes de tours registradas en BD.");
            }

            // Advertencia de archivos restantes
            $this->warn("\nNOTA SOBRE ARCHIVOS ADICIONALES:");
            $this->line("- storage/app/public/documents      → Documentos de pasajeros (revisar manualmente).");
            $this->line("- storage/app/public/payments/proofs → Comprobantes de pago (revisar manualmente).");
            $this->line("NO se borran logos, banners ni imágenes del sitio.");

        } catch (\Exception $e) {
            Schema::enableForeignKeyConstraints();
            $this->error("Ocurrió un error durante la limpieza: " . $e->getMessage());
            return;
        }

        // Conteos finales
        $this->info("\nConteos finales:");
        foreach ($tablesToClean as $table) {
            if (Schema::hasTable($table)) {
                $finalCount = DB::table($table)->count();
                $this->line("- $table: " . $finalCount . " registros");
            }
        }
    }
}
