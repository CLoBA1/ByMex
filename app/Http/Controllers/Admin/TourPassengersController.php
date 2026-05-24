<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use Illuminate\Http\Request;

class TourPassengersController extends Controller
{
    public function index()
    {
        // Get tours that have reservations, count passengers and load needed data
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
        // Load reservations and their passengers
        $tour->load(['reservations' => function($query) {
            $query->whereIn('status', ['paid', 'partial', 'pending'])
                  ->with(['passengers', 'client', 'boardingPoint']);
        }]);

        // Flatten all passengers from all active reservations of this tour
        $allPassengers = collect();
        
        foreach ($tour->reservations as $reservation) {
            // 1. Agregar al Titular como pasajero (Titular)
            $allPassengers->push((object)[
                'id' => 'titular_' . $reservation->client->id,
                'name' => $reservation->client->name,
                'type' => 'Titular',
                'whatsapp' => $reservation->client->whatsapp ?? $reservation->client->phone,
                'boarding_point_name' => collect([$reservation->boardingPoint->name ?? '', $reservation->boardingSubPoint->name ?? ''])->filter()->implode(' - '),
                'status' => $reservation->status->value,
                'is_titular' => true,
                'reservation_id' => $reservation->id
            ]);

            // 2. Agregar a los pasajeros adicionales
            foreach ($reservation->passengers as $passenger) {
                $allPassengers->push((object)[
                    'id' => 'passenger_' . $passenger->id,
                    'name' => $passenger->name . ' ' . $passenger->last_name,
                    'type' => $passenger->passenger_type->value ?? 'Adicional',
                    'whatsapp' => $passenger->whatsapp,
                    'boarding_point_name' => collect([$reservation->boardingPoint->name ?? '', $reservation->boardingSubPoint->name ?? ''])->filter()->implode(' - '),
                    'status' => $passenger->status->value ?? $reservation->status->value,
                    'is_titular' => false,
                    'reservation_id' => $reservation->id
                ]);
            }
        }

        return view('admin.tour_passengers.show', compact('tour', 'allPassengers'));
    }
}
