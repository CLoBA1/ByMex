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

class PaymentService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
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
                'client_email' => $reservation->client->email,
            ],
            'customer_email' => $reservation->client->email,
            'mode' => 'payment',
            'success_url' => route('reservations.success', $reservation->id) . '?payment=success',
            'cancel_url' => route('reservations.success', $reservation->id) . '?payment=cancelled',
        ]);
    }

    /**
     * Handle a successful payment (called from webhook or redirect).
     */
    public function markAsPaid(Reservation $reservation): void
    {
        $reservation->update([
            'status' => ReservationStatus::PAID,
            'balance_due' => 0,
        ]);

        ReservationSeat::where('reservation_id', $reservation->id)
            ->update(['status' => SeatStatus::PAID]);

        Log::info("Reservation #{$reservation->id} marked as PAID via Stripe.");
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
