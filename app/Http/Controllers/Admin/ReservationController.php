<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\ReservationPassenger;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function show($id)
    {
        $reservation = Reservation::with(['client', 'tour', 'passengers.boardingPoint', 'seats'])->findOrFail($id);
        return view('admin.reservations.show', compact('reservation'));
    }

    public function validatePassenger(Request $request, $id)
    {
        $passenger = ReservationPassenger::findOrFail($id);

        $request->validate([
            'validation_status' => 'required|in:validated,rejected',
            'validation_notes' => 'nullable|string|max:255',
        ]);

        $newStatus = $request->validation_status;
        $passenger->validation_status = $newStatus;
        $passenger->validation_notes = $request->validation_notes;

        // --- AJUSTE FINANCIERO DEL PASAJERO ---
        if ($newStatus === 'rejected') {
            // Pierde el descuento: paga tarifa completa
            $passenger->discount_amount = 0;
            $passenger->final_price = $passenger->base_price;
        } elseif ($newStatus === 'validated') {
            // Restaura el descuento original
            $passenger->discount_amount = $passenger->original_discount_amount;
            $passenger->final_price = $passenger->base_price - $passenger->original_discount_amount;
        }

        $passenger->save();

        // --- RECÁLCULO DE TOTALES DE LA RESERVA PADRE ---
        $reservation = $passenger->reservation;
        $allPassengers = $reservation->passengers()->get();

        $newDiscountTotal = $allPassengers->sum('discount_amount');
        $newTotalAmount = $allPassengers->sum('final_price');

        $amountAlreadyPaid = $reservation->total_amount - $reservation->balance_due;
        $newBalanceDue = max(0, $newTotalAmount - $amountAlreadyPaid);

        $reservation->discount_total = $newDiscountTotal;
        $reservation->total_amount = $newTotalAmount;
        $reservation->balance_due = $newBalanceDue;
        $reservation->save();

        $label = $newStatus === 'validated' ? 'Validado' : 'Rechazado';
        return back()->with('success', "Pasajero {$passenger->name} marcado como {$label}. Totales de la reserva actualizados.");
    }

    public function updateStatus(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:pending,paid,cancelled',
        ]);

        $reservation->status = $request->status;
        
        if ($request->status == 'paid') {
            $reservation->balance_due = 0;
            \App\Models\ReservationSeat::where('reservation_id', $reservation->id)
                ->update(['status' => 'paid']);
        } elseif ($request->status == 'cancelled') {
            \App\Models\ReservationSeat::where('reservation_id', $reservation->id)
                ->update(['status' => 'available']);
        }

        $reservation->save();

        return back()->with('success', 'Estado de la reserva actualizado.');
    }
}
