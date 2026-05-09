<?php

namespace App\Services;

use App\Models\Reservation;
use App\Enums\ReservationStatus;
use App\Enums\SeatStatus;
use App\Models\ReservationSeat;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Webhook;
use App\Models\Payment;

class PaymentService
{
    public function __construct()
    {
        $key = config('services.stripe.key');
        $secret = config('services.stripe.secret');

        Log::info('Stripe Config Debug', [
            'has_key' => !empty($key),
            'has_secret' => !empty($secret),
            'key_prefix' => !empty($key) ? substr($key, 0, 8) . '...' : null,
            'secret_prefix' => !empty($secret) ? substr($secret, 0, 8) . '...' : null,
        ]);

        Stripe::setApiKey($secret);
    }

    /**
     * Create a Stripe Checkout Session for a reservation.
     */
    public function createCheckoutSession(Reservation $reservation): StripeSession
    {
        $reservation->loadMissing(['tour', 'client', 'seats']);

        $seatCount = $reservation->seats->count();
        $seatList = $reservation->seats->pluck('seat_number')->sort()->implode(', ');

        return StripeSession::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'mxn',
                    'product_data' => [
                        'name' => $reservation->tour->title,
                        'description' => "Asientos: {$seatList} ({$seatCount} lugar" . ($seatCount > 1 ? 'es' : '') . ")",
                        'images' => $reservation->tour->image
                            ? [url($reservation->tour->image)]
                            : [],
                    ],
                    'unit_amount' => (int) ($reservation->total_amount * 100), // Stripe uses cents
                ],
                'quantity' => 1,
            ]],
            'metadata' => [
                'reservation_id' => $reservation->id,
                'tour_id' => $reservation->tour_id,
            ],
            'customer_email' => $reservation->client->email,
            'client_reference_id' => (string) $reservation->id,
            'mode' => 'payment',
            'success_url' => route('reservations.success', $reservation->public_token) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('reservations.success', $reservation->public_token) . '?payment=cancelled',
        ]);
    }

    /**
     * Create a Mercado Pago Preference for a reservation.
     */
    public function createMercadoPagoPreference(Reservation $reservation)
    {
        $reservation->loadMissing(['tour', 'client', 'seats']);

        $seatCount = $reservation->seats->count();
        $seatList = $reservation->seats->pluck('seat_number')->sort()->implode(', ');

        $accessToken = config('services.mercadopago.access_token');

        if (empty($accessToken)) {
            throw new \Exception('Mercado Pago no está configurado correctamente (Falta Access Token).');
        }

        $response = \Illuminate\Support\Facades\Http::withToken($accessToken)
            ->post('https://api.mercadopago.com/checkout/preferences', [
                'items' => [
                    [
                        'id' => 'RES-' . $reservation->id,
                        'title' => $reservation->tour->title,
                        'description' => "Asientos: {$seatList} ({$seatCount} lugar" . ($seatCount > 1 ? 'es' : '') . ")",
                        'quantity' => 1,
                        'currency_id' => 'MXN',
                        'unit_price' => (float) $reservation->balance_due,
                    ]
                ],
                'payer' => [
                    'name' => $reservation->client->name,
                    'email' => $reservation->client->email ?? 'no-reply@bymex.com',
                ],
                'back_urls' => [
                    'success' => route('reservations.success', $reservation->public_token) . '?mp_status=approved',
                    'failure' => route('reservations.success', $reservation->public_token) . '?mp_status=failure',
                    'pending' => route('reservations.success', $reservation->public_token) . '?mp_status=pending',
                ],
                'auto_return' => 'approved',
                'external_reference' => (string) $reservation->id,
                'notification_url' => route('mercadopago.webhook'),
            ]);

        if ($response->failed()) {
            Log::error('Error Mercado Pago: ' . $response->body());
            throw new \Exception('No se pudo generar la preferencia de pago con Mercado Pago.');
        }

        return $response->json();
    }

    /**
     * Handle a successful payment (called from webhook).
     */
    public function processSuccessfulPayment(Reservation $reservation, $session): void
    {
        // Avoid duplicating payment if already paid completely
        // But Stripe checkout is for full balance in this flow.
        if ($reservation->status->value === 'paid') {
            Log::info("Reservation #{$reservation->id} is already marked as PAID. Ignoring webhook.");
            return;
        }

        // Check if we already registered this session
        $existingPayment = Payment::where('stripe_session_id', $session->id)->first();
        if ($existingPayment) {
            Log::info("Payment for session {$session->id} already processed.");
            return;
        }

        $amountPaid = $session->amount_total / 100;

        Payment::create([
            'reservation_id' => $reservation->id,
            'amount' => $amountPaid,
            'status' => 'approved',
            'uploaded_at' => now(),
            'stripe_session_id' => $session->id,
            'stripe_payment_intent_id' => $session->payment_intent,
            'payment_method' => 'stripe',
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

        Log::info("Reservation #{$reservation->id} processed payment of {$amountPaid} via Stripe webhook.");
    }

    /**
     * Construct and verify a Stripe webhook event.
     */
    public function constructWebhookEvent(string $payload, string $sigHeader): \Stripe\Event
    {
        return Webhook::constructEvent(
            $payload,
            $sigHeader,
            config('services.stripe.webhook_secret')
        );
    }
}
