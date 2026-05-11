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

    public function storeSubPoint(Request $request, $boardingPointId)
    {
        $bp = BoardingPoint::findOrFail($boardingPointId);

        $request->validate([
            'name' => 'required|string|max:150',
            'reference' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
        ]);

        $bp->subPoints()->create([
            'name' => $request->name,
            'reference' => $request->reference,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => true,
        ]);

        return back()->with('success', 'Punto específico agregado a ' . $bp->name . '.');
    }

    public function updateSubPoint(Request $request, $id)
    {
        $sub = \App\Models\BoardingSubPoint::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:150',
            'reference' => 'nullable|string|max:255',
            'is_active' => 'required|boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $sub->update([
            'name' => $request->name,
            'reference' => $request->reference,
            'is_active' => $request->is_active,
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return back()->with('success', 'Punto específico actualizado.');
    }

    public function destroySubPoint($id)
    {
        $sub = \App\Models\BoardingSubPoint::findOrFail($id);

        if ($sub->passengers()->count() > 0) {
            $sub->update(['is_active' => false]);
            return back()->with('success', 'El subpunto ha sido desactivado porque tiene pasajeros vinculados.');
        }

        $sub->delete();
        return back()->with('success', 'Punto específico eliminado.');
    }
}
