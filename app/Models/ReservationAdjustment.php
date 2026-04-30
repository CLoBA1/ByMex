<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservationAdjustment extends Model
{
    protected $fillable = [
        'reservation_id',
        'type',
        'amount',
        'notes',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
