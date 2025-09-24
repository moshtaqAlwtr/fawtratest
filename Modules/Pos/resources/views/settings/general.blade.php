@extends('master')

@section('title')
الإعدادات-عام
@stop

@section('content')

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">الإعدادات-عام</h2>
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">الرئيسية</li>
                        <li class="breadcrumb-item active">الإعدادات</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="content-body">
    <div class="card mb-5">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
                <div>
                    <label>الحقول التي عليها علامة <span style="color: red">*</span> الزامية</label>
                </div>

                <div>
                    <a href="" class="btn btn-outline-danger">
                        <i class="fa fa-ban"></i>الغاء
                    </a>
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="fa fa-save"></i>حفظ
                    </button>
                </div>
            </div>
        </div>
    </div>
<div class="card mt-5">
 

    <!-- Form Card -->
    <div class="card p-4">
        <form>
            <!-- Row 1: Default Customer and Invoice Template -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="default-customer" class="form-label">العميل الافتراضي</label>
                    <select id="default-customer" class="form-control">
                        <option>اختر عميل</option>
                        <option>عميل 1</option>
                        <option>عميل 2</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="invoice-template" class="form-label">قالب الفاتورة الافتراضي</label>
                    <select id="invoice-template" class="form-control">
                        <option>فاتورة حرارية</option>
                        <option>فاتورة إلكترونية</option>
                    </select>
                </div>
            </div>

            <!-- Row 2: Default Payment Method and Allowed Categories -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="payment-method-active" class="form-label">طريقة الدفع المفعلة</label>
                    <select id="payment-method-active" class="form-control">
                        <option>نقدي</option>
                        <option>بطاقة</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="payment-method-default" class="form-label">طريقة الدفع الافتراضية</label>
                    <select id="payment-method-default" class="form-control">
                        <option>نقدي</option>
                        <option>بطاقة</option>
                    </select>
                </div>
            </div>

            <!-- Row 3: Allowed Categories -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="allowed-categories" class="form-label">التصنيفات المسموح بها</label>
                    <select id="allowed-categories" class="form-control">
                        <option>الكل</option>
                        <option>تصنيف 1</option>
                        <option>تصنيف 2</option>
                    </select>
                </div>
            </div>

            <!-- Checkbox Options -->
            <div class="mb-3">
                <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                    <input name="sales_add_quote_all" type="checkbox" class="permission-checkbox-sales permission-main-checkbox">
                    <span class="vs-checkbox">
                        <span class="vs-checkbox--check">
                            <i class="vs-icon feather icon-check"></i>
                        </span>
                    </span>
                    <span class="">تفعيل أزرار الأقام</span>
                </div>
                <div class="mb-3">
                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                        <input name="sales_add_quote_all" type="checkbox" class="permission-checkbox-sales permission-main-checkbox">
                        <span class="vs-checkbox">
                            <span class="vs-checkbox--check">
                                <i class="vs-icon feather icon-check"></i>
                            </span>
                        </span>
                        <span class="">تطبيق إعدادات التحقق من الحقول المخصصة</span>
                    </div>
            
                <div class="mb-3">
                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                        <input name="sales_add_quote_all" type="checkbox" class="permission-checkbox-sales permission-main-checkbox">
                        <span class="vs-checkbox">
                            <span class="vs-checkbox--check">
                                <i class="vs-icon feather icon-check"></i>
                            </span>
                        </span>
                        <span class="">عرض صور المنتجات</span>
                    </div>
                    <div class="mb-3">
                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                            <input name="sales_add_quote_all" type="checkbox" class="permission-checkbox-sales permission-main-checkbox">
                            <span class="vs-checkbox">
                                <span class="vs-checkbox--check">
                                    <i class="vs-icon feather icon-check"></i>
                                </span>
                            </span>
                            <span class="">عرض نافذة الطباعة بعد تأكيد الفاتورة</span>
                        </div>
           
                <div class="mb-3">
                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                        <input name="sales_add_quote_all" type="checkbox" class="permission-checkbox-sales permission-main-checkbox">
                        <span class="vs-checkbox">
                            <span class="vs-checkbox--check">
                                <i class="vs-icon feather icon-check"></i>
                            </span>
                        </span>
                        <span class="">نظام المحاسبة لكل فاتورة</span>
                    </div>
                    <div class="mb-3">
                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                            <input name="sales_add_quote_all" type="checkbox" class="permission-checkbox-sales permission-main-checkbox">
                            <span class="vs-checkbox">
                                <span class="vs-checkbox--check">
                                    <i class="vs-icon feather icon-check"></i>
                                </span>
                            </span>
                            <span class="">تفعيل التسوية التلقائية</span>
                        </div>
         

            <!-- Loss and Profit Account Section -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="profit-account" class="form-label">حساب الارباح</label>
                    <select id="profit-account" class="form-control">
                        <option>الحساب الافتراضي</option>
                        <option>حساب 1</option>
                        <option>حساب 2</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="loss-account" class="form-label">حساب الخسائر</label>
                    <select id="loss-account" class="form-control">
                        <option>الحساب الافتراضي</option>
                        <option>حساب 1</option>
                        <option>حساب 2</option>
                    </select>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
