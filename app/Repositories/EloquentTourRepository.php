<?php

namespace App\Repositories;

use App\Models\Tour;
use App\Enums\TourStatus;
use App\Repositories\Contracts\TourRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class EloquentTourRepository implements TourRepositoryInterface
{
    public function getActiveTours()
    {
        return Tour::where('status', TourStatus::ACTIVE)
            ->where('departure_date', '>', now())
            ->orderBy('departure_date', 'asc')
            ->get();
    }

    public function getAllToursWithStats()
    {
        return Tour::withCount('reservations')->orderBy('departure_date', 'desc')->get();
    }

    public function findTourWithReservations(int $id)
    {
        return Tour::with(['reservations.client', 'reservations.seats'])->findOrFail($id);
    }

    public function createTour(array $data)
    {
        if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
            $path = tap($data['image'])->store('tours', 'public');
            $data['image'] = 'storage/' . $path->hashName();
        }

        return Tour::create($data);
    }

    public function updateTour(int $id, array $data)
    {
        $tour = Tour::findOrFail($id);

        if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
            if ($tour->image) {
                Storage::disk('public')->delete(str_replace('storage/', '', $tour->image));
            }
            $path = tap($data['image'])->store('tours', 'public');
            $data['image'] = 'storage/' . $path->hashName();
        } else {
            unset($data['image']);
        }

        $tour->update($data);
        return $tour;
    }

    public function deleteTour(int $id)
    {
        $tour = Tour::findOrFail($id);
        if ($tour->image) {
            Storage::disk('public')->delete(str_replace('storage/', '', $tour->image));
        }
        return $tour->delete();
    }
}
