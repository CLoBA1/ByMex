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

    public function subPoints()
    {
        return $this->hasMany(BoardingSubPoint::class)->orderBy('sort_order')->orderBy('name');
    }
}
