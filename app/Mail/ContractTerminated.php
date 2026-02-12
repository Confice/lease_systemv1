<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContractTerminated extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $contract;
    public $reason;

    public function __construct($user, $contract, $reason)
    {
        $this->user = $user;
        $this->contract = $contract;
        $this->reason = $reason;
    }

    public function build()
    {
        return $this->subject('Your Lease Contract Has Been Terminated')
                    ->view('emails.contract_terminated');
    }
}
