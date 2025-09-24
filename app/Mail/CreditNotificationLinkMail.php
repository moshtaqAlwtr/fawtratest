<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CreditNotificationLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public $credit;
    public $url;

    public function __construct($credit, $url)
    {
        $this->credit = $credit;
        $this->url = $url;
    }

    public function build()
    {
        return $this->subject('إشعار دائن من Fawtra')
                    ->view('emails.credit-link');
    }
}
