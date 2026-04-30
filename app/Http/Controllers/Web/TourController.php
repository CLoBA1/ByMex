<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Services\TourService;

class TourController extends Controller
{
    protected $tourService;

    public function __construct(TourService $tourService)
    {
        $this->tourService = $tourService;
    }

    public function index()
    {
        $tours = $this->tourService->getActiveTours();
        return view('tours.index', compact('tours'));
    }

    public function show($id)
    {
        $tour = Tour::with(['seats'])->findOrFail($id);
        $boardingPoints = \App\Models\BoardingPoint::where('is_active', true)->orderBy('name')->get();
        return view('tours.show', compact('tour', 'boardingPoints'));
    }
}
