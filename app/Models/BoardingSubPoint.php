<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoardingSubPoint extends Model
{
    protected $fillable = [
        'boarding_point_id',
        'name',
        'reference',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function boardingPoint()
    {
        return $this->belongsTo(BoardingPoint::class);
    }

    public function passengers()
    {
        return $this->hasMany(ReservationPassenger::class);
    }
}
