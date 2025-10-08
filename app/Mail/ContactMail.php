<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactMail extends Mailable
{
    use SerializesModels;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {
        return $this->subject(env('APP_DOMAINE_NAME') . " : Nouveau message du formulaire de contact")
            ->view('emails.contact')
            ->with('data', $this->data);
    }
}
