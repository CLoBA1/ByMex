<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\TourService;

class HomeController extends Controller
{
    protected $tourService;

    public function __construct(TourService $tourService)
    {
        $this->tourService = $tourService;
    }

    public function index()
    {
        $tours = $this->tourService->getActiveTours();
        return view('welcome', compact('tours'));
    }

    public function about()
    {
        return view('about');
    }
}
