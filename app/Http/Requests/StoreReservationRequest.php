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
        return [
            'tour_id' => 'required|exists:tours,id',
            'seats' => 'required|string',
            'name' => 'required|string|max:150',
            'phone' => 'required|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'email' => 'required|email|max:100',
            
            // Nuevo modo con pasajeros (opcional por ahora para compatibilidad)
            'passengers' => 'nullable|array',
            'passengers.*.seat_number' => 'required_with:passengers|string',
            'passengers.*.name' => 'required_with:passengers|string|max:150',
            'passengers.*.passenger_type' => 'required_with:passengers|string',
            'passengers.*.birthdate' => 'nullable|date',
            'passengers.*.benefit_label' => 'nullable|string',
        ];
    }
    
    public function messages(): array
    {
        return [
            'tour_id.required' => 'El viaje es requerido.',
            'seats.required' => 'Debes seleccionar al menos un asiento.',
            'name.required' => 'Tu nombre es obligatorio.',
            'phone.required' => 'Tu teléfono celular es obligatorio.',
            'email.required' => 'Un correo electrónico válido es obligatorio.',
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
                }
            }
        ];
    }

    public function toDTO(): \App\DTOs\ReservationDTO
    {
        return \App\DTOs\ReservationDTO::fromArray($this->validated());
    }
}
