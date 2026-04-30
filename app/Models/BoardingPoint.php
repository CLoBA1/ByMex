<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoardingPoint extends Model
{
    protected $fillable = ['name', 'color_label', 'color_hex', 'is_active', 'notes'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function passengers()
    {
        return $this->hasMany(ReservationPassenger::class);
    }
}
