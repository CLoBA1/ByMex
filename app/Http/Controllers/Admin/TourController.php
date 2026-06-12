<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Enums\TourStatus;
use App\Models\BoardingPoint;
use App\Models\Tour;
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
        $allBoardingPoints = BoardingPoint::where('is_active', true)->orderBy('name')->get();
        return view('admin.tours.form', compact('allBoardingPoints'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'departure_date' => 'required|date',
            'price' => 'required|numeric|min:0',
            'minimum_deposit' => 'nullable|numeric|min:0|lte:price',
            'total_seats' => 'required|integer|min:1',
            'expiration_hours' => 'required|integer|min:1',
            'duration_days' => 'nullable|integer|min:1',
            'status' => 'required|in:active,inactive,completed',
            'description' => 'nullable|string',
            'itinerary' => 'nullable|string',
            'what_includes' => 'nullable|string',
            'what_not_includes' => 'nullable|string',
            'image' => 'nullable|image|max:2048'
        ]);

        $validated['requires_passenger_documents'] = $request->has('requires_passenger_documents');

        $tour = $this->repository->createTour($validated);

        // Sincronizar puntos de abordaje
        $boardingPointsData = [];
        foreach ($request->input('boarding_points', []) as $bpId => $data) {
            if (!empty($data['active'])) {
                $boardingPointsData[$bpId] = [
                    'departure_time' => $data['departure_time'] ?? null,
                    'sort_order'     => $data['sort_order'] ?? 0,
                ];
            }
        }
        $tour->boardingPoints()->sync($boardingPointsData);

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
        $allBoardingPoints = BoardingPoint::where('is_active', true)->orderBy('name')->get();
        $tourBoardingPoints = $tour->boardingPoints->keyBy('id');
        return view('admin.tours.form', compact('tour', 'allBoardingPoints', 'tourBoardingPoints'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'departure_date' => 'required|date',
            'price' => 'required|numeric|min:0',
            'minimum_deposit' => 'nullable|numeric|min:0|lte:price',
            'total_seats' => 'required|integer|min:1',
            'expiration_hours' => 'required|integer|min:1',
            'duration_days' => 'nullable|integer|min:1',
            'status' => 'required|in:active,inactive,completed',
            'description' => 'nullable|string',
            'itinerary' => 'nullable|string',
            'what_includes' => 'nullable|string',
            'what_not_includes' => 'nullable|string',
            'image' => 'nullable|image|max:2048'
        ]);

        $validated['requires_passenger_documents'] = $request->has('requires_passenger_documents');

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image');
        }

        $tour = $this->repository->updateTour($id, $validated);

        // Sincronizar puntos de abordaje
        $boardingPointsData = [];
        foreach ($request->input('boarding_points', []) as $bpId => $data) {
            if (!empty($data['active'])) {
                $boardingPointsData[$bpId] = [
                    'departure_time' => $data['departure_time'] ?? null,
                    'sort_order'     => $data['sort_order'] ?? 0,
                ];
            }
        }
        $tour->boardingPoints()->sync($boardingPointsData);

        return redirect()->route('admin.tours.index')->with('success', 'Tour actualizado exitosamente.');
    }

    public function destroy($id)
    {
        $this->repository->deleteTour($id);
        return redirect()->route('admin.tours.index')->with('success', 'Tour eliminado.');
    }
}
