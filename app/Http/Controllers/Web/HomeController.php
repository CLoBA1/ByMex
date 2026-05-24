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
        $categories = \App\Models\ServiceCategory::with(['options' => function ($query) {
            $query->where('status', 'active')->orderBy('order');
        }])->where('status', 'active')->orderBy('order')->get();

        $paymentSetting = \App\Models\PaymentSetting::first();
        $waNumber = $paymentSetting ? $paymentSetting->whatsapp_number : '527331362024';
        
        // Ensure WA number format is clean (digits only)
        $waNumber = preg_replace('/[^0-9]/', '', $waNumber);

        return view('services', compact('categories', 'waNumber'));
    }
}
