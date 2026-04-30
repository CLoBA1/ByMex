<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservationPassenger extends Model
{
    protected $fillable = [
        'reservation_id',
        'seat_number',
        'name',
        'birthdate',
        'passenger_type',
        'benefit_label',
        'base_price',
        'discount_amount',
        'original_discount_amount',
        'final_price',
        'validation_status',
        'validation_notes',
    ];

    protected function casts(): array
    {
        return [
            'birthdate' => 'date',
            'base_price' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'original_discount_amount' => 'decimal:2',
            'final_price' => 'decimal:2',
        ];
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}
