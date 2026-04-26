<?php

namespace App\Mail;

use App\Models\SupportConversation;
use App\Models\SupportMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewSupportTicketMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public SupportConversation $conversation,
        public SupportMessage $supportMessage
    ) {}

    public function build()
    {
        return $this
            ->subject('Nouveau ticket support #' . $this->conversation->id)
            ->view('emails.new-support-ticket');
    }
}