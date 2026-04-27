<?php

namespace App\Enums;

enum SeatStatus: string
{
    case AVAILABLE = 'available';
    case PENDING = 'pending';
    case PAID = 'paid';
}
