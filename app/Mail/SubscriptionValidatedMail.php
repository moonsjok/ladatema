<?php

namespace App\Mail;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionValidatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Subscription $subscription;
    public User $user;
    public string $itemTitle;
    public string $courseUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(Subscription $subscription)
    {
        $this->subscription = $subscription;
        $this->user = $subscription->user;

        $targetCourseId = null;
        if ($subscription->formation_id && $subscription->formation) {
            $this->itemTitle = $subscription->formation->title ?? 'Formation';
            $firstCourse = $subscription->formation->courses()->first();
            $targetCourseId = $firstCourse ? $firstCourse->id : 1;
        } elseif ($subscription->course_id && $subscription->course) {
            $this->itemTitle = $subscription->course->title ?? 'Cours';
            $targetCourseId = $subscription->course_id;
        } elseif ($subscription->chapter_id && $subscription->chapter) {
            $this->itemTitle = $subscription->chapter->title ?? 'Chapitre';
            $targetCourseId = $subscription->chapter->course_id ?? 1;
        } else {
            $this->itemTitle = 'Votre souscription';
            $targetCourseId = 1;
        }

        $this->courseUrl = route('course-viewer', ['course' => $targetCourseId ?? 1]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🎉 Votre souscription est validée - Accès aux cours activé !',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.subscription_validated',
            with: [
                'subscription' => $this->subscription,
                'user' => $this->user,
                'itemTitle' => $this->itemTitle,
                'courseUrl' => $this->courseUrl,
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
