<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceCategory extends Model
{
    protected $fillable = [
        'name',
        'description',
        'icon',
        'order',
        'status',
    ];

    public function options()
    {
        return $this->hasMany(ServiceOption::class);
    }
}
