<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = ['membership_number', 'bonuses_used', 'name', 'phone', 'whatsapp', 'email', 'birthdate', 'curp', 'origin_city', 'emergency_contact'];

    protected static function booted()
    {
        static::created(function ($client) {
            if (empty($client->membership_number)) {
                $client->membership_number = 'VBM-' . str_pad($client->id, 6, '0', STR_PAD_LEFT);
                $client->saveQuietly();
            }
        });

        static::saving(function ($client) {
            if ($client->exists && empty($client->membership_number)) {
                $client->membership_number = 'VBM-' . str_pad($client->id, 6, '0', STR_PAD_LEFT);
            }
        });
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
