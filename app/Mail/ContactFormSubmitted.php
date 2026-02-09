<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactFormSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $email;
    public $messageText;

    public function __construct(string $name, string $email, string $messageText)
    {
        $this->name = $name;
        $this->email = $email;
        $this->messageText = $messageText;
    }

    public function build()
    {
        return $this->subject('Contact form: ' . config('app.name'))
            ->replyTo($this->email, $this->name)
            ->view('emails.contact_form');
    }
}
