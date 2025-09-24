<?php

namespace Modules\Api\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sales\ClientPaymentRequest;
use App\Models\Account;
use App\Models\Client;
use App\Models\Employee;
use App\Models\Installment;
use App\Models\Invoice;
use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;
use App\Models\notifications;
use App\Models\PaymentMethod;
use App\Models\PaymentsProcess;
use App\Traits\ApiResponseTrait;
use App\Models\TreasuryEmployee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Api\Http\Resources\PaymentResource;

class PaymentProcessController extends Controller
{
     use ApiResponseTrait;
     
  public function index(Request $request)
{
    try {
        $query = PaymentsProcess::with(['invoice.client', 'employee']);

        if (auth()->user()->role == 'employee') {
            $query->where('employee_id', auth()->user()->id);
        }

        // فلترة بالحقول حسب الطلب
        if ($request->filled('invoice_number')) {
            $query->whereHas('invoice', function ($q) use ($request) {
                $q->where('id', 'like', '%' . $request->invoice_number . '%');
            });
        }

        if ($request->filled('payment_number')) {
            $query->where('payment_number', 'like', '%' . $request->payment_number . '%');
        }

        if ($request->filled('customer')) {
            $query->where('client_id', $request->customer);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('from_date')) {
            $query->where('payment_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->where('payment_date', '<=', $request->to_date);
        }

        if ($request->filled('identifier')) {
            $query->where('reference_number', 'like', '%' . $request->identifier . '%');
        }

        if ($request->filled('transfer_id')) {
            $query->where('reference_number', 'like', '%' . $request->transfer_id . '%');
        }

        if ($request->filled('total_greater_than')) {
            $query->where('amount', '>', $request->total_greater_than);
        }

        if ($request->filled('total_less_than')) {
            $query->where('amount', '<', $request->total_less_than);
        }

        if ($request->filled('custom_field')) {
            $query->where('notes', 'like', '%' . $request->custom_field . '%');
        }

        if ($request->filled('invoice_origin')) {
            $query->whereHas('invoice', function ($q) use ($request) {
                $q->where('origin', $request->invoice_origin);
            });
        }

        if ($request->filled('collected_by')) {
            $query->where('employee_id', $request->collected_by);
        }

        // تنفيذ الاستعلام مع pagination
        $payments = $query->where('type', 'client payments')
                          ->orderBy('created_at', 'desc')
                          ->paginate(25);

        $data = PaymentResource::collection($payments);

        return $this->paginatedResponse($data, 'تم جلب بيانات المدفوعات بنجاح');
        
    } catch (\Exception $e) {
        return $this->errorResponse('حدث خطأ أثناء جلب البيانات', 500, $e->getMessage());
    }
}


   public function createPaymentData($id, Request $request)
{
    $type = $request->input('type', 'invoice');

    if ($type === 'installment') {
        $installment = Installment::with('invoice')->findOrFail($id);
        $amount = $installment->amount;
        $invoiceId = $installment->invoice->id;
    } else {
        $invoice = Invoice::findOrFail($id);
        $amount = $invoice->grand_total - $invoice->advance_payment;
        $invoiceId = $invoice->id;
    }

    $payments = PaymentMethod::where('type', 'normal')->where('status', 'active')->get();
    $employees = auth()->user()->role !== 'manager'
        ? Employee::where('id', auth()->user()->employee_id)->get()
        : Employee::all();

    $mainTreasuryAccount = optional(
        TreasuryEmployee::where('employee_id', auth()->user()->employee_id)->first()
    )->treasury
        ?? Account::where('name', 'الخزينة الرئيسية')->first();

    return response()->json([
        'invoice_id' => $invoiceId,
        'amount' => $amount,
        'payment_methods' => $payments,
        'employees' => $employees,
        'treasury_account' => $mainTreasuryAccount,
    ]);
}
public function storeApi(ClientPaymentRequest $request)
{
    try {
        DB::beginTransaction();
        $data = $request->validated();
        $invoice = Invoice::findOrFail($data['invoice_id']);

        $totalPrevious = PaymentsProcess::where('invoice_id', $invoice->id)
            ->where('type', 'client payments')
            ->where('payment_status', '!=', 5)
            ->sum('amount');

        $remaining = $invoice->grand_total - $totalPrevious;

        if (round($data['amount'], 2) - round($remaining, 2) > 0.01) {
            return response()->json([
                'status' => false,
                'message' => 'مبلغ الدفع يتجاوز المبلغ المتبقي: ' . number_format($remaining, 2)
            ], 422);
        }

        $payment_status = match (true) {
            $totalPrevious + $data['amount'] >= $invoice->grand_total => 1, // مكتمل
            default => 2, // غير مكتمل
        };

        if ($request->has('payment_status')) {
            $payment_status = $request->payment_status;
        }

        $data['type'] = 'client payments';
        $data['created_by'] = auth()->id();
        $data['payment_status'] = $payment_status;

        if ($request->hasFile('attachments')) {
            $file = $request->file('attachments');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('assets/uploads/'), $filename);
            $data['attachments'] = $filename;
        }

        $mainTreasuryAccount = optional(
            TreasuryEmployee::where('employee_id', auth()->user()->employee_id)->first()
        )->treasury
            ?? Account::where('name', 'الخزينة الرئيسية')->first();

        if (!$mainTreasuryAccount) {
            throw new \Exception('لا توجد خزينة متاحة');
        }

        $data['employee_id'] = auth()->user()->employee_id;
        $payment = PaymentsProcess::create($data);

        // إشعار
        $client = Client::find($invoice->client_id);
        notifications::create([
            'type' => 'invoice_payment',
            'title' => auth()->user()->name . ' أنشأ عملية دفع',
            'description' => 'دفع للفاتورة #' . $invoice->id . ' - العميل ' . $client->trade_name,
        ]);

        // تحديث الخزينة
        $mainTreasuryAccount->balance += $data['amount'];
        $mainTreasuryAccount->save();

        // تحديث الفاتورة
        $invoice->advance_payment = $totalPrevious + $data['amount'];
        $invoice->payment_status = $payment_status;
        $invoice->is_paid = $payment_status === 1;
        $invoice->due_value = max(0, $invoice->grand_total - ($totalPrevious + $data['amount']));
        $invoice->save();

        // قيد اليومية
        $journal = JournalEntry::create([
            'reference_number' => $payment->reference_number ?? $invoice->code,
            'date' => $data['payment_date'] ?? now(),
            'description' => 'دفعة للفاتورة #' . $invoice->code,
            'status' => 1,
            'currency' => 'SAR',
            'client_id' => $invoice->client_id,
            'invoice_id' => $invoice->id,
        ]);

        JournalEntryDetail::create([
            'journal_entry_id' => $journal->id,
            'account_id' => $mainTreasuryAccount->id,
            'description' => 'استلام دفعة نقدية',
            'debit' => $data['amount'],
            'credit' => 0,
            'is_debit' => true,
        ]);

        $clientAccount = Account::where('client_id', $invoice->client_id)->first();
        JournalEntryDetail::create([
            'journal_entry_id' => $journal->id,
            'account_id' => $clientAccount->id,
            'description' => 'دفعة من العميل',
            'debit' => 0,
            'credit' => $data['amount'],
            'is_debit' => false,
        ]);

        $clientAccount?->decrement('balance', $data['amount']);

        DB::commit();

        return response()->json([
            'status' => true,
            'message' => 'تمت العملية بنجاح',
            'data' => $payment,
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'status' => false,
            'message' => 'خطأ: ' . $e->getMessage(),
        ], 500);
    }
}

public function show($id)
{
    try {
        $payment = PaymentsProcess::with([
            'invoice.client',
            'employee',
            'branch',
            'invoice.employee'
        ])->findOrFail($id);

        return $this->successResponse(
            new PaymentResource($payment),
            'تم جلب تفاصيل الدفع بنجاح'
        );
    } catch (\Exception $e) {
        return $this->errorResponse('فشل في جلب تفاصيل الدفع', 500, $e->getMessage());
    }
}

}



