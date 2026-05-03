<?php

namespace App\Models;

class AdminOwner extends User
{
    protected $table = 'users';

    public function routeNotificationForMail($notification)
    {
        return config('app.owner_notification_email', env('OWNER_NOTIFICATION_EMAIL', 'elvis.18.1970@gmail.com'));
    }
}
