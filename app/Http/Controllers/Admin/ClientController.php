<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::withCount('reservations')->get();
        return view('admin.clients.index', compact('clients'));
    }

    public function show($id)
    {
        $client = Client::with(['reservations.tour', 'reservations.payments'])->findOrFail($id);
        
        $activeTrips = $client->reservations->filter(function ($res) {
            return $res->status->value !== 'cancelled' && \Carbon\Carbon::parse($res->tour->departure_date)->isFuture();
        });

        return view('admin.clients.show', compact('client', 'activeTrips'));
    }
}
