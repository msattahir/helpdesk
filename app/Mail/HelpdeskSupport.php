<?php

namespace App\Mail;

use App\Models\Ddd;
use App\Models\Staff;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class HelpdeskSupport extends Mailable
{
    use Queueable, SerializesModels;

    public $staff;
    public $ddd;

    /**
     * Create a new message instance.
     */
    public function __construct(Staff $staff, Ddd $ddd)
    {
        $this->staff = $staff;
        $this->ddd = $ddd;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Helpdesk Support',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'helpdesk-support-mail',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
