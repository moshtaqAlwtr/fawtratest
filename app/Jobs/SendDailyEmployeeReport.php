<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\Invoice;
use App\Models\Visit;
use App\Models\PaymentsProcess;
use App\Models\Receipt;
use App\Models\Expense;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendDailyEmployeeReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        try {
            $date = Carbon::today();
            $users = User::all();
            $reports = [];

            foreach ($users as $user) {
                $reports[] = $this->generateUserReport($user, $date);
            }

            $pdfPath = $this->generatePdfReport($reports, $date);
            $this->sendToTelegram($pdfPath, $date);

            Log::info('Daily employee report sent successfully at ' . now());

        } catch (\Exception $e) {
            Log::error('Failed to send daily employee report: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function generateUserReport(User $user, Carbon $date): array
    {
        return [
            'user' => $user,
            'invoices' => Invoice::where('created_by', $user->id)
                                ->whereDate('created_at', $date)
                                ->get(),
            'visits' => Visit::where('employee_id', $user->id)
                            ->whereDate('created_at', $date)
                            ->get(),
            'payments' => PaymentsProcess::where('employee_id', $user->id)
                                        ->whereDate('payment_date', $date)
                                        ->get(),
            'receipts' => Receipt::where('created_by', $user->id)
                                ->whereDate('created_at', $date)
                                ->get(),
            'expenses' => Expense::where('created_by', $user->id)
                                ->whereDate('created_at', $date)
                                ->get(),
        ];
    }

    protected function generatePdfReport(array $reports, Carbon $date): string
    {
        $pdf = Pdf::loadView('reports.daily_employee', [
            'reports' => $reports,
            'date' => $date->toDateString(),
        ]);

        $pdfPath = storage_path('app/public/daily_report_' . $date->format('Y-m-d') . '.pdf');
        $pdf->save($pdfPath);

        return $pdfPath;
    }

    protected function sendToTelegram(string $pdfPath, Carbon $date): void
    {
        $botToken = config('services.telegram.bot_token', '7642508596:AAHQ8sST762ErqUpX3Ni0f1WTeGZxiQWyXU');
        $chatId = config('services.telegram.chat_id', '@Salesfatrasmart');

        $response = Http::attach('document', file_get_contents($pdfPath), basename($pdfPath))
            ->post("https://api.telegram.org/bot{$botToken}/sendDocument", [
                'chat_id' => $chatId,
                'caption' => "📊 تقرير الموظفين اليومي: {$date->toDateString()}",
            ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to send to Telegram: ' . $response->body());
        }

        // حذف الملف بعد الإرسال
        unlink($pdfPath);
    }
}
