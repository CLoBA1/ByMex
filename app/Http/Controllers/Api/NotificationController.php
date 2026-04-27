<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $count = AdminNotification::unread()->count();
        $notifications = AdminNotification::unread()
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return response()->json([
            'count' => $count,
            'notifications' => \App\Http\Resources\NotificationResource::collection($notifications)
        ]);
    }

    public function markRead()
    {
        AdminNotification::unread()->update(['read' => true]);
        return response()->json(['success' => true]);
    }
}
