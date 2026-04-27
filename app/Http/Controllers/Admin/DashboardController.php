<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Models\Reservation;
use App\Models\Client;

class DashboardController extends Controller
{
    public function index()
    {
        $tours = Tour::withCount(['reservations', 'seats'])->orderBy('departure_date', 'asc')->get();

        $dbNotifs = \App\Models\AdminNotification::unread()->orderBy('created_at', 'desc')->take(10)->get();
        $notifications = \App\Http\Resources\NotificationResource::collection($dbNotifs)->toArray(request());

        $totalClients = Client::count();
        $totalPaidRevenue = Reservation::where('status', \App\Enums\ReservationStatus::PAID)->sum('total_amount');

        return view('dashboard', compact('tours', 'notifications', 'totalClients', 'totalPaidRevenue'));
    }
}
