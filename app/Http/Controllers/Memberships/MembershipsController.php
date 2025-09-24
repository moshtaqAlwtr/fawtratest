<?php

namespace App\Http\Controllers\Memberships;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Client;
use App\Models\Employee;
use App\Models\Log as ModelsLog;
use App\Models\Installment;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;
use App\Models\Memberships;
use App\Models\MembershipsSetthing;
use App\Models\Package;
use App\Models\PaymentsProcess;
use App\Models\Product;
use App\Models\StoreHouse;
use App\Models\Subscriptions;
use App\Models\Treasury;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MembershipsController extends Controller
{
    public function index()
    {
        $packages	= Package::all();
        $memberships = Memberships::all();
        return view('memberships.mang_memberships.index',compact('memberships'));
    }

    public function subscriptions()
    {
        $packages = Package::all();
        $memberships = Subscriptions::whereHas('invoice', function ($query) {
            $query->where('is_paid', 1);
        })->get();
        
        return view('memberships.mang_memberships.subscriptions', compact('memberships', 'packages'));
        
    }
    public function create()
    {
        $clients = Client::all();
        $packages	= Package::all();
        return view('memberships.mang_memberships.create',compact('clients','packages'));
    }
    public function store(Request $request)
    {
        // تعريف القواعد والرسائل
        $rules = [
            'client_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    // تحقق إذا كان العميل مسجلاً بالفعل في عضوية أخرى
                    if (Memberships::where('client_id', $value)->exists()) {
                        $fail('هذا العميل مسجل بالفعل في عضوية أخرى.');
                    }
                }
            ],
            'package_id' => 'required',
            'join_date' => 'required|date',
            'end_date' => 'required|date',
            'description' => 'nullable|string',
        ];
    
        $messages = [
            'client_id.required' => 'حقل العميل مطلوب.',
            'package_id.required' => 'حقل الباقة مطلوب.',
            'join_date.required' => 'حقل تاريخ الالتحاق مطلوب.',
            'join_date.date' => 'تاريخ الالتحاق يجب أن يكون تاريخًا صالحًا.',
            'end_date.required' => 'حقل تاريخ الفاتورة مطلوب.',
            'end_date.date' => 'تاريخ الفاتورة يجب أن يكون تاريخًا صالحًا.',
            'description.string' => 'الوصف يجب أن يكون نصًا.',
        ];
    
        // تنفيذ الفاليديشن
        $validatedData = $request->validate($rules, $messages);
    
        // إنشاء العضوية
      $Memberships =  Memberships::create($validatedData);
    ModelsLog::create([
                'type' => 'loyaltyRule',
                'type_id' => $Memberships->id, // ID النشاط المرتبط
                'type_log' => 'log', // نوع النشاط
             'description' => 'تم اضافة عضوية    جديدة',
                'created_by' => auth()->id(), // ID المستخدم الحالي
            ]);
      
        //انشاء الفاتورة : 
            // ** الخطوة الأولى: إنشاء كود للفاتورة **
            $code = $request->code;
            if (!$code) {
                $lastOrder = Invoice::orderBy('id', 'desc')->first();
                $nextNumber = $lastOrder ? intval($lastOrder->code) + 1 : 1;
                // التحقق من أن الرقم فريد
                while (Invoice::where('code', str_pad($nextNumber, 5, '0', STR_PAD_LEFT))->exists()) {
                    $nextNumber++;
                }
                $code = str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
            } else {
                $existingCode = Invoice::where('code', $request->code)->exists();
                if ($existingCode) {
                    return redirect()->back()->withInput()->with('error', 'رقم الفاتورة موجود مسبقاً، الرجاء استخدام رقم آخر');
                }
            }
            DB::beginTransaction(); // بدء المعاملة

            // ** تجهيز المتغيرات الرئيسية لحساب الفاتورة **
            $total_amount = 0; // إجمالي المبلغ قبل الخصومات
            $total_discount = 0; // إجمالي الخصومات على البنود
            $items_data = []; // تجميع بيانات البنود

            // ** الخطوة الثانية: معالجة البنود (items) **

                    // حساب تفاصيل الكمية والأسعار
                    $quantity = floatval(1.0);
                    $unit_price = floatval($Memberships->packege->price);
                    $item_total = $quantity * $unit_price;


                    // تحديث الإجماليات
                    $total_amount += $item_total;
                    $total_discount += 0;
                    $type = "package";
                    // تجهيز بيانات البند
                    // $items_data[] = [
                    //     'invoice_id' => null, // سيتم تعيينه لاحقًا بعد إنشاء الفاتورة
                    //     'packege_id' => $Memberships->packege_id,
                    //     'store_house_id' => $store_house_id,
                    //     'item' => $Memberships->packege->commission_name ?? 'الباقة',
                    //     'description' => "" ?? null,
                    //     'quantity' => $quantity,
                    //     'unit_price' => $unit_price,
                    //     'discount' => $item_discount,
                    //     'type'     => $type,
                    //     'discount_type' => isset($item['discount_type']) && $item['discount_type'] === 'percentage' ? 2 : 1,
                    //     'tax_1' => floatval($item['tax_1'] ?? 0),
                    //     'tax_2' => floatval($item['tax_2'] ?? 0),
                    //     'total' => $item_total - $item_discount,
                    // ];
                
            

        
            // الخصومات الإجمالية
            $final_total_discount = $total_discount;

            // حساب المبلغ بعد الخصم
            $amount_after_discount = $Memberships->packege->price;

           $tax_total =  $Memberships->packege->price * 0.15;



            // ** الحساب النهائي للمجموع الكلي **
            $total_with_tax = $amount_after_discount + $tax_total;

           

            // ** تحديد حالة الفاتورة بناءً على المدفوعات **
            $payment_status = 3; // الحالة الافتراضية (مسودة)
            $is_paid = false;

           

            // إذا تم تحديد حالة دفع معينة في الطلب
          
            // ** الخطوة الرابعة: إنشاء الفاتورة في قاعدة البيانات **
          
            $invoice = Invoice::create([
                'client_id' => $Memberships->client_id,
              
                'due_value' => $total_with_tax,
                'code' => $code,
                'type' => 'normal',
                'invoice_date' => Carbon::now()->toDateString(),
                'issue_date' => Carbon::now()->toDateString(),
                'terms' => $request->terms ?? 0,
                'notes' => $request->notes ?? "",
                'payment_status' => $payment_status,
                'is_paid' => $is_paid,
                'created_by' => auth()->user()->id,
                'account_id' => 1,
                'discount_amount' => 0,
                'discount_type' => $request->has('discount_type') ? ($request->discount_type === 'percentage' ? 2 : 1) : 1,
                'advance_payment' => 0,
                'payment_type' => $request->payment_type ?? 1,
                'shipping_cost' => 0,
                'shipping_tax' => 0,
                'tax_type' => $request->tax_type ?? 1,
                'payment_method' => $request->payment_method,
                'reference_number' => $request->reference_number,
                'received_date' => $request->received_date,
                'subtotal' => $total_amount,
                'total_discount' => $final_total_discount,
                'tax_total' => $tax_total,
                'grand_total' => $total_with_tax,
                'paid_amount' => 0,
            ]);
            $quantity = floatval(1);
            $unit_price = floatval($Memberships->packege->price);
            $item_total = $quantity * $unit_price;
            $type = "packege";
                     
            $items_data = InvoiceItem::create([
                'invoice_id' => $invoice->id, // سيتم تعيينه لاحقًا بعد إنشاء الفاتورة
                'packege_id' => $Memberships->package_id,
                'store_house_id' => 1,
                'item' => $Memberships->packege->commission_name ?? 'الباقة',
                'description' => "" ?? null,
                'quantity' => $quantity,
                'unit_price' => $unit_price,
                'discount' => 0,
                'type'     => $type,
                'discount_type' => isset($item['discount_type']) && $item['discount_type'] === 'percentage' ? 2 : 1,
                'tax_1' => floatval($item['tax_1'] ?? 0),
                'tax_2' => floatval($item['tax_2'] ?? 0),
                'total' => $item_total,
            ]);

           
           // استرجاع حساب القيمة المضافة المحصلة
           $vatAccount = Account::where('name', 'القيمة المضافة المحصلة')->first();
           if (!$vatAccount) {
               throw new \Exception('حساب القيمة المضافة المحصلة غير موجود');
           }
           $salesAccount = Account::where('name', 'المبيعات')->first();
           if (!$salesAccount) {
               throw new \Exception('حساب المبيعات غير موجود');
           }

           // إنشاء القيد المحاسبي للفاتورة
           $journalEntry = JournalEntry::create([
               'reference_number' => $invoice->code,
               'date' => now(),
               'description' => 'فاتورة مبيعات رقم ' . $invoice->code,
               'status' => 1,
               'currency' => 'SAR',
               'client_id' => $invoice->client_id,
               'invoice_id' => $invoice->id,
               'created_by_employee' => Auth::id(),
           ]);

           // إضافة تفاصيل القيد المحاسبي
           // 1. حساب العميل (مدين)
           JournalEntryDetail::create([
               'journal_entry_id' => $journalEntry->id,
               'account_id' => $invoice->client->id  , // حساب العميل
               'description' => 'فاتورة مبيعات',
               'debit' => $total_with_tax, // المبلغ الكلي للفاتورة (مدين)
               'credit' => 0,
               'is_debit' => true,
           ]);

           // 2. حساب المبيعات (دائن)
           JournalEntryDetail::create([
               'journal_entry_id' => $journalEntry->id,
               'account_id' => $salesAccount->id, // حساب المبيعات
               'description' => 'إيرادات مبيعات',
               'debit' => 0,
               'credit' => $amount_after_discount, // المبلغ بعد الخصم (دائن)
               'is_debit' => false,
           ]);

           // 3. حساب القيمة المضافة المحصلة (دائن)
           JournalEntryDetail::create([
               'journal_entry_id' => $journalEntry->id,
               'account_id' => $vatAccount->id, // حساب القيمة المضافة المحصلة
               'description' => 'ضريبة القيمة المضافة',
               'debit' => 0,
               'credit' => $tax_total, // قيمة الضريبة (دائن)
               'is_debit' => false,
           ]);

           // ** تحديث رصيد حساب المبيعات (إيرادات) **
           if ($salesAccount) {
               $salesAccount->balance += $amount_after_discount; // إضافة المبلغ بعد الخصم
               $salesAccount->save();
           }

           // تحديث رصيد حساب الإيرادات (المبيعات + الضريبة)
           $revenueAccount = Account::where('name', 'الإيرادات')->first();
           if ($revenueAccount) {
               $revenueAccount->balance += $amount_after_discount; // المبلغ بعد الخصم (بدون الضريبة)
               $revenueAccount->save();
           }

           // تحديث رصيد حساب القيمة المضافة (الخصوم)
           $vatAccount->balance += $tax_total; // قيمة الضريبة
           $vatAccount->save();

           // تحديث رصيد حساب الأصول (المبيعات + الضريبة)
           $assetsAccount = Account::where('name', 'الأصول')->first();
           if ($assetsAccount) {
               $assetsAccount->balance += $total_with_tax; // المبلغ الكلي (المبيعات + الضريبة)
               $assetsAccount->save();
           }

           // ** الخطوة السابعة: إنشاء سجل الدفع إذا كان هناك دفعة مقدمة أو دفع كامل **
    
           $Subscriptions = new Subscriptions();
           $Subscriptions->client_id        = $Memberships->client_id;
           $Subscriptions->package_id       = $Memberships->package_id;
           $Subscriptions->end_date         = $Memberships->end_date;
           $Subscriptions->join_date        = $Memberships->join_date;
           $Subscriptions->join_date        = $Memberships->join_date;
           $Subscriptions->invoice_id       = $invoice->id;
           $Subscriptions->save();

           DB::commit();

         
          
        
        return redirect()->route('Memberships.create_invoice', $invoice->id)->with('success', 'تم إنشاء العضوية بنجاح.');
    }
    
    public function show($id)
    {
        
        $MembershipsSetthing = MembershipsSetthing::find(1);
           $membership = Memberships::find($id);
        
         $client     = Client::find($membership->client_id);
        return view('memberships.mang_memberships.show',compact('membership','client','MembershipsSetthing'));
    }
   
    public function show_subscription($id)
    {
         
        $membership = Subscriptions::find($id);
        $client     = Client::findOrFail($membership->client_id);
        return view('memberships.mang_memberships.show_subscription',compact('membership','client'));
    }
   
   public function create_invoice($id, $type = 'invoice') // $type يحدد إذا كان الدفع لفاتورة أو قسط
    {
       
        if ($type === 'installment') {
            // إذا كانت العملية لقسط، احصل على تفاصيل القسط
            $installment = Installment::with('invoice')->findOrFail($id);
            $amount = $installment->amount; // مبلغ القسط
            $invoiceId = $installment->invoice->id; // معرف الفاتورة
        } else {
            // إذا كانت العملية لفاتورة، احصل على تفاصيل الفاتورة
            $invoice = Invoice::findOrFail($id);
            $amount = $invoice->grand_total; // قيمة الفاتورة
            $invoiceId = $invoice->id; // معرف الفاتورة
        }
    
        // احصل على البيانات الأخرى اللازمة مثل الخزائن والموظفين
        $treasury = Treasury::all();
        $employees = Employee::all();
    
        return view('memberships.mang_memberships.create_invoice', compact('invoiceId', 'amount', 'treasury', 'employees', 'type'));
    }
    public function edit($id)
    {
        $membership = Memberships::findOrFail($id);
        $clients = Client::all();
        $packages = Package::all();
        return view('memberships.mang_memberships.edit',compact('membership','clients','packages'));
    }

    public function renew($id)
    {
        $membership = Memberships::findOrFail($id);
        $clients = Client::all();
        $packages = Package::all();
        return view('memberships.mang_memberships.renewa',compact('membership','clients','packages'));
    }
    
    public function be_active($id) //الغاء الايقاف تنشيط
    {
        $membership = Memberships::findOrFail($id);
        $membership->status = "active";
        $membership->save();
        return back()->with('success', 'تم تنشيط عضوية العميل.');

    }

    public function deactive($id) // ايقاق
    {
        $membership = Memberships::findOrFail($id);
        $membership->status = "deactive";
        $membership->save();
        return back()->with('success', 'تم ايقاف عضوية العميل  .');

    }
    public function renew_update(Request $request, $id)
    {
        $membership = Memberships::findOrFail($id);
        $membership->package_id = $request->package_id ?? $membership->package_id;
        $membership->end_date   = $request->end_date   ?? $membership->end_date;
        $membership->save();

        return redirect()->route('Memberships.index')->with('success', 'تم تجديد العضوية بنجاح.');
    }
   
   public function update(Request $request, $id)
    {
    // تعريف القواعد والرسائل
    $rules = [
        'client_id' => [
            'required',
            function ($attribute, $value, $fail) use ($id) {
                // تحقق إذا كان العميل مسجلاً بالفعل في عضوية أخرى باستثناء العضوية الحالية
                if (Memberships::where('client_id', $value)->where('id', '!=', $id)->exists()) {
                    $fail('هذا العميل مسجل بالفعل في عضوية أخرى.');
                }
            }
        ],
        'package_id' => 'required',
        'join_date' => 'required|date',
        'end_date' => 'required|date',
        'description' => 'nullable|string',
    ];

    $messages = [
        'client_id.required' => 'حقل العميل مطلوب.',
        'package_id.required' => 'حقل الباقة مطلوب.',
        'join_date.required' => 'حقل تاريخ الالتحاق مطلوب.',
        'join_date.date' => 'تاريخ الالتحاق يجب أن يكون تاريخًا صالحًا.',
        'end_date.required' => 'حقل تاريخ الفاتورة مطلوب.',
        'end_date.date' => 'تاريخ الفاتورة يجب أن يكون تاريخًا صالحًا.',
        'description.string' => 'الوصف يجب أن يكون نصًا.',
    ];

    // تنفيذ الفاليديشن
    $validatedData = $request->validate($rules, $messages);

    // تحديث بيانات العضوية
    $membership = Memberships::findOrFail($id);
    $membership->update($validatedData);

    // إعادة التوجيه مع رسالة نجاح
    return redirect()->route('Memberships.index')->with('success', 'تم تعديل العضوية بنجاح.');
}

public function delete($id)
{
   
    $membership = Memberships::findOrFail($id);
    $membership->delete();

    return back()->with('success', 'تم الحذف بنجاح');
}



}
