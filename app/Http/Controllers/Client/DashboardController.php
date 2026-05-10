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

        $activeBonusRequest = \App\Models\BonusRequest::where('client_id', $client->id)
            ->whereIn('status', ['pending', 'approved'])
            ->latest()
            ->first();

        return view('client.dashboard', compact('client', 'activeTrips', 'pastTrips', 'activeBonusRequest'));
    }

    public function reservation($id)
    {
        $client = Auth::guard('client')->user();
        
        $reservation = $client->reservations()
            ->with(['tour', 'passengers', 'seats', 'payments'])
            ->findOrFail($id);

        return view('client.reservation', compact('client', 'reservation'));
    }

    public function requestBonus(\Illuminate\Http\Request $request)
    {
        $client = Auth::guard('client')->user();

        // 1. Validar que tenga bonos disponibles
        if ($client->available_bonuses <= 0) {
            return back()->with('error', 'No tienes bonificaciones disponibles para solicitar.');
        }

        // 2. Validar que no tenga ya una solicitud pendiente
        $hasPending = \App\Models\BonusRequest::where('client_id', $client->id)
            ->where('status', 'pending')
            ->exists();

        if ($hasPending) {
            return back()->with('error', 'Ya tienes una solicitud de bonificación en revisión.');
        }

        $request->validate([
            'request_type' => 'required|string|max:255',
            'client_notes' => 'nullable|string|max:1000',
        ]);

        \App\Models\BonusRequest::create([
            'client_id' => $client->id,
            'request_type' => $request->request_type,
            'requested_bonus_count' => 1, // Phase 2: hardcoded to 1
            'status' => 'pending',
            'client_notes' => $request->client_notes,
        ]);

        return back()->with('success', '¡Tu solicitud ha sido enviada con éxito! La revisaremos y te contactaremos pronto.');
    }
}
