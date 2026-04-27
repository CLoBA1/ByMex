<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Handle incoming Stripe webhook events.
     */
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        try {
            $event = $this->paymentService->constructWebhookEvent($payload, $sigHeader);
        } catch (\UnexpectedValueException $e) {
            Log::error('Stripe Webhook: Invalid payload');
            return response('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error('Stripe Webhook: Invalid signature');
            return response('Invalid signature', 400);
        }

        // Handle the event
        switch ($event->type) {
            case 'checkout.session.completed':
                $session = $event->data->object;
                $reservationId = $session->metadata->reservation_id ?? null;

                if ($reservationId) {
                    $reservation = Reservation::find($reservationId);
                    if ($reservation && $reservation->status->value !== 'paid') {
                        $this->paymentService->markAsPaid($reservation);
                        Log::info("Stripe Webhook: Reservation #{$reservationId} paid successfully.");
                    }
                }
                break;

            default:
                Log::info("Stripe Webhook: Unhandled event type {$event->type}");
        }

        return response('OK', 200);
    }
}
