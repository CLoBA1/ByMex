<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bus;
use App\Models\BusImage;
use Illuminate\Support\Facades\Storage;

class BusController extends Controller
{
    public function index()
    {
        $buses = Bus::with('images')->get();
        return view('admin.buses.index', compact('buses'));
    }

    public function create()
    {
        return view('admin.buses.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120'
        ]);

        $bus = Bus::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->has('is_active')
        ]);

        $this->handleImages($request, $bus);

        return redirect()->route('admin.buses.index')->with('success', 'Autobús creado correctamente.');
    }

    public function edit($id)
    {
        $bus = Bus::with('images')->findOrFail($id);
        return view('admin.buses.edit', compact('bus'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120'
        ]);

        $bus = Bus::findOrFail($id);
        $bus->update([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->has('is_active')
        ]);

        $this->handleImages($request, $bus);

        return redirect()->route('admin.buses.index')->with('success', 'Autobús actualizado correctamente.');
    }

    public function destroy($id)
    {
        $bus = Bus::findOrFail($id);
        
        foreach ($bus->images as $img) {
            Storage::disk('public')->delete($img->image_path);
        }
        
        $bus->delete();

        return redirect()->route('admin.buses.index')->with('success', 'Autobús eliminado.');
    }

    public function destroyImage($imageId)
    {
        $image = BusImage::findOrFail($imageId);
        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        return back()->with('success', 'Imagen eliminada correctamente.');
    }

    public function setPrimaryImage($imageId)
    {
        $image = BusImage::findOrFail($imageId);
        
        // Remove primary flag from all other images of this bus
        BusImage::where('bus_id', $image->bus_id)->update(['is_primary' => false]);
        
        // Set this one as primary
        $image->update(['is_primary' => true]);

        return back()->with('success', 'Imagen marcada como principal.');
    }

    private function handleImages(Request $request, Bus $bus)
    {
        if ($request->hasFile('images')) {
            $hasPrimary = $bus->images()->where('is_primary', true)->exists();
            
            foreach ($request->file('images') as $index => $file) {
                $path = $file->store('buses', 'public');
                
                BusImage::create([
                    'bus_id' => $bus->id,
                    'image_path' => $path,
                    'is_primary' => (!$hasPrimary && $index === 0) ? true : false
                ]);
            }
        }
    }
}
