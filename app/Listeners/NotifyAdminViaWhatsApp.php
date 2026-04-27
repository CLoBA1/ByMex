<?php

namespace App\Listeners;

use App\Events\ReservationCreated;
use App\Models\AdminNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class NotifyAdminViaWhatsApp implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(ReservationCreated $event): void
    {
        try {
            $reservation = $event->reservation;
            
            // Reload relationships if needed
            $reservation->loadMissing(['tour', 'client', 'seats']);
            
            $client = $reservation->client;
            $tour = $reservation->tour;
            
            // Formatear asientos
            $seatNumbers = $reservation->seats->pluck('seat_number')->toArray();
            $seatList = implode(', ', $seatNumbers);
            
            $whatsappMsg = urlencode("🚨 Nueva Reserva ByMex\n\n🎫 Folio: RES-" . str_pad($reservation->id, 4, '0', STR_PAD_LEFT) . "\n👤 Cliente: {$client->name}\n📞 Tel: {$client->phone}\n🚌 Tour: {$tour->title}\n💺 Asientos: {$seatList}\n💰 Total: $" . number_format($reservation->total_amount, 0) . " MXN\n⏳ Estado: Pendiente de pago");

            AdminNotification::create([
                'type' => 'reservation_new',
                'title' => "Nueva Reserva: {$client->name}",
                'message' => "Tour: {$tour->title} | Asientos: {$seatList} | Total: $" . number_format($reservation->total_amount, 0),
                'link' => route('admin.tours.show', $tour->id),
                'whatsapp_link' => "https://wa.me/?text={$whatsappMsg}",
            ]);
            
        } catch (\Exception $e) {
            Log::error("Error in NotifyAdminViaWhatsApp Listener: " . $e->getMessage());
        }
    }
}
