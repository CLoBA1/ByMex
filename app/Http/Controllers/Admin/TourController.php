<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Enums\TourStatus;
use App\Repositories\Contracts\TourRepositoryInterface;
use Illuminate\Http\Request;

class TourController extends Controller
{
    protected $repository;

    public function __construct(TourRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        $tours = $this->repository->getAllToursWithStats();
        return view('admin.tours.index', compact('tours'));
    }

    public function create()
    {
        return view('admin.tours.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'departure_date' => 'required|date',
            'price' => 'required|numeric|min:0',
            'total_seats' => 'required|integer|min:1',
            'expiration_hours' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive,completed',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048'
        ]);

        $validated['requires_passenger_documents'] = $request->has('requires_passenger_documents');

        $this->repository->createTour($validated);

        return redirect()->route('admin.tours.index')->with('success', 'Tour creado exitosamente.');
    }

    public function show($id)
    {
        $tour = $this->repository->findTourWithReservations($id);
        return view('admin.tours.show', compact('tour'));
    }

    public function edit($id)
    {
        $tour = $this->repository->findTourWithReservations($id);
        return view('admin.tours.form', compact('tour'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'departure_date' => 'required|date',
            'price' => 'required|numeric|min:0',
            'total_seats' => 'required|integer|min:1',
            'expiration_hours' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive,completed',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048'
        ]);

        $validated['requires_passenger_documents'] = $request->has('requires_passenger_documents');

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image');
        }

        $this->repository->updateTour($id, $validated);

        return redirect()->route('admin.tours.index')->with('success', 'Tour actualizado exitosamente.');
    }

    public function destroy($id)
    {
        $this->repository->deleteTour($id);
        return redirect()->route('admin.tours.index')->with('success', 'Tour eliminado.');
    }
}
