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
    protected $description = 'Limpia datos transaccionales (reservaciones, clientes, pagos, etc.) para dejar la BD lista para producción.';

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
            
            // Advertencia de archivos
            $this->warn("\nNOTA SOBRE ARCHIVOS:");
            $this->line("Los registros de 'passenger_documents' y comprobantes en 'payments' fueron eliminados de la BD.");
            $this->line("Se recomienda revisar la carpeta storage/app/public/documents y storage/app/public/payments/proofs y vaciarlas manualmente si contienen archivos de prueba.");

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
