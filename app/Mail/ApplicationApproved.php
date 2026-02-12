<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApplicationApproved extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $application;
    public $stall;
    public $contract;

    public function __construct($user, $application, $stall, $contract)
    {
        $this->user = $user;
        $this->application = $application;
        $this->stall = $stall;
        $this->contract = $contract;
    }

    public function build()
    {
        return $this->subject('Your Application Has Been Approved')
                    ->view('emails.application_approved');
    }
}
