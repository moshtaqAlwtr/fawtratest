
@extends('master')

@section('title')
الأدوار الوظيفية
@stop
<style>
.custom-btn {
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: #fff;
    border: none;
    border-radius: 12px;
    padding: 12px 20px;
    font-size: 16px;
    font-weight: bold;
    transition: all 0.3s ease-in-out;
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.custom-btn:hover {
    background: linear-gradient(135deg, #0056b3, #003b80);
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
}

.custom-btn:active {
    transform: scale(0.98);
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
}

.custom-btn i {
    font-size: 20px;
}

.custom-btn .app-manager-role-btn {
    background: rgba(255, 255, 255, 0.2);
    padding: 5px 10px;
    border-radius: 8px;
}

    </style>
@section('content')

<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">الأدوار الوظيفية </h2>
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="">الرئيسيه</a>
                        </li>
                        <li class="breadcrumb-item active">تعديل
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="content-body">
    <form id="permissions_form" action="{{ route('managing_employee_roles.update',$role->id) }}" method="POST">
        @csrf
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <div class="d-flex justify-content-between ">
                    <input type="text" name="role_name" placeholder="اسم الدور الوظيفي" required value="{{ $role->role_name }}">
                    <div class="vs-checkbox-con vs-checkbox-primary px-md-1">
                        <ul class="list-unstyled mb-0">
                            <li class="d-inline-block mr-2">
                                <fieldset>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" class="custom-control-input user-radio" name="customRadio" id="customRadio1" {{ $role->role_type == 1 ? 'checked' : '' }} value="user">
                                        <label class="custom-control-label" for="customRadio1">مستخدم</label>
                                    </div>
                                </fieldset>
                            </li>
                            <li class="d-inline-block mr-2">
                                <fieldset>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" class="custom-control-input employee-radio" name="customRadio" id="customRadio2" {{ $role->role_type == 2 ? 'checked' : '' }} value="employee">
                                        <label class="custom-control-label" for="customRadio2">موظف</label>
                                    </div>
                                </fieldset>
                            </li>
                        </ul>
                    </div>
                    <div class="vs-checkbox-con vs-checkbox-primary px-md-1" id="admin">
                        <input type="checkbox" id="adminCheckbox">
                        <span class="vs-checkbox">
                            <span class="vs-checkbox--check">
                                <i class="vs-icon feather icon-check"></i>
                            </span>
                        </span>
                        <span class="">مدير (أدمن )</span>
                    </div>
                </div>
                <div>
                    <a href="{{ route('managing_employee_roles.index') }}" class="btn btn-outline-danger">
                        <i class="fa fa-ban"></i>الغاء
                    </a>
                    <button type="submit" form="permissions_form" class="btn btn-outline-primary">
                        <i class="fa fa-save"></i>تحديث
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="app-manager-sidebar">
                        <div class="app-manager-dropdown-app">
                            <div>التطبيقات ومجموعات الصلاحيات</div>

                            <!-- زر كل التطبيقات -->
                            <button class="app-manager-dropdown-app nav-link btn btn-outline-primary w-100 mb-2" 
                            data-bs-toggle="tab" 
                            data-bs-target="#plugin-group-5" 
                            type="button" 
                            role="tab" 
                            aria-controls="plugin-group-5" 
                            aria-selected="false"
                            id="allButton">
                        <div class="d-flex align-items-center">
                            <i class="mdi mdi-account-tie app-manager-dropdown-icon"></i>
                            <span class="ms-2">كل التطبيقات</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <label class="app-manager-role-btn">
                                <span class="app-manager-role-label" id="selectedCount">0/236</span>
                                <input class="app-manager-role-check" type="checkbox" value="" data-role-check-all="plugin-group-5">
                                <span class="app-manager-role-control"></span>
                            </label>
                        </div>
                    </button>

                            <!-- زر المبيعات -->
                                     
                    <button class="app-manager-dropdown-app nav-link btn btn-outline-primary w-100 mb-2" 
                    data-bs-toggle="tab" 
                    data-bs-target="#plugin-group-5" 
                    type="button" 
                    role="tab" 
                    aria-controls="plugin-group-5" 
                    aria-selected="false"
                    id="salesButton">
                <div class="d-flex align-items-center">
                    <i class="mdi mdi-account-tie app-manager-dropdown-icon"></i>
                    <span class="ms-2">المبيعات</span>
                </div>
                <div class="d-flex align-items-center">
                    <label class="app-manager-role-btn">
                        
                        <span class="app-manager-role-label" id="selectedCountsales">0/61</span>
                        <input class="app-manager-role-check" type="checkbox" value="" data-role-check-all="plugin-group-5">
                        <span class="app-manager-role-control"></span>
                    </label>
                </div>
            </button>

                            <!-- زر إدارة علاقات العملاء -->
                            <button class="app-manager-dropdown-app nav-link btn btn-outline-primary w-100 mb-2" 
                            data-bs-toggle="tab" 
                            data-bs-target="#plugin-group-5" 
                            type="button" 
                            role="tab" 
                            aria-controls="plugin-group-5" 
                            aria-selected="false"
                            id="customerButton">
                        <div class="d-flex align-items-center">
                            <i class="mdi mdi-account-tie app-manager-dropdown-icon"></i>
                            <span class="ms-2">ادارة علاقات العملاء</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <label class="app-manager-role-btn">
                                
                                <span class="app-manager-role-label" id="selectedCountcustomer">0/26</span>
                                <input class="app-manager-role-check" type="checkbox" value="" data-role-check-all="plugin-group-5">
                                <span class="app-manager-role-control"></span>
                            </label>
                        </div>
                    </button>

                            <!-- زر الموارد البشرية -->
                          
                            <button class="app-manager-dropdown-app nav-link btn btn-outline-primary w-100 mb-2" 
                            data-bs-toggle="tab" 
                            data-bs-target="#plugin-group-5" 
                            type="button" 
                            role="tab" 
                            aria-controls="plugin-group-5" 
                            aria-selected="false"
                            id="crmButton">
                        <div class="d-flex align-items-center">
                            <i class="mdi mdi-account-tie app-manager-dropdown-icon"></i>
                            <span class="ms-2"> الموارد البشرية </span>
                        </div>
                        <div class="d-flex align-items-center">
                            <label class="app-manager-role-btn">
                                
                                <span class="app-manager-role-label" id="selectedCrm">0/49</span>
                                <input class="app-manager-role-check" type="checkbox" value="" data-role-check-all="plugin-group-5">
                                <span class="app-manager-role-control"></span>
                            </label>
                        </div>
                    </button>

                            <!-- زر المخزون والمشتريات -->
                          
<!-- زر المخزون والمشتريات -->
                          
                            
<button class="app-manager-dropdown-app nav-link btn btn-outline-primary w-100 mb-2" 
data-bs-toggle="tab" 
data-bs-target="#plugin-group-5" 
type="button" 
role="tab" 
aria-controls="plugin-group-5" 
aria-selected="false"
id="storeButton">
<div class="d-flex align-items-center">
<i class="mdi mdi-account-tie app-manager-dropdown-icon"></i>
<span class="ms-2">  المخزون والمشتريات </span>
</div>
<div class="d-flex align-items-center">
<label class="app-manager-role-btn">
    
    <span class="app-manager-role-label" id="selectedStore">0/26</span>
    <input class="app-manager-role-check" type="checkbox" value="" data-role-check-all="plugin-group-5">
    <span class="app-manager-role-control"></span>
</label>
</div>
</button>

                            <!-- زر التشغيل -->
                            <button class="app-manager-dropdown-app nav-link btn btn-outline-primary w-100 mb-2" 
                            data-bs-toggle="tab" 
                            data-bs-target="#plugin-group-5" 
                            type="button" 
                            role="tab" 
                            aria-controls="plugin-group-5" 
                            aria-selected="false"
                            id="operatingButton">
                        <div class="d-flex align-items-center">
                            <i class="mdi mdi-account-tie app-manager-dropdown-icon"></i>
                            <span class="ms-2"> التشغيل  </span>
                        </div>
                        <div class="d-flex align-items-center">
                            <label class="app-manager-role-btn">
                                
                                <span class="app-manager-role-label" id="selectedOperating">0/22</span>
                                <input class="app-manager-role-check" type="checkbox" value="" data-role-check-all="plugin-group-5">
                                <span class="app-manager-role-control"></span>
                            </label>
                        </div>
                    </button>

                            <!-- زر الحسابات العامة -->
                            <button class="app-manager-dropdown-app nav-link btn btn-outline-primary w-100 mb-2" 
                            data-bs-toggle="tab" 
                            data-bs-target="#plugin-group-5" 
                            type="button" 
                            role="tab" 
                            aria-controls="plugin-group-5" 
                            aria-selected="false"
                            id="accountButton">
                        <div class="d-flex align-items-center">
                            <i class="mdi mdi-account-tie app-manager-dropdown-icon"></i>
                            <span class="ms-2"> الحسابات العامة  </span>
                        </div>
                        <div class="d-flex align-items-center">
                            <label class="app-manager-role-btn">
                                
                                <span class="app-manager-role-label" id="selectedAccount">0/30</span>
                                <input class="app-manager-role-check" type="checkbox" value="" data-role-check-all="plugin-group-5">
                                <span class="app-manager-role-control"></span>
                            </label>
                        </div>
                    </button>

                            <!-- زر الإعدادات العامة -->
                            <button class="app-manager-dropdown-app nav-link btn btn-outline-primary w-100 mb-2" 
                            data-bs-toggle="tab" 
                            data-bs-target="#plugin-group-5" 
                            type="button" 
                            role="tab" 
                            aria-controls="plugin-group-5" 
                            aria-selected="false"
                            id="settingsButton">
                        <div class="d-flex align-items-center">
                            <i class="mdi mdi-account-tie app-manager-dropdown-icon"></i>
                            <span class="ms-2"> الإعدادات العامة  </span>
                        </div>
                        <div class="d-flex align-items-center">
                            <label class="app-manager-role-btn">
                                
                                <span class="app-manager-role-label" id="selectedSetting">0/4</span>
                                <input class="app-manager-role-check" type="checkbox" value="" data-role-check-all="plugin-group-5">
                                <span class="app-manager-role-control"></span>
                            </label>
                        </div>
                    </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-8">

            <div id="userContent"><!--End User Content-->
                <!-- المبيعات -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">

                                <div class="d-flex align-items-center">
                                    <div class="vs-checkbox-con vs-checkbox-primary me-3">
                                        <input type="checkbox" id="selectAllSales" class="permission-main-checkbox sales-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <strong class="">المبيعات </strong>
                                    </div>
                                </div>
                                <!-- الصلاحيات النشطة -->
                                <div class="d-flex align-items-center">
                                    <span class="panel-role-active-title me-2">الصلاحيات النشطة : </span>
                                    <strong class="panel-role-active-count" data-role-count="34"><span id="activeCountSales">0</span>/34</strong>
                                </div>
                            </div>

                            <div class="row">
                                <!-- العمود الأول -->
                                <div class="col-md-6">
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="sales_add_invoices" type="checkbox" {{ $role->sales_add_invoices == 1 ? 'checked' : '' }} class="permission-checkbox-sales sales-checkbox sales-checkbox   permission-main-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">إضافة فواتير لكل العملاء</span>
                                    </div>
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="sales_add_own_invoices" {{ $role->sales_add_own_invoices == 1 ? 'checked' : '' }} type="checkbox" class="permission-checkbox-sales sales-checkbox sales-checkbox   permission-main-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">إضافه فواتير للعملاء الخاصة به</span>
                                    </div>
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="sales_edit_delete_all_invoices" {{ $role->sales_edit_delete_all_invoices == 1 ? 'checked' : '' }} type="checkbox" class="permission-checkbox-sales sales-checkbox sales-checkbox   permission-main-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">تعديل وحذف كل الفواتير</span>
                                    </div>
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="sales_edit_delete_own_invoices" {{ $role->sales_edit_delete_own_invoices == 1 ? 'checked' : '' }} type="checkbox" class="permission-checkbox-sales sales-checkbox sales-checkbox  permission-main-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">تعديل وحذف الفواتير الخاصة به</span>
                                    </div>
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="sales_view_own_invoices" {{ $role->sales_view_own_invoices == 1 ? 'checked' : '' }} type="checkbox" class="permission-checkbox-sales sales-checkbox sales-checkbox  permission-main-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">عرض الفواتير الخاصة به</span>
                                    </div>
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="sales_view_all_invoices" {{ $role->sales_view_all_invoices == 1 ? 'checked' : '' }} type="checkbox" class="permission-checkbox-sales sales-checkbox sales-checkbox  permission-main-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">عرض جميع الفواتير</span>
                                    </div>
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="sales_create_tax_report" {{ $role->sales_create_tax_report == 1 ? 'checked' : '' }} type="checkbox" class="permission-checkbox-sales sales-checkbox sales-checkbox  permission-main-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">إنشاء تقرير ضرائب</span>
                                    </div>
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="sales_change_seller" {{ $role->sales_change_seller == 1 ? 'checked' : '' }} type="checkbox" class="permission-checkbox-sales sales-checkbox sales-checkbox  permission-main-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">تغيير البائع</span>
                                    </div>
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="sales_invoice_all_products" {{ $role->sales_invoice_all_products == 1 ? 'checked' : '' }} type="checkbox" class="permission-checkbox-sales sales-checkbox sales-checkbox  permission-main-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">فوترة جميع المنتجات</span>
                                    </div>
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="sales_view_invoice_profit" {{ $role->sales_view_invoice_profit == 1 ? 'checked' : '' }} type="checkbox" class="permission-checkbox-sales sales-checkbox permission-main-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">عرض ربح الفاتورة</span>
                                    </div>
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="sales_add_credit_notice_all" {{ $role->sales_add_credit_notice_all == 1 ? 'checked' : '' }} type="checkbox" class="permission-checkbox-sales sales-checkbox permission-main-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">إضافة إشعار مدين جديد لجميع العملاء</span>
                                    </div>
                                    
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="sales_add_credit_notice_own" {{ $role->sales_add_credit_notice_own == 1 ? 'checked' : '' }} type="checkbox" class="permission-checkbox-sales sales-checkbox permission-main-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">إضافة إشعار مدين جديد لعملائه فقط</span>
                                    </div>
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="Issue_an_invoice_to_a_customer_who_has_a_debt" {{ $role->Issue_an_invoice_to_a_customer_who_has_a_debt == 1 ? 'checked' : '' }} type="checkbox" class="permission-checkbox-sales sales-checkbox permission-main-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                     <span class="">اصدار فاتورة لعميل لديه مديونية</span>
                                    </div>
                                </div>

                                <!-- العمود الثاني -->
                                <div class="col-md-6">
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="sales_edit_invoice_date" {{ $role->sales_edit_invoice_date == 1 ? 'checked' : '' }} type="checkbox" class="permission-checkbox-sales sales-checkbox permission-main-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">تعديل تاريخ الفاتورة</span>
                                    </div>
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="sales_add_payments_all" {{ $role->sales_add_payments_all == 1 ? 'checked' : '' }} type="checkbox" class="permission-checkbox-sales sales-checkbox permission-main-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">إضافة عمليات دفع لكل الفواتير</span>
                                    </div>
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="sales_add_payments_own" {{ $role->sales_add_payments_own == 1 ? 'checked' : '' }} type="checkbox" class="permission-checkbox-sales sales-checkbox permission-main-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">إضافة عمليات دفع للفواتير الخاصة به</span>
                                    </div>
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="sales_edit_payment_options" {{ $role->sales_edit_payment_options == 1 ? 'checked' : '' }} type="checkbox" class="permission-checkbox-sales sales-checkbox permission-main-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">تعديل خيارات الدفع</span>
                                    </div>
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="sales_edit_delete_all_payments" {{ $role->sales_edit_delete_all_payments == 1 ? 'checked' : '' }} type="checkbox" class="permission-checkbox-sales sales-checkbox permission-main-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">حذف وتعديل جميع المدفوعات</span>
                                    </div>
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="sales_edit_delete_own_payments" {{ $role->sales_edit_delete_own_payments == 1 ? 'checked' : '' }} type="checkbox" class="permission-checkbox-sales sales-checkbox permission-main-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">حذف وتعديل المدفوعات الخاصة به</span>
                                    </div>
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="sales_add_quote_all" {{ $role->sales_add_quote_all == 1 ? 'checked' : '' }} type="checkbox" class="permission-checkbox-sales sales-checkbox permission-main-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">إضافة عرض سعر لكل العملاء</span>
                                    </div>
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="sales_add_quote_own" {{ $role->sales_add_quote_own == 1 ? 'checked' : '' }} type="checkbox" class="permission-checkbox-sales sales-checkbox permission-main-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">إضافة عرض سعر للعملاء الخاصة به</span>
                                    </div>
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="sales_view_all_quotes" {{ $role->sales_view_all_quotes == 1 ? 'checked' : '' }} type="checkbox" class="permission-checkbox-sales sales-checkbox permission-main-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">عرض جميع عروض الأسعار</span>
                                    </div>
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="sales_view_own_quotes" {{ $role->sales_view_own_quotes == 1 ? 'checked' : '' }} type="checkbox" class="permission-checkbox-sales sales-checkbox permission-main-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">عرض عروض الأسعار الخاصة به</span>
                                    </div>
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="sales_edit_delete_all_quotes" {{ $role->sales_edit_delete_all_quotes == 1 ? 'checked' : '' }} type="checkbox" class="permission-checkbox-sales sales-checkbox permission-main-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">تعديل وحذف جميع عروض الأسعار</span>
                                    </div>
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="sales_edit_delete_own_quotes" {{ $role->sales_edit_delete_own_quotes == 1 ? 'checked' : '' }} type="checkbox" class="permission-checkbox-sales sales-checkbox permission-main-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">تعديل وحذف عروض الأسعار الخاصة به</span>
                                    </div>
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="sales_view_all_sales_orders" {{ $role->sales_view_all_sales_orders == 1 ? 'checked' : '' }} type="checkbox" class="permission-checkbox-sales sales-checkbox permission-main-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">عرض جميع أوامر البيع</span>
                                    </div>
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="sales_view_own_sales_orders" {{ $role->sales_view_own_sales_orders == 1 ? 'checked' : '' }} type="checkbox" class="permission-checkbox-sales sales-checkbox permission-main-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">عرض أوامر البيع الخاصة به</span>
                                    </div>
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="sales_add_sales_order_all" {{ $role->sales_add_sales_order_all == 1 ? 'checked' : '' }} type="checkbox" class="permission-checkbox-sales sales-checkbox permission-main-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">إضافة أمر بيع جديد لجميع العملاء</span>
                                    </div>
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="sales_add_sales_order_own" {{ $role->sales_add_sales_order_own == 1 ? 'checked' : '' }} type="checkbox" class="permission-checkbox-sales sales-checkbox permission-main-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">إضافة أمر بيع جديد لعملائه فقط</span>
                                    </div>
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="sales_edit_delete_all_sales_orders" {{ $role->sales_edit_delete_all_sales_orders == 1 ? 'checked' : '' }} type="checkbox" class="permission-checkbox-sales sales-checkbox permission-main-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">تعديل وحذف جميع أوامر البيع</span>
                                    </div>
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="sales_edit_delete_own_sales_orders" {{ $role->sales_edit_delete_own_sales_orders == 1 ? 'checked' : '' }} type="checkbox" class="permission-checkbox-sales sales-checkbox permission-main-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">تعديل وحذف أوامر البيع الخاصة به فقط</span>
                                    </div>
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="sales_edit_delete_all_credit_notices" {{ $role->sales_edit_delete_all_credit_notices == 1 ? 'checked' : '' }} type="checkbox" class="permission-checkbox-sales sales-checkbox permission-main-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">تعديل وحذف جميع الإشعارات المدينة</span>
                                    </div>
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="sales_edit_delete_own_credit_notices" {{ $role->sales_edit_delete_own_credit_notices == 1 ? 'checked' : '' }} type="checkbox" class="permission-checkbox-sales sales-checkbox permission-main-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">تعديل وحذف الإشعارات المدينة الخاصة به فقط</span>
                                    </div>
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="sales_view_all_credit_notices" {{ $role->sales_view_all_credit_notices == 1 ? 'checked' : '' }} type="checkbox" class="permission-checkbox-sales sales-checkbox permission-main-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">عرض جميع الإشعارات المدينة</span>
                                    </div>
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="sales_view_own_credit_notices" {{ $role->sales_view_own_credit_notices == 1 ? 'checked' : '' }} type="checkbox" class="permission-checkbox-sales sales-checkbox permission-main-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">عرض الإشعارات المدينة الخاصة به فقط</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- نقاط البيع -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <!-- نقاط البيع والصلاحيات -->
                            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="vs-checkbox-con vs-checkbox-primary me-3">
                                        <input type="checkbox" id="selectAllSalesPoints" class="permission-main-checkbox sales-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <strong class="">نقاط البيع</strong>
                                    </div>
                                </div>
                                <!-- الصلاحيات النشطة -->
                                <div class="d-flex align-items-center">
                                    <span class="panel-role-active-title me-2">الصلاحيات النشطة: </span>
                                    <strong class="panel-role-active-count" data-role-count="10"><span id="activeCountSalesPoints">0</span>/10</strong>
                                </div>
                            </div>

                            <!-- قائمة الصلاحيات -->
                            <div class="row">
                                <!-- العمود الأول -->
                                <div class="col-md-6">
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">          
                                        <input name="points_sale_edit_product_prices" {{ $role->points_sale_edit_product_prices == 1 ? 'checked' : '' }} type="checkbox" class="permission-checkbox-sales-points sales-checkbox permission-main-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">تعديل أسعار المنتجات</span>
                                    </div>
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="points_sale_add_discount" {{ $role->points_sale_add_discount == 1 ? 'checked' : '' }} type="checkbox" class="permission-checkbox-sales-points sales-checkbox permission-main-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">إضافة خصم</span>
                                    </div>
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="points_sale_open_sessions_all" {{ $role->points_sale_open_sessions_all == 1 ? 'checked' : '' }} type="checkbox" class="permission-checkbox-sales-points sales-checkbox permission-main-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">فتح جلسات لجميع المستخدمين</span>
                                    </div>
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="points_sale_open_sessions_own" {{ $role->points_sale_open_sessions_own == 1 ? 'checked' : '' }} type="checkbox" class="permission-checkbox-sales-points sales-checkbox permission-main-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">فتح جلسات لنفسه</span>
                                    </div>
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="points_sale_close_sessions_all" {{ $role->points_sale_close_sessions_all == 1 ? 'checked' : '' }} type="checkbox" class="permission-checkbox-sales-points permission-main-checkbox sales-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">إغلاق جلسات جميع المستخدمين</span>
                                    </div>
                                </div>
                                <!-- العمود الثاني -->
                                <div class="col-md-6">
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="points_sale_close_sessions_own" {{ $role->points_sale_close_sessions_own == 1 ? 'checked' : '' }} type="checkbox" class="permission-checkbox-sales-points permission-main-checkbox sales-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">إغلاق الجلسات الخاصة</span>
                                    </div>
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="points_sale_view_all_sessions" {{ $role->points_sale_view_all_sessions == 1 ? 'checked' : '' }} type="checkbox" class="permission-checkbox-sales-points permission-main-checkbox sales-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">عرض جميع الجلسات</span>
                                    </div>
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="points_sale_view_own_sessions" {{ $role->points_sale_view_own_sessions == 1 ? 'checked' : '' }} type="checkbox" class="permission-checkbox-sales-points permission-main-checkbox sales-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">عرض الجلسات الخاصة به فقط</span>
                                    </div>
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="points_sale_confirm_close_sessions_all" {{ $role->points_sale_confirm_close_sessions_all == 1 ? 'checked' : '' }} type="checkbox" class="permission-checkbox-sales-points permission-main-checkbox sales-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">تأكيد إغلاق جميع الجلسات</span>
                                    </div>
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="points_sale_confirm_close_sessions_own" {{ $role->points_sale_confirm_close_sessions_own == 1 ? 'checked' : '' }} type="checkbox" class="permission-checkbox-sales-points permission-main-checkbox sales-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">تأكيد إغلاق الجلسات الخاصة به</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- نقاط ولاء العملاء -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="vs-checkbox-con vs-checkbox-primary me-3">
                                        <input type="checkbox" id="SelectAllCustomerLoyalty" class="permission-main-checkbox customer-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <strong class="">نقاط ولاء العملاء</strong>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center">
                                    <span class="panel-role-active-title me-2">الصلاحيات النشطة:</span>
                                    <strong class="panel-role-active-count" data-role-count="2"><span id="activeCountCustomerLoyalty">0</span>/2</strong>
                                </div>
                            </div>

                            <div class="panel-body">
                                <div class="row">
                                    <!-- الزر الأول -->
                                    <div class="col-md-6 mb-3">
                                        <div class="vs-checkbox-con vs-checkbox-primary">
                                            <input name="customer_loyalty_points_managing_customer_bases" {{ $role->customer_loyalty_points_managing_customer_bases == 1 ? 'checked' : '' }} type="checkbox" class="permission-checkbox-customer-loyalty permission-main-checkbox customer-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">أدارة قواعد العملاء</span>
                                        </div>
                                    </div>

                                    <!-- الزر الثاني -->
                                    <div class="col-md-6 mb-3">
                                        <div class="vs-checkbox-con vs-checkbox-primary">
                                            <input name="customer_loyalty_points_redeem_loyalty_points" {{ $role->customer_loyalty_points_redeem_loyalty_points == 1 ? 'checked' : '' }} type="checkbox" class="permission-checkbox-customer-loyalty permission-main-checkbox customer-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">صرف نقاط الولاء</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- المبيعات المستهدفة والعمولات -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="vs-checkbox-con vs-checkbox-primary me-3">
                                        <input type="checkbox" id="SelectAllTargetedSalesCommissions" class="permission-main-checkbox sales-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <strong class="">المبيعات المستهدفة والعمولات </strong>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="panel-role-active-title me-2">الصلاحيات النشطة:</span>
                                    <strong class="panel-role-active-count" data-role-count="4"><span id="activeCountTargetedSalesCommissions">0</span>/4</strong>
                                </div>
                            </div>

                            <div class="row">
                                <!-- الزر الأول -->
                                <div class="col-md-6 mb-1">
                                    <div class="vs-checkbox-con vs-checkbox-primary">
                                        <input name="targeted_sales_commissions_manage_sales_periods" {{ $role->targeted_sales_commissions_manage_sales_periods == 1 ? 'checked' : '' }} type="checkbox" class="targeted-sales-commissions permission-main-checkbox sales-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">أدارة فترات المبيعات</span>
                                    </div>
                                </div>

                                <!-- الزر الثاني -->
                                <div class="col-md-6 mb-1">
                                    <div class="vs-checkbox-con vs-checkbox-primary">
                                        <input name="targeted_sales_commissions_view_all_sales_commissions" {{ $role->targeted_sales_commissions_view_all_sales_commissions == 1 ? 'checked' : '' }} type="checkbox" class="targeted-sales-commissions permission-main-checkbox sales-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">عرض جميع عمولات المبيعات</span>
                                    </div>
                                </div>

                                <!-- الزر الثالث -->
                                <div class="col-md-6 mb-1">
                                    <div class="vs-checkbox-con vs-checkbox-primary">
                                        <input name="targeted_sales_commissions_view_own_sales_commissions" {{ $role->targeted_sales_commissions_view_own_sales_commissions == 1 ? 'checked' : '' }} type="checkbox" class="targeted-sales-commissions permission-main-checkbox sales-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">عرض عمولات المبيعات الخاصة بة</span>
                                    </div>
                                </div>

                                <!-- الزر الرابع -->
                                <div class="col-md-6 mb-1">
                                    <div class="vs-checkbox-con vs-checkbox-primary">
                                        <input name="targeted_sales_commissions_manage_commission_rules" {{ $role->targeted_sales_commissions_manage_commission_rules == 1 ? 'checked' : '' }} type="checkbox" class="targeted-sales-commissions permission-main-checkbox sales-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">أدارة قواعد العمولة</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- المنتجات    -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="vs-checkbox-con vs-checkbox-primary me-3">
                                        <input type="checkbox" id="SelectAllProducts" class="permission-main-checkbox store-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <strong class="">المنتجات </strong>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center">
                                    <span class="panel-role-active-title me-2">الصلاحيات النشطة:</span>
                                    <strong class="panel-role-active-count" data-role-count="8"><span id="activeCountProducts">0</span>/8</strong>
                                </div>
                            </div>

                            <div class="row">
                                <!-- العمود الأول -->
                                <div class="col-md-6">
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-2">
                                        <input name="products_add_product" {{ $role->products_add_product == 1 ? 'checked' : '' }} type="checkbox" class="select-all-products permission-main-checkbox store-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">أضافة منتج</span>
                                    </div>

                                    <div class="vs-checkbox-con vs-checkbox-primary mb-2">
                                        <input name="products_view_all_products" {{ $role->products_view_all_products == 1 ? 'checked' : '' }} type="checkbox" class="select-all-products permission-main-checkbox store-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">عرض كل المنتجات</span>
                                    </div>

                                    <div class="vs-checkbox-con vs-checkbox-primary mb-2">
                                        <input name="products_view_own_products" {{ $role->products_view_own_products == 1 ? 'checked' : '' }} type="checkbox" class="select-all-products permission-main-checkbox store-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">عرض المنتجات الخاصة به</span>
                                    </div>

                                    <div class="vs-checkbox-con vs-checkbox-primary mb-2">
                                        <input name="products_edit_delete_all_products" {{ $role->products_edit_delete_all_products == 1 ? 'checked' : '' }} type="checkbox" class="select-all-products permission-main-checkbox store-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">تعديل وحذف كل المنتجات</span>
                                    </div>
                                </div>

                                <!-- العمود الثاني -->
                                <div class="col-md-6">
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-2">
                                        <input name="products_edit_delete_own_products" {{ $role->products_edit_delete_own_products == 1 ? 'checked' : '' }} type="checkbox" class="select-all-products permission-main-checkbox store-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">تعديل وحذف المنتجات الخاصة به</span>
                                    </div>

                                    <div class="vs-checkbox-con vs-checkbox-primary mb-2">
                                        <input name="products_view_price_groups" {{ $role->products_view_price_groups == 1 ? 'checked' : '' }} type="checkbox" class="select-all-products permission-main-checkbox store-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">عرض مجموعة الأسعار</span>
                                    </div>

                                    <div class="vs-checkbox-con vs-checkbox-primary mb-2">
                                        <input name="products_add_edit_price_groups" {{ $role->products_add_edit_price_groups == 1 ? 'checked' : '' }} type="checkbox" class="select-all-products permission-main-checkbox store-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">أضافة وتعديل مجموعة أسعار</span>
                                    </div>

                                    <div class="vs-checkbox-con vs-checkbox-primary mb-2">
                                        <input name="products_delete_price_groups" {{ $role->products_delete_price_groups == 1 ? 'checked' : '' }} type="checkbox" class="select-all-products permission-main-checkbox store-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">حذف مجموعة أسعار</span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- الفاتورة الألكترونية السعودية -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">

                                <div class="d-flex align-items-center">
                                    <div class="vs-checkbox-con vs-checkbox-primary me-3">
                                        <input type="checkbox" id="SelectAllSaudiElectronicInvoice" class="permission-main-checkbox setting-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <strong class="">الفاتورة الألكترونية السعودية </strong>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center">
                                    <span class="panel-role-active-title me-2">الصلاحيات النشطة:</span>
                                    <strong class="panel-role-active-count" data-role-count="1"><span id="activeCountSaudiElectronicInvoice">0</span>/1</strong>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="l-flex-row">

                                    <div class="vs-checkbox-con vs-checkbox-primary">
                                        <input name="sending_invoices_to_the_tax_authority" {{ $role->sending_invoices_to_the_tax_authority == 1 ? 'checked' : '' }} type="checkbox" class="select-all-Saudi-electronic-invoice permission-main-checkbox setting-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">أرسال الفواتير ألى هيئة الضرائب </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- التامينات -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">

                                <div class="d-flex align-items-center">
                                    <div class="vs-checkbox-con vs-checkbox-primary me-3">
                                        <input type="checkbox" id="SelectAllInsurances" class="permission-main-checkbox sales-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <strong class="">التأمينات </strong>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center">
                                    <span class="panel-role-active-title me-2">الصلاحيات النشطة:</span>
                                    <strong class="panel-role-active-count" data-role-count="1"><span id="activeCountInsurances">0</span>/1</strong>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="l-flex-row">

                                    <div class="vs-checkbox-con vs-checkbox-primary">
                                        <input name="management_of_insurance_agents" {{ $role->management_of_insurance_agents == 1 ? 'checked' : '' }} type="checkbox" class="select-all-insurances permission-main-checkbox sales-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">أدارة وكلاء التأمين</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- متابعة العميل   -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="vs-checkbox-con vs-checkbox-primary me-3">
                                        <input type="checkbox" id="SelectAllClientFollowUp" class="permission-main-checkbox customer-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <strong class=""> متابعة العميل </strong>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center">
                                    <span class="panel-role-active-title me-2">الصلاحيات النشطة:</span>
                                    <strong class="panel-role-active-count" data-role-count="8"><span id="activeCountClientFollowUp">0</span>/8</strong>
                                </div>
                            </div>

                            <div class="panel-body">
                                <div class="row">
                                    <!-- العمود الأول -->
                                    <div class="col-md-6">
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="client_follow_up_add_notes_attachments_appointments_all" {{ $role->client_follow_up_add_notes_attachments_appointments_all == 1 ? 'checked' : '' }} type="checkbox" class="select-all-client-follow-up permission-main-checkbox customer-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class=""> إضافة ملاحظات / مرفقات / مواعيد لجميع العملاء</span>
                                        </div>

                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="client_follow_up_add_notes_attachments_appointments_own" {{ $role->client_follow_up_add_notes_attachments_appointments_own == 1 ? 'checked' : '' }} type="checkbox" class="select-all-client-follow-up permission-main-checkbox customer-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class=""> إضافة ملاحظات / مرفقات / مواعيد لعملائه المعينين</span>
                                        </div>

                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="client_follow_up_edit_delete_notes_attachments_appointments_all" {{ $role->client_follow_up_edit_delete_notes_attachments_appointments_all == 1 ? 'checked' : '' }} type="checkbox" class="select-all-client-follow-up permission-main-checkbox customer-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">تعديل وحذف جميع الملاحظات / المرفقات / مواعيد لجميع العملاء</span>
                                        </div>

                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="client_follow_up_edit_delete_notes_attachments_appointments_own" {{ $role->client_follow_up_edit_delete_notes_attachments_appointments_own == 1 ? 'checked' : '' }} type="checkbox" class="select-all-client-follow-up permission-main-checkbox customer-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">تعديل وحذف ملاحظاتة - مرفقاتة ومواعيدة الخاصة</span>
                                        </div>
                                    </div>

                                    <!-- العمود الثاني -->
                                    <div class="col-md-6">
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="client_follow_up_view_notes_attachments_appointments_all" {{ $role->client_follow_up_view_notes_attachments_appointments_all == 1 ? 'checked' : '' }} type="checkbox" class="select-all-client-follow-up permission-main-checkbox customer-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">عرض جميع الملاحظات / المرفقات / المواعيد لجميع العملاء</span>
                                        </div>

                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="client_follow_up_view_notes_attachments_appointments_assigned" {{ $role->client_follow_up_view_notes_attachments_appointments_assigned == 1 ? 'checked' : '' }} type="checkbox" class="select-all-client-follow-up permission-main-checkbox customer-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">عرض جميع الملاحظات / المرفقات / المواعيد لعملائه المعينين</span>
                                        </div>

                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="client_follow_up_view_notes_attachments_appointments_own" {{ $role->client_follow_up_view_notes_attachments_appointments_own == 1 ? 'checked' : '' }} type="checkbox" class="select-all-client-follow-up permission-main-checkbox customer-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">عرض كافة ملاحظاتة / مرفقاتة / مواعيدة الخاصة</span>
                                        </div>

                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="client_follow_up_assign_clients_to_employees" {{ $role->client_follow_up_assign_clients_to_employees == 1 ? 'checked' : '' }} type="checkbox" class="select-all-client-follow-up permission-main-checkbox customer-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">تعيين العملاء إلى الموظفين</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- العملاء   -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="vs-checkbox-con vs-checkbox-primary me-3">
                                        <input type="checkbox" id="SelectAllCustomers" class="permission-main-checkbox customer-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <strong class="">العملاء</strong>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center">
                                    <span class="panel-role-active-title me-2">الصلاحيات النشطة:</span>
                                    <strong class="panel-role-active-count" data-role-count="10"><span id="activeCountCustomers">0</span>/10</strong>
                                </div>
                            </div>

                            <div class="panel-body">
                                <div class="row">
                                    <!-- العمود الأول -->
                                    <div class="col-md-6 col-lg-4">
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="clients_add_client" {{ $role->clients_add_client == 1 ? 'checked' : '' }} type="checkbox" class="select-all-customers permission-main-checkbox customer-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">إضافة عميل جديد</span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="clients_view_all_clients" {{ $role->clients_view_all_clients == 1 ? 'checked' : '' }} type="checkbox" class="select-all-customers permission-main-checkbox customer-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">عرض جميع العملاء</span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="clients_view_own_clients" {{ $role->clients_view_own_clients == 1 ? 'checked' : '' }} type="checkbox" class="select-all-customers permission-main-checkbox customer-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">عرض عملائه</span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="Edit_Client" {{ $role->Edit_Client == 1 ? 'checked' : '' }} type="checkbox" class="select-all-customers permission-main-checkbox customer-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">تعديل  العملاء </span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="Delete_Client" {{ $role->Delete_Client == 1 ? 'checked' : '' }} type="checkbox" class="select-all-customers permission-main-checkbox customer-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class=""> حذف العملاء</span>
                                        </div>
                                    </div>

                                    <!-- العمود الثاني -->
                                    <div class="col-md-6 col-lg-4">
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="clients_view_all_activity_logs" {{ $role->clients_view_all_activity_logs == 1 ? 'checked' : '' }} type="checkbox" class="select-all-customers permission-main-checkbox customer-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">عرض جميع سجلات الأنشطة</span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="clients_view_own_activity_log" {{ $role->clients_view_own_activity_log == 1 ? 'checked' : '' }} type="checkbox" class="select-all-customers permission-main-checkbox customer-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">عرض سجل نشاطه</span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="clients_edit_client_settings" {{ $role->clients_edit_client_settings == 1 ? 'checked' : '' }} type="checkbox" class="select-all-customers permission-main-checkbox customer-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">تعديل إعدادات العملاء</span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="clients_view_all_reports" {{ $role->clients_view_all_reports == 1 ? 'checked' : '' }} type="checkbox" class="select-all-customers permission-main-checkbox customer-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">عرض تقارير كل العملاء</span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="clients_view_own_reports" {{ $role->clients_view_own_reports == 1 ? 'checked' : '' }} type="checkbox" class="select-all-customers permission-main-checkbox customer-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">عرض تقارير العملاء الخاصة به</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- النقاط والأرصدة -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="vs-checkbox-con vs-checkbox-primary me-3">
                                        <input type="checkbox" id="SelectAllPointsBalances" class="permission-main-checkbox customer-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <strong class="">النقاط والأرصدة </strong>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center">
                                    <span class="panel-role-active-title me-2">الصلاحيات النشطة:</span>
                                    <strong class="panel-role-active-count" data-role-count="4"><span id="activeCountPointsBalances">0</span>/4</strong>
                                </div>
                            </div>

                            <div class="panel-body">
                                <div class="row">
                                    <!-- الزر الأول والثاني في عمود -->
                                    <div class="col-md-6 col-lg-6">
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="points_credits_packages_manage" {{ $role->points_credits_packages_manage == 1 ? 'checked' : '' }} type="checkbox" class="select-all-points-balances permission-main-checkbox customer-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">إدارة الباقات</span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="points_credits_credit_recharge_manage" {{ $role->points_credits_credit_recharge_manage == 1 ? 'checked' : '' }} type="checkbox" class="select-all-points-balances permission-main-checkbox customer-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">إدارة شحن الأرصدة</span>
                                        </div>
                                    </div>

                                    <!-- الزر الثالث والرابع في عمود -->
                                    <div class="col-md-6 col-lg-6">
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="points_credits_credit_usage_manage" {{ $role->points_credits_credit_usage_manage == 1 ? 'checked' : '' }} type="checkbox" class="select-all-points-balances permission-main-checkbox customer-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">إدارة استهلاك الأرصدة</span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="points_credits_credit_settings_manage" {{ $role->points_credits_credit_settings_manage == 1 ? 'checked' : '' }} type="checkbox" class="select-all-points-balances permission-main-checkbox customer-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">إدارة إعدادات الأرصدة</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- العضوية -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">

                                <div class="d-flex align-items-center">
                                    <div class="vs-checkbox-con vs-checkbox-primary me-3">
                                        <input type="checkbox" id="SelectAllMemberships" class="permission-main-checkbox customer-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <strong class="">العضوية </strong>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center">
                                    <span class="panel-role-active-title me-2">الصلاحيات النشطة:</span>
                                    <strong class="panel-role-active-count" data-role-count="2"><span id="activeCountMemberships">0</span>/2</strong>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="l-flex-row">

                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="membership_management" {{ $role->membership_management == 1 ? 'checked' : '' }} type="checkbox" class="select-all-memberships  permission-main-checkbox customer-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">أدارة العضويات</span>
                                    </div>

                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="membership_setting_management" {{ $role->membership_setting_management == 1 ? 'checked' : '' }} type="checkbox" class="select-all-memberships permission-main-checkbox customer-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">أدارة أعدادات العضوية</span>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- حضور العملاء -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="vs-checkbox-con vs-checkbox-primary me-3">
                                        <input type="checkbox" id="SelectAllCustomerAttendance" class="permission-main-checkbox customer-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <strong class="">حضور العملاء</strong>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="panel-role-active-title me-2">الصلاحيات النشطة:</span>
                                    <strong class="panel-role-active-count" data-role-count="2"><span id="activeCountCustomerAttendance">0</span>/2</strong>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <!-- زر عرض حضور العملاء -->
                                    <div class="col-md-6">
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="customer_attendance_display" {{ $role->customer_attendance_display == 1 ? 'checked' : '' }} type="checkbox" class="select-all-customer-attendance permission-main-checkbox customer-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">عرض حضور العملاء</span>
                                        </div>
                                    </div>

                                    <!-- زر إدارة حضور العملاء -->
                                    <div class="col-md-6">
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="customer_attendance_manage" {{ $role->customer_attendance_manage == 1 ? 'checked' : '' }} type="checkbox" class="select-all-customer-attendance permission-main-checkbox customer-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">إدارة حضور العملاء</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- الموظفين -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="vs-checkbox-con vs-checkbox-primary me-3">
                                        <input type="checkbox" id="SelectAllEmployees" class="permission-main-checkbox crm-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <strong class="">الموظفين</strong>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="panel-role-active-title me-2">الصلاحيات النشطة:</span>
                                    <strong class="panel-role-active-count" data-role-count="5"><span id="activeCountEmployees">0</span>/5</strong>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <!-- العمود الأول -->
                                    <div class="col-md-6">
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="employees_add" {{ $role->employees_add == 1 ? 'checked' : '' }} type="checkbox" class="select-all-employees permission-main-checkbox crm-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">أضافة موظف جديد</span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="employees_edit_delete" {{ $role->employees_edit_delete == 1 ? 'checked' : '' }} type="checkbox" class="select-all-employees permission-main-checkbox crm-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">تعديل وحذف موظف</span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="employees_roles_add" {{ $role->employees_roles_add == 1 ? 'checked' : '' }} type="checkbox" class="select-all-employees permission-main-checkbox crm-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">أضافة دور وظيفي جديد</span>
                                        </div>
                                    </div>

                                    <!-- العمود الثاني -->
                                    <div class="col-md-6">
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="employees_roles_edit" {{ $role->employees_roles_edit == 1 ? 'checked' : '' }} type="checkbox" class="select-all-employees permission-main-checkbox crm-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">تعديل الدور الوظيفي</span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="employees_view_profile" {{ $role->employees_view_profile == 1 ? 'checked' : '' }} type="checkbox" class="select-all-employees permission-main-checkbox crm-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">أظهار الملف الشخصي للموظف</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- الهيكل التنظيمي  -->
                <div class="col-md-12">  
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="vs-checkbox-con vs-checkbox-primary me-3">
                                        <input type="checkbox" id="SelectAllOrganizationalStructure" class="permission-main-checkbox crm-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <strong class="">الهيكل التنظيمي </strong>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center">
                                    <span class="panel-role-active-title me-2">الصلاحيات النشطة:</span>
                                    <strong class="panel-role-active-count" data-role-count="1"><span id="activeCountOrganizationalStructure">0</span>/1</strong>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="l-flex-row">

                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="hr_system_management" {{ $role->hr_system_management == 1 ? 'checked' : '' }} type="checkbox" class="select-all-organizational-structure permission-main-checkbox crm-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">أدارة نظام الموارد البشرية</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- المرتبات -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="vs-checkbox-con vs-checkbox-primary me-3">
                                        <input type="checkbox" id="SelectAllSalaries" class="permission-main-checkbox account-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <strong class="">المرتبات</strong>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="panel-role-active-title me-2">الصلاحيات النشطة:</span>
                                    <strong class="panel-role-active-count" data-role-count="17"><span id="activeCountSalaries">0</span>/17</strong>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <!-- العمود الأول -->
                                    <div class="col-md-6">
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="salaries_loans_manage" {{ $role->salaries_loans_manage == 1 ? 'checked' : '' }} type="checkbox" class="select-all-salaries permission-main-checkbox account-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">أدارة السلفيات والأقساط</span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="salaries_payroll_view" {{ $role->salaries_payroll_view == 1 ? 'checked' : '' }} type="checkbox" class="select-all-salaries permission-main-checkbox account-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">عرض مسير الرواتب</span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="salaries_payroll_create" {{ $role->salaries_payroll_create == 1 ? 'checked' : '' }} type="checkbox" class="select-all-salaries permission-main-checkbox account-checkbox  ">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">أنشاء مسير رواتب</span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="salaries_payroll_approve" {{ $role->salaries_payroll_approve == 1 ? 'checked' : '' }} type="checkbox" class="select-all-salaries permission-main-checkbox account-checkbox  ">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">موافقة قسائم الرواتب</span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="salaries_payroll_edit" {{ $role->salaries_payroll_edit == 1 ? 'checked' : '' }} type="checkbox" class="select-all-salaries permission-main-checkbox account-checkbox  ">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">تعديل قسائم الرواتب</span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="salaries_payroll_delete" {{ $role->salaries_payroll_delete == 1 ? 'checked' : '' }} type="checkbox" class="select-all-salaries permission-main-checkbox account-checkbox  ">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">مسح مدفوعات مسير الرواتب</span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="salaries_contracts_notifications" {{ $role->salaries_contracts_notifications == 1 ? 'checked' : '' }} type="checkbox" class="select-all-salaries permission-main-checkbox account-checkbox  ">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">أشعارات العقد</span>
                                        </div>

                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="salaries_contracts_edit_delete_own" {{ $role->salaries_contracts_edit_delete_own == 1 ? 'checked' : '' }} type="checkbox" class="select-all-salaries permission-main-checkbox account-checkbox  ">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">تعديل / مسح العقود الخاصة به</span>
                                        </div>
                                    </div>

                                    <!-- العمود الثاني -->
                                    <div class="col-md-6">
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="salaries_payroll_settings_manage" {{ $role->salaries_payroll_settings_manage == 1 ? 'checked' : '' }} type="checkbox" class="select-all-salaries permission-main-checkbox account-checkbox  ">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">أدارة أعدادات المرتبات</span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="salaries_payroll_view_own" {{ $role->salaries_payroll_view_own == 1 ? 'checked' : '' }} type="checkbox" class="select-all-salaries permission-main-checkbox account-checkbox  ">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">عرض قسيمة الراتب الخاصة بة</span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="salaries_payroll_delete_all" {{ $role->salaries_payroll_delete_all == 1 ? 'checked' : '' }} type="checkbox" class="select-all-salaries permission-main-checkbox account-checkbox  ">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">مسح مسير الرواتب</span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="salaries_payroll_payment" {{ $role->salaries_payroll_payment == 1 ? 'checked' : '' }} type="checkbox" class="select-all-salaries permission-main-checkbox account-checkbox  ">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">دفع قسائم الرواتب</span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="salaries_contracts_receive_notifications" {{ $role->salaries_contracts_receive_notifications == 1 ? 'checked' : '' }} type="checkbox" class="select-all-salaries permission-main-checkbox account-checkbox  ">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">أستلام أشعارات العقود</span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="salaries_contracts_view_all" {{ $role->salaries_contracts_view_all == 1 ? 'checked' : '' }} type="checkbox" class="select-all-salaries permission-main-checkbox account-checkbox  ">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">عرض جميع العقود</span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="salaries_contracts_edit_delete_all" {{ $role->salaries_contracts_edit_delete_all == 1 ? 'checked' : '' }} type="checkbox" class="select-all-salaries permission-main-checkbox account-checkbox  ">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">تعديل /مسح جميع العقود</span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="salaries_contracts_create" {{ $role->salaries_contracts_create == 1 ? 'checked' : '' }} type="checkbox" class="select-all-salaries permission-main-checkbox account-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">أنشاء عقود</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- حضور الموظفين -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div
                                class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="vs-checkbox-con vs-checkbox-primary me-3">
                                        <input type="checkbox" id="SelectAllStaffAttendance" class="permission-main-checkbox crm-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <strong class="">حضور الموظفين </strong>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="panel-role-active-title me-2">الصلاحيات النشطة:</span>
                                    <strong class="panel-role-active-count" data-role-count="23"><span id="activeCountStaffAttendance">0</span>/23</strong>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="l-flex-row">
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="staff_attendance_online" {{ $role->staff_attendance_online == 1 ? 'checked' : '' }} type="checkbox" class="select-all-staff-attendance permission-main-checkbox  crm-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">
                                        تسجيل حضور الموظفين (اونلاين)</span>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="l-flex-row">
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="staff_attendance_pull_from_device" {{ $role->staff_attendance_pull_from_device == 1 ? 'checked' : '' }} type="checkbox" class="select-all-staff-attendance permission-main-checkbox crm-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">
                                        سحب سجل الحضور من الجهاز</span>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="l-flex-row">
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="staff_attendance_view_all" {{ $role->staff_attendance_view_all == 1 ? 'checked' : '' }} type="checkbox" class="select-all-staff-attendance permission-main-checkbox crm-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">عرض كل سجلات الحضور</span>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="l-flex-row">
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="staff_attendance_settings_manage" {{ $role->staff_attendance_settings_manage == 1 ? 'checked' : '' }} type="checkbox" class="select-all-staff-attendance permission-main-checkbox crm-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">أدارة أعدادات الحضور</span>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="l-flex-row">
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="staff_attendance_delete" {{ $role->staff_attendance_delete == 1 ? 'checked' : '' }} type="checkbox" class="select-all-staff-attendance permission-main-checkbox crm-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">مسح سجل الحضور</span>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="l-flex-row">
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="staff_attendance_edit_days" {{ $role->staff_attendance_edit_days == 1 ? 'checked' : '' }} type="checkbox" class="select-all-staff-attendance permission-main-checkbox crm-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class=""> تعديل أيام الحضور  </span>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="l-flex-row">
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="staff_attendance_view_own" {{ $role->staff_attendance_view_own == 1 ? 'checked' : '' }} type="checkbox" class="select-all-staff-attendance permission-main-checkbox crm-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class=""> عرض دفاتر الحضور الخاصة بة</span>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="l-flex-row">
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="staff_attendance_change_status" {{ $role->staff_attendance_change_status == 1 ? 'checked' : '' }} type="checkbox" class="select-all-staff-attendance permission-main-checkbox crm-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">تغيير حالة دفاتر الحضور</span>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="l-flex-row">
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="staff_attendance_report_view" {{ $role->staff_attendance_report_view == 1 ? 'checked' : '' }} type="checkbox" class="select-all-staff-attendance permission-main-checkbox crm-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">عرض تقرير الحضور</span>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="l-flex-row">
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="staff_leave_requests_edit_delete_own" {{ $role->staff_leave_requests_edit_delete_own == 1 ? 'checked' : '' }} type="checkbox" class="select-all-staff-attendance permission-main-checkbox crm-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">  تعديل / حذف طلبات أجازاتة فقط</span>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="l-flex-row">
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="staff_leave_requests_view_own" {{ $role->staff_leave_requests_view_own == 1 ? 'checked' : '' }} type="checkbox" class="select-all-staff-attendance permission-main-checkbox crm-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">عرض طلبات أجازاته فقط</span>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="l-flex-row">
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="staff_leave_requests_approve_reject" {{ $role->staff_leave_requests_approve_reject == 1 ? 'checked' : '' }} type="checkbox" class="select-all-staff-attendance permission-main-checkbox crm-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">الموافقة على / رفض طلبات الأجازة</span>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="l-flex-row">
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="staff_attendance_self_registration" {{ $role->staff_attendance_self_registration == 1 ? 'checked' : '' }} type="checkbox" class="select-all-staff-attendance permission-main-checkbox crm-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">تسجيل الحظور بنفسة أون لاين</span>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="l-flex-row">
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="staff_attendance_import" {{ $role->staff_attendance_import == 1 ? 'checked' : '' }} type="checkbox" class="select-all-staff-attendance permission-main-checkbox crm-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">أستيراد سجل الحضور</span>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="l-flex-row">
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="staff_attendance_view_own_records" {{ $role->staff_attendance_view_own_records == 1 ? 'checked' : '' }} type="checkbox" class="select-all-staff-attendance permission-main-checkbox crm-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">عرض كل سجلات الحضور الخاصة به</span>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="l-flex-row">
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="staff_inventory_permissions_manage" {{ $role->staff_inventory_permissions_manage == 1 ? 'checked' : '' }} type="checkbox" class="select-all-staff-attendance permission-main-checkbox crm-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">أدارة الأذونات المخزنية </span>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="l-flex-row">
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="staff_attendance_calculate_days" {{ $role->staff_attendance_calculate_days == 1 ? 'checked' : '' }} type="checkbox" class="select-all-staff-attendance permission-main-checkbox crm-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">حساب أيام الحضور</span>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="l-flex-row">
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="staff_attendance_create_book" {{ $role->staff_attendance_create_book == 1 ? 'checked' : '' }} type="checkbox" class="select-all-staff-attendance permission-main-checkbox crm-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">أنشاء دفتر حضور</span>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="l-flex-row">
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="staff_attendance_view_other_books" {{ $role->staff_attendance_view_other_books == 1 ? 'checked' : '' }} type="checkbox" class="select-all-staff-attendance permission-main-checkbox crm-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">عرض دفاتر الحضور الأخرى</span>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="l-flex-row">
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="staff_attendance_delete_books" {{ $role->staff_attendance_delete_books == 1 ? 'checked' : '' }} type="checkbox" class="select-all-staff-attendance permission-main-checkbox crm-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">مسح دفاتر الحضور</span>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="l-flex-row">
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="staff_leave_requests_add" {{ $role->staff_leave_requests_add == 1 ? 'checked' : '' }} type="checkbox" class="select-all-staff-attendance permission-main-checkbox crm-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">أضافة طلب أجازة</span>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="l-flex-row">
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="staff_leave_requests_edit_delete_all" {{ $role->staff_leave_requests_edit_delete_all == 1 ? 'checked' : '' }} type="checkbox" class="select-all-staff-attendance permission-main-checkbox crm-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">تعديل/وحذف جميع طلبات الأجازات</span>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="l-flex-row">
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="staff_leave_requests_view_all" {{ $role->staff_leave_requests_view_all == 1 ? 'checked' : '' }} type="checkbox" class="select-all-staff-attendance permission-main-checkbox crm-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">عرض جميع طلبات الأجازة</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- الطلبات -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="vs-checkbox-con vs-checkbox-primary me-3">
                                        <input type="checkbox" id="SelectAllOrders" class="permission-main-checkbox sales-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <strong class="">الطلبات</strong>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center">
                                    <span class="panel-role-active-title me-2">الصلاحيات النشطة:</span>
                                    <strong class="panel-role-active-count" data-role-count="2"><span id="activeCountOrders">0</span>/2</strong>
                                </div>
                            </div>

                            <div class="panel-body">
                                <div class="d-flex flex-wrap">
                                    <div class="vs-checkbox-con vs-checkbox-primary me-1">
                                        <input name="orders_management" {{ $role->orders_management == 1 ? 'checked' : '' }} type="checkbox" class="select-all-orders permission-main-checkbox sales-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">أدارة الطلبات</span>
                                    </div>

                                    <div class="vs-checkbox-con vs-checkbox-primary me-1">
                                        <input name="orders_setting_management" {{ $role->orders_setting_management == 1 ? 'checked' : '' }} type="checkbox" class="select-all-orders permission-main-checkbox sales-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">أدارة أعدادات الطلبات</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- أدارة المخزون -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div
                                class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="vs-checkbox-con vs-checkbox-primary me-3">
                                        <input type="checkbox" id="SelectAllInventoryManagement" class="permission-main-checkbox store-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <strong class="">أدارة المخزون </strong>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="panel-role-active-title me-2">الصلاحيات النشطة:</span>
                                    <strong class="panel-role-active-count" data-role-count="19"><span id="activeCountInventoryManagement">0</span>/19</strong>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="l-flex-row">
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="inv_manage_inventory_permission_add" {{ $role->inv_manage_inventory_permission_add == 1 ? 'checked' : '' }} type="checkbox" class="select-all-inventory-management permission-main-checkbox store-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">أضافة أذن مخزني</span>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="l-flex-row">
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="inv_manage_inventory_permission_view" {{ $role->inv_manage_inventory_permission_view == 1 ? 'checked' : '' }} type="checkbox" class="select-all-inventory-management permission-main-checkbox store-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">عرض الأذن المخزني</span>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="l-flex-row">
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="inv_manage_inventory_price_edit" {{ $role->inv_manage_inventory_price_edit == 1 ? 'checked' : '' }} type="checkbox" class="select-all-inventory-management permission-main-checkbox store-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">تعديل سعر حركة المخزون</span>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="l-flex-row">
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="inv_manage_purchase_invoices_view_own" {{ $role->inv_manage_purchase_invoices_view_own == 1 ? 'checked' : '' }} type="checkbox" class="select-all-inventory-management permission-main-checkbox store-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">عرض فواتيرالشراءالخاصة به</span>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="l-flex-row">
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="inv_manage_suppliers_add" {{ $role->inv_manage_suppliers_add == 1 ? 'checked' : '' }} type="checkbox" class="select-all-inventory-management permission-main-checkbox store-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">أضافة موردين جدد</span>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="l-flex-row">
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="inv_manage_suppliers_view_all" {{ $role->inv_manage_suppliers_view_all == 1 ? 'checked' : '' }} type="checkbox" class="select-all-inventory-management permission-main-checkbox store-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">عرض كل الموردين</span>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="l-flex-row">
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="inv_manage_suppliers_edit_delete_all" {{ $role->inv_manage_suppliers_edit_delete_all == 1 ? 'checked' : '' }} type="checkbox" class="select-all-inventory-management permission-main-checkbox store-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">تعديل وحذف كل الموردين</span>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="l-flex-row">
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="inv_manage_inventory_edit_quantity" {{ $role->inv_manage_inventory_edit_quantity == 1 ? 'checked' : '' }} type="checkbox" class="select-all-inventory-management permission-main-checkbox store-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">تعديل عدد المنتجات بالمخزون</span>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="l-flex-row">
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="inv_manage_inventory_transfer" {{ $role->inv_manage_inventory_transfer == 1 ? 'checked' : '' }} type="checkbox" class="select-all-inventory-management permission-main-checkbox store-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">نقل المخزون</span>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="l-flex-row">
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="inv_manage_inventory_permission_edit" {{ $role->inv_manage_inventory_permission_edit == 1 ? 'checked' : '' }} type="checkbox" class="select-all-inventory-management permission-main-checkbox store-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">تعديل الأذن المخزني</span>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="l-flex-row">
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="inv_manage_inventory_view_price" {{ $role->inv_manage_inventory_view_price == 1 ? 'checked' : '' }} type="checkbox" class="select-all-inventory-management permission-main-checkbox store-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">عرض سعر حركة المخزون</span>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="l-flex-row">
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="inv_manage_purchase_invoice_add" {{ $role->inv_manage_purchase_invoice_add == 1 ? 'checked' : '' }} type="checkbox" class="select-all-inventory-management permission-main-checkbox store-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">أضافة فاتورة شراء جديدة</span>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="l-flex-row">
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="inv_manage_purchase_invoice_edit_delete_own" {{ $role->inv_manage_purchase_invoice_edit_delete_own == 1 ? 'checked' : '' }} type="checkbox" class="select-all-inventory-management permission-main-checkbox store-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="" >تعديل أو حذف  فواتير الشراء الخاصة بة </span>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="l-flex-row">
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="inv_manage_purchase_invoice_edit_delete_all" {{ $role->inv_manage_purchase_invoice_edit_delete_all == 1 ? 'checked' : '' }} type="checkbox" class="select-all-inventory-management permission-main-checkbox store-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">تعديل أو حذف كل فواتير الشراء</span>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="l-flex-row">
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="inv_manage_purchase_invoices_view_all" {{ $role->inv_manage_purchase_invoices_view_all == 1 ? 'checked' : '' }} type="checkbox" class="select-all-inventory-management permission-main-checkbox store-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">عرض كل فواتير الشراء</span>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="l-flex-row">
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="inv_manage_suppliers_view_created" {{ $role->inv_manage_suppliers_view_created == 1 ? 'checked' : '' }} type="checkbox" class="select-all-inventory-management permission-main-checkbox store-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">عرض الموردين الذين تم أنشائهم</span>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="l-flex-row">
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="inv_manage_allow_sale_below_min_price" {{ $role->inv_manage_allow_sale_below_min_price == 1 ? 'checked' : '' }} type="checkbox" class="select-all-inventory-management permission-main-checkbox store-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">السماح للبيع بأقل من السعر الأدنى للمنتج</span>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="l-flex-row">
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="inv_manage_inventory_monitor" {{ $role->inv_manage_inventory_monitor == 1 ? 'checked' : '' }} type="checkbox" class="select-all-inventory-management permission-main-checkbox store-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">متابعة المخزون</span>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="l-flex-row">
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="inv_manage_suppliers_edit_delete_own" {{ $role->inv_manage_suppliers_edit_delete_own == 1 ? 'checked' : '' }} type="checkbox" class="select-all-inventory-management permission-main-checkbox store-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">تعديل وحذف الموردين الخاصين به</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- دورة المشتريات -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="vs-checkbox-con vs-checkbox-primary me-3"> 
                                        <input type="checkbox" id="SelectAllProcurementCycle" class="permission-main-checkbox store-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <strong class="">دورة المشتريات</strong>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center">
                                    <span class="panel-role-active-title me-2">الصلاحيات النشطة:</span>
                                    <strong class="panel-role-active-count" data-role-count="7"><span id="activeCountProcurementCycle">0</span>/7</strong>
                                </div>
                            </div>

                            <div class="panel-body">
                                <div class="row">
                                    <!-- العمود الأول -->
                                    <div class="col-md-6 col-lg-4">
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="purchase_cycle_orders_manage" {{ $role->purchase_cycle_orders_manage == 1 ? 'checked' : '' }} type="checkbox" class="select-all-procurement-cycle permission-main-checkbox store-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">إدارة طلبات الشراء</span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="purchase_cycle_quotes_manage" {{ $role->purchase_cycle_quotes_manage == 1 ? 'checked' : '' }} type="checkbox" class="select-all-procurement-cycle permission-main-checkbox store-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">إدارة عروض أسعار المشتريات</span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="purchase_cycle_quotes_to_orders" {{ $role->purchase_cycle_quotes_to_orders == 1 ? 'checked' : '' }} type="checkbox" class="select-all-procurement-cycle permission-main-checkbox store-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">تحويل عروض أسعار المشتريات الي أوامر الشراء</span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="purchase_cycle_order_to_invoice" {{ $role->purchase_cycle_order_to_invoice == 1 ? 'checked' : '' }} type="checkbox" class="select-all-procurement-cycle permission-main-checkbox store-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">تحويل امر الشراء الي فاتورة شراء</span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="purchase_cycle_orders_approve_reject" {{ $role->purchase_cycle_orders_approve_reject == 1 ? 'checked' : '' }} type="checkbox" class="select-all-procurement-cycle permission-main-checkbox store-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">موافقة/رفض طلبات الشراء</span>
                                        </div>
                                    </div>

                                    <!-- العمود الثاني -->
                                    <div class="col-md-6 col-lg-4">
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="purchase_cycle_quotes_approve_reject" {{ $role->purchase_cycle_quotes_approve_reject == 1 ? 'checked' : '' }} type="checkbox" class="select-all-procurement-cycle permission-main-checkbox store-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">موافقة/رفض عروض أسعار المشتريات</span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="purchase_cycle_orders_manage_orders" {{ $role->purchase_cycle_orders_manage_orders == 1 ? 'checked' : '' }} type="checkbox" class="select-all-procurement-cycle permission-main-checkbox store-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">أدارة أوامر الشراء</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- أدارة أوامر التوريد -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">

                                <div class="d-flex align-items-center">
                                    <div class="vs-checkbox-con vs-checkbox-primary me-3">
                                        <input type="checkbox" id="SelectAllSupplyOrderManagement" class="permission-main-checkbox account-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <strong class="">أدارة أوامر التوريد </strong>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center">
                                    <span class="panel-role-active-title me-2">الصلاحيات النشطة:</span>
                                    <strong class="panel-role-active-count" data-role-count="6"><span id="activeCountSupplyOrderManagement">0</span>/6</strong>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="l-flex-row">

                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="supply_orders_view_all" {{ $role->supply_orders_view_all == 1 ? 'checked' : '' }} type="checkbox" class="select-all-supply-order-management permission-main-checkbox account-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">عرض جميع أوامر التوريد  </span>
                                    </div>
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="supply_orders_add" {{ $role->supply_orders_add == 1 ? 'checked' : '' }} type="checkbox" class="select-all-supply-order-management permission-main-checkbox account-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">أضافة أوامر شغل </span>
                                    </div>
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="supply_orders_edit_delete_all" {{ $role->supply_orders_edit_delete_all == 1 ? 'checked' : '' }} type="checkbox" class="select-all-supply-order-management permission-main-checkbox account-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">تعديل وحذف جميع اوامر التوريد </span>
                                    </div>
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="supply_orders_edit_delete_own" {{ $role->supply_orders_edit_delete_own == 1 ? 'checked' : '' }} type="checkbox" class="select-all-supply-order-management permission-main-checkbox account-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">تعديل وحذف أوامر التوريد الخاصة به </span>
                                    </div>
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="supply_orders_update_status" {{ $role->supply_orders_update_status == 1 ? 'checked' : '' }} type="checkbox" class="select-all-supply-order-management permission-main-checkbox account-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">حدث حالة أمر التوريد</span>
                                    </div>
                                    <div class="panel-body">
                                        <div class="l-flex-row">
                                            <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                                <input name="supply_orders_view_own" {{ $role->supply_orders_view_own == 1 ? 'checked' : '' }} type="checkbox" class="select-all-supply-order-management permission-main-checkbox account-checkbox">
                                                <span class="vs-checkbox">
                                                    <span class="vs-checkbox--check">
                                                        <i class="vs-icon feather icon-check"></i>
                                                    </span>
                                                </span>
                                                <span class="">عرض أوامر التوريد الخاصة به</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!--  تتبع الوقت -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="vs-checkbox-con vs-checkbox-primary me-3">
                                        <input type="checkbox" id="SelectAllTrackTime" class="permission-main-checkbox operating-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <strong class=""> تتبع الوقت</strong>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center">
                                    <span class="panel-role-active-title me-2">الصلاحيات النشطة:</span>
                                    <strong class="panel-role-active-count" data-role-count="7"><span id="activeCountTrackTime">0</span>/7</strong>
                                </div>
                            </div>

                            <div class="panel-body">
                                <div class="row">
                                    <!-- العمود الأول -->
                                    <div class="col-md-6 col-lg-4">
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">      
                                            <input name="track_time_add_employee_work_hours" {{ $role->track_time_add_employee_work_hours == 1 ? 'checked' : '' }} type="checkbox" class="select-all-track-time permission-main-checkbox operating-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">أضافة ساعات عملة  </span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="track_time_edit_other_employees_work_hours" {{ $role->track_time_edit_other_employees_work_hours == 1 ? 'checked' : '' }} type="checkbox" class="select-all-track-time permission-main-checkbox operating-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">تعديل ساعات عمل الموظفين الأخرين</span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="track_time_edit_delete_all_projects" {{ $role->track_time_edit_delete_all_projects == 1 ? 'checked' : '' }} type="checkbox" class="select-all-track-time permission-main-checkbox operating-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">تعديل وحذف كل المشاريع</span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="track_time_edit_delete_all_activities" {{ $role->track_time_edit_delete_all_activities == 1 ? 'checked' : '' }} type="checkbox" class="select-all-track-time permission-main-checkbox operating-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">تعديل وحذف كل الأنشطة</span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="track_time_view_other_employees_work_hours" {{ $role->track_time_view_other_employees_work_hours == 1 ? 'checked' : '' }} type="checkbox" class="select-all-track-time permission-main-checkbox operating-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">عرض ساعات عمل الموظفين الأخرين</span>
                                        </div>
                                    </div>

                                    <!-- العمود الثاني -->
                                    <div class="col-md-6 col-lg-4">
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="track_time_add_new_project" {{ $role->track_time_add_new_project == 1 ? 'checked' : '' }} type="checkbox" class="select-all-track-time permission-main-checkbox operating-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">أضافة مشروع جديد</span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="track_time_add_new_activity" {{ $role->track_time_add_new_activity == 1 ? 'checked' : '' }} type="checkbox" class="select-all-track-time permission-main-checkbox operating-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">أضافة نشاط جديد</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- أدارة الوحدات والأجارات  -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="vs-checkbox-con vs-checkbox-primary me-3">
                                        <input type="checkbox" id="SelectAllRentalUnitManagement" class="permission-main-checkbox account-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <strong class=""> أدارة الأجارات والوحدات </strong>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center">
                                    <span class="panel-role-active-title me-2">الصلاحيات النشطة:</span>
                                    <strong class="panel-role-active-count" data-role-count="3"><span id="activeCountRentalUnitManagement">0</span>/3</strong>
                                </div>
                            </div>

                            <div class="panel-body">
                                <div class="row">
                                    <!-- الزر الأول والثاني في عمود -->
                                    <div class="col-md-6 col-lg-6">
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="rental_unit_manage_rental_settings" {{ $role->rental_unit_manage_rental_settings == 1 ? 'checked' : '' }} type="checkbox" class="select-all-rental-unit-management permission-main-checkbox account-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">إدارة وأعدادات الأيجارات</span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="rental_unit_manage_booking_orders" {{ $role->rental_unit_manage_booking_orders == 1 ? 'checked' : '' }} type="checkbox" class="select-all-rental-unit-management permission-main-checkbox account-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">ادارة أوامر الحجز</span>
                                        </div>
                                    </div>

                                    <!-- الزر الثالث والرابع في عمود -->
                                    <div class="col-md-6 col-lg-6">
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="rental_unit_view_booking_orders" {{ $role->rental_unit_view_booking_orders == 1 ? 'checked' : '' }} type="checkbox" class="select-all-rental-unit-management permission-main-checkbox account-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">عرض أوامر الحجز</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- الحسابات العامه & القيود اليومية -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="vs-checkbox-con vs-checkbox-primary me-3">
                                        <input type="checkbox" id="SelectAllGeneralAccountsDailyRestrictions" class="permission-main-checkbox account-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <strong class="">الحسابات العامه & القيود اليومية</strong>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center">
                                    <span class="panel-role-active-title me-2">الصلاحيات النشطة:</span>
                                    <strong class="panel-role-active-count" data-role-count="11"><span id="activeCountGeneralAccountsDailyRestrictions">0</span>/11</strong>
                                </div>
                            </div>

                            <div class="panel-body">
                                <div class="row">
                                    <!-- العمود الأول -->
                                    <div class="col-md-6 col-lg-4">
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="g_a_d_r_add_new_assets" {{ $role->g_a_d_r_add_new_assets == 1 ? 'checked' : '' }} type="checkbox" class="select-all-general-accounts-daily-restrictions permission-main-checkbox account-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">اضافة اصول جديدة</span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="g_a_d_r_view_cost_centers" {{ $role->g_a_d_r_view_cost_centers == 1 ? 'checked' : '' }} type="checkbox" class="select-all-general-accounts-daily-restrictions permission-main-checkbox account-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">عرض مراكز التكلفة</span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="g_a_d_r_manage_cost_centers" {{ $role->g_a_d_r_manage_cost_centers == 1 ? 'checked' : '' }} type="checkbox" class="select-all-general-accounts-daily-restrictions permission-main-checkbox account-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">إدارة مراكز التكلفة</span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="g_a_d_r_manage_closed_periods" {{ $role->g_a_d_r_manage_closed_periods == 1 ? 'checked' : '' }} type="checkbox" class="select-all-general-accounts-daily-restrictions permission-main-checkbox account-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">إداراة الفترات المقفلة</span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="g_a_d_r_view_closed_periods" {{ $role->g_a_d_r_view_closed_periods == 1 ? 'checked' : '' }} type="checkbox" class="select-all-general-accounts-daily-restrictions permission-main-checkbox account-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">عرض الفترات المقفلة</span>
                                        </div>
                                    </div>

                                    <!-- العمود الثاني -->
                                    <div class="col-md-6 col-lg-4">
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="g_a_d_r_manage_journal_entries" {{ $role->g_a_d_r_manage_journal_entries == 1 ? 'checked' : '' }} type="checkbox" class="select-all-general-accounts-daily-restrictions permission-main-checkbox account-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">إدارة حسابات القيود</span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="g_a_d_r_view_all_journal_entries" {{ $role->g_a_d_r_view_all_journal_entries == 1 ? 'checked' : '' }} type="checkbox" class="select-all-general-accounts-daily-restrictions permission-main-checkbox account-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">عرض جميع القيود</span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="g_a_d_r_view_own_journal_entries" {{ $role->g_a_d_r_view_own_journal_entries == 1 ? 'checked' : '' }} type="checkbox" class="select-all-general-accounts-daily-restrictions permission-main-checkbox account-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">عرض القيود الخاصة به</span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="g_a_d_r_add_edit_delete_all_journal_entries" {{ $role->g_a_d_r_add_edit_delete_all_journal_entries == 1 ? 'checked' : '' }} type="checkbox" class="select-all-general-accounts-daily-restrictions permission-main-checkbox account-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">إضافة/تعديل/مسح جميع القيود</span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="g_a_d_r_add_edit_delete_own_journal_entries" {{ $role->g_a_d_r_add_edit_delete_own_journal_entries == 1 ? 'checked' : '' }} type="checkbox" class="select-all-general-accounts-daily-restrictions permission-main-checkbox account-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">إضافة/تعديل/مسح القيود الخاصة به</span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="g_a_d_r_add_edit_delete_draft_journal_entries" {{ $role->g_a_d_r_add_edit_delete_draft_journal_entries == 1 ? 'checked' : '' }} type="checkbox" class="select-all-general-accounts-daily-restrictions permission-main-checkbox account-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">إضافة/تعديل/مسح القيود المسودة</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- المالية -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="vs-checkbox-con vs-checkbox-primary me-3">
                                        <input type="checkbox" id="SelectAllFinance" class="permission-main-checkbox account-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <strong class="">المالية</strong>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center">
                                    <span class="panel-role-active-title me-2">الصلاحيات النشطة:</span>
                                    <strong class="panel-role-active-count" data-role-count="15"><span id="activeCountFinance">0</span>/15</strong>
                                </div>
                            </div>

                            <div class="panel-body">
                                <div class="row">
                                    <!-- العمود الأول -->
                                    <div class="col-md-6 col-lg-4">
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="finance_add_expense" {{ $role->finance_add_expense == 1 ? 'checked' : '' }} type="checkbox" class="select-all-finance permission-main-checkbox account-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">إضافة مصروف</span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="finance_edit_delete_all_expenses" {{ $role->finance_edit_delete_all_expenses == 1 ? 'checked' : '' }} type="checkbox" class="select-all-finance permission-main-checkbox account-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">تعديل وحذف كل المصروفات</span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="finance_edit_delete_own_expenses" {{ $role->finance_edit_delete_own_expenses == 1 ? 'checked' : '' }} type="checkbox" class="select-all-finance permission-main-checkbox account-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">تعديل وحذف المصروفات الخاصة به</span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="finance_view_all_expenses" {{ $role->finance_view_all_expenses == 1 ? 'checked' : '' }} type="checkbox" class="select-all-finance permission-main-checkbox account-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">مشاهدة كل المصروفات</span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="finance_view_own_expenses" {{ $role->finance_view_own_expenses == 1 ? 'checked' : '' }} type="checkbox" class="select-all-finance permission-main-checkbox account-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">مشاهدة المصروفات التي أنشأها</span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="finance_add_edit_delete_draft_expenses" {{ $role->finance_add_edit_delete_draft_expenses == 1 ? 'checked' : '' }} type="checkbox" class="select-all-finance permission-main-checkbox account-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">إضافة/تعديل/مسح مصروفات مسودة</span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="finance_edit_default_cashbox" {{ $role->finance_edit_default_cashbox == 1 ? 'checked' : '' }} type="checkbox" class="select-all-finance permission-main-checkbox account-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">تعديل الخزينة الافتراضية</span>
                                        </div>
                                    </div>

                                    <!-- العمود الثاني -->
                                    <div class="col-md-6 col-lg-4">
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="finance_view_own_cashboxes" {{ $role->finance_view_own_cashboxes == 1 ? 'checked' : '' }} type="checkbox" class="select-all-finance permission-main-checkbox account-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">عرض خزائنه الخاصة</span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="finance_add_revenue" {{ $role->finance_add_revenue == 1 ? 'checked' : '' }} type="checkbox" class="select-all-finance permission-main-checkbox account-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">إضافة إيراد</span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="finance_edit_delete_all_receipts" {{ $role->finance_edit_delete_all_receipts == 1 ? 'checked' : '' }} type="checkbox" class="select-all-finance permission-main-checkbox account-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">تعديل وحذف كل سندات القبض</span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="finance_edit_delete_own_receipts" {{ $role->finance_edit_delete_own_receipts == 1 ? 'checked' : '' }} type="checkbox" class="select-all-finance permission-main-checkbox account-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">تعديل وحذف سندات القبض الخاص به</span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="finance_view_all_receipts" {{ $role->finance_view_all_receipts == 1 ? 'checked' : '' }} type="checkbox" class="select-all-finance permission-main-checkbox account-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">عرض كل سندات القبض</span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="finance_view_own_receipts" {{ $role->finance_view_own_receipts == 1 ? 'checked' : '' }} type="checkbox" class="select-all-finance permission-main-checkbox account-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">عرض سندات القبض التي أنشأها</span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="finance_add_edit_delete_draft_revenue" {{ $role->finance_add_edit_delete_draft_revenue == 1 ? 'checked' : '' }} type="checkbox" class="select-all-finance permission-main-checkbox account-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">إضافة/تعديل/مسح إيرادات مسودة</span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="finance_add_revenue_expense_category" {{ $role->finance_add_revenue_expense_category == 1 ? 'checked' : '' }} type="checkbox" class="select-all-finance permission-main-checkbox account-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">إضافة تصنيف إيرادات/مصروفات</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- دورة الشيكات -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="vs-checkbox-con vs-checkbox-primary me-3">
                                        <input type="checkbox" id="SelectAllCheckCycle" class="permission-main-checkbox account-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <strong class="">دورة الشيكات</strong>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center">
                                    <span class="panel-role-active-title me-2">الصلاحيات النشطة:</span>
                                    <strong class="panel-role-active-count" data-role-count="4"><span id="activeCountCheckCycle">0</span>/4</strong>
                                </div>
                            </div>

                            <div class="panel-body">
                                <div class="row">
                                    <!-- العمود الأول -->
                                    <div class="col-md-6 col-lg-4">
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="check_cycle_add_checkbook" {{ $role->check_cycle_add_checkbook == 1 ? 'checked' : '' }} type="checkbox" class="select-all-check-cycle permission-main-checkbox account-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">إضافة دفتر الشيكات</span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="check_cycle_view_checkbook" {{ $role->check_cycle_view_checkbook == 1 ? 'checked' : '' }} type="checkbox" class="select-all-check-cycle permission-main-checkbox account-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">عرض دفتر الشيكات</span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="check_cycle_edit_delete_checkbook" {{ $role->check_cycle_edit_delete_checkbook == 1 ? 'checked' : '' }} type="checkbox" class="select-all-check-cycle permission-main-checkbox account-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">تعديل وحذف دفتر الشيكات</span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="check_cycle_manage_received_checks" {{ $role->check_cycle_manage_received_checks == 1 ? 'checked' : '' }} type="checkbox" class="select-all-check-cycle permission-main-checkbox account-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">إدارة الشيكات المستلمة</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- الإعدادات -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="vs-checkbox-con vs-checkbox-primary me-3">
                                        <input type="checkbox" id="SelectAllSettings" class="permission-main-checkbox setting-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <strong class="">الإعدادات</strong>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center">
                                    <span class="panel-role-active-title me-2">الصلاحيات النشطة:</span>
                                    <strong class="panel-role-active-count" data-role-count="3"><span id="activeCountSettings">0</span>/3</strong>
                                </div>
                            </div>

                            <div class="panel-body">
                                <div class="row">
                                    <!-- العمود الأول -->
                                    <div class="col-md-6 col-lg-4">
                                    
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="settings_edit_general_settings" {{ $role->settings_edit_general_settings == 1 ? 'checked' : '' }} type="checkbox" class="select-all-settings permission-main-checkbox setting-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">تعديل الإعدادات العامه</span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="settings_edit_tax_settings" {{ $role->view_notifications == 1 ? 'checked' : '' }} type="checkbox" class="select-all-settings permission-main-checkbox setting-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">تعديل إعدادات الضرائب</span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="settings_view_own_reports" {{ $role->settings_view_own_reports == 1 ? 'checked' : '' }} type="checkbox" class="select-all-settings permission-main-checkbox setting-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">عرض تقاريره الخاصة</span>
                                        </div>
                                        {{-- // الفروع  --}}
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="branches" {{ $role->branches == 1 ? 'checked' : '' }} type="checkbox" class="select-all-settings permission-main-checkbox setting-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">عرض اشعارات النظام</span>
                                        </div>
                                        {{-- //القوالب  --}}
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="templates" {{ $role->templates == 1 ? 'checked' : '' }} type="checkbox" class="select-all-settings permission-main-checkbox setting-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">القوالب</span>
                                        </div>
                                        {{-- //دورة العمل  --}}
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="work_cycle" {{ $role->work_cycle == 1 ? 'checked' : '' }} type="checkbox" class="select-all-settings permission-main-checkbox setting-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">دورة العمل</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- المتجر الألكتروني -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="vs-checkbox-con vs-checkbox-primary me-3">
                                        <input type="checkbox" id="SelectAllOnlineStore" class="permission-main-checkbox setting-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <strong class="">المتجر الألكتروني </strong>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center">
                                    <span class="panel-role-active-title me-2">الصلاحيات النشطة:</span>
                                    <strong class="panel-role-active-count" data-role-count="1"><span id="activeCountOnlineStore">0</span>/1</strong>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="l-flex-row">

                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="online_store_content_management" {{ $role->online_store_content_management == 1 ? 'checked' : '' }} type="checkbox" class="select-all-online-store permission-main-checkbox setting-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">أدارة محتوى المتجر الألكتروني</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div><!--End User Content-->
            <!------------------------------------------------------------------------------------------------->
            <div id="employeeContent" style="display: none"><!--End Employee Content-->
                <!--  المرتبات -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="vs-checkbox-con vs-checkbox-primary me-3">
                                        <input type="checkbox" id="SelectAllSalariesEmployee">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <strong class="">المرتبات</strong>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center">
                                    <span class="panel-role-active-title me-2">الصلاحيات النشطة:</span>
                                    <strong class="panel-role-active-count" data-role-count="1"><span id="activeCountSalariesEmployee">0</span>/1</strong>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="l-flex-row">

                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="employee_view_his_salary_slip" {{ $role->employee_view_his_salary_slip == 1 ? 'checked' : '' }} type="checkbox" class="select-all-salaries-employee">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">عرض قسيمة الراتب الخاصة به</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- حضور الموظفين-->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="vs-checkbox-con vs-checkbox-primary me-3">
                                        <input type="checkbox" id="SelectAllStaffAttendanceEmployee">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <strong class="">حضور الموظفين</strong>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center">
                                    <span class="panel-role-active-title me-2">الصلاحيات النشطة:</span>
                                    <strong class="panel-role-active-count" data-role-count="6"><span id="activeCountStaffAttendanceEmployee">0</span>/6</strong>
                                </div>
                            </div>

                            <div class="row">
                                <!-- العمود الأول -->
                                <div class="col-md-6">
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-2">
                                        <input name="employee_staffmark_own_attendance_online" {{ $role->employee_staffmark_own_attendance_online == 1 ? 'checked' : '' }} type="checkbox" class="select-all-staff-attendance-employee">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">تسجيل الحضور بنفسه (اونلاين)</span>
                                    </div>

                                    <div class="vs-checkbox-con vs-checkbox-primary mb-2">
                                        <input name="employee_staffview_own_attendance_books" {{ $role->employee_staffview_own_attendance_books == 1 ? 'checked' : '' }} type="checkbox" class="select-all-staff-attendance-employee">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">عرض دفاتر الحضور الخاصه به</span>
                                    </div>

                                    <div class="vs-checkbox-con vs-checkbox-primary mb-2">
                                        <input name="employee_staffedit_delete_own_leave_requests" {{ $role->employee_staffedit_delete_own_leave_requests == 1 ? 'checked' : '' }} type="checkbox" class="select-all-staff-attendance-employee">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">تعديل/ حذف طلبات إجازاته فقط</span>
                                    </div>
                                </div>

                                <!-- العمود الثاني -->
                                <div class="col-md-6">
                                    <div class="vs-checkbox-con vs-checkbox-primary mb-2">
                                        <input name="employee_staffview_own_attendance_logs" {{ $role->employee_staffview_own_attendance_logs == 1 ? 'checked' : '' }} type="checkbox" class="select-all-staff-attendance-employee">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">عرض كل سجلات الحضور الخاصه به</span>
                                    </div>

                                    <div class="vs-checkbox-con vs-checkbox-primary mb-2">
                                        <input name="employee_staffadd_leave_request" {{ $role->employee_staffadd_leave_request == 1 ? 'checked' : '' }} type="checkbox" class="select-all-staff-attendance-employee">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">إضافة طلب إجازة</span>
                                    </div>

                                    <div class="vs-checkbox-con vs-checkbox-primary mb-2">
                                        <input name="employee_staffview_own_leave_requests" {{ $role->employee_staffview_own_leave_requests == 1 ? 'checked' : '' }} type="checkbox" class="select-all-staff-attendance-employee">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">عرض طلبات إجازاته فقط</span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- الطلبات -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="vs-checkbox-con vs-checkbox-primary me-3">
                                        <input type="checkbox" id="SelectAllOrdersEmployee">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <strong class="">الطلبات</strong>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center">
                                    <span class="panel-role-active-title me-2">الصلاحيات النشطة:</span>
                                    <strong class="panel-role-active-count" data-role-count="1"><span id="activeCountOrdersEmployee">0</span>/1</strong>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="l-flex-row">

                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="employee_orders_management" {{ $role->employee_orders_management == 1 ? 'checked' : '' }} type="checkbox" class="select-all-orders-employee">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">إدارة الطلبات</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div><!--End Employee Content-->

            </form>

        </div><!-- end col-md-8 -->

    </div>

</div>

@endsection

@section('scripts')

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // تعريف الأقسام مع معرفات العناصر الخاصة بها
        const sections = [
            {
                selectAllId: 'selectAllSales',
                checkboxesClass: 'permission-checkbox-sales',
                activeCountId: 'activeCountSales',
            },
            {
                selectAllId: 'selectAllSalesPoints',
                checkboxesClass: 'permission-checkbox-sales-points',
                activeCountId: 'activeCountSalesPoints',
            },
            {
                selectAllId: 'SelectAllCustomerLoyalty',
                checkboxesClass: 'permission-checkbox-customer-loyalty',
                activeCountId: 'activeCountCustomerLoyalty',
            },
            {
                selectAllId: 'SelectAllProducts',
                checkboxesClass: 'select-all-products',
                activeCountId: 'activeCountProducts',
            },
            {
                selectAllId: 'SelectAllSaudiElectronicInvoice',
                checkboxesClass: 'select-all-Saudi-electronic-invoice',
                activeCountId: 'activeCountSaudiElectronicInvoice',
            },
            {
                selectAllId: 'SelectAllInsurances',
                checkboxesClass: 'select-all-insurances',
                activeCountId: 'activeCountInsurances',
            },
            {
                selectAllId: 'SelectAllClientFollowUp',
                checkboxesClass: 'select-all-client-follow-up',
                activeCountId: 'activeCountClientFollowUp',
            },
            {
                selectAllId: 'SelectAllCustomers',
                checkboxesClass: 'select-all-customers',
                activeCountId: 'activeCountCustomers',
            },
            {
                selectAllId: 'SelectAllPointsBalances',
                checkboxesClass: 'select-all-points-balances',
                activeCountId: 'activeCountPointsBalances',
            },
            {
                selectAllId: 'SelectAllMemberships',
                checkboxesClass: 'select-all-memberships',
                activeCountId: 'activeCountMemberships',
            },
            {
                selectAllId: 'SelectAllEmployees',
                checkboxesClass: 'select-all-employees',
                activeCountId: 'activeCountEmployees',
            },
            {
                selectAllId: 'SelectAllOrganizationalStructure',
                checkboxesClass: 'select-all-organizational-structure',
                activeCountId: 'activeCountOrganizationalStructure',
            },
            {
                selectAllId: 'SelectAllSalaries',
                checkboxesClass: 'select-all-salaries',
                activeCountId: 'activeCountSalaries',
            },
            {
                selectAllId: 'SelectAllStaffAttendance',
                checkboxesClass: 'select-all-staff-attendance',
                activeCountId: 'activeCountStaffAttendance',
            },
            {
                selectAllId: 'SelectAllOrders',
                checkboxesClass: 'select-all-orders',
                activeCountId: 'activeCountOrders',
            },
            {
                selectAllId: 'SelectAllInventoryManagement',
                checkboxesClass: 'select-all-inventory-management',
                activeCountId: 'activeCountInventoryManagement',
            },
            {
                selectAllId: 'SelectAllProcurementCycle',
                checkboxesClass: 'select-all-procurement-cycle',
                activeCountId: 'activeCountProcurementCycle',
            },
            {
                selectAllId: 'SelectAllSupplyOrderManagement',
                checkboxesClass: 'select-all-supply-order-management',
                activeCountId: 'activeCountSupplyOrderManagement',
            },
            {
                selectAllId: 'SelectAllTrackTime',
                checkboxesClass: 'select-all-track-time',
                activeCountId: 'activeCountTrackTime',
            },
            {
                selectAllId: 'SelectAllRentalUnitManagement',
                checkboxesClass: 'select-all-rental-unit-management',
                activeCountId: 'activeCountRentalUnitManagement',
            },
            {
                selectAllId: 'SelectAllGeneralAccountsDailyRestrictions',
                checkboxesClass: 'select-all-general-accounts-daily-restrictions',
                activeCountId: 'activeCountGeneralAccountsDailyRestrictions',
            },
            {
                selectAllId: 'SelectAllFinance',
                checkboxesClass: 'select-all-finance',
                activeCountId: 'activeCountFinance',
            },
            {
                selectAllId: 'SelectAllSettings',
                checkboxesClass: 'select-all-settings',
                activeCountId: 'activeCountSettings',
            },
            {
                selectAllId: 'SelectAllCheckCycle',
                checkboxesClass: 'select-all-check-cycle',
                activeCountId: 'activeCountCheckCycle',
            },
            {
                selectAllId: 'SelectAllCustomerAttendance',
                checkboxesClass: 'select-all-customer-attendance',
                activeCountId: 'activeCountCustomerAttendance',
            },
            {
                selectAllId: 'SelectAllOnlineStore',
                checkboxesClass: 'select-all-online-store',
                activeCountId: 'activeCountOnlineStore',
            },
            {
                selectAllId: 'SelectAllTargetedSalesCommissions',
                checkboxesClass: 'targeted-sales-commissions',
                activeCountId: 'activeCountTargetedSalesCommissions',
            },
            {
                selectAllId: 'SelectAllOrdersEmployee',
                checkboxesClass: 'select-all-orders-employee',
                activeCountId: 'activeCountOrdersEmployee',
            },
            {
                selectAllId: 'SelectAllStaffAttendanceEmployee',
                checkboxesClass: 'select-all-staff-attendance-employee',
                activeCountId: 'activeCountStaffAttendanceEmployee',
            },
            {
                selectAllId: 'SelectAllSalariesEmployee',
                checkboxesClass: 'select-all-salaries-employee',
                activeCountId: 'activeCountSalariesEmployee',
            }
        ];

        // تحديث عدد الـ checkboxes المحددة
        function updateCheckedCount(checkboxes, activeCountElement) {
            const checkedCount = checkboxes.filter(checkbox => checkbox.checked).length;
            activeCountElement.textContent = checkedCount; // تحديث الرقم في واجهة المستخدم
        }

        // تحديث الأعداد لجميع الأقسام (للمدير "أدمن")
        function updateAllSectionsCounts() {
            sections.forEach(section => {
                const checkboxes = Array.from(document.querySelectorAll(`.${section.checkboxesClass}`));
                const activeCountElement = document.getElementById(section.activeCountId);
                updateCheckedCount(checkboxes, activeCountElement);
            });
        }

        // إضافة الأحداث ومعالجة الأقسام بشكل موحد
        sections.forEach(section => {
            const selectAll = document.getElementById(section.selectAllId);
            const checkboxes = Array.from(document.querySelectorAll(`.${section.checkboxesClass}`));
            const activeCountElement = document.getElementById(section.activeCountId);

            // حدث اختيار "تحديد الكل"
            selectAll.addEventListener('change', function () {
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateCheckedCount(checkboxes, activeCountElement);
            });

            // إضافة حدث عند تغيير أي checkbox
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function () {
                    updateCheckedCount(checkboxes, activeCountElement);
                });
            });

            // استدعاء الوظيفة لتحديث العدد عند التحميل
            updateCheckedCount(checkboxes, activeCountElement);
        });

        // مدير (أدمن)
        const adminCheckbox = document.getElementById('adminCheckbox');
        const permissionCheckboxes = document.querySelectorAll('.permission-main-checkbox');

        adminCheckbox.addEventListener('change', function () {
            const isChecked = adminCheckbox.checked;
            permissionCheckboxes.forEach(checkbox => {
                checkbox.checked = isChecked;
            });
            updateAllSectionsCounts(); // تحديث الأعداد لجميع الأقسام
        });

        // استدعاء التحديث عند التحميل
        updateAllSectionsCounts();
    });

    // --------------------------------------------------------------------
    document.addEventListener('DOMContentLoaded', function () {
            // العناصر
            const employeeRadio = document.getElementById('customRadio2');
            const userRadio = document.getElementById('customRadio1');
            const employeeContent = document.getElementById('employeeContent');
            const userContent = document.getElementById('userContent');
            const admin = document.getElementById('admin');

            // تغيير العرض بناءً على الاختيار
            function toggleContent() {
                if (employeeRadio.checked) {
                    employeeContent.style.display = 'block';
                    userContent.style.display = 'none';
                    admin.style.display = 'none';
                } else if (userRadio.checked) {
                    employeeContent.style.display = 'none';
                    userContent.style.display = 'block';
                    admin.style.display = '';
                }
            }

            // إضافة الأحداث
            employeeRadio.addEventListener('change', toggleContent);
            userRadio.addEventListener('change', toggleContent);

            // استدعاء الوظيفة عند التحميل
            toggleContent();
        });

</script>
// المبيعات
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const allButton = document.getElementById("allButton");
        const checkboxes = document.querySelectorAll('.permission-main-checkbox');
        const selectedCountSpan = document.getElementById("selectedCount");
    
        function updateCount() {
            const checkedCount = document.querySelectorAll('.permission-main-checkbox:checked').length;
            const totalCount = checkboxes.length;
            selectedCountSpan.innerText = `${checkedCount}/${totalCount}`;
        }
    
        // عند الضغط على الزر، تحديد جميع الصلاحيات
        allButton.addEventListener("click", function() {
            let allChecked = [...checkboxes].every(checkbox => checkbox.checked); // فحص إذا كانت كل الشيك بوكس محددة
            checkboxes.forEach(checkbox => {
                checkbox.checked = !allChecked; // تبديل الحالة
            });
            updateCount();
        });
    
        // تحديث العدد عند تغيير أي شيك بوكس
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener("change", updateCount);
        });
    
        // تحديث العدد عند تحميل الصفحة
        updateCount();
    });
    </script>
    // المبيعات
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const salesButton = document.getElementById("salesButton");
            const checkboxes = document.querySelectorAll('.sales-checkbox');
            const selectedCountSpan = document.getElementById("selectedCountsales");
            
            function updateCount() {
                const checkedCount = document.querySelectorAll('.sales-checkbox:checked').length;
                const totalCount = checkboxes.length;
                selectedCountSpan.innerText = `${checkedCount}/${totalCount}`;
            }
            
            // عند الضغط على الزر، تحديد جميع الصلاحيات
            salesButton.addEventListener("click", function() {
                let allChecked = [...checkboxes].every(checkbox => checkbox.checked); // فحص إذا كانت كل الشيك بوكس محددة
                checkboxes.forEach(checkbox => {
                    checkbox.checked = !allChecked; // تبديل الحالة
                });
                updateCount();
            });
        
            // تحديث العدد عند تغيير أي شيك بوكس
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener("change", updateCount);
            });
        
            // تحديث العدد عند تحميل الصفحة
            updateCount();
        });
        </script>
     // العملاء 
     <script>
        document.addEventListener("DOMContentLoaded", function () {
            const customerButton = document.getElementById("customerButton");
            const checkboxes = document.querySelectorAll('.customer-checkbox');
            const selectedCountSpan = document.getElementById("selectedCountcustomer");
            
            function updateCount() {
                const checkedCount = document.querySelectorAll('.customer-checkbox:checked').length;
                const totalCount = checkboxes.length;
                selectedCountSpan.innerText = `${checkedCount}/${totalCount}`;
            }
            
            // عند الضغط على الزر، تحديد جميع الصلاحيات
            customerButton.addEventListener("click", function() {
                let allChecked = [...checkboxes].every(checkbox => checkbox.checked); // فحص إذا كانت كل الشيك بوكس محددة
                checkboxes.forEach(checkbox => {
                    checkbox.checked = !allChecked; // تبديل الحالة
                });
                updateCount();
            });
        
            // تحديث العدد عند تغيير أي شيك بوكس
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener("change", updateCount);
            });
        
            // تحديث العدد عند تحميل الصفحة
            updateCount();
        });
        </script>   
        // الموارد البشرية
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const crmButton = document.getElementById("crmButton");
                const checkboxes = document.querySelectorAll('.crm-checkbox');
                const selectedCountSpan = document.getElementById("selectedCrm");
                
                function updateCount() {
                    const checkedCount = document.querySelectorAll('.crm-checkbox:checked').length;
                    const totalCount = checkboxes.length;
                    selectedCountSpan.innerText = `${checkedCount}/${totalCount}`;
                }
                
                // عند الضغط على الزر، تحديد جميع الصلاحيات
                crmButton.addEventListener("click", function() {
                    let allChecked = [...checkboxes].every(checkbox => checkbox.checked); // فحص إذا كانت كل الشيك بوكس محددة
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = !allChecked; // تبديل الحالة
                    });
                    updateCount();
                });
            
                // تحديث العدد عند تغيير أي شيك بوكس
                checkboxes.forEach(checkbox => {
                    checkbox.addEventListener("change", updateCount);
                });
            
                // تحديث العدد عند تحميل الصفحة
                updateCount();
            });
            </script>   
       // المخزن والمشتريات
       <script>
        document.addEventListener("DOMContentLoaded", function () {
            const storeButton = document.getElementById("storeButton");
            const checkboxes = document.querySelectorAll('.store-checkbox');
            const selectedCountSpan = document.getElementById("selectedStore");
            
            function updateCount() {
                const checkedCount = document.querySelectorAll('.store-checkbox:checked').length;
                const totalCount = checkboxes.length;
                selectedCountSpan.innerText = `${checkedCount}/${totalCount}`;
            }
            
            // عند الضغط على الزر، تحديد جميع الصلاحيات
            storeButton.addEventListener("click", function() {
                let allChecked = [...checkboxes].every(checkbox => checkbox.checked); // فحص إذا كانت كل الشيك بوكس محددة
                checkboxes.forEach(checkbox => {
                    checkbox.checked = !allChecked; // تبديل الحالة
                });
                updateCount();
            });
        
            // تحديث العدد عند تغيير أي شيك بوكس
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener("change", updateCount);
            });
        
            // تحديث العدد عند تحميل الصفحة
            updateCount();
        });
        </script>  
        // operating
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const operatingButton = document.getElementById("operatingButton");
                const checkboxes = document.querySelectorAll('.operating-checkbox');
                const selectedCountSpan = document.getElementById("selectedOperating");
                
                function updateCount() {
                    const checkedCount = document.querySelectorAll('.operating-checkbox:checked').length;
                    const totalCount = checkboxes.length;
                    selectedCountSpan.innerText = `${checkedCount}/${totalCount}`;
                }
                
                // عند الضغط على الزر، تحديد جميع الصلاحيات
                operatingButton.addEventListener("click", function() {
                    let allChecked = [...checkboxes].every(checkbox => checkbox.checked); // فحص إذا كانت كل الشيك بوكس محددة
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = !allChecked; // تبديل الحالة
                    });
                    updateCount();
                });
            
                // تحديث العدد عند تغيير أي شيك بوكس
                checkboxes.forEach(checkbox => {
                    checkbox.addEventListener("change", updateCount);
                });
            
                // تحديث العدد عند تحميل الصفحة
                updateCount();
            });
            </script>   
            // الحسابات العامة
            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    const accountButton = document.getElementById("accountButton");
                    const checkboxes = document.querySelectorAll('.account-checkbox');
                    const selectedCountSpan = document.getElementById("selectedAccount");
                    
                    function updateCount() {                      
                        const checkedCount = document.querySelectorAll('.account-checkbox:checked').length;
                        const totalCount = checkboxes.length;
                        selectedCountSpan.innerText = `${checkedCount}/${totalCount}`;
                    }
                    
                    // عند الضغط على الزر، تحديد جميع الصلاحيات
                    accountButton.addEventListener("click", function() {
                        let allChecked = [...checkboxes].every(checkbox => checkbox.checked); // فحص إذا كانت كل الشيك بوكس محددة
                        checkboxes.forEach(checkbox => {
                            checkbox.checked = !allChecked; // تبديل الحالة
                        });
                        updateCount();
                    });
                
                    // تحديث العدد عند تغيير أي شيك بوكس
                    checkboxes.forEach(checkbox => {
                        checkbox.addEventListener("change", updateCount);
                    });
                
                    // تحديث العدد عند تحميل الصفحة
                    updateCount();
                });
                </script>   
          // settingsButton  الاعدادات العامة
          <script>
            document.addEventListener("DOMContentLoaded", function () {
                const settingsButton = document.getElementById("settingsButton");
                const checkboxes = document.querySelectorAll('.setting-checkbox');
                const selectedCountSpan = document.getElementById("selectedSetting");
                
                function updateCount() {                                          
                    const checkedCount = document.querySelectorAll('.setting-checkbox:checked').length;
                    const totalCount = checkboxes.length;
                    selectedCountSpan.innerText = `${checkedCount}/${totalCount}`;
                }
                
                // عند الضغط على الزر، تحديد جميع الصلاحيات
                settingsButton.addEventListener("click", function() {
                    let allChecked = [...checkboxes].every(checkbox => checkbox.checked); // فحص إذا كانت كل الشيك بوكس محددة
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = !allChecked; // تبديل الحالة
                    });
                    updateCount();
                });
            
                // تحديث العدد عند تغيير أي شيك بوكس
                checkboxes.forEach(checkbox => {
                    checkbox.addEventListener("change", updateCount);
                });
            
                // تحديث العدد عند تحميل الصفحة
                updateCount(); 
            });
            </script>   


@endsection

