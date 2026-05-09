<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bus extends Model
{
    protected $fillable = ['name', 'description', 'is_active'];

    public function images()
    {
        return $this->hasMany(BusImage::class);
    }
    
    public function getPrimaryImageAttribute()
    {
        return $this->images->where('is_primary', true)->first() ?? $this->images->first();
    }
}
