<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $details;

    public function __construct($details)
    {
        $this->details = $details;
    }

    public function build()
    {
        // تحديد نوع البريد والموضوع والعرض المناسب
        $type = $this->details['type'] ?? 'employee_credentials';

        switch ($type) {
            case 'project_invite':
                return $this->subject('دعوة للانضمام إلى مشروع: ' . $this->details['project_title'])
                    ->view('emails.project_invite')
                    ->with('details', $this->details);

            case 'project_notification':
                return $this->subject('تم إضافتك إلى مشروع: ' . $this->details['project_title'])
                    ->view('emails.project_notification')
                    ->with('details', $this->details);

            default: // employee_credentials
                return $this->subject('تفاصيل تسجيل الدخول')
                    ->view('emails.login_credentials')
                    ->with('details', $this->details);
        }
    }
}
