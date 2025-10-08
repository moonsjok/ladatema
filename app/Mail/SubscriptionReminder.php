<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SubscriptionReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $content; // Changement de nom ici
    public $student;

    public function __construct($content, User $student)
    {
        $this->content = $content; // Changement de nom ici
        $this->student = $student;

        Log::debug('Construction de SubscriptionReminder.', [
            'content_type' => gettype($content), // Mise à jour du log
            'content_preview' => substr($content, 0, 50) . '...', // Mise à jour du log
            'student_id' => $student->id,
        ]);
    }

    public function build()
    {
        return $this->subject('Rappel de Souscription')
            ->view('emails.subscription_reminder')
            ->with([
                'content' => $this->content, // Changement de nom ici
                'student' => $this->student,
            ]);
    }
}
