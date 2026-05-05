<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PassengerDocument extends Model
{
    protected $fillable = [
        'reservation_passenger_id',
        'original_name',
        'file_path',
        'mime_type',
        'file_size',
    ];

    public function passenger()
    {
        return $this->belongsTo(ReservationPassenger::class, 'reservation_passenger_id');
    }
}
