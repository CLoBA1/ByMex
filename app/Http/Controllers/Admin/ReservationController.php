<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\ReservationPassenger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ReservationController extends Controller
{
    public function show($id)
    {
        $reservation = Reservation::with(['client', 'tour', 'passengers.boardingPoint', 'passengers.documents', 'seats', 'payments.approvedBy', 'adjustments.user'])->findOrFail($id);
        $settings = \App\Models\PaymentSetting::first();
        return view('admin.reservations.show', compact('reservation', 'settings'));
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
        $this->recalculateReservation($reservation);

        $label = $newStatus === 'validated' ? 'Validado' : 'Rechazado';
        return back()->with('success', "Pasajero {$passenger->name} marcado como {$label}. Totales de la reserva actualizados.");
    }

    public function updatePassengerStatus(Request $request, $id)
    {
        $passenger = ReservationPassenger::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:active,no_show,boarded',
            'action_notes' => 'nullable|string|max:255',
        ]);

        // Block status change on already cancelled passengers
        if ($passenger->status->value === 'cancelled') {
            return back()->with('error', 'No se puede cambiar el estado de un pasajero cancelado.');
        }

        $passenger->status = $request->status;
        if ($request->action_notes) {
            $passenger->action_notes = $request->action_notes;
        }
        $passenger->save();

        return back()->with('success', "Estado del pasajero actualizado a {$request->status}.");
    }

    /**
     * Cancel a passenger with financial traceability.
     * Requires cancellation reason and retained amount via modal.
     */
    public function cancelPassenger(Request $request, $id)
    {
        $passenger = ReservationPassenger::findOrFail($id);

        // Block double cancellation
        if ($passenger->status->value === 'cancelled') {
            return back()->with('error', 'Este pasajero ya está cancelado.');
        }

        $request->validate([
            'cancellation_reason' => 'required|string|max:1000',
            'retained_amount' => 'required|numeric|min:0|max:' . $passenger->final_price,
        ], [
            'cancellation_reason.required' => 'El motivo de cancelación es obligatorio.',
            'retained_amount.required' => 'El monto retenido es obligatorio.',
            'retained_amount.max' => 'El monto retenido no puede ser mayor al costo neto del pasajero ($' . number_format($passenger->final_price, 2) . ').',
        ]);

        $reservation = $passenger->reservation;

        DB::transaction(function () use ($passenger, $reservation, $request) {
            // 1. Mark passenger as cancelled
            $passenger->status = 'cancelled';
            $passenger->cancelled_at = now();
            $passenger->cancellation_reason = $request->cancellation_reason;
            $passenger->cancellation_retained_amount = $request->retained_amount;
            $passenger->action_notes = $request->cancellation_reason;
            $passenger->save();

            // 2. Release seat from map
            \App\Models\ReservationSeat::where('reservation_id', $passenger->reservation_id)
                ->where('seat_number', $passenger->seat_number)
                ->delete();

            // 3. Create penalty adjustment if retained_amount > 0
            if ($request->retained_amount > 0) {
                \App\Models\ReservationAdjustment::create([
                    'reservation_id' => $reservation->id,
                    'type' => 'penalty',
                    'amount' => $request->retained_amount,
                    'notes' => "Penalización por cancelación de pasajero {$passenger->name} (Asiento {$passenger->seat_number}). Motivo: {$request->cancellation_reason}",
                    'user_id' => auth()->id(),
                ]);
            }

            // 4. Recalculate reservation totals
            $this->recalculateReservation($reservation);
        });

        // Notify admin
        try {
            $admin = \App\Models\AdminOwner::first();
            if ($admin) {
                $retainedLabel = $request->retained_amount > 0
                    ? "Retención: \$" . number_format($request->retained_amount, 2)
                    : 'Sin retención';
                $admin->notify(new \App\Notifications\SystemAlert(
                    'Pasajero Cancelado',
                    "El pasajero {$passenger->name} fue cancelado en la reserva #{$reservation->id}. {$retainedLabel}.",
                    route('admin.reservations.show', $reservation->id),
                    'fa-solid fa-user-xmark'
                ));
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Notificación de cancelación falló: ' . $e->getMessage());
        }

        $msg = $request->retained_amount > 0
            ? "Pasajero cancelado correctamente. El asiento fue liberado y se registró la retención por cancelación de \$" . number_format($request->retained_amount, 2) . "."
            : 'Pasajero cancelado correctamente. El asiento fue liberado sin retención aplicada.';

        return back()->with('success', $msg);
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
        $pricing = $reservationService->calculatePassengerPricing($passenger->reservation->tour, $passenger->passenger_type);
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
        $reservation->refresh();
        $allActivePassengers = $reservation->passengers()->where('status', '!=', 'cancelled')->get();

        // Sum of cancellation penalties from adjustments
        $penaltiesTotal = $reservation->adjustments()->where('type', 'penalty')->sum('amount');
        // Sum of refunds from adjustments
        $refundsTotal = $reservation->adjustments()->where('type', 'refund')->sum('amount');

        if ($allActivePassengers->isEmpty() && $penaltiesTotal == 0) {
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
        $activePassengersTotal = $allActivePassengers->sum('final_price');

        // Total = active passengers + penalties - refunds
        $newTotalAmount = $activePassengersTotal + $penaltiesTotal - $refundsTotal;
        $newTotalAmount = max(0, $newTotalAmount);

        $amountAlreadyPaid = $reservation->payments()->where('status', 'approved')->sum('amount');
        $newBalanceDue = max(0, $newTotalAmount - $amountAlreadyPaid);

        if ($amountAlreadyPaid > $newTotalAmount) {
            try {
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
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Notificación de saldo a favor falló: ' . $e->getMessage());
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
            'notes' => 'nullable|string|max:500',
        ]);

        $notificationData = null;

        DB::transaction(function () use ($reservation, $request, &$notificationData) {
            \App\Models\Payment::create([
                'reservation_id' => $reservation->id,
                'amount' => $request->amount,
                'notes' => $request->notes ?? null,
                'status' => 'approved',
                'approved_by' => auth()->id(),
            ]);

            $newBalance = max(0, $reservation->balance_due - $request->amount);
            $reservation->balance_due = $newBalance;

            if ($newBalance == 0) {
                $reservation->status = \App\Enums\ReservationStatus::PAID;
                \App\Models\ReservationSeat::where('reservation_id', $reservation->id)
                    ->update(['status' => 'paid']);

                // Otorgar puntos de bonificación si aplica
                if ($reservation->tour && $reservation->tour->duration_days > 0) {
                    \App\Models\BonusRequest::create([
                        'client_id' => $reservation->client_id,
                        'request_type' => 'Viaje: ' . $reservation->tour->title,
                        'requested_bonus_count' => $reservation->tour->duration_days,
                        'status' => 'approved',
                        'admin_notes' => 'Otorgado automáticamente por liquidación de reserva #' . $reservation->id
                    ]);
                }

                $notificationData = [
                    'title' => 'Reserva Liquidada',
                    'message' => "La reserva #{$reservation->id} de {$reservation->client->name} ha sido pagada en su totalidad.",
                    'url' => route('admin.reservations.show', $reservation->id),
                    'icon' => 'fa-solid fa-check-double',
                ];
            } else {
                $reservation->status = \App\Enums\ReservationStatus::PARTIAL;
                \App\Models\ReservationSeat::where('reservation_id', $reservation->id)
                    ->update(['status' => 'pending']);

                $notificationData = [
                    'title' => 'Abono Registrado',
                    'message' => "Se registró un abono de \$" . number_format($request->amount, 2) . " en la reserva #{$reservation->id}.",
                    'url' => route('admin.reservations.show', $reservation->id),
                    'icon' => 'fa-solid fa-money-bill-wave',
                ];
            }
            $reservation->save();
        });

        // Enviar notificación FUERA de la transacción para que un fallo de SMTP no reviente el pago
        if ($notificationData) {
            try {
                $admin = \App\Models\AdminOwner::first();
                if ($admin) {
                    $admin->notify(new \App\Notifications\SystemAlert(
                        $notificationData['title'],
                        $notificationData['message'],
                        $notificationData['url'],
                        $notificationData['icon']
                    ));
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Notificación de abono falló (el pago SÍ se registró correctamente): ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Pago registrado correctamente. Saldo actualizado.');
    }

    public function downloadVoucher($id)
    {
        $payment = \App\Models\Payment::with('reservation.client', 'reservation.tour')->findOrFail($id);
        $paymentSettings = \App\Models\PaymentSetting::first();
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.voucher', compact('payment', 'paymentSettings'));
        return $pdf->download('Comprobante_Pago_' . str_pad($payment->id, 5, '0', STR_PAD_LEFT) . '.pdf');
    }

    public function downloadTicket($id)
    {
        $reservation = Reservation::with(['tour.boardingPoints', 'client', 'seats', 'passengers', 'payments'])->findOrFail($id);
        $paymentSettings = \App\Models\PaymentSetting::first();
        $activeBanks = \App\Models\BankAccount::where('is_active', true)->orderBy('sort_order')->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.ticket', compact('reservation', 'paymentSettings', 'activeBanks'));
        return $pdf->download('Ticket_ByMex_' . str_pad($reservation->id, 5, '0', STR_PAD_LEFT) . '.pdf');
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

    /**
     * Upload a document for a specific passenger.
     */
    public function uploadDocument(Request $request, $passengerId)
    {
        $passenger = ReservationPassenger::findOrFail($passengerId);

        $request->validate([
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $file = $request->file('document');
        $path = $file->store('passenger-documents', 'public');

        \App\Models\PassengerDocument::create([
            'reservation_passenger_id' => $passenger->id,
            'original_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'mime_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
        ]);

        return back()->with('success', "Documento subido para {$passenger->name}.");
    }

    /**
     * Delete a passenger document.
     */
    public function deleteDocument($documentId)
    {
        $doc = \App\Models\PassengerDocument::findOrFail($documentId);

        if (Storage::disk('public')->exists($doc->file_path)) {
            Storage::disk('public')->delete($doc->file_path);
        }

        $doc->delete();

        return back()->with('success', 'Documento eliminado correctamente.');
    }

    /**
     * Download a passenger document securely (Admin).
     */
    public function downloadDocument($documentId)
    {
        $doc = \App\Models\PassengerDocument::findOrFail($documentId);

        if (!Storage::disk('public')->exists($doc->file_path)) {
            return back()->with('error', 'El archivo físico no se encuentra disponible.');
        }

        return Storage::disk('public')->download($doc->file_path, $doc->original_name);
    }
    public function updateSeat(\Illuminate\Http\Request $request, $id)
    {
        $request->validate([
            'seat_number' => 'required|integer|min:1',
        ]);

        $passenger = \App\Models\ReservationPassenger::findOrFail($id);
        
        if ($passenger->status->value === 'cancelled') {
            return back()->with('error', 'No se puede cambiar el asiento de un pasajero cancelado.');
        }

        $nuevoAsiento = $request->seat_number;
        
        if ($nuevoAsiento == $passenger->seat_number) {
            return back()->with('info', 'El asiento es el mismo, no hubo cambios.');
        }

        // Verificar si está ocupado en el mismo tour (ignorando la reserva actual)
        $isOccupied = \App\Models\ReservationSeat::where('tour_id', $passenger->reservation->tour_id)
            ->where('seat_number', $nuevoAsiento)
            ->where('reservation_id', '!=', $passenger->reservation_id)
            ->exists();

        if ($isOccupied) {
            return back()->with('error', "El asiento $nuevoAsiento ya está ocupado por otra reservación.");
        }

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($passenger, $nuevoAsiento) {
                // Actualizar la tabla pivot de ReservationSeat
                \App\Models\ReservationSeat::where('reservation_id', $passenger->reservation_id)
                    ->where('seat_number', $passenger->seat_number)
                    ->update(['seat_number' => $nuevoAsiento]);

                // Actualizar el pasajero
                $passenger->seat_number = $nuevoAsiento;
                $passenger->save();
            });
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->errorInfo[1] == 1062 || $e->getCode() == 23000) {
                return back()->with('error', "Error de concurrencia: el asiento $nuevoAsiento fue ocupado en este instante.");
            }
            throw $e;
        }

        return back()->with('success', "Asiento actualizado a $nuevoAsiento correctamente.");
    }
}
