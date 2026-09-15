<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccountStatusChangedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $isActive;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, bool $isActive)
    {
        $this->user = $user;
        $this->isActive = $isActive;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $subject = $this->isActive
            ? "Réactivation de votre compte - " . config('app.name')
            : "Notification de désactivation de compte - " . config('app.name');

        return $this->subject($subject)
                    ->view('emails.account_status_changed', [
                        'user' => $this->user,
                        'isActive' => $this->isActive,
                    ]);
    }
}
