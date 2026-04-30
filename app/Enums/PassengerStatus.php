<?php

namespace App\Enums;

enum PassengerStatus: string
{
    case ACTIVE = 'active';
    case CANCELLED = 'cancelled';
    case NO_SHOW = 'no_show';
    case BOARDED = 'boarded';
}
