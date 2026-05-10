<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BonusRequest;
use Illuminate\Http\Request;

class BonusRequestController extends Controller
{
    public function index()
    {
        $requests = BonusRequest::with('client')
            ->orderByRaw("FIELD(status, 'pending') DESC")
            ->latest()
            ->paginate(20);

        return view('admin.bonus-requests.index', compact('requests'));
    }

    public function updateStatus(Request $request, $id)
    {
        $bonusRequest = BonusRequest::with('client')->findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,approved,rejected,applied',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $oldStatus = $bonusRequest->status;
        $newStatus = $validated['status'];

        $bonusRequest->status = $newStatus;
        if (isset($validated['admin_notes'])) {
            $bonusRequest->admin_notes = $validated['admin_notes'];
        }
        
        $bonusRequest->save();

        // Lógica de consumo de bono (solo se consume cuando el estado cambia a 'applied')
        if ($oldStatus !== 'applied' && $newStatus === 'applied') {
            $client = $bonusRequest->client;
            if ($client->available_bonuses > 0) {
                $client->bonuses_used += 1;
                $client->save();
            }
        } elseif ($oldStatus === 'applied' && $newStatus !== 'applied') {
            // Revertir consumo de bono
            $client = $bonusRequest->client;
            if ($client->bonuses_used > 0) {
                $client->bonuses_used -= 1;
                $client->save();
            }
        }

        return redirect()->back()->with('success', 'El estado de la solicitud se ha actualizado correctamente.');
    }
}
