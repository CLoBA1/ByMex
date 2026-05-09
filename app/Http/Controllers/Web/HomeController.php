<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\TourService;
use App\Models\Banner;

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
        $banners = Banner::where('is_active', true)->orderBy('sort_order')->get();
        return view('welcome', compact('tours', 'banners'));
    }

    public function about()
    {
        $buses = \App\Models\Bus::where('is_active', true)->with('images')->get();
        return view('about', compact('buses'));
    }

    public function services()
    {
        return view('services');
    }
}
