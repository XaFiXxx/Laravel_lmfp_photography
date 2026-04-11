<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewsletterMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $subjectLine;
    public string $contentText;
    public string $unsubscribeUrl;

    public function __construct(string $subjectLine, string $contentText, string $unsubscribeUrl)
    {
        $this->subjectLine = $subjectLine;
        $this->contentText = $contentText;
        $this->unsubscribeUrl = $unsubscribeUrl;
    }

    public function build()
    {
        return $this->subject($this->subjectLine)
            ->view('emails.newsletter');
    }
}