<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceOption extends Model
{
    protected $fillable = [
        'service_category_id',
        'name',
        'description',
        'image',
        'order',
        'whatsapp_message',
        'status',
    ];

    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }
}
