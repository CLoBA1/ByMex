<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $client = Auth::guard('client')->user();
        $client->load(['reservations.tour', 'reservations.payments']);

        $activeTrips = $client->reservations->filter(function ($res) {
            return $res->status->value !== 'cancelled'
                && $res->status->value !== 'expired'
                && \Carbon\Carbon::parse($res->tour->departure_date)->isFuture();
        });

        $pastTrips = $client->reservations->filter(function ($res) {
            return $res->status->value === 'cancelled'
                || $res->status->value === 'expired'
                || \Carbon\Carbon::parse($res->tour->departure_date)->isPast();
        });

        return view('client.dashboard', compact('client', 'activeTrips', 'pastTrips'));
    }

    public function reservation($id)
    {
        $client = Auth::guard('client')->user();
        
        $reservation = $client->reservations()
            ->with(['tour', 'passengers', 'seats', 'payments'])
            ->findOrFail($id);

        return view('client.reservation', compact('client', 'reservation'));
    }
}
