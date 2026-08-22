<?php

namespace App\Mail;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Subscription $subscription;
    public User $user;
    public string $itemTitle;
    public array $changes;

    /**
     * Create a new message instance.
     */
    public function __construct(Subscription $subscription, array $changes = [])
    {
        $this->subscription = $subscription;
        $this->user = $subscription->user;
        $this->changes = $changes;

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
            subject: 'Mise à jour de votre souscription - ' . config('app.name'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.subscription_updated',
            with: [
                'subscription' => $this->subscription,
                'user' => $this->user,
                'itemTitle' => $this->itemTitle,
                'changes' => $this->changes,
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
