<?php

namespace Modules\Sitting\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\AccountSetting;
use App\Models\Client;
use App\Models\ColorSitting;
use App\Models\User;
use App\Models\Log as ModelsLog;
use Illuminate\Container\Attributes\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;


class SittingAccountController extends Controller
{
    public function index()
    {

        $client          = Client::where('user_id',auth()->user()->id)->first();
        $user            = User::find(auth()->id());
        $account_setting = AccountSetting::where('user_id',auth()->user()->id)->first();
        return view('sitting::sittingAccount.index',compact('client','account_setting','user'));
    }



    public function store(Request $request)
    {
        try {
            // التحقق من صحة البيانات المدخلة
            $request->validate([
                'attachments' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // دعم صور JPG و PNG بحد أقصى 2MB
            ]);

            $request->validate([
                'trade_name' => ['required'],
            ], [

                'trade_name.required' => 'الأسم التجاري مطلوب.',
            ]);
            // البحث عن الإعدادات الخاصة بالمستخدم الحالي
            $AccountSetting = AccountSetting::where('user_id', auth()->id())->first();

            if (!$AccountSetting) {
                // إذا لم يكن هناك سجل للمستخدم، يتم إنشاؤه
                $AccountSetting = new AccountSetting();
                $AccountSetting->user_id = auth()->user()->id;
            }

            // تحديث الحقول
            $AccountSetting->currency = $request->currency;
            $AccountSetting->timezone = $request->timezone;
            $AccountSetting->negative_currency_formats = $request->negative_currency_formats;
            $AccountSetting->time_formula = $request->time_formula;
            $AccountSetting->business_type = $request->business_type;
            $AccountSetting->printing_method = $request->printing_method;
            $AccountSetting->language = $request->language;

            if ($request->hasFile('attachments')) {
                // رفع الصورة الجديدة إلى مجلد storage/app/public/attachments
                $logoPath = $request->file('attachments')->store('attachments', 'public');

                // تحديث مسار الصورة في قاعدة البيانات
                $AccountSetting->attachments = $logoPath;
            }

            $AccountSetting->save();

            // البحث عن العميل الخاص بالمستخدم الحالي
            $Client = Client::where('user_id', auth()->id())->first();

            if (!$Client) {
                // إذا لم يكن هناك سجل للمستخدم، يتم إنشاؤه
                $Client = new Client();
                $Client->user_id = auth()->id();
            }

            // تحديث حقول العميل
            $Client->employee_id = $request->employee_id;
            $Client->user_id = auth()->user()->id;
            $Client->category = $request->category;
            $Client->attachments = $request->attachments;
            $Client->notes = $request->notes;
            $Client->client_type = $request->client_type;
            $Client->email = $request->email;
            $Client->currency = $request->currency;
            $Client->code = $request->code;
            $Client->opening_balance_date = $request->opening_balance_date;
            $Client->commercial_registration = $request->commercial_registration;
            $Client->country = $request->country;
            $Client->postal_code = $request->postal_code;
            $Client->street2 = $request->street2;
            $Client->street1 = $request->street1;
            $Client->region = $request->region;
            $Client->city = $request->city;
            $Client->mobile = $request->mobile;
            $Client->phone = $request->phone;
            $Client->last_name = $request->last_name;
            $Client->trade_name = $request->trade_name;
            $Client->tax_number = $request->tax_number;
            $Client->commercial_registration = $request->commercial_registration;

            $Client->save();

                 ModelsLog::create([
                'type' => 'setting',

                'type_log' => 'log', // نوع النشاط
                'description' => 'تم  التعديل على اعدادات الحساب ',
                'created_by' => auth()->id(), // ID المستخدم الحالي
            ]);


            // إعادة التوجيه مع رسالة نجاح
            return redirect()->route('SittingAccount.index')->with('success', 'تم حفظ البيانات بنجاح!');

        } catch (\Exception $e) {
            // إعادة التوجيه مع رسالة خطأ في حالة حدوث استثناء
            return redirect()->back()->withInput()->with('error', 'حدث خطأ أثناء حفظ البيانات: ' . $e->getMessage());
        }
    }

    //تغيير البريد الالكتروني
    public function Change_email(Request $request)
    {
        $request->validate([
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore(auth()->id()), // ✅ منع تكرار البريد
            ],
        ], [
            'email.required' => 'البريد الإلكتروني مطلوب.',
            'email.email' => 'يجب إدخال بريد إلكتروني صحيح.',
            'email.unique' => 'هذا البريد الإلكتروني مستخدم بالفعل.',
        ]);

        $user = User::find(auth()->id());
        $user->email = $request->email;
        $user->save();

              ModelsLog::create([
                'type' => 'setting',

                'type_log' => 'log', // نوع النشاط
                'description' => 'تم  التعديل على  البريد الالكتروني ',
                'created_by' => auth()->id(), // ID المستخدم الحالي
            ]);


        return redirect()->route('SittingAccount.index')->with('success', 'تم تغيير البريد الإلكتروني بنجاح!');



    }
    // تغيير كلمة المرور
    public function change_password(Request $request)
    {

        // التحقق من صحة المدخلات
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed', // تأكد من أن كلمة المرور تتكون من 8 أحرف على الأقل
        ], [
            'password.confirmed' => 'كلمة المرور الجديدة غير متطابقة.',
        ]);

        // التحقق من تطابق كلمة المرور القديمة
        $user = User::find(auth()->id());
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'كلمة المرور القديمة غير صحيحة.']);
        }

        // تحديث كلمة المرور الجديدة
        $user->password = Hash::make($request->password); // تشفير كلمة المرور
        $user->save();

         ModelsLog::create([
                'type' => 'setting',

                'type_log' => 'log', // نوع النشاط
                'description' => 'تم  التعديل على  كلمة السر  ',
                'created_by' => auth()->id(), // ID المستخدم الحالي
            ]);

        return redirect()->route('SittingAccount.index')->with('success', 'تم تغيير كلمة السر بنجاح!');


    }


    public function color()
    {
        $backgroundColor = ColorSitting::find(1);

        $backgroundColorr = $backgroundColor->color ?? '#ffffff';
        return view('sitting::backgroundColor.index',compact('backgroundColor','backgroundColorr'));
    }

    public function backgroundColor()
    {
        $background = ColorSitting::find(1);
        $backgroundColor = $background->color;
        return view('layouts.header',compact('backgroundColor'));
    }

    public function updateColor(Request $request)
    {
        $request->validate([
            'color' => 'required|string|max:7', // التأكد أن الكود لون صالح
        ]);

        // البحث عن اللون، وإذا لم يكن موجودًا يتم إنشاؤه
        $backgroundColor = ColorSitting::first();

        if ($backgroundColor) {
            // تحديث اللون إذا كان موجودًا
            $backgroundColor->color = $request->color;
            $backgroundColor->save();
        } else {
            // إنشاء لون جديد إذا لم يكن هناك سجل
            $backgroundColor = new ColorSitting();
            $backgroundColor->color = $request->color;
            $backgroundColor->save();
        }

        return redirect()->back()->with('success', 'تم تحديث اللون بنجاح');
    }



}

