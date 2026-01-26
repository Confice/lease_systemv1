<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PresentationScheduled extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $application;
    public $presentationDate;
    public $presentationTime;
    public $marketplace;

    public function __construct($user, $application, $presentationDate, $presentationTime, $marketplace)
    {
        $this->user = $user;
        $this->application = $application;
        $this->presentationDate = $presentationDate;
        $this->presentationTime = $presentationTime;
        $this->marketplace = $marketplace;
    }

    public function build()
    {
        return $this->subject('Confirmation of Your Business Proposal Presentation')
                    ->view('emails.presentation_scheduled');
    }
}

