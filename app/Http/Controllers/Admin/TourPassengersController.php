<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Models\ReservationPassenger;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TourPassengersController extends Controller
{
    public function index()
    {
        $tours = Tour::whereHas('reservations', function($query) {
            $query->whereIn('status', ['paid', 'partial', 'pending']);
        })
        ->with(['reservations' => function($query) {
            $query->whereIn('status', ['paid', 'partial', 'pending'])
                  ->with('passengers', 'client');
        }])
        ->orderBy('start_date', 'desc')
        ->get();

        return view('admin.tour_passengers.index', compact('tours'));
    }

    public function show(Tour $tour)
    {
        $tour->load(['reservations' => function($query) {
            $query->whereIn('status', ['paid', 'partial', 'pending'])
                  ->with(['passengers.client', 'client', 'boardingPoint']);
        }]);

        $allPassengers = collect();

        foreach ($tour->reservations as $reservation) {
            // 1. Titular (ya existe como cliente, solo lo mostramos)
            $allPassengers->push((object)[
                'id'                  => null, // titulares no tienen passenger_id
                'name'                => $reservation->client->name,
                'type'                => 'Titular',
                'whatsapp'            => $reservation->client->whatsapp ?? $reservation->client->phone,
                'boarding_point_name' => collect([$reservation->boardingPoint->name ?? '', $reservation->boardingSubPoint->name ?? ''])->filter()->implode(' - '),
                'status'              => $reservation->status->value,
                'is_titular'          => true,
                'reservation_id'      => $reservation->id,
                'client_id'           => $reservation->client->id, // siempre tiene client
            ]);

            // 2. Pasajeros adicionales
            foreach ($reservation->passengers as $passenger) {
                $allPassengers->push((object)[
                    'id'                  => $passenger->id,
                    'name'                => trim($passenger->name . ' ' . ($passenger->last_name ?? '')),
                    'type'                => ucfirst($passenger->passenger_type->value ?? 'Adulto'),
                    'whatsapp'            => $passenger->whatsapp,
                    'boarding_point_name' => collect([$reservation->boardingPoint->name ?? '', $reservation->boardingSubPoint->name ?? ''])->filter()->implode(' - '),
                    'status'              => $passenger->status->value ?? $reservation->status->value,
                    'is_titular'          => false,
                    'reservation_id'      => $reservation->id,
                    'client_id'           => $passenger->client_id, // null si aún no está registrado
                ]);
            }
        }

        return view('admin.tour_passengers.show', compact('tour', 'allPassengers'));
    }

    public function addClient(ReservationPassenger $passenger)
    {
        // Verificar si ya fue vinculado
        if ($passenger->client_id) {
            return back()->with('info', "'{$passenger->name}' ya está registrado como cliente.");
        }

        $whatsapp = trim($passenger->whatsapp ?? '');

        // Buscar si ya existe un cliente con ese WhatsApp
        $existingClient = null;
        if ($whatsapp) {
            $existingClient = Client::where('whatsapp', $whatsapp)->first();
        }

        if ($existingClient) {
            // Vincular al pasajero con el cliente encontrado
            $passenger->update(['client_id' => $existingClient->id]);
            return back()->with('success', "'{$passenger->name}' ya tenía cuenta de cliente. Se vinculó correctamente.");
        }

        // Crear nuevo cliente
        $passengerName = trim($passenger->name . ' ' . ($passenger->last_name ?? ''));
        $passwordBase  = $whatsapp ?: 'bymex2024';

        $client = Client::create([
            'name'     => $passengerName,
            'whatsapp' => $whatsapp ?: null,
            'phone'    => $whatsapp ?: null,
            'email'    => null,
            'password' => Hash::make($passwordBase),
        ]);

        // Vincular pasajero con el nuevo cliente
        $passenger->update(['client_id' => $client->id]);

        return back()->with('success', "¡Listo! '{$passengerName}' fue registrado como cliente. Su contraseña temporal es su número de WhatsApp.");
    }
}
