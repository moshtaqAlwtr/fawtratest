<?php

namespace Modules\Account\Http\Controllers\Cheques;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChequeBookRequest;
use Illuminate\Http\Request;
use App\Models\ChequeBook;
use App\Models\Treasury;

class CheckBooksController extends Controller
{
    public function index()
    {
        $check_books = ChequeBook::select()->orderBy('id', 'desc')->get();
        return view('account::cheques.check_books.index', compact('check_books'));
    }

    public function create()
    {
        $bank_accounts = Treasury::where('type', 1)->select('id', 'bank_name')->get();
        return view('account::cheques.check_books.create', compact('bank_accounts'));
    }

    public function store(ChequeBookRequest $request)
    {
        # Number of checks = (end serial number - start serial number) + 1
        $cheque_book = new ChequeBook();
        $cheque_book->bank_id = $request->bank_id;
        $cheque_book->cheque_book_number = $request->cheque_book_number;
        $cheque_book->currency = $request->currency;
        $cheque_book->start_serial_number = $request->start_serial_number;
        $cheque_book->end_serial_number = $request->end_serial_number;
        $cheque_book->status = $request->status;
        $cheque_book->notes = $request->notes;

        $cheque_book->save();

        return redirect()->route('check_books.index')->with(['success' => 'تم اضافة دفتر الشيكات بنجاح']);
    }

    public function edit($id)
    {
        $bank_accounts = Treasury::where('type', 1)->select('id', 'bank_name')->get();
        $checkbook = ChequeBook::findOrFail($id);
        return view('account::cheques.check_books.edit', compact('checkbook', 'bank_accounts'));
    }

    public function update(ChequeBookRequest $request, $id)
    {
        $cheque_book = ChequeBook::findOrFail($id);
        $cheque_book->bank_id = $request->bank_id;
        $cheque_book->cheque_book_number = $request->cheque_book_number;
        $cheque_book->currency = $request->currency;
        $cheque_book->start_serial_number = $request->start_serial_number;
        $cheque_book->end_serial_number = $request->end_serial_number;
        $cheque_book->status = $request->status;
        $cheque_book->notes = $request->notes;

        $cheque_book->update();

        return redirect()->route('check_books.index')->with(['success' => 'تم تعديل دفتر الشيكات بنجاح']);
    }

    public function delete($id)
    {
        $cheque_book = ChequeBook::findOrFail($id);
        if ($cheque_book->cheques()->count() > 0) {
            return redirect()->route('check_books.index')->with( ['error'=>'لا يمكن حذف دفتر الشيكات لانه مستخدم بالفعل !!']);
        }
        $cheque_book->delete();
        return redirect()->route('check_books.index')->with( ['error'=>'تم حذف دفتر الشيكات بنجاج !!']);
    }

}
