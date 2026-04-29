<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ExpirePendingReservations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reservations:expire-pending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancela reservaciones pendientes cuyo tiempo de expiración ha vencido y libera sus asientos.';

    /**
     * Execute the console command.
     */
    public function handle(\App\Services\ReservationService $reservationService)
    {
        $this->info('Iniciando limpieza de reservaciones expiradas...');
        $count = $reservationService->cancelExpiredReservations();
        $this->info("Proceso terminado. Se cancelaron y liberaron {$count} reservaciones.");
    }
}
