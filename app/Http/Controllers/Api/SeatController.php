<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ReservationService;

class SeatController extends Controller
{
    protected $reservationService;

    public function __construct(ReservationService $reservationService)
    {
        $this->reservationService = $reservationService;
    }

    public function getSeats($tourId)
    {
        return response()->json($this->reservationService->getSeatStatus($tourId));
    }
}
