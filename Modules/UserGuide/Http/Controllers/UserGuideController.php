<?php

namespace Modules\UserGuide\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserGuideController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {

    $company = [
        'name' => 'meta code',
        'id' => '4367919',
        'url' => 'https://alfakhrealhomsi.foutrah.com',
        'expiry_date' => '9 أغسطس 2025'
    ];

    return view('userguide::index', compact('company'));
    }

    public function paymentUser()
    {
        $company = [
        'name' => 'meta code',
        'id' => '4367919',
        'url' => 'https://alfakhrealhomsi.foutrah.com',
        'expiry_date' => '9 أغسطس 2025'
    ];

    return view('userguide::payment_user', compact('company'));
    }

    public function myCompany()
    {
        $page='url_company';
    return view('userguide::my_company', compact('page'));
    }

    public function referrals()
    {
        $page='referrals';
    return view('userguide::my_company', compact('page'));
    }

    public function accountStatement()
    {
        $page='account_statement';
        return view('userguide::my_company', compact('page'));
    }

    public function activateCouponPage()
    {
        $page='activate_coupon';
        return view('userguide::my_company', compact('page'));
    }

    public function activateCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string|max:50'
        ]);

        $couponCode = $request->input('coupon_code');

        // هنا يمكنك إضافة منطق التحقق من القسيمة
        // مثال بسيط:
        $validCoupons = ['WELCOME2024', 'DISCOUNT50', 'NEWUSER'];

        if (in_array(strtoupper($couponCode), $validCoupons)) {
            // القسيمة صحيحة
            return redirect()->back()->with('success', 'تم تفعيل القسيمة بنجاح! تم إضافة المكافأة إلى حسابك.');
        } else {
            // القسيمة غير صحيحة
            return redirect()->back()->with('error', 'رمز القسيمة غير صحيح أو منتهي الصلاحية. يرجى المحاولة مرة أخرى.');
        }
    }
    public function changeEmailPage()
    {
        return view('userguide::auth_user', ['page' => 'change_email']);
    }
    public function changePassword()
    {
        return view('userguide::auth_user', ['page' => 'change_password']);
    }

    public function paymentSettings()
    {
        return view('userguide::auth_user', ['page' => 'payment_settings']);
    }

    public function register()
    {
        return view('userguide::register');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('userguide::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {}

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('userguide::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('userguide::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) {}
    public function contactUs()
    {
        return view('userguide::home_pages', ['page' => 'contact_us']);
    }

    public function sendContactMessage(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string',
            'message' => 'required|string|max:1000'
        ]);

        // هنا يمكنك إضافة منطق إرسال البريد الإلكتروني
        // أو حفظ الرسالة في قاعدة البيانات

        return redirect()->back()->with('success', 'تم إرسال رسالتك بنجاح! سنقوم بالرد عليك قريباً.');
    }
        public function prices()
    {
        return view('userguide::home_pages', ['page' => 'prices']);
    }
    public function successPartners()
    {
        return view('userguide::home_pages', ['page' => 'success_partners']);
    }
    public function systemFunctions()
    {
        return view('userguide::home_pages', ['page' => 'system_functions']);
    }
}
