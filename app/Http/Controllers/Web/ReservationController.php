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
            return redirect()->route('reservations.success', $reservation->id);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function success($id)
    {
        $reservation = Reservation::with(['tour', 'client', 'seats'])->findOrFail($id);
        return view('checkout.success', compact('reservation'));
    }

    public function downloadTicket($id)
    {
        $reservation = Reservation::with(['tour', 'client', 'seats'])->findOrFail($id);
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.ticket', compact('reservation'));
        
        $filename = 'Ticket_ByMex_' . str_pad($reservation->id, 5, '0', STR_PAD_LEFT) . '.pdf';
        
        return $pdf->download($filename);
    }

    public function mockPay($id)
    {
        $reservation = Reservation::findOrFail($id);
        
        sleep(2);
        
        $reservation->status = 'paid';
        $reservation->balance_due = 0;
        $reservation->save();

        \App\Models\ReservationSeat::where('reservation_id', $reservation->id)
            ->update(['status' => 'paid']);

        return redirect()->route('reservations.success', $reservation->id)
            ->with('success', '¡Pago procesado exitosamente vía Tarjeta de Crédito (Mock)!');
    }
}
