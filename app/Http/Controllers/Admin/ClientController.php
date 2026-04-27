<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::withCount('reservations')->get();
        return view('admin.clients.index', compact('clients'));
    }
}
