<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Client extends Authenticatable
{
    protected $fillable = ['membership_number', 'bonuses_used', 'name', 'phone', 'whatsapp', 'email', 'birthdate', 'curp', 'origin_city', 'emergency_contact', 'password', 'temp_password', 'status'];

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

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // --- Lógica de Bonificaciones ---
    
    // Regla de negocio simple (configurable a futuro en BD)
    public const TRIPS_FOR_BONUS = 10;

    public function getCompletedTripsCountAttribute()
    {
        return $this->reservations()->where('status', \App\Enums\ReservationStatus::PAID)->count();
    }

    public function bonusRequests()
    {
        return $this->hasMany(BonusRequest::class);
    }

    public function getBonusesEarnedAttribute()
    {
        // Bonos ganados por viajes completados
        $tripBonuses = (int) floor($this->completed_trips_count / self::TRIPS_FOR_BONUS);
        
        // Ajustes manuales aprobados: sumar adds, restar subtracts
        $manualAdjustments = $this->bonusRequests()
            ->where('status', 'approved')
            ->get()
            ->sum(function ($br) {
                return $br->adjustment_type === 'subtract' 
                    ? -$br->requested_bonus_count 
                    :  $br->requested_bonus_count;
            });
        
        return max(0, $tripBonuses + $manualAdjustments);
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
