<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;

class ContactMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $email;
    public $subjectLine;
    public $messageContent;
    public $location;
    public $social_link;

    public function __construct($name, $email, $subjectLine, $messageContent, $location, $social_link)
    {
        $this->name = $name;
        $this->email = $email;
        $this->subjectLine = $subjectLine;
        $this->messageContent = $messageContent;
        $this->location = $location;
        $this->social_link = $social_link;
    }

    public function build()
    {
        return $this->subject('📩 Nouveau message - ' . $this->subjectLine)
            ->replyTo($this->email, $this->name)
            ->view('emails.contact-message');
    }
}