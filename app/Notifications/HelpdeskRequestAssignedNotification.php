<?php

namespace App\Notifications;

use App\Models\Ddd;
use Illuminate\Bus\Queueable;
use Illuminate\Support\HtmlString;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class HelpdeskRequestAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Ddd $ddd, public String $issue)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('ICT Helpdesk Request Ticket')
                    ->greeting('Dear ' . $notifiable->name . ', ')
                    ->line('You have been assigned a helpdesk support ticket for:')

                    ->line(new HtmlString('<table style="width: 100%;">'))
                    ->line(new HtmlString('<tr>
                        <td>Department: </td>
                        <td><b>' . $this->ddd->short . ' - ' . $this->ddd->name . '</b></td>
                    </tr>'))

                    ->line(new HtmlString('<tr><td>Floor: </td>
                        <td><b>' . $this->ddd->floor . '</b></td>
                    </tr>'))

                    ->line(new HtmlString('<tr><td>Issue: </td>
                        <td><i>' . $this->issue . '</i></td>
                    </tr>'))
                    ->line(new HtmlString('</table><br>'))
                    ->line('Please attend to this issue by clicking the button below:')
                    ->action('Attend to Issue', url('/'))
                    ->line('Your prompt attention to this matter is greatly appreciated.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
