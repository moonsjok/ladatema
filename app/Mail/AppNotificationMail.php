<?php

namespace App\Mail;

use App\Models\AppNotification;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public AppNotification $appNotification;
    public User $recipient;

    /**
     * Create a new message instance.
     */
    public function __construct(AppNotification $appNotification, User $recipient)
    {
        $this->appNotification = $appNotification;
        $this->recipient = $recipient;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🔔 ' . $this->appNotification->title . ' - ' . config('app.name'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.app_notification',
            with: [
                'appNotification' => $this->appNotification,
                'recipient' => $this->recipient,
                'sender' => $this->appNotification->sender,
            ],
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
