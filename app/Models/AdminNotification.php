<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminNotification extends Model
{
    protected $fillable = ['type', 'title', 'message', 'link', 'whatsapp_link', 'read'];

    public function scopeUnread($query)
    {
        return $query->where('read', false);
    }
}
