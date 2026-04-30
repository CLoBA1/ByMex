<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BoardingPoint;
use Illuminate\Http\Request;

class BoardingPointController extends Controller
{
    public function index()
    {
        $boardingPoints = BoardingPoint::orderBy('name')->get();
        return view('admin.boarding-points.index', compact('boardingPoints'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'color_label' => 'required|string|max:50',
            'color_hex' => 'required|string|max:7',
            'notes' => 'nullable|string|max:255',
        ]);

        BoardingPoint::create($request->only(['name', 'color_label', 'color_hex', 'notes']));

        return back()->with('success', 'Punto de abordaje creado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $bp = BoardingPoint::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100',
            'color_label' => 'required|string|max:50',
            'color_hex' => 'required|string|max:7',
            'is_active' => 'required|boolean',
            'notes' => 'nullable|string|max:255',
        ]);

        $bp->update($request->only(['name', 'color_label', 'color_hex', 'is_active', 'notes']));

        return back()->with('success', 'Punto de abordaje actualizado.');
    }

    public function destroy($id)
    {
        $bp = BoardingPoint::findOrFail($id);

        // Solo desactivar si tiene pasajeros vinculados
        if ($bp->passengers()->count() > 0) {
            $bp->update(['is_active' => false]);
            return back()->with('success', 'Punto desactivado (tiene pasajeros vinculados).');
        }

        $bp->delete();
        return back()->with('success', 'Punto de abordaje eliminado.');
    }
}
