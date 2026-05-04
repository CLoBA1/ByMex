<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReservationRequest;
use App\Services\ReservationService;
use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    protected $reservationService;

    public function __construct(ReservationService $reservationService)
    {
        $this->reservationService = $reservationService;
    }

    public function store(StoreReservationRequest $request)
    {
        try {
            $reservation = $this->reservationService->processNewReservation($request->toDTO());
            
            $admin = \App\Models\AdminOwner::first();
            if ($admin) {
                $admin->notify(new \App\Notifications\SystemAlert(
                    'Nueva Reservación',
                    "El cliente {$reservation->client->name} ha creado la reserva #{$reservation->id} para el tour {$reservation->tour->title}.",
                    route('admin.reservations.show', $reservation->id),
                    'fa-solid fa-ticket'
                ));
            }

            return redirect()->route('reservations.success', $reservation->public_token);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function success($token)
    {
        $reservation = Reservation::with(['tour', 'client', 'seats', 'passengers'])
            ->where('public_token', $token)
            ->firstOrFail();
            
        $paymentSettings = \App\Models\PaymentSetting::first();
        $activeBanks = \App\Models\BankAccount::where('is_active', true)->orderBy('sort_order')->get();
            
        return view('checkout.success', compact('reservation', 'paymentSettings', 'activeBanks'));
    }

    public function downloadTicket($token)
    {
        $reservation = Reservation::with(['tour', 'client', 'seats', 'passengers'])
            ->where('public_token', $token)
            ->firstOrFail();
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.ticket', compact('reservation'));
        
        $filename = 'Ticket_ByMex_' . str_pad($reservation->id, 5, '0', STR_PAD_LEFT) . '.pdf';
        
        return $pdf->download($filename);
    }

    public function mockPay($token)
    {
        abort_unless(app()->environment('local', 'testing'), 404);

        $reservation = Reservation::where('public_token', $token)->firstOrFail();
        
        sleep(2);
        
        $reservation->status = 'paid';
        $reservation->balance_due = 0;
        $reservation->save();

        \App\Models\ReservationSeat::where('reservation_id', $reservation->id)
            ->update(['status' => 'paid']);

        return redirect()->route('reservations.success', $reservation->public_token)
            ->with('success', '¡Pago procesado exitosamente vía Tarjeta de Crédito (Mock)!');
    }
}
