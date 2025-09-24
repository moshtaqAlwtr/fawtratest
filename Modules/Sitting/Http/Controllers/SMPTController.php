<?php

namespace Modules\Sitting\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestMail;

class SMPTController extends Controller
{
    public function index()
    {

        return view('sitting::smpt.index');
    }

    public function sendTestMail()
    {

        $details = [
            'message' => 'هذه رسالة تجريبية من Laravel عبر SMTP!'
        ];

        Mail::to('mon3em0560@hotmail.com')->send(new TestMail($details));

        return 'تم إرسال البريد بنجاح!';
    }
}
