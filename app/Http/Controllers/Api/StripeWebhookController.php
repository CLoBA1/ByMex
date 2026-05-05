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
                
                if ($session->payment_status !== 'paid') {
                    Log::info("Stripe Webhook: Session {$session->id} completed but payment_status is '{$session->payment_status}'. Waiting for async_payment_succeeded.");
                    break;
                }

                $reservationId = $session->client_reference_id ?? ($session->metadata->reservation_id ?? null);
                if ($reservationId) {
                    $reservation = Reservation::find($reservationId);
                    if ($reservation) {
                        $this->paymentService->processSuccessfulPayment($reservation, $session);
                    } else {
                        Log::error("Stripe Webhook: Reservation #{$reservationId} not found.");
                    }
                }
                break;

            case 'checkout.session.async_payment_succeeded':
                $session = $event->data->object;
                
                $reservationId = $session->client_reference_id ?? ($session->metadata->reservation_id ?? null);
                if ($reservationId) {
                    $reservation = Reservation::find($reservationId);
                    if ($reservation) {
                        $this->paymentService->processSuccessfulPayment($reservation, $session);
                    } else {
                        Log::error("Stripe Webhook: Reservation #{$reservationId} not found.");
                    }
                }
                break;
            
            case 'checkout.session.async_payment_failed':
            case 'checkout.session.expired':
                $session = $event->data->object;
                $reservationId = $session->client_reference_id ?? ($session->metadata->reservation_id ?? null);
                if ($reservationId) {
                    Log::info("Stripe Webhook: Payment failed/expired for Reservation #{$reservationId}.");
                    // Opcional: Podríamos marcar un intento fallido o liberar si hubiera lógica. 
                    // Por reglas conservadoras, no tocamos nada, solo registramos log.
                }
                break;

            default:
                Log::info("Stripe Webhook: Unhandled event type {$event->type}");
        }

        return response('OK', 200);
    }
}
