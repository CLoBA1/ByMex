<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
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
