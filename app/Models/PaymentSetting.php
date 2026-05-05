<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_name',
        'rfc',
        'address',
        'phones',
        'whatsapp_number',
        'general_instructions',
        'final_note',
        'reservation_policies',
        'cancellation_policies',
        'no_show_policies',
        'refund_policies',
    ];
}
