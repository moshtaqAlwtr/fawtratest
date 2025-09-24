<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SimpleLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subjectText;
    public $bodyMessage;

    public function __construct($subjectText, $bodyMessage)
    {
        $this->subjectText = $subjectText;
        $this->bodyMessage = $bodyMessage;
    }

    public function build()
    {
        return $this->subject($this->subjectText)
                    ->html($this->bodyMessage);
    }
}
