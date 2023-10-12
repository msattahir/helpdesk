<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Log;
use App\Events\HelpdeskRequestSubmitted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\HelpdeskRequestAssignedNotification;
use App\Notifications\HelpdeskRequestSubmittedNotification;

class NotifyHelpdeskRequestSubmission
{
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
    public function handle(HelpdeskRequestSubmitted $event): void
    {
        $helpdesk_request = $event->helpdesk_request;
        $staff = $helpdesk_request->staff;
        Notification::send($staff, new HelpdeskRequestSubmittedNotification());

        $support_staff = $event->helpdesk_support->staff;
        $issue = $helpdesk_request->description;
        Notification::send($support_staff, new HelpdeskRequestAssignedNotification($staff->ddd, $issue));
    }
}
