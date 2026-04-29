<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reservation;
use Illuminate\Support\Str;

class BackfillTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reservations:backfill-tokens';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera public_tokens para las reservaciones antiguas';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando generacion de tokens...');
        
        $reservations = Reservation::whereNull('public_token')->get();
        $count = 0;
        
        foreach ($reservations as $res) {
            $res->update(['public_token' => Str::random(32)]);
            $count++;
        }
        
        $this->info("Proceso terminado. Se actualizaron {$count} reservaciones antiguas.");
    }
}
