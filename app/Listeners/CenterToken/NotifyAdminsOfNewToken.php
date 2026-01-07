<?php

namespace App\Listeners\CenterToken;

use App\Events\CenterToken\CenterTokenEvent;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class NotifyAdminsOfNewToken implements ShouldQueue
{
    use InteractsWithQueue;
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(CenterTokenEvent $event): void
    {
        $admins = User::whereHas('roles', function ($query): void {
            $query->where('name', 'admin');
        })->get();
 
        if ($event->type === 'create') {
            Notification::send($admins, new \App\Notifications\Alerts\TokenCreatedNotification($event->center, $event->tokenName));
        } else {
            Notification::send($admins, new \App\Notifications\Alerts\TokenDeletedNotification($event->center, $event->tokenName));
        }
    }
}
