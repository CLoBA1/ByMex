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
                $discount = 0;
                
                // Reglas de negocio de descuentos por categoría
                if ($p['passenger_type'] === 'Niño') {
                    $discount = $basePrice * 0.5; // 50% descuento
                } elseif ($p['passenger_type'] === 'Adulto Mayor') {
                    $discount = $basePrice * 0.3; // 30% descuento
                }
                
                $finalPrice = $basePrice - $discount;

                $subtotal += $basePrice;
                $discountTotal += $discount;
                $totalAmount += $finalPrice;

                $passengersData[] = [
                    'seat_number' => $p['seat_number'],
                    'name' => $p['name'],
                    'passenger_type' => $p['passenger_type'],
                    'birthdate' => $p['birthdate'] ?? null,
                    'benefit_label' => $p['benefit_label'] ?? null,
                    'boarding_point_id' => $p['boarding_point_id'] ?? null,
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

        DB::beginTransaction();
        try {
            // 1. Create or Find Client
            $client = Client::firstOrCreate(
                ['email' => $dto->email],
                [
                    'name' => $dto->name,
                    'phone' => $dto->phone,
                    'whatsapp' => $dto->whatsapp ?? $dto->phone,
                ]
            );

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
            $res->update(['status' => \App\Enums\ReservationStatus::CANCELLED]);
            ReservationSeat::where('reservation_id', $res->id)->delete();
            $count++;
        }

        return $count;
    }

    /**
     * Get real-time available/occupied seats logic
     */
    public function getSeatStatus(int $tourId): array
    {
        // Fetch current active seats
        $seats = ReservationSeat::where('tour_id', $tourId)->get();
        $response = [];
        
        foreach ($seats as $seat) {
            $response[$seat->seat_number] = $seat->status->value;
        }
        
        return $response;
    }
}
