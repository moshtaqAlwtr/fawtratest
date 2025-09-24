<?php

namespace Modules\Account\Http\Controllers\Cheques;
use App\Http\Controllers\Controller;
use App\Http\Requests\PayableChequeRequest;
use App\Models\ChequeBook;
use App\Models\PayableCheque;
use App\Models\Treasury;
use Illuminate\Http\Request;

class PayableChequesController extends Controller
{
    public function index()
    {
        $payable_cheques = PayableCheque::select()->orderBy('id', 'desc')->get();
        return view('account::cheques.payable_cheques.index', compact('payable_cheques'));
    }

    public function create()
    {
        $bank_accounts = Treasury::where('type', 1)->select('id', 'bank_name')->get();
        $check_books = ChequeBook::select()->orderBy('id', 'desc')->get();
        return view('account::cheques.payable_cheques.create', compact('bank_accounts','check_books'));
    }

    public function store(PayableChequeRequest $request)
    {
        $attachmentPath = null;

        if ($request->hasFile('attachment')) {
            $allowedExtensions = ['png', 'jpg', 'jpeg', 'gif', 'bmp', 'rar', 'zip', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'pdf'];
            $maxFileSize = 5 * 1024 * 1024;

            $file = $request->file('attachment');
            $extension = $file->getClientOriginalExtension();
            $fileSize = $file->getSize();

            if (!in_array(strtolower($extension), $allowedExtensions)) {
                return back()->withErrors(['attachment' => 'نوع الملف غير مدعوم.']);
            }

            if ($fileSize > $maxFileSize) {
                return back()->withErrors(['attachment' => 'حجم الملف يجب أن لا يتجاوز 5 ميجابايت.']);
            }

            $attachmentPath = $file->store('assets/uploads/cheques/attachments', 'public');
        }

        PayableCheque::create([
            'amount' => $request->amount,
            'cheque_number' => $request->cheque_number,
            'bank_id' => $request->bank_id,
            'cheque_book_id' => $request->cheque_book_id,
            'issue_date' => $request->issue_date,
            'due_date' => $request->due_date,
            'recipient_account_id' => $request->recipient_account_id,
            'attachment' => $attachmentPath,
            'description' => $request->description,
            'payee_name' => $request->payee_name,
        ]);

        return redirect()->route('payable_cheques.index')->with(['success' => 'تم إصدار الشيك بنجاح.']);
    }

    public function getCheckbooks($bankId)
    {
        // جلب دفاتر الشيكات المرتبطة بالبنك المحدد
        $chequeBooks = ChequeBook::where('bank_id', $bankId)->get(['id', 'cheque_book_number']);
        return response()->json($chequeBooks);
    }

    public function show($id)
    {
        $payable_cheque = PayableCheque::findOrFail($id);
        return view('account::cheques.payable_cheques.show', compact('payable_cheque'));
    }

}
