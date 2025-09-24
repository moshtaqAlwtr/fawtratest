@extends('master')

@section('title')
    اعدادات الفواتير
@stop

@section('content')
<style>
/* تخصيص الـ checkbox */
.form-check-input.custom-checkbox {
    accent-color: blue; /* تغيير لون الـ checkbox إلى الأزرق */
}
/* تخصيص النص بجانب الـ checkbox */
.form-check-label.custom-label {
    color: #333;
}
</style>

<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">إعدادات الفواتير</h2>
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="">الرئيسية</a></li>
                        <li class="breadcrumb-item active">إعدادات الفواتير</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<form action="{{ route('settings.update_invoices') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <label>الحقول التي عليها علامة <span style="color: red">*</span> الزامية</label>
                </div>
                <div>
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="fa fa-save"></i> حفظ
                    </button>
                </div>
            </div>
        </div>
    </div>

    @if (Session::has('success'))
    <div class="alert alert-success text-xl-center" role="alert">
        <p class="mb-0">
            {{ Session::get('success') }}
        </p>
    </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="row" id="settings-container">
                @php
                // عناصر select
                $selectFields = [
                    'min_price_calculation' => [
                        'label' => 'حساب الحد الأدنى لسعر البيع',
                        'options' => ['بالضريبة', 'بدون الضريبة', 'كلاهما'],
                    ],
                    'last_price_display' => [
                        'label' => 'عرض سعر البيع الأخير والحد الأدنى للسعر',
                        'options' => ['لا شئ', 'آخر سعر بيع', 'الحد الأدنى للسعر', 'كلاهما'],
                    ],
                ];
                // عناصر checkbox
                $checkboxFields = [
                    'allow_free_entry' => 'إيقاف الإدخال الحر للمنتجات في الفاتورة',
                    'disable_quotes' => 'تعطيل عروض الأسعار',
                    'manual_invoice_status' => 'إعطاء الفواتير حالات يدوية',
                    'manual_quote_status' => 'إعطاء عروض الأسعار حالات يدوية',
                    'disable_delivery_options' => 'تعطيل خيارات التوصيل',
                    'enable_max_discount' => 'تفعيل الحد الأقصى للخصم',
                    'enable_sales_adjustment' => 'تفعيل تسوية المبيعات',
                    'default_paid_status' => 'إجعل الفواتير مدفوعه بالفعل افتراضياً',
                    'preview_before_save' => 'تفعيل معاينة الفاتورة قبل الحفظ',
                    'auto_pay_if_balance' => 'دفع الفاتورة تلقائيا في حالة وجود رصيد للعميل',
                    'select_price_list' => 'اختيار قائمه الاسعار فى الفواتير',
                    'send_on_social' => 'إرسال المعاملات عبر وسائل التواصل الاجتماعي',
                    'show_invoice_profit' => 'إظهار ربح الفاتورة',
                    'custom_journal_description' => 'وصف مخصص للقيود اليومية',
                    'no_sell_below_cost' => 'عدم البيع باقل من سعر التكلفة',
                    'apply_offers_to_quotes' => 'تطبيق العروض علي عروض الأسعار',
                    'enable_sales_orders' => 'تفعيل أوامر البيع',
                    'manual_sales_order_status' => 'إعطاء أوامر البيع حالات يدوية',
                    'enable_debit_notification' => 'تفعيل الإشعار المدين',
                    'copy_notes_on_conversion' => 'نسخ الملاحظات/الشروط عند تحويل أمر مبيعات أو عرض السعر إلى فاتورة',
                ];
                @endphp

                {{-- الحقول من نوع select --}}
                @foreach ($selectFields as $key => $field)
                <div class="col-md-6 mb-3">
                    <label class="form-label">{{ $field['label'] }} <span style="color: red">*</span></label>
                    <select class="form-control" name="{{ $key }}">
                        @foreach ($field['options'] as $option)
                            <option value="{{ $option }}" {{ (isset($settings[$key]) && $settings[$key] == $option) ? 'selected' : '' }}>
                                {{ $option }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endforeach

                {{-- الحقول من نوع checkbox --}}
                @foreach ($checkboxFields as $key => $label)
                <div class="col-md-6 mb-2">
                    <div class="form-check">
                        <input type="hidden" name="{{ $key }}" value="0">
                        <input
                            type="checkbox"
                            class="form-check-input custom-checkbox"
                            id="{{ $key }}"
                            name="{{ $key }}"
                            value="1"
                            {{ (isset($settings[$key]) && $settings[$key] == '1') ? 'checked' : '' }}
                        >
                        <label class="form-check-label custom-label" for="{{ $key }}">
                            {{ $label }}
                        </label>
                    </div>
                </div>
                @endforeach

            </div>
        </div>
    </div>
</form>
@endsection
