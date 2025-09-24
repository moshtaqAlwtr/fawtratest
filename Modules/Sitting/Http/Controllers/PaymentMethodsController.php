<?php

namespace App\Http\Controllers\Sitting;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use App\Models\Log as ModelsLog;

class PaymentMethodsController extends Controller
{
    public function index()
    {
        $payments =  PaymentMethod::where('type','normal')->get();
        $payments_electronics =  PaymentMethod::where('type','electronic')->get();
        return view('sitting::paymentMethod.index',compact('payments','payments_electronics'));
    }

    public function store(Request $request)
    {

        $payments =  new PaymentMethod();
        $payments->name        = $request->name;
        $payments->description = $request->description;
        $payments->status      = $request->status;
        $payments->is_online   = $request->is_online;
        $payments->type        = $request->type;
        $payments->save();

           // تسجيل اشعار نظام جديد
            ModelsLog::create([
                'type' => 'setting',
                'type_id' => $payments->id, // ID النشاط المرتبط
                'type_log' => 'log', // نوع النشاط
                'description' => 'تم اضافة طريقة دفع  جديد **' . $payments->name . '**',
                'created_by' => auth()->id(), // ID المستخدم الحالي
            ]);

        return redirect()->route('PaymentMethods.index')->with('success', 'تم حفظ البيانات بنجاح!');

    }
    public function updatePaymentStatus(Request $request)
    {


        foreach ($request->payments as $id => $data) {
            $payment = PaymentMethod::find($id);
            if ($payment) {
                // التأكد من أن القيمة ليست فارغة أو NULL
                $payment->status = !empty($data['status']) ? $data['status'] : 'inactive';
                $payment->save();
            }
        }





        return redirect()->back()->with('success', 'تم تحديث الحالات بنجاح');
    }

    public function create()
    {

        return view('sitting::paymentMethod.create');
    }
}
