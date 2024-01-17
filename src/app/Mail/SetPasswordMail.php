<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SetPasswordMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $toEmail;
    public $token;
    public $userName;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->onQueue('reset-password-email');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Set Password')
            ->view('emails.create-user')->to($this->toEmail);
    }

    public function sendMail()
    {
        Mail::queue($this);
    }

    public function setToEmail($toEmail)
    {
        $this->toEmail = $toEmail;
        return $this;
    }

    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    public function setUserName($userName)
    {
        $this->userName = $userName;
        return $this;
    }
}
