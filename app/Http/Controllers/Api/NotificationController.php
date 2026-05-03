<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $admin = \App\Models\AdminOwner::first();
        if (!$admin) return response()->json(['count' => 0, 'notifications' => []]);

        $count = $admin->unreadNotifications()->count();
        $notifications = $admin->unreadNotifications()->take(10)->get();

        return response()->json([
            'count' => $count,
            'notifications' => $notifications->map(function ($notif) {
                return [
                    'id' => $notif->id,
                    'title' => $notif->data['title'] ?? 'Alerta',
                    'message' => $notif->data['message'] ?? '',
                    'link' => $notif->data['url'] ?? '#',
                    'icon' => $notif->data['icon'] ?? 'fa-bell',
                    'time' => $notif->created_at->diffForHumans()
                ];
            })
        ]);
    }

    public function markRead(Request $request)
    {
        $admin = \App\Models\AdminOwner::first();
        if ($admin) {
            if ($request->has('id')) {
                $notification = $admin->notifications()->where('id', $request->id)->first();
                if ($notification) $notification->markAsRead();
            } else {
                $admin->unreadNotifications->markAsRead();
            }
        }
        return response()->json(['success' => true]);
    }
}
