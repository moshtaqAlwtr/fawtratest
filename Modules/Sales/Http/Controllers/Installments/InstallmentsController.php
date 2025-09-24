<?php

namespace Modules\Sales\Http\Controllers\Installments;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Installment; // إضافة نموذج الأقساط
use App\Models\InstallmentDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InstallmentsController extends Controller
{
    public function index(Request $request)
{
    // Initialize the query with relationships
    $query = Installment::with(['invoice.client', 'details']);

    // Apply filters based on the request
    if ($request->filled('status') && $request->status != 'الكل') {
        $query->whereHas('invoice', function ($q) use ($request) {
            $q->where('status', $request->status == '1' ? 'مكتمل' : 'غير مكتمل');
        });
    }

    if ($request->filled('identifier')) {
        $query->where('id', $request->identifier);
    }

    if ($request->filled('client')) {
        $query->whereHas('invoice.client', function ($q) use ($request) {
            $q->where('trade_name', 'like', '%' . $request->client . '%')
              ->orWhere('id', $request->client); // للبحث بالرقم التعريفي أيضاً
        });
    }

    // فلترة حسب الفترة (أسبوع/شهر)
    if ($request->filled('period')) {
        $now = now();
        if ($request->period == '1') { // أسبوع
            $query->whereBetween('due_date', [$now->startOfWeek(), $now->endOfWeek()]);
        } elseif ($request->period == '2') { // شهر
            $query->whereBetween('due_date', [$now->startOfMonth(), $now->endOfMonth()]);
        }
    }

    // فلترة حسب التاريخ
    if ($request->filled('fromDate')) {
        $query->where('due_date', '>=', $request->fromDate);
    }

    if ($request->filled('toDate')) {
        $query->where('due_date', '<=', $request->toDate);
    }

    // فلترة حسب حالة القسط (مدفوع/غير مدفوع)
    if ($request->filled('installment_status')) {
        if ($request->installment_status == 'paid') {
            $query->whereHas('details', function($q) {
                $q->where('status', 'paid');
            });
        } else {
            $query->whereDoesntHave('details')
                  ->orWhereHas('details', function($q) {
                      $q->where('status', '!=', 'paid');
                  });
        }
    }

    // Retrieve filtered installments with pagination
    $installments = $query->orderBy('due_date', 'desc')->paginate(20);

    // Calculate status for each installment
    foreach ($installments as $installment) {
        $installment->status = $this->calculateInstallmentStatus($installment);
    }

    return view('sales::installments.index', compact('installments'));
}
 public function create(Request $request)
    {
        $id = $request->query('id'); // الحصول على id من الـ query parameters
        Log::info('Invoice ID: ' . $id); // تسجيل قيمة $id
        $clients = Client::all();
        $invoice = Invoice::findOrFail($id);
        return view('sales::installments.create', compact('clients', 'invoice'));
    }

public function store(Request $request)
{
    $request->validate([
        'invoice_id' => 'required|exists:invoices,id',
        'client_id' => 'required|exists:clients,id',
        'amount' => 'required|numeric|min:0.01',
        'installment_number' => 'required|integer|min:1',
        'payment_rate' => 'required|integer|in:1,2,3,4,5',
        'due_date' => 'required|date',
        'note' => 'nullable|string',
    ]);

    $invoice = Invoice::findOrFail($request->invoice_id);

    // إنشاء اتفاقية التقسيط
    $installment = Installment::create([
        'invoice_id' => $request->invoice_id,
        'client_id' => $request->client_id,
        'amount' => $request->amount,
        'installment_number' => $request->installment_number,
        'payment_rate' => $request->payment_rate,
        'start_date' => Carbon::parse($request->due_date)->format('Y-m-d'),
        'note' => $request->note,
    ]);

    // 👇 هذا السطر ضروري حتى نضمن أن payment_rate مش null
    $installment->refresh();

    // إنشاء تفاصيل الأقساط
    $this->createInstallmentDetails($installment, $invoice, $request->amount);

    return redirect()->route('installments.index')->with('success', 'تم إضافة اتفاقية التقسيط بنجاح.');
}


private function createInstallmentDetails(Installment $installment, Invoice $invoice, $installmentAmount)
{
    $remainingAmount = $invoice->due_value;
    $numberOfInstallments = $installment->installment_number;

    if ($remainingAmount <= 0 || $numberOfInstallments <= 0) {
        throw new \InvalidArgumentException('المبلغ المتبقي وعدد الأقساط يجب أن يكونا أكبر من صفر');
    }

    $dueDate = Carbon::parse($installment->start_date);

    for ($i = 1; $i <= $numberOfInstallments; $i++) {
        // القسط الأخير يأخذ الباقي
        if ($i == $numberOfInstallments) {
            $amount = $remainingAmount;
        } else {
            $amount = min($installmentAmount, $remainingAmount);
        }

        // لا تسمح بالقيمة أقل من 0.01
        $amount = max(round($amount, 2), 0.01);

        // تخزين القسط
        InstallmentDetail::create([
            'installments_id' => $installment->id,
            'amount' => $amount,
            'due_date' => $dueDate->format('Y-m-d'),
            'status' => 'unactive',
            'installment_number' => $i,
            'payment_rate' => $installment->payment_rate,
        ]);

        // تحديث الرصيد
        $remainingAmount -= $amount;

        // نحرك التاريخ للقسط الجاي
        $dueDate = $this->calculateNextDueDate($dueDate, $installment->payment_rate);
    }

    // تحديث الفاتورة
    $invoice->update([
        'status' => 'installment',
        'remaining_amount' => round($remainingAmount, 2),
    ]);
}

private function calculateNextDueDate(Carbon $currentDate, int $paymentRate): Carbon
{
    switch ($paymentRate) {
        case 1: // شهري
            return $currentDate->addMonth();
        case 2: // أسبوعي
            return $currentDate->addWeek();
        case 3: // سنوي
            return $currentDate->addYear();
        case 4: // ربع سنوي
            return $currentDate->addMonths(3);
        case 5: // مرة كل أسبوعين
            return $currentDate->addWeeks(2);
        default: // افتراضي شهري
            return $currentDate->addMonth();
    }
}

   public function edit($id)
    {
        // Retrieve the specific installment along with its related invoice and client
        $installment = Installment::with('invoice.client')->findOrFail($id);
        $clients = Client::all(); // Assuming you have a Client model to get all clients
        $invoice = $installment->invoice; // Get the related invoice

        // Return the edit view with the installment data
        return view('sales::installments.edit', compact('installment', 'clients', 'invoice'));
    }
    public function update(Request $request, $id)
    {
        // Validate the input data
        $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'amount' => 'nullable|numeric',
            'payment_rate' => 'nullable|integer',
            'due_date' => 'nullable|date',
            'note' => 'nullable|string',
        ]);

        // Find the installment
        $installment = Installment::findOrFail($id);

        // Create an array of the fields to update
        $dataToUpdate = [];

        if ($request->filled('client_id')) {
            $dataToUpdate['client_id'] = $request->client_id;
        }

        if ($request->filled('amount')) {
            $dataToUpdate['amount'] = $request->amount;

            // حساب عدد الأقساط بناءً على المبلغ المدخل
            $grandTotal = $installment->invoice->grand_total; // المبلغ الإجمالي
            $installmentAmount = $request->amount; // مبلغ القسط الجديد

            // حساب عدد الأقساط
            $numberOfInstallments = floor($grandTotal / $installmentAmount); // عدد الأقساط الكاملة
            $remainingAmount = $grandTotal % $installmentAmount; // المبلغ المتبقي

            // إذا كان هناك مبلغ متبقي، فسيكون هناك قسط إضافي
            if ($remainingAmount > 0) {
                $numberOfInstallments += 1; // إضافة قسط إضافي
            }

            // تحديث عدد الأقساط
            $dataToUpdate['installment_number'] = $numberOfInstallments;
        }

        if ($request->filled('due_date')) {
            $dataToUpdate['due_date'] = $request->due_date;
        }
        if ($request->filled('payment_rate')) {
            $dataToUpdate['payment_rate'] = $request->payment_rate;
        }
        if ($request->filled('note')) {
            $dataToUpdate['note'] = $request->note;
        }

        // Update the installment with the specified fields
        $installment->fill($dataToUpdate);
        $installment->save(); // Save the changes

        return redirect()->route('installments.index')->with('success', 'تم تحديث القسط بنجاح.');
    }
    public function destroy($id)
    {
        try{


        $installment = Installment::findOrFail($id);
        $installment->delete();

        return redirect()->route('installments.index')->with('success', 'تم حذف القسط بنجاح.');
    }
    catch(\Exception $e){
        return redirect()->route('installments.index')->with('error', 'حدث خطأ في حذف القسط.');
    }
    }
