<?php

namespace App\Mail;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminNewSubscriptionMail extends Mailable
{
    use Queueable, SerializesModels;

    public Subscription $subscription;
    public User $user;
    public string $itemTitle;

    /**
     * Create a new message instance.
     */
    public function __construct(Subscription $subscription)
    {
        $this->subscription = $subscription;
        $this->user = $subscription->user;

        if ($subscription->formation) {
            $this->itemTitle = $subscription->formation->title ?? 'Formation';
        } elseif ($subscription->course) {
            $this->itemTitle = $subscription->course->title ?? 'Cours';
        } elseif ($subscription->chapter) {
            $this->itemTitle = $subscription->chapter->title ?? 'Chapitre';
        } else {
            $this->itemTitle = 'Souscription';
        }
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🔔 Nouvelle souscription reçue - ' . config('app.name'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.admin_new_subscription',
            with: [
                'subscription' => $this->subscription,
                'user' => $this->user,
                'itemTitle' => $this->itemTitle,
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
