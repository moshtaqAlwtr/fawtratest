<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoicePdfMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;
    public $filePath;

    public function __construct($invoice, $filePath)
    {
        $this->invoice = $invoice;
        $this->filePath = $filePath;
    }

    public function build()
    {
        return $this->subject('فاتورتك من فواترا')
                    ->markdown('emails.invoices.pdf')
                    ->attach($this->filePath, [
                        'as' => 'فاتورة-' . $this->invoice->code . '.pdf',
                        'mime' => 'application/pdf',
                    ]);
    }
}
