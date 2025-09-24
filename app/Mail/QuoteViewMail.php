<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
class QuoteViewMail extends Mailable
{
    use Queueable, SerializesModels;

    public $quote;
    public $viewUrl;

    public function __construct($quote, $viewUrl)
    {
        $this->quote = $quote;
        $this->viewUrl = $viewUrl;
    }

    public function build()
    {
        return $this->subject('رابط عرض السعر')
                    ->view('emails.quote-view');
    }
}
