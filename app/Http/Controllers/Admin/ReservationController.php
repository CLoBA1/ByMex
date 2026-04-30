<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\ReservationPassenger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
    public function show($id)
    {
        $reservation = Reservation::with(['client', 'tour', 'passengers.boardingPoint', 'seats', 'payments', 'adjustments.user'])->findOrFail($id);
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
        $allPassengers = $reservation->passengers()->where('status', '!=', 'cancelled')->get();

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

    public function updatePassengerStatus(Request $request, $id)
    {
        $passenger = ReservationPassenger::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:active,cancelled,no_show,boarded',
            'action_notes' => 'nullable|string|max:255',
        ]);

        $passenger->status = $request->status;
        if ($request->action_notes) {
            $passenger->action_notes = $request->action_notes;
        }
        $passenger->save();

        if ($request->status === 'cancelled') {
            \App\Models\ReservationSeat::where('reservation_id', $passenger->reservation_id)
                ->where('seat_number', $passenger->seat_number)
                ->delete();

            $this->recalculateReservation($passenger->reservation);
        }

        return back()->with('success', "Estado del pasajero actualizado a {$request->status}.");
    }

    public function updatePassengerType(Request $request, $id, \App\Services\ReservationService $reservationService)
    {
        $passenger = ReservationPassenger::findOrFail($id);

        $request->validate([
            'passenger_type' => 'required|string|max:50',
            'action_notes' => 'nullable|string|max:255',
        ]);

        // Evitar cambios si el pasajero está cancelado
        if ($passenger->status->value === 'cancelled') {
            return back()->with('error', 'No se puede cambiar el tipo de un pasajero cancelado.');
        }

        $oldType = $passenger->passenger_type;
        $passenger->passenger_type = $request->passenger_type;
        if ($request->action_notes) {
            $passenger->action_notes = $request->action_notes;
        }

        // Recalcular precios de este pasajero usando la lógica del servicio
        $pricing = $reservationService->calculatePassengerPricing((float)$passenger->base_price, $passenger->passenger_type);
        $passenger->discount_amount = $pricing['discount_amount'];
        $passenger->original_discount_amount = $pricing['discount_amount'];
        $passenger->final_price = $pricing['final_price'];

        // Si cambia a una categoría con descuento, resetear validación a pendiente
        if ($pricing['discount_amount'] > 0) {
            $passenger->validation_status = 'pending';
        } else {
            $passenger->validation_status = 'validated'; // Adulto regular
        }

        $passenger->save();
        $this->recalculateReservation($passenger->reservation);

        return back()->with('success', "Tipo de pasajero actualizado de {$oldType} a {$request->passenger_type}. Totales recalculados.");
    }

    private function recalculateReservation(Reservation $reservation)
    {
        $allActivePassengers = $reservation->passengers()->where('status', '!=', 'cancelled')->get();

        if ($allActivePassengers->isEmpty()) {
            $reservation->status = \App\Enums\ReservationStatus::CANCELLED;
            $reservation->subtotal = 0;
            $reservation->discount_total = 0;
            $reservation->total_amount = 0;
            $reservation->balance_due = 0;
            $reservation->save();
            return;
        }

        $newSubtotal = $allActivePassengers->sum('base_price');
        $newDiscountTotal = $allActivePassengers->sum('discount_amount');
        $newTotalAmount = $allActivePassengers->sum('final_price');

        $amountAlreadyPaid = $reservation->total_amount - $reservation->balance_due;
        $newBalanceDue = max(0, $newTotalAmount - $amountAlreadyPaid);

        $reservation->subtotal = $newSubtotal;
        $reservation->discount_total = $newDiscountTotal;
        $reservation->total_amount = $newTotalAmount;
        $reservation->balance_due = $newBalanceDue;
        $reservation->save();
    }

    public function storePayment(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);

        $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ]);

        DB::transaction(function () use ($reservation, $request) {
            \App\Models\Payment::create([
                'reservation_id' => $reservation->id,
                'amount' => $request->amount,
                'status' => 'approved',
                'approved_by' => auth()->id(),
            ]);

            $newBalance = max(0, $reservation->balance_due - $request->amount);
            $reservation->balance_due = $newBalance;

            if ($newBalance == 0) {
                $reservation->status = \App\Enums\ReservationStatus::PAID;
                \App\Models\ReservationSeat::where('reservation_id', $reservation->id)
                    ->update(['status' => 'paid']);
            } else {
                $reservation->status = \App\Enums\ReservationStatus::PARTIAL;
            }

            $reservation->save();
        });

        return back()->with('success', 'Pago registrado correctamente. Saldo actualizado.');
    }

    public function updateStatus(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:cancelled',
        ]);

        $reservation->status = $request->status;
        
        if ($request->status == 'cancelled') {
            \App\Models\ReservationSeat::where('reservation_id', $reservation->id)->delete();
        }

        $reservation->save();

        return back()->with('success', 'Estado de la reserva actualizado.');
    }

    public function storeAdjustment(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);

        $request->validate([
            'type' => 'required|in:refund,penalty,note',
            'amount' => 'required_unless:type,note|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        \App\Models\ReservationAdjustment::create([
            'reservation_id' => $reservation->id,
            'type' => $request->type,
            'amount' => $request->type === 'note' ? 0 : $request->amount,
            'notes' => $request->notes,
            'user_id' => auth()->id(),
        ]);

        $typeLabels = ['refund' => 'Devolución', 'penalty' => 'Penalización', 'note' => 'Nota'];
        return back()->with('success', "{$typeLabels[$request->type]} registrada correctamente.");
    }
}
