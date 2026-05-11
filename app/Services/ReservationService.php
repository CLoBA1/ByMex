<?php

namespace App\Services;

use App\Models\Tour;
use App\Models\Client;
use App\Models\Reservation;
use App\Models\ReservationSeat;
use App\Events\ReservationCreated;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class ReservationService
{
    /**
     * Process a new reservation with DB Transactions.
     * Throws Exception on failure.
     */
    public function processNewReservation(\App\DTOs\ReservationDTO $dto): Reservation
    {
        $tour = Tour::findOrFail($dto->tour_id);
        $seatNumbers = explode(',', $dto->seats);
        
        if (empty($seatNumbers) || count($seatNumbers) === 0 || $seatNumbers[0] === "") {
            throw new Exception("Debes seleccionar al menos un asiento.");
        }

        // --- CÁLCULO DE IMPORTES Y PASAJEROS (MODO HÍBRIDO) ---
        $subtotal = 0;
        $discountTotal = 0;
        $totalAmount = 0;
        $passengersData = [];

        if ($dto->passengers && count($dto->passengers) > 0) {
            // MODO NUEVO: Se recibieron pasajeros detallados
            foreach ($dto->passengers as $p) {
                $basePrice = $tour->price;
                $pricing = $this->calculatePassengerPricing($tour, $p['passenger_type']);
                $discount = $pricing['discount_amount'];
                $finalPrice = $pricing['final_price'];

                $subtotal += $basePrice;
                $discountTotal += $discount;
                $totalAmount += $finalPrice;

                $passengersData[] = [
                    'seat_number' => $p['seat_number'],
                    'name' => $p['name'],
                    'phone' => $p['phone'] ?? null,
                    'passenger_type' => $p['passenger_type'],
                    'birthdate' => $p['birthdate'] ?? null,
                    'benefit_label' => $p['benefit_label'] ?? null,
                    'boarding_point_id' => $p['boarding_point_id'] ?? null,
                    'boarding_sub_point_id' => $p['boarding_sub_point_id'] ?? null,
                    'base_price' => $basePrice,
                    'discount_amount' => $discount,
                    'original_discount_amount' => $discount,
                    'final_price' => $finalPrice,
                    'validation_status' => 'pending',
                ];
            }
        } else {
            // MODO LEGACY: Generar pasajeros por defecto basados en los asientos
            foreach ($seatNumbers as $seatNumber) {
                $basePrice = $tour->price;
                
                $subtotal += $basePrice;
                $totalAmount += $basePrice;

                $passengersData[] = [
                    'seat_number' => $seatNumber,
                    'name' => $dto->name . ' (Pasajero)',
                    'passenger_type' => 'Adulto',
                    'birthdate' => null,
                    'benefit_label' => null,
                    'base_price' => $basePrice,
                    'discount_amount' => 0,
                    'final_price' => $basePrice,
                    'validation_status' => 'pending',
                ];
            }
        }

        // Limpieza dinámica "Lazy" justo antes de guardar: si un asiento acaba de expirar, lo liberamos
        // físicamente para evitar el error 23000 (Integrity Constraint) en la inserción.
        $this->cancelExpiredReservations($tour->id);

        DB::beginTransaction();
        try {
            // 1. Create or Find Client
            // Since email is no longer required, we find by phone instead of email
            if (!empty($dto->email)) {
                $client = Client::firstOrCreate(
                    ['email' => $dto->email],
                    [
                        'name' => $dto->name,
                        'phone' => $dto->phone,
                        'whatsapp' => $dto->whatsapp ?? $dto->phone,
                    ]
                );
            } else {
                $client = Client::firstOrCreate(
                    ['phone' => $dto->phone],
                    [
                        'name' => $dto->name,
                        'whatsapp' => $dto->whatsapp ?? $dto->phone,
                        'email' => null,
                    ]
                );
            }

            // 2. Create Reservation
            $reservation = Reservation::create([
                'public_token' => \Illuminate\Support\Str::random(32),
                'tour_id' => $tour->id,
                'client_id' => $client->id,
                'subtotal' => $subtotal,
                'discount_total' => $discountTotal,
                'total_amount' => $totalAmount,
                'balance_due' => $totalAmount,
                'status' => \App\Enums\ReservationStatus::PENDING,
                'expires_at' => Carbon::now()->addHours($tour->expiration_hours),
            ]);

            // 3. Create Seats and Passengers
            foreach ($seatNumbers as $seatNumber) {
                // Mantenemos intacto el inventario de asientos
                ReservationSeat::create([
                    'reservation_id' => $reservation->id,
                    'tour_id' => $tour->id,
                    'seat_number' => (int) $seatNumber,
                    'status' => \App\Enums\SeatStatus::PENDING
                ]);
            }

            foreach ($passengersData as $passenger) {
                $passenger['reservation_id'] = $reservation->id;
                \App\Models\ReservationPassenger::create($passenger);
            }

            DB::commit();
            
            // Dispatch domain event! The listener will handle Whatsapp Notification independently.
            ReservationCreated::dispatch($reservation);

            return $reservation;

        } catch (Exception $e) {
            DB::rollBack();
            if ($e->getCode() == 23000) {
                throw new Exception('Lo sentimos, uno de los asientos seleccionados acaba de ser reservado por alguien más. Por favor, elige otros.');
            }
            throw new Exception('Ocurrió un error interno al procesar tu reserva. Inténtalo de nuevo.');
        }
    }
    
    public function cancelExpiredReservations(?int $tourId = null): int
    {
        $query = Reservation::where('status', \App\Enums\ReservationStatus::PENDING)
            ->where('expires_at', '<', Carbon::now());

        if ($tourId) {
            $query->where('tour_id', $tourId);
        }

        $expiredReservations = $query->get();
        $count = 0;

        foreach ($expiredReservations as $res) {
            $res->update(['status' => \App\Enums\ReservationStatus::EXPIRED]);
            ReservationSeat::where('reservation_id', $res->id)->delete();
            $count++;

            $admin = \App\Models\AdminOwner::first();
            if ($admin) {
                $admin->notify(new \App\Notifications\SystemAlert(
                    'Reservación Expirada',
                    "La reserva #{$res->id} expiró automáticamente por falta de pago.",
                    route('admin.reservations.show', $res->id),
                    'fa-solid fa-clock'
                ));
            }
        }

        return $count;
    }

    /**
     * Get real-time available/occupied seats logic
     */
    public function getSeatStatus(int $tourId): array
    {
        // Limpieza dinámica "Lazy": Aseguramos que las reservaciones vencidas sean canceladas 
        // y liberen sus asientos antes de consultar la disponibilidad, por si el cron se retrasó.
        $this->cancelExpiredReservations($tourId);

        // Fetch current active seats
        $seats = ReservationSeat::where('tour_id', $tourId)->get();
        $response = [];
        
        foreach ($seats as $seat) {
            $response[$seat->seat_number] = $seat->status->value;
        }
        
        return $response;
    }

    /**
     * Calcula los precios y descuentos base para un tipo de pasajero
     */
    public function calculatePassengerPricing(Tour $tour, string $passengerType): array
    {
        $discount = 0;
        $basePrice = (float) $tour->price;
        
        if ($passengerType === 'Niño') {
            if ($tour->duration_days <= 2) {
                $discount = $basePrice * 0.5; // 50% descuento
            } else {
                $discount = $basePrice * 0.75; // 75% descuento
            }
        }
        
        return [
            'base_price' => $basePrice,
            'discount_amount' => $discount,
            'final_price' => $basePrice - $discount
        ];
    }
}
