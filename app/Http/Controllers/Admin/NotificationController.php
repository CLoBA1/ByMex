<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdminOwner;

class NotificationController extends Controller
{
    public function unread()
    {
        $admin = AdminOwner::first();
        if (!$admin) return response()->json(['count' => 0, 'notifications' => []]);

        $notifications = $admin->unreadNotifications()->take(5)->get();
        return response()->json([
            'count' => $admin->unreadNotifications()->count(),
            'notifications' => $notifications
        ]);
    }

    public function markAsRead($id)
    {
        $admin = AdminOwner::first();
        if ($admin) {
            $notification = $admin->notifications()->where('id', $id)->first();
            if ($notification) {
                $notification->markAsRead();
            }
        }
        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        $admin = AdminOwner::first();
        if ($admin) {
            $admin->unreadNotifications->markAsRead();
        }
        return response()->json(['success' => true]);
    }
}
