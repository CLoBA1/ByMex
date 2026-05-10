<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;

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

    public function edit($id)
    {
        $client = Client::findOrFail($id);
        return view('admin.clients.edit', compact('client'));
    }

    public function update(Request $request, $id)
    {
        $client = Client::findOrFail($id);

        $request->validate([
            'name'              => 'required|string|max:150',
            'phone'             => 'required|string|max:20',
            'email'             => 'nullable|email|max:100',
            'whatsapp'          => 'nullable|string|max:20',
            'origin_city'       => 'nullable|string|max:100',
            'curp'              => 'nullable|string|max:18',
            'birthdate'         => 'nullable|date',
            'emergency_contact' => 'nullable|string|max:150',
            'membership_number' => 'nullable|string|max:50|unique:clients,membership_number,' . $client->id,
        ], [
            'membership_number.unique' => 'Este código de membresía ya está asignado a otro cliente.',
        ]);

        $data = $request->only(['name', 'phone', 'email', 'whatsapp', 'origin_city', 'curp', 'birthdate', 'emergency_contact', 'membership_number']);

        // Normalise empty string to null for membership_number
        if (empty($data['membership_number'])) {
            $data['membership_number'] = null;
        }

        $client->update($data);

        return redirect()->route('admin.clients.show', $client->id)
            ->with('success', 'Datos del cliente actualizados correctamente.');
    }
}
