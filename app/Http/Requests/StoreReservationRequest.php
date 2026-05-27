<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReservationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Any public user can make a reservation
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        \Illuminate\Support\Facades\Log::info('VALIDACION_DATOS', ['input' => request()->all()]);

        return [
            'tour_id'                            => 'required|exists:tours,id',
            'seats'                              => 'required|string',
            'name'                               => 'required|string|max:150',
            'phone'                              => 'nullable|string|max:20',
            'whatsapp'                           => 'required|string|max:20',
            'email'                              => 'nullable|email|max:100',

            // Pasajeros detallados
            'passengers'                              => 'nullable|array',
            'passengers.*.seat_number'                => 'required_with:passengers|string',
            'passengers.*.name'                       => 'required_with:passengers|string|max:150',
            'passengers.*.phone'                      => 'nullable|string|max:20',
            'passengers.*.whatsapp'                   => 'required_with:passengers|string|max:20',
            'passengers.*.passenger_type'             => 'required_with:passengers|string',
            'passengers.*.birthdate'                  => 'nullable|date',
            'passengers.*.benefit_label'              => 'nullable|string',
            'passengers.*.boarding_point_id'          => 'nullable|exists:boarding_points,id',
            'passengers.*.boarding_sub_point_id'      => 'nullable|exists:boarding_sub_points,id',
        ];
    }
    
    public function messages(): array
    {
        return [
            'tour_id.required'     => 'El viaje es requerido.',
            'seats.required'       => 'Debes seleccionar al menos un asiento.',
            'name.required'        => 'Tu nombre es obligatorio.',
            'whatsapp.required'    => 'Tu número de WhatsApp es obligatorio.',
            'email.email'          => 'Un correo electrónico válido es obligatorio.',
        ];
    }

    /**
     * Añadir reglas complejas de integridad de negocio.
     */
    public function after(): array
    {
        return [
            function (\Illuminate\Validation\Validator $validator) {
                $seatsStr = $this->input('seats');
                $passengers = $this->input('passengers');

                // Solo validamos si vienen pasajeros (Modo Nuevo) y asientos
                if (!empty($seatsStr) && is_array($passengers)) {
                    $seatNumbers = array_filter(explode(',', $seatsStr));
                    
                    // 1. La cantidad debe ser exacta
                    if (count($seatNumbers) !== count($passengers)) {
                        $validator->errors()->add('passengers', 'La cantidad de pasajeros debe coincidir exactamente con los asientos seleccionados.');
                    }

                    $passengerSeats = array_map('strval', array_column($passengers, 'seat_number'));
                    
                    // 2. No debe haber duplicados dentro de los pasajeros
                    if (count($passengerSeats) !== count(array_unique($passengerSeats))) {
                        $validator->errors()->add('passengers', 'Existen asientos duplicados asignados a los pasajeros.');
                    }

                    // 3. Todos los asientos asignados deben existir en la selección principal
                    foreach ($passengerSeats as $ps) {
                        if (!in_array($ps, $seatNumbers)) {
                            $validator->errors()->add('passengers', "El asiento asignado ($ps) no está dentro de los asientos seleccionados.");
                        }
                    }

                    // 4. Validar subpuntos de abordaje
                    foreach ($passengers as $index => $p) {
                        if (!empty($p['boarding_point_id'])) {
                            // Revisar si el punto principal tiene subpuntos activos
                            $hasSubPoints = \App\Models\BoardingSubPoint::where('boarding_point_id', $p['boarding_point_id'])
                                ->where('is_active', true)
                                ->exists();

                            if ($hasSubPoints) {
                                if (empty($p['boarding_sub_point_id'])) {
                                    $validator->errors()->add("passengers.$index.boarding_sub_point_id", "Debe seleccionar un lugar específico de abordaje.");
                                } else {
                                    // Validar que el subpunto pertenezca al punto principal
                                    $validSubPoint = \App\Models\BoardingSubPoint::where('id', $p['boarding_sub_point_id'])
                                        ->where('boarding_point_id', $p['boarding_point_id'])
                                        ->where('is_active', true)
                                        ->exists();
                                    
                                    if (!$validSubPoint) {
                                        $validator->errors()->add("passengers.$index.boarding_sub_point_id", "El lugar específico de abordaje seleccionado no es válido o no está activo.");
                                    }
                                }
                            }
                        }
                    }
                }
            }
        ];
    }

    public function toDTO(): \App\DTOs\ReservationDTO
    {
        return \App\DTOs\ReservationDTO::fromArray($this->validated());
    }
}
