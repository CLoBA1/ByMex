<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusImage extends Model
{
    protected $fillable = ['bus_id', 'image_path', 'is_primary'];

    public function bus()
    {
        return $this->belongsTo(Bus::class);
    }
}
