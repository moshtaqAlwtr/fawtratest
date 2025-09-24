<?php

namespace App\Console\Commands;

use App\Models\Expense;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Invoice;
use App\Models\Visit;
use App\Models\PaymentsProcess;
use App\Models\Receipt;
use App\Models\ClientRelation;
use TCPDF;

class DailyEmployeeReport extends Command
{
    protected $signature = 'reports:daily_employee';
    protected $description = 'Send daily employee report to Telegram';

    public function handle()
    {
        $date = Carbon::today();

        // جلب فقط الموظفين الذين لديهم دور employee
        $users = User::where('role', 'employee')->get();

        foreach ($users as $user) {
            // الفواتير التي أنشأها الموظف اليوم
            $invoices = Invoice::where('created_by', $user->id)
                ->whereDate('created_at', $date)
                ->get();

            // جلب أرقام الفواتير
            $invoiceIds = $invoices->pluck('id')->toArray();

            // المدفوعات المرتبطة بهذه الفواتير
            $payments = PaymentsProcess::whereIn('invoice_id', $invoiceIds)
                ->whereDate('payment_date', $date)
                ->get();

            // الزيارات التي قام بها الموظف اليوم
            $visits = Visit::where('employee_id', $user->id)
                ->whereDate('created_at', $date)
                ->get();

            // الإيصالات التي أنشأها الموظف اليوم
            $receipts = Receipt::where('created_by', $user->id)
                ->whereDate('created_at', $date)
                ->get();

            // المصروفات التي أنشأها الموظف اليوم
            $expenses = Expense::where('created_by', $user->id)
                ->whereDate('created_at', $date)
                ->get();

            // الملاحظات التي أنشأها الموظف اليوم للعملاء (مباشرة بدون علاقة بالفواتير)
            $notes = ClientRelation::with('client')
                ->where('employee_id', $user->id) // فقط ملاحظات الموظف الحالي
                ->whereDate('created_at', $date)
                ->get();

            // إنشاء ملف PDF للموظف الحالي
            $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
            $pdf->SetCreator('Your Application');
            $pdf->SetAuthor('Your Name');
            $pdf->SetTitle('Daily Employee Report - ' . $user->name);
            $pdf->SetSubject('Daily Report');
            $pdf->AddPage();

            // محتوى التقرير للموظف الحالي
            $html = view('reports.daily_employee_single', [
                'user' => $user,
                'invoices' => $invoices,
                'visits' => $visits,
                'payments' => $payments,
                'receipts' => $receipts,
                'expenses' => $expenses,
                'notes' => $notes,
                'total_payments' => $payments->sum('amount'),
                'total_invoices' => $invoices->sum('total_amount'),
                'date' => $date->format('Y-m-d'),
            ])->render();

            $pdf->writeHTML($html, true, false, true, false, 'R');

            // حفظ الملف باسم فريد لكل موظف
            $pdfPath = storage_path('app/public/daily_report_'.$user->id.'_'.$date->format('Y-m-d').'.pdf');
            $pdf->Output($pdfPath, 'F');

            // إرسال إلى Telegram
            $botToken = '7642508596:AAHQ8sST762ErqUpX3Ni0f1WTeGZxiQWyXU';
            $chatId = '@Salesfatrasmart';

            $response = Http::attach('document', file_get_contents($pdfPath), 'daily_report_'.$user->name.'.pdf')
                ->post("https://api.telegram.org/bot{$botToken}/sendDocument", [
                    'chat_id' => $chatId,
                    'caption' => "📊 تقرير الموظف اليومي - ".$user->name." - ".$date->format('Y-m-d'),
                ]);

            if ($response->successful()) {
                $this->info('✅ تم إرسال تقرير الموظف '.$user->name.' بنجاح إلى Telegram');
            } else {
                $this->error('❌ فشل إرسال تقرير الموظف '.$user->name.': '.$response->body());
            }

            // حذف الملف بعد الإرسال
            if (file_exists($pdfPath)) {
                unlink($pdfPath);
            }
        }
    }
}
