<?php

namespace App\Console;

use App\Jobs\ProcessAutoDepartures;
use App\Models\Log;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {
        // تنفيذ مهمة مغادرة الزوار تلقائيًا كل دقيقة
        $schedule->job(new ProcessAutoDepartures())
            ->everyMinute()
            ->onFailure(function () {
                Log::error('Auto departure job failed');
            });

        // ✅ تنفيذ التقرير اليومي للموظفين الساعة 10 مساءً كل يوم
        $schedule->command('reports:daily_employee')
            ->dailyAt('23:55') // يمكنك تغيير الوقت مثلاً '08:00' صباحًا أو غيره
            ->onFailure(function () {
                Log::error('فشل في تنفيذ أمر التقرير اليومي للموظفين.');
            });
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
