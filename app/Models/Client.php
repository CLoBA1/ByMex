<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = ['membership_number', 'name', 'phone', 'whatsapp', 'email', 'birthdate', 'curp', 'origin_city', 'emergency_contact'];

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
}
