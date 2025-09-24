<?php

namespace App\Listeners;

use App\Events\DailyReportEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Artisan;

class SendDailyReportListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(DailyReportEvent $event)
{
    Artisan::call('reports:daily_employee');
}
}
