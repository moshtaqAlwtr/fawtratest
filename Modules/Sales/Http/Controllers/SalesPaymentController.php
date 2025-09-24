<?php

namespace Modules\Sales\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sales\ClientPaymentRequest;
use App\Models\Account;
use App\Models\Employee;
use App\Models\Installment;
use App\Models\Invoice;
use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;
use App\Models\PaymentMethod;
use App\Models\PaymentsProcess;
use App\Models\Treasury;
use App\Models\User;
use App\Models\AccountSetting;
use App\Models\Client;
use App\Models\notifications;
use App\Models\TreasuryEmployee;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SalesPaymentController extends Controller
{
    public function index(Request $request)
    {
        // استعلام أساسي لجميع البيانات
        $query = PaymentsProcess::with(['invoice', 'employee', 'client', 'treasury'])
            ->where('type', 'client payments');

        // إذا كان اليوزر الحالي لديه دور employee، نضيف شرط لعرض مدفوعاته فقط
        if (auth()->user()->role == 'employee') {
            $query->where('employee_id', auth()->user()->id);
        }

        // البحث الأساسي
        if ($request->has('invoice_number') && $request->invoice_number != '') {
            $query->whereHas('invoice', function ($q) use ($request) {
                $q->where('code', 'like', '%' . $request->invoice_number . '%');
            });
        }

        if ($request->has('payment_number') && $request->payment_number != '') {
            $query->where('payment_number', 'like', '%' . $request->payment_number . '%');
        }

        if ($request->has('customer') && $request->customer != '') {
            $query->where('employee_id', $request->customer);
        }

        // البحث المتقدم
        if ($request->has('payment_status') && $request->payment_status != '') {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->has('from_date') && $request->from_date != '') {
            $query->where('payment_date', '>=', $request->from_date);
        }

        if ($request->has('to_date') && $request->to_date != '') {
            $query->where('payment_date', '<=', $request->to_date);
        }

        if ($request->has('identifier') && $request->identifier != '') {
            $query->where('reference_number', 'like', '%' . $request->identifier . '%');
        }

        if ($request->has('transfer_id') && $request->transfer_id != '') {
            $query->where('reference_number', 'like', '%' . $request->transfer_id . '%');
        }

        if ($request->has('total_greater_than') && $request->total_greater_than != '') {
            $query->where('amount', '>', $request->total_greater_than);
        }

        if ($request->has('total_less_than') && $request->total_less_than != '') {
            $query->where('amount', '<', $request->total_less_than);
        }

        if ($request->has('custom_field') && $request->custom_field != '') {
            $query->where('notes', 'like', '%' . $request->custom_field . '%');
        }

        if ($request->has('invoice_origin') && $request->invoice_origin != '') {
            $query->whereHas('invoice', function ($q) use ($request) {
                $q->where('origin', $request->invoice_origin);
            });
        }

        if ($request->has('collected_by') && $request->collected_by != '') {
            $query->where('employee_id', $request->collected_by);
        }

        // تنفيذ الاستعلام مع Pagination لعرض 25 عنصر في الصفحة
        $payments = $query->orderBy('created_at', 'DESC')->paginate(25);
        $employees = Employee::all();
        $account_setting = AccountSetting::where('user_id', auth()->user()->id)->first();
        return view('sales::payment.index', compact('payments', 'employees', 'account_setting'));
    }

    public function create($id, $type = 'invoice')
    {
        // $type يحدد إذا كان الدفع لفاتورة أو قسط
        if ($type === 'installment') {
            $installment = Installment::with('invoice')->findOrFail($id);
            $amount = $installment->amount;
            $invoiceId = $installment->invoice->id;
        } else {
            $invoice = Invoice::findOrFail($id);
            $amount = $invoice->due_value;
            $invoiceId = $invoice->id;
        }

        $treasury = Treasury::all();
        $user = User::find(auth()->user()->id);

        if ($user->role != 'manager') {
            $employees = Employee::where('id', $user->employee_id)->get();
        } else {
            $employees = Employee::all();
        }

        $payments = PaymentMethod::where('type', 'normal')->where('status', 'active')->get();

        // تحديد الخزينة بنفس طريقة سندات القبض
        $mainTreasuryAccount = null;
        $treasury_id = null;
        $user = auth()->user();

        if ($user && $user->employee_id) {
            $treasuryEmployee = TreasuryEmployee::where('employee_id', $user->employee_id)->first();

            if ($treasuryEmployee && $treasuryEmployee->treasury_id) {
                $mainTreasuryAccount = Account::where('id', $treasuryEmployee->treasury_id)->first();
                $treasury_id = $treasuryEmployee->treasury_id;
            } else {
                $mainTreasuryAccount = Account::where('name', 'الخزينة الرئيسية')->first();
                $treasury_id = $mainTreasuryAccount->id ?? null;
            }
        } else {
            $mainTreasuryAccount = Account::where('name', 'الخزينة الرئيسية')->first();
            $treasury_id = $mainTreasuryAccount->id ?? null;
        }

        if (!$mainTreasuryAccount) {
            throw new \Exception('لا توجد خزينة متاحة. يرجى التحقق من إعدادات الخزينة.');
        }

        return view('sales::payment.create', compact(
            'invoiceId', 'payments', 'amount', 'treasury', 'employees',
            'type', 'mainTreasuryAccount', 'user', 'treasury_id'
        ));
    }

    public function store(ClientPaymentRequest $request)
    {
        try {
            DB::beginTransaction();

            // استرجاع البيانات المصادق عليها
            $data = $request->validated();

            // التحقق من وجود الفاتورة وجلب تفاصيلها
            $invoice = Invoice::findOrFail($data['invoice_id']);

            // حساب إجمالي المدفوعات السابقة
            $totalPreviousPayments = PaymentsProcess::where('invoice_id', $invoice->id)
                ->where('type', 'client payments')
                ->where('payment_status', '!=', 5) // استثناء المدفوعات الفاشلة
                ->sum('amount');

            // حساب المبلغ المتبقي للدفع
            $remainingAmount = $invoice->grand_total - $totalPreviousPayments;

            // التحقق من أن مبلغ الدفع لا يتجاوز المبلغ المتبقي
            if (round($data['amount'], 2) - round($remainingAmount, 2) > 0.01) {
                return back()
                    ->with('error', 'مبلغ الدفع يتجاوز المبلغ المتبقي للفاتورة. المبلغ المتبقي هو: ' . number_format($remainingAmount, 2))
                    ->withInput();
            }

            // تعيين حالة الدفع الافتراضية كمسودة
            $payment_status = 3; // مسودة

            // تحديد حالة الدفع بناءً على المبلغ المدفوع والمبلغ المتبقي
            $newTotalPayments = $totalPreviousPayments + $data['amount'];

            if ($newTotalPayments >= $invoice->grand_total) {
                $payment_status = 1; // مكتمل
                $invoice->is_paid = true;
                $invoice->due_value = 0;
            } else {
                $payment_status = 2; // غير مكتمل
                $invoice->is_paid = false;
                $invoice->due_value = $invoice->grand_total - $newTotalPayments;
            }

            // إذا تم تحديد حالة دفع معينة في الطلب
            if ($request->has('payment_status')) {
                switch ($request->payment_status) {
                    case 4: // تحت المراجعة
                        $payment_status = 4;
                        $invoice->is_paid = false;
                        break;
                    case 5: // فاشلة
                        $payment_status = 5;
                        $invoice->is_paid = false;
                        // إعادة حساب المبلغ المتبقي بدون احتساب هذه الدفعة
                        $invoice->due_value = $invoice->grand_total - $totalPreviousPayments;
                        break;
                }
            }

            // إضافة البيانات الإضافية للدفعة
            $data['type'] = 'client payments';
            $data['created_by'] = Auth::id();
            $data['payment_status'] = $payment_status;

            // معالجة المرفقات
            if ($request->hasFile('attachments')) {
                $file = $request->file('attachments');
                if ($file->isValid()) {
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('assets/uploads/'), $filename);
                    $data['attachments'] = $filename;
                }
            }

            // تحديد الخزينة المستهدفة بناءً على الموظف
            $mainTreasuryAccount = null;
            $user = Auth::user();

            if ($user && $user->employee_id) {
                // البحث عن الخزينة المرتبطة بالموظف
                $treasuryEmployee = TreasuryEmployee::where('employee_id', $user->employee_id)->first();

                if ($treasuryEmployee && $treasuryEmployee->treasury_id) {
                    // إذا كان الموظف لديه خزينة مرتبطة
                    $mainTreasuryAccount = Account::where('id', $treasuryEmployee->treasury_id)->first();
                } else {
                    // إذا لم يكن لدى الموظف خزينة مرتبطة، استخدم الخزينة الرئيسية
                    $mainTreasuryAccount = Account::where('name', 'الخزينة الرئيسية')->first();
                }
            } else {
                // إذا لم يكن المستخدم موجودًا أو لم يكن لديه employee_id، استخدم الخزينة الرئيسية
                $mainTreasuryAccount = Account::where('name', 'الخزينة الرئيسية')->first();
            }

            // إذا لم يتم العثور على خزينة، توقف العملية وأظهر خطأ
            if (!$mainTreasuryAccount) {
                throw new \Exception('لا توجد خزينة متاحة. يرجى التحقق من إعدادات الخزينة.');
            }

            // إنشاء سجل الدفع
            $data['employee_id'] = auth()->user()->id;

            $payment = PaymentsProcess::create($data);

            $client = Client::find($invoice->client_id);
            $user = auth()->user();

            notifications::create([
                'type' => 'invoice_payment',
                'title' => $user->name . ' أنشأ عملية دفع',
                'description' => 'عملية دفع للفاتورة رقم ' . $invoice->id . ' للعميل ' . $client->trade_name . ' بقيمة ' . number_format($payment->amount, 2) . ' ر.س',
            ]);

            // تحديث رصيد الخزينة
            $mainTreasuryAccount->balance += $data['amount'];
            $mainTreasuryAccount->save();

            // تحديث المبلغ المدفوع في الفاتورة
            $invoice->advance_payment = $newTotalPayments;
            $invoice->payment_status = $payment_status;
            $invoice->save();

            // إنشاء قيد محاسبي للدفعة
            $journalEntry = JournalEntry::create([
                'reference_number' => $payment->reference_number ?? $invoice->code,
                'date' => $data['payment_date'] ?? now(),
                'description' => 'دفعة للفاتورة رقم ' . $invoice->code,
                'status' => 1,
                'currency' => 'SAR',
                'client_id' => $invoice->client_id,
                'invoice_id' => $invoice->id,
            ]);

            // إضافة تفاصيل القيد المحاسبي للدفعة
            // 1. حساب الصندوق/البنك (مدين)
            JournalEntryDetail::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $mainTreasuryAccount->id,
                'description' => 'استلام دفعة نقدية',
                'debit' => $data['amount'],
                'credit' => 0,
                'is_debit' => true,
            ]);

            // 2. حساب العميل (دائن)
            $clientaccounts = Account::where('client_id', $invoice->client_id)->first();
            JournalEntryDetail::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $clientaccounts->id,
                'description' => 'دفعة من العميل',
                'debit' => 0,
                'credit' => $data['amount'],
                'is_debit' => false,
            ]);

            if ($clientaccounts) {
                $clientaccounts->balance -= $data['amount'];
                $clientaccounts->save();
            }

            DB::commit();

            // إعداد رسالة النجاح مع حالة الدفع
            $paymentStatusText = match ($payment_status) {
                1 => 'مكتمل',
                2 => 'غير مكتمل',
                3 => 'مسودة',
                4 => 'تحت المراجعة',
                5 => 'فاشلة',
                default => 'غير معروف',
            };

            $successMessage = sprintf('تم تسجيل عملية الدفع بنجاح. المبلغ المدفوع: %s، المبلغ المتبقي: %s - حالة الدفع: %s', number_format($data['amount'], 2), number_format($invoice->due_value, 2), $paymentStatusText);

            return redirect()->route('paymentsClient.index')->with('success', $successMessage);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('خطأ في تسجيل عملية الدفع: ' . $e->getMessage());
            return back()
                ->with('error', 'حدث خطأ أثناء تسجيل عملية الدفع: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show($id)
    {
        $payment = PaymentsProcess::with(['invoice.client', 'invoice.payments_process', 'employee'])->findOrFail($id);
        $employees = Employee::all();

        $account_setting = AccountSetting::where('user_id', auth()->user()->id)->first();
        return view('sales::payment.show', compact('payment', 'employees', 'account_setting'));
    }

    public function edit($id)
    {
        $payment = PaymentsProcess::with(['invoice', 'employee'])->findOrFail($id);
        $employees = Employee::all();
        return view('sales::payment.edit', compact('payment', 'employees', 'id'));
    }

    public function update(ClientPaymentRequest $request, PaymentsProcess $payment)
    {
        try {
            DB::beginTransaction();

            // استرجاع البيانات المصادق عليها
            $data = $request->validated();

            // التحقق من وجود الفاتورة وجلب تفاصيلها
            $invoice = Invoice::findOrFail($data['invoice_id']);

            // حساب إجمالي المدفوعات السابقة (باستثناء الدفعة الحالية)
            $totalPreviousPayments = PaymentsProcess::where('invoice_id', $invoice->id)
                ->where('type', 'client payments')
                ->where('payment_status', '!=', 5) // استثناء المدفوعات الفاشلة
                ->where('id', '!=', $payment->id) // استثناء الدفعة الحالية
                ->sum('amount');

            // حساب المبلغ المتبقي للدفع
            $remainingAmount = $invoice->grand_total - $totalPreviousPayments;

            // التحقق من أن مبلغ الدفع لا يتجاوز المبلغ المتبقي
            if ($data['amount'] > $remainingAmount) {
                return back()
                    ->with('error', 'مبلغ الدفع يتجاوز المبلغ المتبقي للفاتورة. المبلغ المتبقي هو: ' . number_format($remainingAmount, 2))
                    ->withInput();
            }

            // تعيين حالة الدفع الافتراضية كمسودة
            $payment_status = 3; // مسودة

            // تحديد حالة الدفع بناءً على المبلغ المدفوع والمبلغ المتبقي
            $newTotalPayments = $totalPreviousPayments + $data['amount'];

            if ($newTotalPayments >= $invoice->grand_total) {
                $payment_status = 1; // مكتمل
                $invoice->is_paid = true;
                $invoice->due_value = 0;
            } else {
                $payment_status = 2; // غير مكتمل
                $invoice->is_paid = false;
                $invoice->due_value = $invoice->grand_total - $newTotalPayments;
            }

            // إذا تم تحديد حالة دفع معينة في الطلب
            if ($request->has('payment_status')) {
                switch ($request->payment_status) {
                    case 4: // تحت المراجعة
                        $payment_status = 4;
                        $invoice->is_paid = false;
                        break;
                    case 5: // فاشلة
                        $payment_status = 5;
                        $invoice->is_paid = false;
                        // إعادة حساب المبلغ المتبقي بدون احتساب هذه الدفعة
                        $invoice->due_value = $invoice->grand_total - $totalPreviousPayments;
                        break;
                }
            }

            // إضافة البيانات الإضافية للدفعة
            $data['type'] = 'client payments';
            $data['created_by'] = Auth::id();
            $data['payment_status'] = $payment_status;

            // معالجة المرفقات
            if ($request->hasFile('attachments')) {
                $file = $request->file('attachments');
                if ($file->isValid()) {
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('assets/uploads/'), $filename);
                    $data['attachments'] = $filename;
                }
            }

            // تحديث سجل الدفع
            $payment->update($data);

            // تحديث المبلغ المدفوع في الفاتورة
            $invoice->advance_payment = $newTotalPayments;
            $invoice->payment_status = $payment_status;
            $invoice->save();

            // تحديث القيد المحاسبي للدفعة
            $journalEntry = JournalEntry::where('invoice_id', $invoice->id)
                ->where('reference_number', $payment->reference_number ?? $invoice->code)
                ->first();

            if ($journalEntry) {
                $journalEntry->update([
                    'date' => $data['payment_date'] ?? now(),
                    'description' => 'تحديث دفعة للفاتورة رقم ' . $invoice->code,
                ]);

                // تحديث تفاصيل القيد المحاسبي للدفعة
                // 1. حساب الصندوق/البنك (مدين)
                JournalEntryDetail::where('journal_entry_id', $journalEntry->id)
                    ->where('is_debit', true)
                    ->update([
                        'account_id' => $data['payment_account_id'] ?? $this->getAccountId('cash'),
                        'description' => 'تحديث استلام دفعة نقدية',
                        'debit' => $data['amount'],
                        'credit' => 0,
                    ]);

                // 2. حساب العميل (دائن)
                JournalEntryDetail::where('journal_entry_id', $journalEntry->id)
                    ->where('is_debit', false)
                    ->update([
                        'account_id' => $invoice->client->account_id,
                        'description' => 'تحديث دفعة من العميل',
                        'debit' => 0,
                        'credit' => $data['amount'],
                    ]);
            }

            DB::commit();

            // إعداد رسالة النجاح مع حالة الدفع
            $paymentStatusText = match ($payment_status) {
                1 => 'مكتمل',
                2 => 'غير مكتمل',
                3 => 'مسودة',
                4 => 'تحت المراجعة',
                5 => 'فاشلة',
                default => 'غير معروف',
            };

            $successMessage = sprintf('تم تحديث عملية الدفع بنجاح. المبلغ المدفوع: %s، المبلغ المتبقي: %s - حالة الدفع: %s', number_format($data['amount'], 2), number_format($invoice->due_value, 2), $paymentStatusText);

            return redirect()->route('paymentsClient.index')->with('success', $successMessage);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('خطأ في تحديث عملية الدفع: ' . $e->getMessage());
            return back()
                ->with('error', 'حدث خطأ أثناء تحديث عملية الدفع: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        PaymentsProcess::destroy($id);
        return redirect()->route('paymentsClient.index')->with('success', 'تم حذف عملية الدفع بنجاح');
    }

    public function rereceipt($id)
    {
        $receipt = PaymentsProcess::findOrFail($id);
        $type = request()->query('type', 'a4'); // افتراضي A4 إذا لم يتم تحديد النوع

        return view('sales::payment.receipt.index_repeat', [
            'receipt' => $receipt,
            'receiptType' => $type,
        ]);
    }

    public function pdfReceipt($id)
    {
        $receipt = PaymentsProcess::findOrFail($id);

        $pdf = Pdf::loadView('sales::payment.receipt.pdf_receipt', compact('receipt'));

        return $pdf->stream('receipt_' . $id . '.pdf');
    }

    public function cancel($id)
    {
        try {
            DB::beginTransaction();

            $user = Auth::user();
            $payment = PaymentsProcess::findOrFail($id);

            if ($payment->type !== 'client payments') {
                return back()->with('error', 'لا يمكن إلغاء هذا النوع من العمليات');
            }

            $invoice = Invoice::findOrFail($payment->invoice_id);
            $client = Client::find($invoice->client_id);

            // استرجاع رصيد الخزينة
            $mainTreasuryAccount = null;
            if ($user && $user->employee_id) {
                $treasuryEmployee = TreasuryEmployee::where('employee_id', $user->employee_id)->first();
                if ($treasuryEmployee && $treasuryEmployee->treasury_id) {
                    $mainTreasuryAccount = Account::find($treasuryEmployee->treasury_id);
                }
            }
            $mainTreasuryAccount = $mainTreasuryAccount ?? Account::where('name', 'الخزينة الرئيسية')->first();

            if ($mainTreasuryAccount) {
                $mainTreasuryAccount->balance -= $payment->amount;
                $mainTreasuryAccount->save();
            }

            // استرجاع رصيد العميل
            $clientAccount = Account::where('client_id', $invoice->client_id)->first();
            if ($clientAccount) {
                $clientAccount->balance += $payment->amount;
                $clientAccount->save();
            }

            // تحديث بيانات الفاتورة
            $totalPreviousPayments = PaymentsProcess::where('invoice_id', $invoice->id)->where('type', 'client payments')->where('payment_status', '!=', 5)->where('id', '!=', $payment->id)->sum('amount');

            $invoice->advance_payment = $totalPreviousPayments;
            $invoice->due_value = $invoice->grand_total - $totalPreviousPayments;

            if ($totalPreviousPayments == 0) {
                $invoice->is_paid = false;
                $invoice->payment_status = 3;
            } elseif ($totalPreviousPayments >= $invoice->grand_total) {
                $invoice->is_paid = true;
                $invoice->payment_status = 1;
                $invoice->due_value = 0;
            } else {
                $invoice->is_paid = false;
                $invoice->payment_status = 2;
            }
            $invoice->save();

            // حذف القيد المحاسبي المرتبط بالدفع
            $referenceNumber = $payment->reference_number ?? $invoice->code;

            $journalEntry = JournalEntry::with('details')
                ->where('invoice_id', $invoice->id)
                ->where(function ($query) use ($referenceNumber, $invoice) {
                    $query->where('reference_number', $referenceNumber)->orWhere('description', 'like', '%دفعة للفاتورة رقم ' . $invoice->code . '%');
                })
                ->first();

            if (!$journalEntry) {
                DB::rollBack();
                return back()->with('error', 'لم يتم العثور على القيد المحاسبي المرتبط بهذه الدفعة، لذا لا يمكن إلغاء الدفعة.');
            }

            // حذف تفاصيل القيد
            JournalEntryDetail::where('journal_entry_id', $journalEntry->id)->delete();

            // حذف القيد الرئيسي
            $journalEntry->delete();

            // حذف الإشعارات القديمة
            notifications::where('type', 'invoice_payment')
                ->where('description', 'like', '%عملية دفع للفاتورة رقم ' . $invoice->id . '%')
                ->delete();

            // إنشاء إشعار إلغاء
            notifications::create([
                'type' => 'invoice_payment_cancelled',
                'title' => $user->name . ' ألغى عملية دفع',
                'description' => 'تم إلغاء عملية دفع للفاتورة رقم ' . $invoice->id . ' للعميل ' . ($client->trade_name ?? 'غير محدد') . ' بقيمة ' . number_format($payment->amount, 2) . ' ر.س',
            ]);

            // حذف المرفقات
            if ($payment->attachments && file_exists(public_path('assets/uploads/' . $payment->attachments))) {
                unlink(public_path('assets/uploads/' . $payment->attachments));
            }

            // حذف عملية الدفع
            $payment->delete();

            DB::commit();

            return redirect()
                ->route('paymentsClient.index')
                ->with('success', sprintf('تم إلغاء عملية الدفع والقيد المحاسبي بنجاح. تم استرداد مبلغ %s ر.س، المبلغ المستحق للفاتورة الآن: %s ر.س', number_format($payment->amount, 2), number_format($invoice->due_value, 2)));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('خطأ في إلغاء الدفع أو القيد: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء إلغاء الدفع أو القيد: ' . $e->getMessage());
        }
    }

    //   public function cancel($id)
    // {
    //     try {
    //         DB::beginTransaction();

    //         $user = Auth::user();
    //         $payment = PaymentsProcess::findOrFail($id);

    //         if ($payment->type !== 'client payments') {
    //             return back()->with('error', 'لا يمكن إلغاء هذا النوع من العمليات');
    //         }

    //         $invoice = Invoice::findOrFail($payment->invoice_id);
    //         $client = Client::find($invoice->client_id);

    //         // استرجاع رصيد الخزينة
    //         $mainTreasuryAccount = null;
    //         if ($user && $user->employee_id) {
    //             $treasuryEmployee = TreasuryEmployee::where('employee_id', $user->employee_id)->first();
    //             if ($treasuryEmployee && $treasuryEmployee->treasury_id) {
    //                 $mainTreasuryAccount = Account::find($treasuryEmployee->treasury_id);
    //             }
    //         }
    //         $mainTreasuryAccount = $mainTreasuryAccount ?? Account::where('name', 'الخزينة الرئيسية')->first();

    //         if ($mainTreasuryAccount) {
    //             $mainTreasuryAccount->balance -= $payment->amount;
    //             $mainTreasuryAccount->save();
    //         }

    //         // استرجاع رصيد العميل
    //         $clientAccount = Account::where('client_id', $invoice->client_id)->first();
    //         if ($clientAccount) {
    //             $clientAccount->balance += $payment->amount;
    //             $clientAccount->save();
    //         }

    //         // تحديث بيانات الفاتورة
    //         $totalPreviousPayments = PaymentsProcess::where('invoice_id', $invoice->id)
    //             ->where('type', 'client payments')
    //             ->where('payment_status', '!=', 5)
    //             ->where('id', '!=', $payment->id)
    //             ->sum('amount');

    //         $invoice->advance_payment = $totalPreviousPayments;
    //         $invoice->due_value = $invoice->grand_total - $totalPreviousPayments;

    //         if ($totalPreviousPayments == 0) {
    //             $invoice->is_paid = false;
    //             $invoice->payment_status = 3;
    //         } elseif ($totalPreviousPayments >= $invoice->grand_total) {
    //             $invoice->is_paid = true;
    //             $invoice->payment_status = 1;
    //             $invoice->due_value = 0;
    //         } else {
    //             $invoice->is_paid = false;
    //             $invoice->payment_status = 2;
    //         }
    //         $invoice->save();

    //         // البحث عن القيد المحاسبي الأصلي
    //         $referenceNumber = $payment->reference_number ?? $invoice->code;
    //         $originalJournalEntry = JournalEntry::with('details')
    //             ->where('invoice_id', $invoice->id)
    //             ->where(function ($query) use ($referenceNumber, $invoice) {
    //                 $query->where('reference_number', $referenceNumber)
    //                       ->orWhere('description', 'like', '%دفعة للفاتورة رقم ' . $invoice->code . '%');
    //             })
    //             ->first();

    //         if (!$originalJournalEntry) {
    //             DB::rollBack();
    //             return back()->with('error', 'لم يتم العثور على القيد المحاسبي المرتبط بهذه الدفعة، لذا لا يمكن إلغاء الدفعة.');
    //         }

    //         // إنشاء قيد عكسي بدلاً من حذف القيد الأصلي
    //         $reversalJournalEntry = JournalEntry::create([
    //             'reference_number' => 'REV-' . $originalJournalEntry->reference_number,
    //             'date' => now(),
    //             'description' => 'قيد عكسي لإلغاء دفعة للفاتورة رقم ' . $invoice->code . ' (إلغاء القيد رقم: ' . $originalJournalEntry->id . ')',
    //             'status' => 1,
    //             'currency' => 'SAR',
    //             'client_id' => $invoice->client_id,
    //             'invoice_id' => $invoice->id,
    //             'reference_number' => $originalJournalEntry->id, // ربط بالقيد الأصلي
    //         ]);

    //         // إنشاء تفاصيل القيد العكسي (عكس القيد الأصلي)
    //         foreach ($originalJournalEntry->details as $originalDetail) {
    //             JournalEntryDetail::create([
    //                 'journal_entry_id' => $reversalJournalEntry->id,
    //                 'account_id' => $originalDetail->account_id,
    //                 'description' => 'قيد عكسي - ' . $originalDetail->description,
    //                 'debit' => $originalDetail->credit, // عكس المبالغ
    //                 'credit' => $originalDetail->debit, // عكس المبالغ
    //                 'is_debit' => !$originalDetail->is_debit, // عكس نوع القيد
    //                 'client_account_id' => $originalDetail->client_account_id,
    //             ]);
    //         }

    //         // تحديث حالة القيد الأصلي لتوضيح أنه تم إلغاؤه
    //         $originalJournalEntry->update([
    //             'status' => 2, // حالة ملغي أو معدل
    //             'description' => $originalJournalEntry->description . ' (تم إلغاؤه بالقيد العكسي رقم: ' . $reversalJournalEntry->id . ' للدفعة رقم: ' . $payment->id . ')'
    //         ]);

    //         // حذف الإشعارات القديمة
    //         notifications::where('type', 'invoice_payment')
    //             ->where('description', 'like', '%عملية دفع للفاتورة رقم ' . $invoice->id . '%')
    //             ->delete();

    //         // إنشاء إشعار إلغاء
    //         notifications::create([
    //             'type' => 'invoice_payment_cancelled',
    //             'title' => $user->name . ' ألغى عملية دفع',
    //             'description' => 'تم إلغاء عملية دفع للفاتورة رقم ' . $invoice->id . ' للعميل ' . ($client->trade_name ?? 'غير محدد') . ' بقيمة ' . number_format($payment->amount, 2) . ' ر.س وتم إنشاء قيد عكسي رقم ' . $reversalJournalEntry->id,
    //         ]);

    //         // حذف المرفقات
    //         if ($payment->attachments && file_exists(public_path('assets/uploads/' . $payment->attachments))) {
    //             unlink(public_path('assets/uploads/' . $payment->attachments));
    //         }

    //         // تحديث حالة الدفعة بدلاً من حذفها (للاحتفاظ بالسجل)
    //         $payment->update([
    //             'payment_status' => 5, // حالة ملغية
    //             'notes' => ($payment->notes ?? '') . ' - تم إلغاء هذه الدفعة في ' . now()->format('Y-m-d H:i:s') . ' بواسطة ' . $user->name,
    //             'cancelled_at' => now(),
    //             'cancelled_by' => $user->id,
    //         ]);

    //         DB::commit();

    //         return redirect()
    //             ->route('paymentsClient.index')
    //             ->with('success', sprintf(
    //                 'تم إلغاء عملية الدفع بنجاح وإنشاء قيد عكسي رقم %d. تم استرداد مبلغ %s ر.س، المبلغ المستحق للفاتورة الآن: %s ر.س',
    //                 $reversalJournalEntry->id,
    //                 number_format($payment->amount, 2),
    //                 number_format($invoice->due_value, 2)
    //             ));

    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error('خطأ في إلغاء الدفع: ' . $e->getMessage());
    //         return back()->with('error', 'حدث خطأ أثناء إلغاء الدفع: ' . $e->getMessage());
    //     }
    // }
}
