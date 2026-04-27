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

    public function toDTO(): \App\DTOs\ReservationDTO
    {
        return \App\DTOs\ReservationDTO::fromArray($this->validated());
    }
}
