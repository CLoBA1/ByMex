<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservationSeat extends Model
{
    protected $fillable = ['reservation_id', 'tour_id', 'seat_number', 'status'];

    protected function casts(): array
    {
        return [
            'status' => \App\Enums\SeatStatus::class,
        ];
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }
}
