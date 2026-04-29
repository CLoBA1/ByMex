<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Create Stripe Checkout Session and redirect.
     */
    public function checkout($token)
    {
        $reservation = Reservation::with(['tour', 'client', 'seats'])->where('public_token', $token)->firstOrFail();

        // Don't allow payment if already paid
        if ($reservation->status->value === 'paid') {
            return redirect()->route('reservations.success', $reservation->public_token)
                ->with('info', 'Esta reserva ya fue pagada.');
        }

        try {
            $session = $this->paymentService->createCheckoutSession($reservation);
            return redirect()->away($session->url);
        } catch (\Exception $e) {
            return redirect()->route('reservations.success', $reservation->public_token)
                ->with('error', 'Error al conectar con el procesador de pagos: ' . $e->getMessage());
        }
    }
}
