<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;

class ServiceCategoryController extends Controller
{
    public function index()
    {
        $categories = ServiceCategory::orderBy('order')->get();
        return view('admin.services.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.services.categories.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'order' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        ServiceCategory::create($validated);

        return redirect()->route('admin.service-categories.index')->with('success', 'Categoría creada exitosamente.');
    }

    public function edit(ServiceCategory $serviceCategory)
    {
        return view('admin.services.categories.form', compact('serviceCategory'));
    }

    public function update(Request $request, ServiceCategory $serviceCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'order' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        $serviceCategory->update($validated);

        return redirect()->route('admin.service-categories.index')->with('success', 'Categoría actualizada exitosamente.');
    }

    public function destroy(ServiceCategory $serviceCategory)
    {
        $serviceCategory->delete();
        return redirect()->route('admin.service-categories.index')->with('success', 'Categoría eliminada.');
    }
}
