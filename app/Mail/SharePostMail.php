<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SharePostMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subjectLine;
    public $introMessage;
    public $postTitle;
    public $postDescription;
    public $postUrl;
    public $postImage;
    public $unsubscribeUrl;

    public function __construct(
        $subjectLine,
        $introMessage,
        $postTitle,
        $postDescription,
        $postUrl,
        $postImage,
        $unsubscribeUrl
    ) {
        $this->subjectLine = $subjectLine;
        $this->introMessage = $introMessage;
        $this->postTitle = $postTitle;
        $this->postDescription = $postDescription;
        $this->postUrl = $postUrl;
        $this->postImage = $postImage;
        $this->unsubscribeUrl = $unsubscribeUrl;
    }

    public function build()
    {
        return $this
            ->subject($this->subjectLine)
            ->view('emails.share-post');
    }
}