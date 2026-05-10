<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BonusRequest extends Model
{
    protected $fillable = [
        'client_id',
        'request_type',
        'requested_bonus_count',
        'status',
        'client_notes',
        'admin_notes',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
