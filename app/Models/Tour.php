<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tour extends Model
{
    protected $fillable = ['title', 'destination', 'departure_date', 'boarding_point', 'price', 'total_seats', 'expiration_hours', 'description', 'status', 'image'];

    protected function casts(): array
    {
        return [
            'status' => \App\Enums\TourStatus::class,
            'departure_date' => 'datetime',
        ];
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function seats()
    {
        return $this->hasMany(ReservationSeat::class);
    }
}
