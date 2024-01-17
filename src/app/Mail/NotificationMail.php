<?php

namespace App\Mail;

use App\Helpers\QueueHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class NotificationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $title;
    public $subject = "[OpsAcademy] | Cron Notification";
    public $toEmail;
    public $messages = [];

    public function __construct()
    {
        $this->onQueue(QueueHelper::NOTIFICATION_EMAIL_QUEUE);
    }


    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->subject)
            ->view('emails.crons.general-notification')
            ->to($this->toEmail);
    }

    public function sendMail()
    {
        Mail::send($this);
    }

    public function setToEmail($toEmail)
    {
        $this->toEmail = $toEmail;
        return $this;
    }

    public function setMessage($message){
        $this->messages = $message;
        return $this;
    }

    public function appendMessage($message){
        $this->messages[] = $message;
        return $this;
    }
}

