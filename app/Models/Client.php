<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = ['name', 'phone', 'whatsapp', 'email', 'birthdate', 'curp', 'origin_city', 'emergency_contact'];

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
