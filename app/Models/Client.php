<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Client extends Authenticatable
{
    protected $fillable = ['membership_number', 'bonuses_used', 'name', 'phone', 'whatsapp', 'email', 'birthdate', 'curp', 'origin_city', 'emergency_contact', 'password'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    // --- Lógica de Bonificaciones ---
    
    // Regla de negocio simple (configurable a futuro en BD)
    public const TRIPS_FOR_BONUS = 10;

    public function getCompletedTripsCountAttribute()
    {
        return $this->reservations()->where('status', \App\Enums\ReservationStatus::PAID)->count();
    }

    public function getBonusesEarnedAttribute()
    {
        return (int) floor($this->completed_trips_count / self::TRIPS_FOR_BONUS);
    }

    public function getAvailableBonusesAttribute()
    {
        return max(0, $this->bonuses_earned - $this->bonuses_used);
    }

    public function getNextBonusProgressAttribute()
    {
        return $this->completed_trips_count % self::TRIPS_FOR_BONUS;
    }
}
