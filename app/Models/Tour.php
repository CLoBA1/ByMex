<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tour extends Model
{
    protected $fillable = ['title', 'destination', 'departure_date', 'boarding_point', 'price', 'minimum_deposit', 'total_seats', 'expiration_hours', 'description', 'itinerary', 'status', 'image', 'requires_passenger_documents', 'what_includes', 'what_not_includes', 'duration_days'];

    protected function casts(): array
    {
        return [
            'status' => \App\Enums\TourStatus::class,
            'departure_date' => 'datetime',
            'requires_passenger_documents' => 'boolean',
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

    public function boardingPoints()
    {
        return $this->belongsToMany(
            BoardingPoint::class,
            'tour_boarding_points'
        )
        ->withPivot('departure_time', 'sort_order')
        ->orderBy('tour_boarding_points.sort_order');
    }
}
