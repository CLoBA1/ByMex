<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceOption;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ServiceOptionController extends Controller
{
    public function index()
    {
        $options = ServiceOption::with('category')->orderBy('order')->get();
        return view('admin.services.options.index', compact('options'));
    }

    public function create()
    {
        $categories = ServiceCategory::orderBy('name')->get();
        return view('admin.services.options.form', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_category_id' => 'required|exists:service_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'required|integer|min:0',
            'whatsapp_message' => 'required|string',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('services/options', 'public');
        }

        ServiceOption::create($validated);

        return redirect()->route('admin.service-options.index')->with('success', 'Opción creada exitosamente.');
    }

    public function edit(ServiceOption $serviceOption)
    {
        $categories = ServiceCategory::orderBy('name')->get();
        return view('admin.services.options.form', compact('serviceOption', 'categories'));
    }

    public function update(Request $request, ServiceOption $serviceOption)
    {
        $validated = $request->validate([
            'service_category_id' => 'required|exists:service_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'required|integer|min:0',
            'whatsapp_message' => 'required|string',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($serviceOption->image) {
                Storage::disk('public')->delete($serviceOption->image);
            }
            $validated['image'] = $request->file('image')->store('services/options', 'public');
        }

        $serviceOption->update($validated);

        return redirect()->route('admin.service-options.index')->with('success', 'Opción actualizada exitosamente.');
    }

    public function destroy(ServiceOption $serviceOption)
    {
        if ($serviceOption->image) {
            Storage::disk('public')->delete($serviceOption->image);
        }
        $serviceOption->delete();
        return redirect()->route('admin.service-options.index')->with('success', 'Opción eliminada.');
    }
}
