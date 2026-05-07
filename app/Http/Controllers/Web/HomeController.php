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
        return view('about');
    }
}
