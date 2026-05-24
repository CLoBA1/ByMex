<?php

namespace App\Http\Controllers\Api;

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
        Log::info('[MP Webhook] Recibido', [
            'type'  => $request->input('type') ?? $request->input('topic'),
            'data'  => $request->input('data'),
            'query' => $request->query(),
        ]);

        // ── 1. Validar firma (opcional pero recomendado en producción) ──────
        $webhookSecret = config('services.mercadopago.webhook_secret');
        if (!empty($webhookSecret)) {
            if (!$this->validateSignature($request, $webhookSecret)) {
                Log::warning('[MP Webhook] Firma inválida rechazada.', [
                    'x-signature' => $request->header('x-signature'),
                    'x-request-id' => $request->header('x-request-id'),
                ]);
                return response()->json(['error' => 'Invalid signature'], 401);
            }
        }

        // ── 2. Solo procesar notificaciones de tipo "payment" ───────────────
        $topic = $request->input('type') ?? $request->input('topic');

        if ($topic !== 'payment') {
            Log::info("[MP Webhook] Tipo '{$topic}' ignorado (no es pago).");
            return response()->json(['status' => 'ignored']);
        }

        $paymentId = $request->input('data.id') ?? $request->query('id');

        if (!$paymentId) {
            Log::warning('[MP Webhook] Notificación de pago sin ID.');
            return response()->json(['error' => 'Missing payment ID'], 400);
        }

        try {
            $this->processPayment((string) $paymentId);
        } catch (\Throwable $e) {
            Log::error('[MP Webhook] Error procesando pago', [
                'mp_payment_id' => $paymentId,
                'error'         => $e->getMessage(),
            ]);
            // Responder 200 para evitar que MP reintente en errores de negocio.
            // Solo retornar 500 si fue un error de infraestructura real.
            return response()->json(['error' => 'Processing error'], 500);
        }

        return response()->json(['status' => 'ok']);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Valida la firma HMAC-SHA256 que envía Mercado Pago en x-signature.
    // Ref: https://www.mercadopago.com.mx/developers/es/docs/your-integrations/notifications/webhooks
    // ─────────────────────────────────────────────────────────────────────────
    protected function validateSignature(Request $request, string $secret): bool
    {
        $xSignature  = $request->header('x-signature', '');
        $xRequestId  = $request->header('x-request-id', '');
        $dataId      = $request->input('data.id') ?? $request->query('id', '');
        $ts          = null;
        $receivedHash = null;

        // Parsear x-signature: ts=...;v1=...
        foreach (explode(',', $xSignature) as $part) {
            [$key, $value] = array_pad(explode('=', trim($part), 2), 2, '');
            if ($key === 'ts')  $ts = $value;
            if ($key === 'v1')  $receivedHash = $value;
        }

        if (!$ts || !$receivedHash) {
            return false;
        }

        // Cadena canónica: id:{dataId};request-id:{xRequestId};ts:{ts};
        $manifest = "id:{$dataId};request-id:{$xRequestId};ts:{$ts};";
        $expected = hash_hmac('sha256', $manifest, $secret);

        return hash_equals($expected, $receivedHash);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Consulta el pago en la API de MP y lo aplica si está aprobado.
    // ─────────────────────────────────────────────────────────────────────────
    protected function processPayment(string $paymentId): void
    {
        // ── 1. Idempotencia: ya procesado antes? ─────────────────────────────
        $existing = Payment::where('mp_payment_id', $paymentId)->first();
        if ($existing) {
            Log::info("[MP Webhook] Pago {$paymentId} ya fue procesado (Payment #{$existing->id}). Ignorado.");
            return;
        }

        // ── 2. Consultar el pago real en MP API ──────────────────────────────
        $accessToken = config('services.mercadopago.access_token');
        if (empty($accessToken)) {
            throw new \RuntimeException('MERCADOPAGO_ACCESS_TOKEN no configurado.');
        }

        $response = Http::withToken($accessToken)
            ->get("https://api.mercadopago.com/v1/payments/{$paymentId}");

        if ($response->failed()) {
            throw new \RuntimeException("MP API error al consultar pago {$paymentId}: " . $response->body());
        }

        $paymentData = $response->json();
        $mpStatus    = $paymentData['status'] ?? 'unknown';

        Log::info("[MP Webhook] Pago {$paymentId} consultado", [
            'mp_status'          => $mpStatus,
            'external_reference' => $paymentData['external_reference'] ?? null,
            'amount'             => $paymentData['transaction_amount'] ?? null,
        ]);

        // ── 3. Solo procesar si está aprobado ────────────────────────────────
        if ($mpStatus !== 'approved') {
            Log::info("[MP Webhook] Pago {$paymentId} no aprobado ('{$mpStatus}'). Sin acción.");
            return;
        }

        // ── 4. Encontrar la reservación ──────────────────────────────────────
        $reservationId = $paymentData['external_reference'] ?? null;
        if (!$reservationId) {
            Log::warning("[MP Webhook] Pago aprobado {$paymentId} sin external_reference.");
            return;
        }

        $reservation = Reservation::find($reservationId);
        if (!$reservation) {
            Log::warning("[MP Webhook] Reserva {$reservationId} no encontrada para pago {$paymentId}.");
            return;
        }

        // ── 5. Guard: reserva ya pagada completamente ─────────────────────────
        if ($reservation->status->value === 'paid') {
            Log::info("[MP Webhook] Reserva #{$reservation->id} ya estaba PAGADA. Pago {$paymentId} ignorado.");
            return;
        }

        // ── 6. Registrar el pago ─────────────────────────────────────────────
        $amountPaid = (float) ($paymentData['transaction_amount'] ?? 0);

        if ($amountPaid <= 0) {
            Log::warning("[MP Webhook] Pago {$paymentId} con monto cero/inválido.", ['data' => $paymentData]);
            return;
        }

        Payment::create([
            'reservation_id'           => $reservation->id,
            'amount'                   => $amountPaid,
            'status'                   => 'approved',
            'uploaded_at'              => now(),
            'payment_method'           => 'mercadopago',
            'mp_payment_id'            => $paymentId,
            // Mantener compatibilidad con campos existentes
            'stripe_payment_intent_id' => null,
            'stripe_session_id'        => null,
        ]);

        // ── 7. Actualizar balance y estado de reserva ────────────────────────
        $newBalance = max(0, $reservation->balance_due - $amountPaid);

        $reservation->update([
            'status'      => $newBalance == 0 ? ReservationStatus::PAID : ReservationStatus::PARTIAL,
            'balance_due' => $newBalance,
        ]);

        if ($newBalance == 0) {
            ReservationSeat::where('reservation_id', $reservation->id)
                ->update(['status' => SeatStatus::PAID]);

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
        }

        Log::info("[MP Webhook] ✅ Reserva #{$reservation->id} — abono de \${$amountPaid} aplicado. Balance restante: \${$newBalance}.", [
            'mp_payment_id' => $paymentId,
            'new_status'    => $newBalance == 0 ? 'paid' : 'partial',
        ]);
    }
}
