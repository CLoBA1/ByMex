<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = ['tour_id', 'client_id', 'total_amount', 'balance_due', 'status', 'expires_at'];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'status' => \App\Enums\ReservationStatus::class,
        ];
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    public function seats()
    {
        return $this->hasMany(ReservationSeat::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
