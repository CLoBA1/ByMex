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
        $reservation = Reservation::with(['client', 'tour', 'passengers.boardingPoint', 'seats', 'payments.approvedBy', 'adjustments.user'])->findOrFail($id);
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

            $admin = \App\Models\AdminOwner::first();
            if ($admin) {
                $admin->notify(new \App\Notifications\SystemAlert(
                    'Pasajero Cancelado',
                    "El pasajero {$passenger->name} fue cancelado en la reserva #{$passenger->reservation->id}.",
                    route('admin.reservations.show', $passenger->reservation_id),
                    'fa-solid fa-user-xmark'
                ));
            }
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

        if ($amountAlreadyPaid > $newTotalAmount) {
            $admin = \App\Models\AdminOwner::first();
            if ($admin) {
                $surplus = number_format($amountAlreadyPaid - $newTotalAmount, 2);
                $admin->notify(new \App\Notifications\SystemAlert(
                    'Saldo a Favor Generado',
                    "La reserva #{$reservation->id} ahora tiene un saldo a favor de \${$surplus} por recálculo.",
                    route('admin.reservations.show', $reservation->id),
                    'fa-solid fa-hand-holding-dollar'
                ));
            }
        }

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

                $admin = \App\Models\AdminOwner::first();
                if ($admin) {
                    $admin->notify(new \App\Notifications\SystemAlert(
                        'Reserva Liquidada',
                        "La reserva #{$reservation->id} de {$reservation->client->name} ha sido pagada en su totalidad.",
                        route('admin.reservations.show', $reservation->id),
                        'fa-solid fa-check-double'
                    ));
                }
            } else {
                $reservation->status = \App\Enums\ReservationStatus::PARTIAL;
                \App\Models\ReservationSeat::where('reservation_id', $reservation->id)
                    ->update(['status' => 'reserved']);

                $admin = \App\Models\AdminOwner::first();
                if ($admin) {
                    $admin->notify(new \App\Notifications\SystemAlert(
                        'Abono Registrado',
                        "Se registró un abono de \$" . number_format($request->amount, 2) . " en la reserva #{$reservation->id}.",
                        route('admin.reservations.show', $reservation->id),
                        'fa-solid fa-money-bill-wave'
                    ));
                }
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

        if ($request->status === 'cancelled') {
            $admin = \App\Models\AdminOwner::first();
            if ($admin) {
                $admin->notify(new \App\Notifications\SystemAlert(
                    'Reserva Cancelada',
                    "La reserva #{$reservation->id} fue cancelada manualmente.",
                    route('admin.reservations.show', $reservation->id),
                    'fa-solid fa-ban'
                ));
            }
        }

        return back()->with('success', 'Estado de la reservación actualizado.');
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

    public function destroy($id)
    {
        $reservation = Reservation::with(['payments', 'adjustments'])->findOrFail($id);

        // Safety checks: only allow deletion of cancelled/expired without financial history
        $safeStatuses = ['cancelled', 'expired'];
        if (!in_array($reservation->status->value, $safeStatuses)) {
            return back()->with('error', 'Solo se pueden eliminar reservaciones canceladas o expiradas.');
        }

        if ($reservation->payments->count() > 0) {
            return back()->with('error', 'No se puede eliminar: esta reservación tiene pagos registrados.');
        }

        if ($reservation->adjustments->count() > 0) {
            return back()->with('error', 'No se puede eliminar: esta reservación tiene ajustes financieros registrados.');
        }

        $tourId = $reservation->tour_id;

        // Delete related records safely
        DB::transaction(function () use ($reservation) {
            \App\Models\ReservationSeat::where('reservation_id', $reservation->id)->delete();
            \App\Models\ReservationPassenger::where('reservation_id', $reservation->id)->delete();
            $reservation->delete();
        });

        return redirect()->route('admin.tours.show', $tourId)->with('success', "Reservación #{$id} eliminada definitivamente.");
    }

    /**
     * Listado operativo de reservaciones con saldo a favor pendiente de atención.
     * Solo lectura – no altera estados, pagos ni cálculos.
     */
    public function surplusList(Request $request)
    {
        $query = Reservation::with(['client', 'tour'])
            ->withSum(['payments as amount_paid' => fn($q) => $q->where('status', 'approved')], 'amount')
            ->withSum('adjustments as adjustments_total', 'amount');

        // Filtro por búsqueda (folio o nombre de cliente)
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('client', fn($c) => $c->where('name', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%"));
            });
        }

        // Filtro por estado
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $allReservations = $query->orderBy('updated_at', 'desc')->get();

        // Filtrar solo las que tienen saldo a favor disponible > 0
        $reservations = $allReservations->filter(function ($r) {
            $paid = (float) ($r->amount_paid ?? 0);
            $total = (float) $r->total_amount;
            $adjustments = (float) ($r->adjustments_total ?? 0);
            $surplusBruto = max(0, $paid - $total);
            $available = max(0, $surplusBruto - $adjustments);
            return $available > 0;
        })->values();

        // Totalizadores para las cards
        $totalCount = $reservations->count();
        $totalSurplus = $reservations->sum(function ($r) {
            $paid = (float) ($r->amount_paid ?? 0);
            $total = (float) $r->total_amount;
            $adjustments = (float) ($r->adjustments_total ?? 0);
            return max(0, max(0, $paid - $total) - $adjustments);
        });

        return view('admin.reservations.surplus', compact('reservations', 'totalCount', 'totalSurplus'));
    }
}
