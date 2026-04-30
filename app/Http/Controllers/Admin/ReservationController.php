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
        $reservation = Reservation::with(['client', 'tour', 'passengers', 'seats'])->findOrFail($id);
        return view('admin.reservations.show', compact('reservation'));
    }

    public function validatePassenger(Request $request, $id)
    {
        $passenger = ReservationPassenger::findOrFail($id);

        $request->validate([
            'validation_status' => 'required|in:validated,rejected',
            'validation_notes' => 'nullable|string|max:255',
        ]);

        $passenger->validation_status = $request->validation_status;
        $passenger->validation_notes = $request->validation_notes;
        $passenger->save();

        return back()->with('success', 'Pasajero ' . $passenger->name . ' marcado como ' . ($request->validation_status == 'validated' ? 'Validado' : 'Rechazado') . '.');
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