public function agreement(Request $request)
{
    // ابدأ الاستعلام مع العلاقات المطلوبة

    // جلب الأقساط
    $installments =InstallmentDetail::all();

    // إرسال البيانات للواجهة
    return view('sales::installments.installments_detites.agreement_installments', compact('installments'));
}

public function show($id)
{
    // استرجاع القسط مع التفاصيل والفاتورة والعميل
    $installment = Installment::with([ 'details'])->findOrFail($id);
    $invoice = $installment->invoice;

    return view('sales::installments.show', compact('installment', 'invoice'));
}

    public function edit_amount($id)
    {
        $installment = Installment::with('invoice.client')->findOrFail($id);
        $clients = Client::all(); // Assuming you have a Client model to get all clients
        $invoice = $installment->invoice; // Get the related invoice

        // Return the edit view with the installment data
        return view('sales::installments.installments_detites.edit', compact('installment', 'clients', 'invoice'));
    }
    public function show_amount($id)
    {
        $installment = Installment::with('invoice.client')->findOrFail($id);
        $invoice = Invoice::findOrFail($installment->invoice_id);

        // ا��ترجا�� ��ميع الأقسا�� المرتبطة بالفاتورة
        $installments = Installment::where('invoice_id', $invoice->id)->get();

        return view('sales::Installments.installments_detites.show', compact('installment', 'invoice', 'installments'));
    }
private function calculateInstallmentStatus($installment)
    {
        $totalPaid = $installment->invoice->installments()->sum('amount'); // Total paid so far
        $remainingBalance = $installment->invoice->grand_total - $totalPaid; // Remaining balance

        if ($remainingBalance > 0) {
            // Check if the due date is in the past
            if (Carbon::parse($installment->due_date)->isPast()) {
                return 'متأخر'; // Late
            }
            return 'غير مكتمل'; // Incomplete
        }
        return 'مكتمل'; // Complete
    }
}
