<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Reservation;
use App\Models\Payment;
use App\Enums\ReservationStatus;
use App\Enums\SeatStatus;
use App\Models\ReservationSeat;

class MercadoPagoWebhookController extends Controller
{
    public function handle(Request $request)
    {
        Log::info('MercadoPago Webhook Received', $request->all());

        $topic = $request->input('type') ?? $request->input('topic');
        
        if ($topic === 'payment') {
            $paymentId = $request->input('data.id') ?? $request->query('id');

            if ($paymentId) {
                try {
                    $this->processPayment($paymentId);
                } catch (\Exception $e) {
                    Log::error('Error processing MP Payment Webhook: ' . $e->getMessage());
                    return response()->json(['error' => 'Server Error'], 500);
                }
            }
        }

        return response()->json(['status' => 'success']);
    }

    protected function processPayment($paymentId)
    {
        $accessToken = config('services.mercadopago.access_token');
        
        $response = Http::withToken($accessToken)->get("https://api.mercadopago.com/v1/payments/{$paymentId}");

        if ($response->failed()) {
            throw new \Exception('No se pudo verificar el pago en MP: ' . $response->body());
        }

        $paymentData = $response->json();
        
        if ($paymentData['status'] === 'approved') {
            $reservationId = $paymentData['external_reference'];
            
            if (!$reservationId) {
                Log::warning("Mercado Pago Webhook: Pago aprobado {$paymentId} sin external_reference.");
                return;
            }

            $reservation = Reservation::find($reservationId);
            
            if (!$reservation) {
                Log::warning("Mercado Pago Webhook: Reserva {$reservationId} no encontrada.");
                return;
            }

            if ($reservation->status->value === 'paid') {
                Log::info("Reserva #{$reservation->id} ya está PAGADA. Ignorando MP Webhook.");
                return;
            }

            $existingPayment = Payment::where('stripe_payment_intent_id', (string)$paymentId)->first();
            if ($existingPayment) {
                Log::info("Pago MP {$paymentId} ya procesado anteriormente.");
                return;
            }

            $amountPaid = $paymentData['transaction_amount'];

            Payment::create([
                'reservation_id' => $reservation->id,
                'amount' => $amountPaid,
                'status' => 'approved',
                'uploaded_at' => now(),
                'stripe_session_id' => 'mp_' . uniqid(), // Reuse the field or leave empty
                'stripe_payment_intent_id' => (string)$paymentId,
                'payment_method' => 'mercadopago',
            ]);

            $newBalance = max(0, $reservation->balance_due - $amountPaid);

            $reservation->update([
                'status' => $newBalance == 0 ? ReservationStatus::PAID : ReservationStatus::PARTIAL,
                'balance_due' => $newBalance,
            ]);

            if ($newBalance == 0) {
                ReservationSeat::where('reservation_id', $reservation->id)
                    ->update(['status' => SeatStatus::PAID]);
            }

            Log::info("Reserva #{$reservation->id} procesó abono de {$amountPaid} vía Mercado Pago.");
        }
    }
}
