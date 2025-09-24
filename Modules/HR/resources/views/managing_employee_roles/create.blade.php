@extends('master')

@section('title')
الأدوار الوظيفية
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/sitting.css') }}">
<style>
.role-tabs {
    display: flex;
    margin-bottom: 20px;
    background: #f8f9fa;
    border-radius: 8px;
    padding: 5px;
}

.role-tab {
    flex: 1;
    text-align: center;
    padding: 12px 20px;
    border: none;
    background: transparent;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 500;
}

.role-tab.active {
    background: #667eea;
    color: white;
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
}

.role-content {
    display: none;
}

.role-content.active {
    display: block;
}

.admin-checkbox-container {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 20px;
}

.admin-checkbox-container label {
    color: white;
    font-weight: 600;
    margin: 0;
}

.apps-sidebar {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 20px;
    height: fit-content;
    position: sticky;
    top: 20px;
    max-width: 100%;
    overflow-x: hidden;
}

.app-button {
    width: 100%;
    max-width: 350px;
    margin-bottom: 10px;
    padding: 12px 15px;
    border: 1px solid #e3e6f0;
    background: white;
    border-radius: 6px;
    text-align: left;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    justify-content: space-between;
    align-items: center;
    overflow: hidden;
    white-space: nowrap;
}

.app-button:hover {
    border-color: #667eea;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.app-button.active {
    border-color: #667eea;
    background: #667eea;
    color: white;
}

.app-info {
    display: flex;
    align-items: center;
    flex: 1;
    min-width: 0;
}

.app-info span {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.app-count {
    background: #e3e6f0;
    color: #5a5c69;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
    flex-shrink: 0;
    min-width: fit-content;
}

.app-button.active .app-count {
    background: rgba(255,255,255,0.2);
    color: white;
}

.permission-section {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin-bottom: 20px;
    overflow: hidden;
}

.permission-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.permission-title {
    display: flex;
    align-items: center;
    margin: 0;
}

.permission-count {
    background: rgba(255,255,255,0.2);
    padding: 4px 12px;
    border-radius: 15px;
    font-size: 12px;
}

.permission-grid {
    padding: 20px;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 15px;
}

.permission-item-new {
    display: flex;
    align-items: center;
    padding: 12px;
    border: 1px solid #e3e6f0;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.permission-item-new:hover {
    border-color: #667eea;
    background: #f8f9fb;
}

.permission-item-new.checked {
    border-color: #667eea;
    background: #f0f3ff;
}

.custom-checkbox-new {
    margin-right: 10px;
    width: 18px;
    height: 18px;
    accent-color: #667eea;
}

.permission-label-new {
    margin: 0;
    font-weight: 500;
    color: #2d3748;
}

.form-control-modern {
    border: 1px solid #e3e6f0;
    border-radius: 6px;
    padding: 12px 15px;
    font-size: 14px;
    transition: all 0.3s ease;
}

.form-control-modern:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.btn-save-modern {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 12px 25px;
    border-radius: 6px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-save-modern:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    color: white;
}

.btn-cancel-modern {
    background: #6c757d;
    color: white;
    border: none;
    padding: 12px 25px;
    border-radius: 6px;
    font-weight: 600;
    transition: all 0.3s ease;
    margin-left: 10px;
}

.btn-cancel-modern:hover {
    background: #545b62;
    transform: translateY(-1px);
    color: white;
}
</style>
@endsection

@section('content')
<div class="content-wrapper">
    <!-- رأس الصفحة -->
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="main-title">👥 الأدوار الوظيفية</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb breadcrumb-custom">
                                <li class="breadcrumb-item">
                                    <a href="">🏠 الرئيسية</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    ➕ إضافة دور وظيفي
                                </li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form id="permissions_form" action="{{ route('managing_employee_roles.store') }}" method="POST">
        @csrf

        <!-- بطاقة معلومات الحفظ -->
        <div class="custom-card">
            <div class="card-header-custom">
                <h5 class="mb-0">💾 معلومات الدور الوظيفي</h5>
            </div>
            <div class="card-body-custom">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">اسم الدور الوظيفي <span class="required-star">*</span></label>
                            <input type="text" class="form-control-modern" name="role_name"
                                   placeholder="أدخل اسم الدور الوظيفي" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">نوع المستخدم <span class="required-star">*</span></label>
                            <div class="role-tabs">
                                <button type="button" class="role-tab active" onclick="switchRole('user')">
                                    👤 مستخدم
                                </button>
                                <button type="button" class="role-tab" onclick="switchRole('employee')">
                                    👨‍💼 موظف
                                </button>
                            </div>
                            <input type="hidden" name="customRadio" id="roleType" value="user">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="admin-checkbox-container">
                            <div class="d-flex align-items-center">
                                <input type="checkbox" id="adminCheckbox" class="custom-checkbox-new">
                                <label for="adminCheckbox" class="permission-label-new">
                                    👑 مدير (أدمن)
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end">
                    <a href="{{ route('managing_employee_roles.index') }}" class="btn btn-cancel-modern">
                        ❌ إلغاء
                    </a>
                    <button type="submit" form="permissions_form" class="btn btn-save-modern">
                        💾 حفظ الدور
                    </button>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- الشريط الجانبي للتطبيقات -->
            <div class="col-md-4">
                <div class="apps-sidebar">
                    <h6 class="mb-3">📱 التطبيقات ومجموعات الصلاحيات</h6>

                    <button type="button" class="app-button active" id="allButton" onclick="selectApp('all')">
                        <div class="app-info">
                            <i class="fas fa-globe-americas me-2"></i>
                            <span>كل التطبيقات</span>
                        </div>
                        <span class="app-count" id="selectedCount">0/236</span>
                    </button>

                    <button type="button" class="app-button" id="salesButton" onclick="selectApp('sales')">
                        <div class="app-info">
                            <i class="fas fa-shopping-cart me-2"></i>
                            <span>المبيعات</span>
                        </div>
                        <span class="app-count" id="selectedCountsales">0/61</span>
                    </button>

                    <button type="button" class="app-button" id="customerButton" onclick="selectApp('customer')">
                        <div class="app-info">
                            <i class="fas fa-users me-2"></i>
                            <span>إدارة علاقات العملاء</span>
                        </div>
                        <span class="app-count" id="selectedCountcustomer">0/26</span>
                    </button>

                    <button type="button" class="app-button" id="crmButton" onclick="selectApp('crm')">
                        <div class="app-info">
                            <i class="fas fa-user-tie me-2"></i>
                            <span>الموارد البشرية</span>
                        </div>
                        <span class="app-count" id="selectedCrm">0/49</span>
                    </button>

                    <button type="button" class="app-button" id="storeButton" onclick="selectApp('store')">
                        <div class="app-info">
                            <i class="fas fa-warehouse me-2"></i>
                            <span>المخزون والمشتريات</span>
                        </div>
                        <span class="app-count" id="selectedStore">0/26</span>
                    </button>

                    <button type="button" class="app-button" id="operatingButton" onclick="selectApp('operating')">
                        <div class="app-info">
                            <i class="fas fa-cogs me-2"></i>
                            <span>التشغيل</span>
                        </div>
                        <span class="app-count" id="selectedOperating">0/22</span>
                    </button>

                    <button type="button" class="app-button" id="accountButton" onclick="selectApp('account')">
                        <div class="app-info">
                            <i class="fas fa-calculator me-2"></i>
                            <span>الحسابات العامة</span>
                        </div>
                        <span class="app-count" id="selectedAccount">0/30</span>
                    </button>

                    <button type="button" class="app-button" id="settingsButton" onclick="selectApp('settings')">
                        <div class="app-info">
                            <i class="fas fa-sliders-h me-2"></i>
                            <span>الإعدادات العامة</span>
                        </div>
                        <span class="app-count" id="selectedSetting">0/4</span>
                    </button>
                </div>
            </div>

            <!-- محتوى الصلاحيات -->
            <div class="col-md-8">
                <!-- محتوى المستخدم -->
                <div id="userContent" class="role-content active">

                    <!-- المبيعات -->
                    <div class="permission-section">
                        <div class="permission-header">
                            <div class="permission-title">
                                <input type="checkbox" id="selectAllSales" class="custom-checkbox-new permission-main-checkbox sales-checkbox" onclick="toggleSectionPermissions('sales')">
                                <h6 class="mb-0 ms-2">🛒 المبيعات</h6>
                            </div>
                            <span class="permission-count">
                                الصلاحيات النشطة: <span id="activeCountSales">0</span>/34
                            </span>
                        </div>
                        <div class="permission-grid">
                            <div class="permission-item-new" onclick="togglePermission('sales_add_invoices')">
                                <input type="checkbox" id="sales_add_invoices" name="sales_add_invoices" class="custom-checkbox-new permission-checkbox-sales permission-main-checkbox sales-checkbox">
                                <label for="sales_add_invoices" class="permission-label-new">إضافة فواتير لكل العملاء</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('sales_add_own_invoices')">
                                <input type="checkbox" id="sales_add_own_invoices" name="sales_add_own_invoices" class="custom-checkbox-new permission-checkbox-sales permission-main-checkbox sales-checkbox">
                                <label for="sales_add_own_invoices" class="permission-label-new">إضافة فواتير للعملاء الخاصة به</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('sales_edit_delete_all_invoices')">
                                <input type="checkbox" id="sales_edit_delete_all_invoices" name="sales_edit_delete_all_invoices" class="custom-checkbox-new permission-checkbox-sales permission-main-checkbox sales-checkbox">
                                <label for="sales_edit_delete_all_invoices" class="permission-label-new">تعديل وحذف كل الفواتير</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('sales_edit_delete_own_invoices')">
                                <input type="checkbox" id="sales_edit_delete_own_invoices" name="sales_edit_delete_own_invoices" class="custom-checkbox-new permission-checkbox-sales permission-main-checkbox sales-checkbox">
                                <label for="sales_edit_delete_own_invoices" class="permission-label-new">تعديل وحذف الفواتير الخاصة به</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('sales_view_own_invoices')">
                                <input type="checkbox" id="sales_view_own_invoices" name="sales_view_own_invoices" class="custom-checkbox-new permission-checkbox-sales permission-main-checkbox sales-checkbox">
                                <label for="sales_view_own_invoices" class="permission-label-new">عرض الفواتير الخاصة به</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('sales_view_all_invoices')">
                                <input type="checkbox" id="sales_view_all_invoices" name="sales_view_all_invoices" class="custom-checkbox-new permission-checkbox-sales permission-main-checkbox sales-checkbox">
                                <label for="sales_view_all_invoices" class="permission-label-new">عرض جميع الفواتير</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('sales_create_tax_report')">
                                <input type="checkbox" id="sales_create_tax_report" name="sales_create_tax_report" class="custom-checkbox-new permission-checkbox-sales permission-main-checkbox sales-checkbox">
                                <label for="sales_create_tax_report" class="permission-label-new">إنشاء تقرير ضرائب</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('sales_change_seller')">
                                <input type="checkbox" id="sales_change_seller" name="sales_change_seller" class="custom-checkbox-new permission-checkbox-sales permission-main-checkbox sales-checkbox">
                                <label for="sales_change_seller" class="permission-label-new">تغيير البائع</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('sales_invoice_all_products')">
                                <input type="checkbox" id="sales_invoice_all_products" name="sales_invoice_all_products" class="custom-checkbox-new permission-checkbox-sales permission-main-checkbox sales-checkbox">
                                <label for="sales_invoice_all_products" class="permission-label-new">فوترة جميع المنتجات</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('sales_view_invoice_profit')">
                                <input type="checkbox" id="sales_view_invoice_profit" name="sales_view_invoice_profit" class="custom-checkbox-new permission-checkbox-sales permission-main-checkbox sales-checkbox">
                                <label for="sales_view_invoice_profit" class="permission-label-new">عرض ربح الفاتورة</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('sales_add_credit_notice_all')">
                                <input type="checkbox" id="sales_add_credit_notice_all" name="sales_add_credit_notice_all" class="custom-checkbox-new permission-checkbox-sales permission-main-checkbox sales-checkbox">
                                <label for="sales_add_credit_notice_all" class="permission-label-new">إضافة إشعار مدين جديد لجميع العملاء</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('sales_add_credit_notice_own')">
                                <input type="checkbox" id="sales_add_credit_notice_own" name="sales_add_credit_notice_own" class="custom-checkbox-new permission-checkbox-sales permission-main-checkbox sales-checkbox">
                                <label for="sales_add_credit_notice_own" class="permission-label-new">إضافة إشعار مدين جديد لعملائه فقط</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('Issue_an_invoice_to_a_customer_who_has_a_debt')">
                                <input type="checkbox" id="Issue_an_invoice_to_a_customer_who_has_a_debt" name="Issue_an_invoice_to_a_customer_who_has_a_debt" class="custom-checkbox-new permission-checkbox-sales permission-main-checkbox sales-checkbox">
                                <label for="Issue_an_invoice_to_a_customer_who_has_a_debt" class="permission-label-new">إضافة فاتورة لعميل لديه مديونية</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('sales_edit_invoice_date')">
                                <input type="checkbox" id="sales_edit_invoice_date" name="sales_edit_invoice_date" class="custom-checkbox-new permission-checkbox-sales permission-main-checkbox sales-checkbox">
                                <label for="sales_edit_invoice_date" class="permission-label-new">تعديل تاريخ الفاتورة</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('sales_add_payments_all')">
                                <input type="checkbox" id="sales_add_payments_all" name="sales_add_payments_all" class="custom-checkbox-new permission-checkbox-sales permission-main-checkbox sales-checkbox">
                                <label for="sales_add_payments_all" class="permission-label-new">إضافة عمليات دفع لكل الفواتير</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('sales_add_payments_own')">
                                <input type="checkbox" id="sales_add_payments_own" name="sales_add_payments_own" class="custom-checkbox-new permission-checkbox-sales permission-main-checkbox sales-checkbox">
                                <label for="sales_add_payments_own" class="permission-label-new">إضافة عمليات دفع للفواتير الخاصة به</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('sales_edit_payment_options')">
                                <input type="checkbox" id="sales_edit_payment_options" name="sales_edit_payment_options" class="custom-checkbox-new permission-checkbox-sales permission-main-checkbox sales-checkbox">
                                <label for="sales_edit_payment_options" class="permission-label-new">تعديل خيارات الدفع</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('sales_edit_delete_all_payments')">
                                <input type="checkbox" id="sales_edit_delete_all_payments" name="sales_edit_delete_all_payments" class="custom-checkbox-new permission-checkbox-sales permission-main-checkbox sales-checkbox">
                                <label for="sales_edit_delete_all_payments" class="permission-label-new">حذف وتعديل جميع المدفوعات</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('sales_edit_delete_own_payments')">
                                <input type="checkbox" id="sales_edit_delete_own_payments" name="sales_edit_delete_own_payments" class="custom-checkbox-new permission-checkbox-sales permission-main-checkbox sales-checkbox">
                                <label for="sales_edit_delete_own_payments" class="permission-label-new">حذف وتعديل المدفوعات الخاصة به</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('sales_add_quote_all')">
                                <input type="checkbox" id="sales_add_quote_all" name="sales_add_quote_all" class="custom-checkbox-new permission-checkbox-sales permission-main-checkbox sales-checkbox">
                                <label for="sales_add_quote_all" class="permission-label-new">إضافة عرض سعر لكل العملاء</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('sales_add_quote_own')">
                                <input type="checkbox" id="sales_add_quote_own" name="sales_add_quote_own" class="custom-checkbox-new permission-checkbox-sales permission-main-checkbox sales-checkbox">
                                <label for="sales_add_quote_own" class="permission-label-new">إضافة عرض سعر للعملاء الخاصة به</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('sales_view_all_quotes')">
                                <input type="checkbox" id="sales_view_all_quotes" name="sales_view_all_quotes" class="custom-checkbox-new permission-checkbox-sales permission-main-checkbox sales-checkbox">
                                <label for="sales_view_all_quotes" class="permission-label-new">عرض جميع عروض الأسعار</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('sales_view_own_quotes')">
                                <input type="checkbox" id="sales_view_own_quotes" name="sales_view_own_quotes" class="custom-checkbox-new permission-checkbox-sales permission-main-checkbox sales-checkbox">
                                <label for="sales_view_own_quotes" class="permission-label-new">عرض عروض الأسعار الخاصة به</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('sales_edit_delete_all_quotes')">
                                <input type="checkbox" id="sales_edit_delete_all_quotes" name="sales_edit_delete_all_quotes" class="custom-checkbox-new permission-checkbox-sales permission-main-checkbox sales-checkbox">
                                <label for="sales_edit_delete_all_quotes" class="permission-label-new">تعديل وحذف جميع عروض الأسعار</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('sales_edit_delete_own_quotes')">
                                <input type="checkbox" id="sales_edit_delete_own_quotes" name="sales_edit_delete_own_quotes" class="custom-checkbox-new permission-checkbox-sales permission-main-checkbox sales-checkbox">
                                <label for="sales_edit_delete_own_quotes" class="permission-label-new">تعديل وحذف عروض الأسعار الخاصة به</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('sales_view_all_sales_orders')">
                                <input type="checkbox" id="sales_view_all_sales_orders" name="sales_view_all_sales_orders" class="custom-checkbox-new permission-checkbox-sales permission-main-checkbox sales-checkbox">
                                <label for="sales_view_all_sales_orders" class="permission-label-new">عرض جميع أوامر البيع</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('sales_view_own_sales_orders')">
                                <input type="checkbox" id="sales_view_own_sales_orders" name="sales_view_own_sales_orders" class="custom-checkbox-new permission-checkbox-sales permission-main-checkbox sales-checkbox">
                                <label for="sales_view_own_sales_orders" class="permission-label-new">عرض أوامر البيع الخاصة به</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('sales_add_sales_order_all')">
                                <input type="checkbox" id="sales_add_sales_order_all" name="sales_add_sales_order_all" class="custom-checkbox-new permission-checkbox-sales permission-main-checkbox sales-checkbox">
                                <label for="sales_add_sales_order_all" class="permission-label-new">إضافة أمر بيع جديد لجميع العملاء</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('sales_add_sales_order_own')">
                                <input type="checkbox" id="sales_add_sales_order_own" name="sales_add_sales_order_own" class="custom-checkbox-new permission-checkbox-sales permission-main-checkbox sales-checkbox">
                                <label for="sales_add_sales_order_own" class="permission-label-new">إضافة أمر بيع جديد لعملائه فقط</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('sales_edit_delete_all_sales_orders')">
                                <input type="checkbox" id="sales_edit_delete_all_sales_orders" name="sales_edit_delete_all_sales_orders" class="custom-checkbox-new permission-checkbox-sales permission-main-checkbox sales-checkbox">
                                <label for="sales_edit_delete_all_sales_orders" class="permission-label-new">تعديل وحذف جميع أوامر البيع</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('sales_edit_delete_own_sales_orders')">
                                <input type="checkbox" id="sales_edit_delete_own_sales_orders" name="sales_edit_delete_own_sales_orders" class="custom-checkbox-new permission-checkbox-sales permission-main-checkbox sales-checkbox">
                                <label for="sales_edit_delete_own_sales_orders" class="permission-label-new">تعديل وحذف أوامر البيع الخاصة به فقط</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('sales_edit_delete_all_credit_notices')">
                                <input type="checkbox" id="sales_edit_delete_all_credit_notices" name="sales_edit_delete_all_credit_notices" class="custom-checkbox-new permission-checkbox-sales permission-main-checkbox sales-checkbox">
                                <label for="sales_edit_delete_all_credit_notices" class="permission-label-new">تعديل وحذف جميع الإشعارات المدينة</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('sales_edit_delete_own_credit_notices')">
                                <input type="checkbox" id="sales_edit_delete_own_credit_notices" name="sales_edit_delete_own_credit_notices" class="custom-checkbox-new permission-checkbox-sales permission-main-checkbox sales-checkbox">
                                <label for="sales_edit_delete_own_credit_notices" class="permission-label-new">تعديل وحذف الإشعارات المدينة الخاصة به فقط</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('sales_view_all_credit_notices')">
                                <input type="checkbox" id="sales_view_all_credit_notices" name="sales_view_all_credit_notices" class="custom-checkbox-new permission-checkbox-sales permission-main-checkbox sales-checkbox">
                                <label for="sales_view_all_credit_notices" class="permission-label-new">عرض جميع الإشعارات المدينة</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('sales_view_own_credit_notices')">
                                <input type="checkbox" id="sales_view_own_credit_notices" name="sales_view_own_credit_notices" class="custom-checkbox-new permission-checkbox-sales permission-main-checkbox sales-checkbox">
                                <label for="sales_view_own_credit_notices" class="permission-label-new">عرض الإشعارات المدينة الخاصة به فقط</label>
                            </div>
                        </div>
                    </div>

                    <!-- نقاط البيع -->
                    <div class="permission-section">
                        <div class="permission-header">
                            <div class="permission-title">
                                <input type="checkbox" id="selectAllSalesPoints" class="custom-checkbox-new permission-main-checkbox sales-checkbox" onclick="toggleSectionPermissions('salesPoints')">
                                <h6 class="mb-0 ms-2">🏪 نقاط البيع</h6>
                            </div>
                            <span class="permission-count">
                                الصلاحيات النشطة: <span id="activeCountSalesPoints">0</span>/10
                            </span>
                        </div>
                        <div class="permission-grid">
                            <div class="permission-item-new" onclick="togglePermission('points_sale_edit_product_prices')">
                                <input type="checkbox" id="points_sale_edit_product_prices" name="points_sale_edit_product_prices" class="custom-checkbox-new permission-checkbox-sales-points permission-main-checkbox sales-checkbox">
                                <label for="points_sale_edit_product_prices" class="permission-label-new">تعديل أسعار المنتجات</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('points_sale_add_discount')">
                                <input type="checkbox" id="points_sale_add_discount" name="points_sale_add_discount" class="custom-checkbox-new permission-checkbox-sales-points permission-main-checkbox sales-checkbox">
                                <label for="points_sale_add_discount" class="permission-label-new">إضافة خصم</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('points_sale_open_sessions_all')">
                                <input type="checkbox" id="points_sale_open_sessions_all" name="points_sale_open_sessions_all" class="custom-checkbox-new permission-checkbox-sales-points permission-main-checkbox sales-checkbox">
                                <label for="points_sale_open_sessions_all" class="permission-label-new">فتح جلسات لجميع المستخدمين</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('points_sale_open_sessions_own')">
                                <input type="checkbox" id="points_sale_open_sessions_own" name="points_sale_open_sessions_own" class="custom-checkbox-new permission-checkbox-sales-points permission-main-checkbox sales-checkbox">
                                <label for="points_sale_open_sessions_own" class="permission-label-new">فتح جلسات لنفسه</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('points_sale_close_sessions_all')">
                                <input type="checkbox" id="points_sale_close_sessions_all" name="points_sale_close_sessions_all" class="custom-checkbox-new permission-checkbox-sales-points permission-main-checkbox sales-checkbox">
                                <label for="points_sale_close_sessions_all" class="permission-label-new">إغلاق جلسات جميع المستخدمين</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('points_sale_close_sessions_own')">
                                <input type="checkbox" id="points_sale_close_sessions_own" name="points_sale_close_sessions_own" class="custom-checkbox-new permission-checkbox-sales-points permission-main-checkbox sales-checkbox">
                                <label for="points_sale_close_sessions_own" class="permission-label-new">إغلاق الجلسات الخاصة</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('points_sale_view_all_sessions')">
                                <input type="checkbox" id="points_sale_view_all_sessions" name="points_sale_view_all_sessions" class="custom-checkbox-new permission-checkbox-sales-points permission-main-checkbox sales-checkbox">
                                <label for="points_sale_view_all_sessions" class="permission-label-new">عرض جميع الجلسات</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('points_sale_view_own_sessions')">
                                <input type="checkbox" id="points_sale_view_own_sessions" name="points_sale_view_own_sessions" class="custom-checkbox-new permission-checkbox-sales-points permission-main-checkbox sales-checkbox">
                                <label for="points_sale_view_own_sessions" class="permission-label-new">عرض الجلسات الخاصة به فقط</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('points_sale_confirm_close_sessions_all')">
                                <input type="checkbox" id="points_sale_confirm_close_sessions_all" name="points_sale_confirm_close_sessions_all" class="custom-checkbox-new permission-checkbox-sales-points permission-main-checkbox sales-checkbox">
                                <label for="points_sale_confirm_close_sessions_all" class="permission-label-new">تأكيد إغلاق جميع الجلسات</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('points_sale_confirm_close_sessions_own')">
                                <input type="checkbox" id="points_sale_confirm_close_sessions_own" name="points_sale_confirm_close_sessions_own" class="custom-checkbox-new permission-checkbox-sales-points permission-main-checkbox sales-checkbox">
                                <label for="points_sale_confirm_close_sessions_own" class="permission-label-new">تأكيد إغلاق الجلسات الخاصة به</label>
                            </div>
                        </div>
                    </div>

                    <!-- نقاط ولاء العملاء -->
                    <div class="permission-section">
                        <div class="permission-header">
                            <div class="permission-title">
                                <input type="checkbox" id="SelectAllCustomerLoyalty" class="custom-checkbox-new permission-main-checkbox customer-checkbox" onclick="toggleSectionPermissions('customerLoyalty')">
                                <h6 class="mb-0 ms-2">⭐ نقاط ولاء العملاء</h6>
                            </div>
                            <span class="permission-count">
                                الصلاحيات النشطة: <span id="activeCountCustomerLoyalty">0</span>/2
                            </span>
                        </div>
                        <div class="permission-grid">
                            <div class="permission-item-new" onclick="togglePermission('customer_loyalty_points_managing_customer_bases')">
                                <input type="checkbox" id="customer_loyalty_points_managing_customer_bases" name="customer_loyalty_points_managing_customer_bases" class="custom-checkbox-new permission-checkbox-customer-loyalty permission-main-checkbox customer-checkbox">
                                <label for="customer_loyalty_points_managing_customer_bases" class="permission-label-new">إدارة قواعد العملاء</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('customer_loyalty_points_redeem_loyalty_points')">
                                <input type="checkbox" id="customer_loyalty_points_redeem_loyalty_points" name="customer_loyalty_points_redeem_loyalty_points" class="custom-checkbox-new permission-checkbox-customer-loyalty permission-main-checkbox customer-checkbox">
                                <label for="customer_loyalty_points_redeem_loyalty_points" class="permission-label-new">صرف نقاط الولاء</label>
                            </div>
                        </div>
                    </div>

                    <!-- المبيعات المستهدفة والعمولات -->
                    <div class="permission-section">
                        <div class="permission-header">
                            <div class="permission-title">
                                <input type="checkbox" id="SelectAllTargetedSalesCommissions" class="custom-checkbox-new permission-main-checkbox sales-checkbox" onclick="toggleSectionPermissions('targetedSales')">
                                <h6 class="mb-0 ms-2">🎯 المبيعات المستهدفة والعمولات</h6>
                            </div>
                            <span class="permission-count">
                                الصلاحيات النشطة: <span id="activeCountTargetedSalesCommissions">0</span>/4
                            </span>
                        </div>
                        <div class="permission-grid">
                            <div class="permission-item-new" onclick="togglePermission('targeted_sales_commissions_manage_sales_periods')">
                                <input type="checkbox" id="targeted_sales_commissions_manage_sales_periods" name="targeted_sales_commissions_manage_sales_periods" class="custom-checkbox-new targeted-sales-commissions permission-main-checkbox sales-checkbox">
                                <label for="targeted_sales_commissions_manage_sales_periods" class="permission-label-new">إدارة فترات المبيعات</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('targeted_sales_commissions_view_all_sales_commissions')">
                                <input type="checkbox" id="targeted_sales_commissions_view_all_sales_commissions" name="targeted_sales_commissions_view_all_sales_commissions" class="custom-checkbox-new targeted-sales-commissions permission-main-checkbox sales-checkbox">
                                <label for="targeted_sales_commissions_view_all_sales_commissions" class="permission-label-new">عرض جميع عمولات المبيعات</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('targeted_sales_commissions_view_own_sales_commissions')">
                                <input type="checkbox" id="targeted_sales_commissions_view_own_sales_commissions" name="targeted_sales_commissions_view_own_sales_commissions" class="custom-checkbox-new targeted-sales-commissions permission-main-checkbox sales-checkbox">
                                <label for="targeted_sales_commissions_view_own_sales_commissions" class="permission-label-new">عرض عمولات المبيعات الخاصة به</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('targeted_sales_commissions_manage_commission_rules')">
                                <input type="checkbox" id="targeted_sales_commissions_manage_commission_rules" name="targeted_sales_commissions_manage_commission_rules" class="custom-checkbox-new targeted-sales-commissions permission-main-checkbox sales-checkbox">
                                <label for="targeted_sales_commissions_manage_commission_rules" class="permission-label-new">إدارة قواعد العمولة</label>
                            </div>
                        </div>
                    </div>

                    <!-- المنتجات -->
                    <div class="permission-section">
                        <div class="permission-header">
                            <div class="permission-title">
                                <input type="checkbox" id="SelectAllProducts" class="custom-checkbox-new permission-main-checkbox store-checkbox" onclick="toggleSectionPermissions('products')">
                                <h6 class="mb-0 ms-2">📦 المنتجات</h6>
                            </div>
                            <span class="permission-count">
                                الصلاحيات النشطة: <span id="activeCountProducts">0</span>/8
                            </span>
                        </div>
                        <div class="permission-grid">
                            <div class="permission-item-new" onclick="togglePermission('products_add_product')">
                                <input type="checkbox" id="products_add_product" name="products_add_product" class="custom-checkbox-new select-all-products permission-main-checkbox store-checkbox">
                                <label for="products_add_product" class="permission-label-new">إضافة منتج</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('products_view_all_products')">
                                <input type="checkbox" id="products_view_all_products" name="products_view_all_products" class="custom-checkbox-new select-all-products permission-main-checkbox store-checkbox">
                                <label for="products_view_all_products" class="permission-label-new">عرض كل المنتجات</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('products_view_own_products')">
                                <input type="checkbox" id="products_view_own_products" name="products_view_own_products" class="custom-checkbox-new select-all-products permission-main-checkbox store-checkbox">
                                <label for="products_view_own_products" class="permission-label-new">عرض المنتجات الخاصة به</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('products_edit_delete_all_products')">
                                <input type="checkbox" id="products_edit_delete_all_products" name="products_edit_delete_all_products" class="custom-checkbox-new select-all-products permission-main-checkbox store-checkbox">
                                <label for="products_edit_delete_all_products" class="permission-label-new">تعديل وحذف كل المنتجات</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('products_edit_delete_own_products')">
                                <input type="checkbox" id="products_edit_delete_own_products" name="products_edit_delete_own_products" class="custom-checkbox-new select-all-products permission-main-checkbox store-checkbox">
                                <label for="products_edit_delete_own_products" class="permission-label-new">تعديل وحذف المنتجات الخاصة به</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('products_view_price_groups')">
                                <input type="checkbox" id="products_view_price_groups" name="products_view_price_groups" class="custom-checkbox-new select-all-products permission-main-checkbox store-checkbox">
                                <label for="products_view_price_groups" class="permission-label-new">عرض مجموعة الأسعار</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('products_add_edit_price_groups')">
                                <input type="checkbox" id="products_add_edit_price_groups" name="products_add_edit_price_groups" class="custom-checkbox-new select-all-products permission-main-checkbox store-checkbox">
                                <label for="products_add_edit_price_groups" class="permission-label-new">إضافة وتعديل مجموعة أسعار</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('products_delete_price_groups')">
                                <input type="checkbox" id="products_delete_price_groups" name="products_delete_price_groups" class="custom-checkbox-new select-all-products permission-main-checkbox store-checkbox">
                                <label for="products_delete_price_groups" class="permission-label-new">حذف مجموعة أسعار</label>
                            </div>
                        </div>
                    </div>

                    <!-- الفاتورة الإلكترونية السعودية -->
                    <div class="permission-section">
                        <div class="permission-header">
                            <div class="permission-title">
                                <input type="checkbox" id="SelectAllSaudiElectronicInvoice" class="custom-checkbox-new permission-main-checkbox setting-checkbox" onclick="toggleSectionPermissions('saudiInvoice')">
                                <h6 class="mb-0 ms-2">🧾 الفاتورة الإلكترونية السعودية</h6>
                            </div>
                            <span class="permission-count">
                                الصلاحيات النشطة: <span id="activeCountSaudiElectronicInvoice">0</span>/1
                            </span>
                        </div>
                        <div class="permission-grid">
                            <div class="permission-item-new" onclick="togglePermission('sending_invoices_to_the_tax_authority')">
                                <input type="checkbox" id="sending_invoices_to_the_tax_authority" name="sending_invoices_to_the_tax_authority" class="custom-checkbox-new select-all-Saudi-electronic-invoice permission-main-checkbox setting-checkbox">
                                <label for="sending_invoices_to_the_tax_authority" class="permission-label-new">إرسال الفواتير إلى هيئة الضرائب</label>
                            </div>
                        </div>
                    </div>

                    <!-- التأمينات -->
                    <div class="permission-section">
                        <div class="permission-header">
                            <div class="permission-title">
                                <input type="checkbox" id="SelectAllInsurances" class="custom-checkbox-new permission-main-checkbox sales-checkbox" onclick="toggleSectionPermissions('insurances')">
                                <h6 class="mb-0 ms-2">🛡️ التأمينات</h6>
                            </div>
                            <span class="permission-count">
                                الصلاحيات النشطة: <span id="activeCountInsurances">0</span>/1
                            </span>
                        </div>
                        <div class="permission-grid">
                            <div class="permission-item-new" onclick="togglePermission('management_of_insurance_agents')">
                                <input type="checkbox" id="management_of_insurance_agents" name="management_of_insurance_agents" class="custom-checkbox-new select-all-insurances permission-main-checkbox sales-checkbox">
                                <label for="management_of_insurance_agents" class="permission-label-new">إدارة وكلاء التأمين</label>
                            </div>
                        </div>
                    </div>

                    <!-- متابعة العميل -->
                    <div class="permission-section">
                        <div class="permission-header">
                            <div class="permission-title">
                                <input type="checkbox" id="SelectAllClientFollowUp" class="custom-checkbox-new permission-main-checkbox customer-checkbox" onclick="toggleSectionPermissions('clientFollowUp')">
                                <h6 class="mb-0 ms-2">👀 متابعة العميل</h6>
                            </div>
                            <span class="permission-count">
                                الصلاحيات النشطة: <span id="activeCountClientFollowUp">0</span>/8
                            </span>
                        </div>
                        <div class="permission-grid">
                            <div class="permission-item-new" onclick="togglePermission('client_follow_up_add_notes_attachments_appointments_all')">
                                <input type="checkbox" id="client_follow_up_add_notes_attachments_appointments_all" name="client_follow_up_add_notes_attachments_appointments_all" class="custom-checkbox-new select-all-client-follow-up permission-main-checkbox customer-checkbox">
                                <label for="client_follow_up_add_notes_attachments_appointments_all" class="permission-label-new">إضافة ملاحظات / مرفقات / مواعيد لجميع العملاء</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('client_follow_up_add_notes_attachments_appointments_own')">
                                <input type="checkbox" id="client_follow_up_add_notes_attachments_appointments_own" name="client_follow_up_add_notes_attachments_appointments_own" class="custom-checkbox-new select-all-client-follow-up permission-main-checkbox customer-checkbox">
                                <label for="client_follow_up_add_notes_attachments_appointments_own" class="permission-label-new">إضافة ملاحظات / مرفقات / مواعيد لعملائه المعينين</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('client_follow_up_edit_delete_notes_attachments_appointments_all')">
                                <input type="checkbox" id="client_follow_up_edit_delete_notes_attachments_appointments_all" name="client_follow_up_edit_delete_notes_attachments_appointments_all" class="custom-checkbox-new select-all-client-follow-up permission-main-checkbox customer-checkbox">
                                <label for="client_follow_up_edit_delete_notes_attachments_appointments_all" class="permission-label-new">تعديل وحذف جميع الملاحظات / المرفقات / مواعيد لجميع العملاء</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('client_follow_up_edit_delete_notes_attachments_appointments_own')">
                                <input type="checkbox" id="client_follow_up_edit_delete_notes_attachments_appointments_own" name="client_follow_up_edit_delete_notes_attachments_appointments_own" class="custom-checkbox-new select-all-client-follow-up permission-main-checkbox customer-checkbox">
                                <label for="client_follow_up_edit_delete_notes_attachments_appointments_own" class="permission-label-new">تعديل وحذف ملاحظاته - مرفقاته ومواعيده الخاصة</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('client_follow_up_view_notes_attachments_appointments_all')">
                                <input type="checkbox" id="client_follow_up_view_notes_attachments_appointments_all" name="client_follow_up_view_notes_attachments_appointments_all" class="custom-checkbox-new select-all-client-follow-up permission-main-checkbox customer-checkbox">
                                <label for="client_follow_up_view_notes_attachments_appointments_all" class="permission-label-new">عرض جميع الملاحظات / المرفقات / المواعيد لجميع العملاء</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('client_follow_up_view_notes_attachments_appointments_assigned')">
                                <input type="checkbox" id="client_follow_up_view_notes_attachments_appointments_assigned" name="client_follow_up_view_notes_attachments_appointments_assigned" class="custom-checkbox-new select-all-client-follow-up permission-main-checkbox customer-checkbox">
                                <label for="client_follow_up_view_notes_attachments_appointments_assigned" class="permission-label-new">عرض جميع الملاحظات / المرفقات / المواعيد لعملائه المعينين</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('client_follow_up_view_notes_attachments_appointments_own')">
                                <input type="checkbox" id="client_follow_up_view_notes_attachments_appointments_own" name="client_follow_up_view_notes_attachments_appointments_own" class="custom-checkbox-new select-all-client-follow-up permission-main-checkbox customer-checkbox">
                                <label for="client_follow_up_view_notes_attachments_appointments_own" class="permission-label-new">عرض كافة ملاحظاته / مرفقاته / مواعيده الخاصة</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('client_follow_up_assign_clients_to_employees')">
                                <input type="checkbox" id="client_follow_up_assign_clients_to_employees" name="client_follow_up_assign_clients_to_employees" class="custom-checkbox-new select-all-client-follow-up permission-main-checkbox customer-checkbox">
                                <label for="client_follow_up_assign_clients_to_employees" class="permission-label-new">تعيين العملاء إلى الموظفين</label>
                            </div>
                        </div>
                    </div>

                    <!-- العملاء -->
                    <div class="permission-section">
                        <div class="permission-header">
                            <div class="permission-title">
                                <input type="checkbox" id="SelectAllCustomers" class="custom-checkbox-new permission-main-checkbox customer-checkbox" onclick="toggleSectionPermissions('customers')">
                                <h6 class="mb-0 ms-2">👥 العملاء</h6>
                            </div>
                            <span class="permission-count">
                                الصلاحيات النشطة: <span id="activeCountCustomers">0</span>/10
                            </span>
                        </div>
                        <div class="permission-grid">
                            <div class="permission-item-new" onclick="togglePermission('clients_add_client')">
                                <input type="checkbox" id="clients_add_client" name="clients_add_client" class="custom-checkbox-new select-all-customers permission-main-checkbox customer-checkbox">
                                <label for="clients_add_client" class="permission-label-new">إضافة عميل جديد</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('clients_view_all_activity_logs')">
                                <input type="checkbox" id="clients_view_all_activity_logs" name="clients_view_all_activity_logs" class="custom-checkbox-new select-all-customers permission-main-checkbox customer-checkbox">
                                <label for="clients_view_all_activity_logs" class="permission-label-new">عرض جميع سجلات الأنشطة</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('clients_view_own_activity_log')">
                                <input type="checkbox" id="clients_view_own_activity_log" name="clients_view_own_activity_log" class="custom-checkbox-new select-all-customers permission-main-checkbox customer-checkbox">
                                <label for="clients_view_own_activity_log" class="permission-label-new">عرض سجل نشاطه</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('clients_edit_client_settings')">
                                <input type="checkbox" id="clients_edit_client_settings" name="clients_edit_client_settings" class="custom-checkbox-new select-all-customers permission-main-checkbox customer-checkbox">
                                <label for="clients_edit_client_settings" class="permission-label-new">تعديل إعدادات العملاء</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('clients_view_all_reports')">
                                <input type="checkbox" id="clients_view_all_reports" name="clients_view_all_reports" class="custom-checkbox-new select-all-customers permission-main-checkbox customer-checkbox">
                                <label for="clients_view_all_reports" class="permission-label-new">عرض تقارير كل العملاء</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('clients_view_own_reports')">
                                <input type="checkbox" id="clients_view_own_reports" name="clients_view_own_reports" class="custom-checkbox-new select-all-customers permission-main-checkbox customer-checkbox">
                                <label for="clients_view_own_reports" class="permission-label-new">عرض تقارير العملاء الخاصة به</label>
                            </div>
                        </div>
                    </div>

                    <!-- النقاط والأرصدة -->
                    <div class="permission-section">
                        <div class="permission-header">
                            <div class="permission-title">
                                <input type="checkbox" id="SelectAllPointsBalances" class="custom-checkbox-new permission-main-checkbox customer-checkbox" onclick="toggleSectionPermissions('pointsBalances')">
                                <h6 class="mb-0 ms-2">💰 النقاط والأرصدة</h6>
                            </div>
                            <span class="permission-count">
                                الصلاحيات النشطة: <span id="activeCountPointsBalances">0</span>/4
                            </span>
                        </div>
                        <div class="permission-grid">
                            <div class="permission-item-new" onclick="togglePermission('points_credits_packages_manage')">
                                <input type="checkbox" id="points_credits_packages_manage" name="points_credits_packages_manage" class="custom-checkbox-new select-all-points-balances permission-main-checkbox customer-checkbox">
                                <label for="points_credits_packages_manage" class="permission-label-new">إدارة الباقات</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('points_credits_credit_recharge_manage')">
                                <input type="checkbox" id="points_credits_credit_recharge_manage" name="points_credits_credit_recharge_manage" class="custom-checkbox-new select-all-points-balances permission-main-checkbox customer-checkbox">
                                <label for="points_credits_credit_recharge_manage" class="permission-label-new">إدارة شحن الأرصدة</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('points_credits_credit_usage_manage')">
                                <input type="checkbox" id="points_credits_credit_usage_manage" name="points_credits_credit_usage_manage" class="custom-checkbox-new select-all-points-balances permission-main-checkbox customer-checkbox">
                                <label for="points_credits_credit_usage_manage" class="permission-label-new">إدارة استهلاك الأرصدة</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('points_credits_credit_settings_manage')">
                                <input type="checkbox" id="points_credits_credit_settings_manage" name="points_credits_credit_settings_manage" class="custom-checkbox-new select-all-points-balances permission-main-checkbox customer-checkbox">
                                <label for="points_credits_credit_settings_manage" class="permission-label-new">إدارة إعدادات الأرصدة</label>
                            </div>
                        </div>
                    </div>

                    <!-- العضوية -->
                    <div class="permission-section">
                        <div class="permission-header">
                            <div class="permission-title">
                                <input type="checkbox" id="SelectAllMemberships" class="custom-checkbox-new permission-main-checkbox customer-checkbox" onclick="toggleSectionPermissions('memberships')">
                                <h6 class="mb-0 ms-2">🎫 العضوية</h6>
                            </div>
                            <span class="permission-count">
                                الصلاحيات النشطة: <span id="activeCountMemberships">0</span>/2
                            </span>
                        </div>
                        <div class="permission-grid">
                            <div class="permission-item-new" onclick="togglePermission('membership_management')">
                                <input type="checkbox" id="membership_management" name="membership_management" class="custom-checkbox-new select-all-memberships permission-main-checkbox customer-checkbox">
                                <label for="membership_management" class="permission-label-new">إدارة العضويات</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('membership_setting_management')">
                                <input type="checkbox" id="membership_setting_management" name="membership_setting_management" class="custom-checkbox-new select-all-memberships permission-main-checkbox customer-checkbox">
                                <label for="membership_setting_management" class="permission-label-new">إدارة إعدادات العضوية</label>
                            </div>
                        </div>
                    </div>

                    <!-- حضور العملاء -->
                    <div class="permission-section">
                        <div class="permission-header">
                            <div class="permission-title">
                                <input type="checkbox" id="SelectAllCustomerAttendance" class="custom-checkbox-new permission-main-checkbox customer-checkbox" onclick="toggleSectionPermissions('customerAttendance')">
                                <h6 class="mb-0 ms-2">📝 حضور العملاء</h6>
                            </div>
                            <span class="permission-count">
                                الصلاحيات النشطة: <span id="activeCountCustomerAttendance">0</span>/2
                            </span>
                        </div>
                        <div class="permission-grid">
                            <div class="permission-item-new" onclick="togglePermission('customer_attendance_display')">
                                <input type="checkbox" id="customer_attendance_display" name="customer_attendance_display" class="custom-checkbox-new select-all-customer-attendance permission-main-checkbox customer-checkbox">
                                <label for="customer_attendance_display" class="permission-label-new">عرض حضور العملاء</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('customer_attendance_manage')">
                                <input type="checkbox" id="customer_attendance_manage" name="customer_attendance_manage" class="custom-checkbox-new select-all-customer-attendance permission-main-checkbox customer-checkbox">
                                <label for="customer_attendance_manage" class="permission-label-new">إدارة حضور العملاء</label>
                            </div>
                        </div>
                    </div>

                    <!-- الموظفين -->
                    <div class="permission-section">
                        <div class="permission-header">
                            <div class="permission-title">
                                <input type="checkbox" id="SelectAllEmployees" class="custom-checkbox-new permission-main-checkbox crm-checkbox" onclick="toggleSectionPermissions('employees')">
                                <h6 class="mb-0 ms-2">👨‍💼 الموظفين</h6>
                            </div>
                            <span class="permission-count">
                                الصلاحيات النشطة: <span id="activeCountEmployees">0</span>/5
                            </span>
                        </div>
                        <div class="permission-grid">
                            <div class="permission-item-new" onclick="togglePermission('employees_add')">
                                <input type="checkbox" id="employees_add" name="employees_add" class="custom-checkbox-new select-all-employees permission-main-checkbox crm-checkbox">
                                <label for="employees_add" class="permission-label-new">إضافة موظف جديد</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('employees_edit_delete')">
                                <input type="checkbox" id="employees_edit_delete" name="employees_edit_delete" class="custom-checkbox-new select-all-employees permission-main-checkbox crm-checkbox">
                                <label for="employees_edit_delete" class="permission-label-new">تعديل وحذف موظف</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('employees_roles_add')">
                                <input type="checkbox" id="employees_roles_add" name="employees_roles_add" class="custom-checkbox-new select-all-employees permission-main-checkbox crm-checkbox">
                                <label for="employees_roles_add" class="permission-label-new">إضافة دور وظيفي جديد</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('employees_roles_edit')">
                                <input type="checkbox" id="employees_roles_edit" name="employees_roles_edit" class="custom-checkbox-new select-all-employees permission-main-checkbox crm-checkbox">
                                <label for="employees_roles_edit" class="permission-label-new">تعديل الدور الوظيفي</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('employees_view_profile')">
                                <input type="checkbox" id="employees_view_profile" name="employees_view_profile" class="custom-checkbox-new select-all-employees permission-main-checkbox crm-checkbox">
                                <label for="employees_view_profile" class="permission-label-new">إظهار الملف الشخصي للموظف</label>
                            </div>
                        </div>
                    </div>

                    <!-- الهيكل التنظيمي -->
                    <div class="permission-section">
                        <div class="permission-header">
                            <div class="permission-title">
                                <input type="checkbox" id="SelectAllOrganizationalStructure" class="custom-checkbox-new permission-main-checkbox crm-checkbox" onclick="toggleSectionPermissions('organizationalStructure')">
                                <h6 class="mb-0 ms-2">🏢 الهيكل التنظيمي</h6>
                            </div>
                            <span class="permission-count">
                                الصلاحيات النشطة: <span id="activeCountOrganizationalStructure">0</span>/1
                            </span>
                        </div>
                        <div class="permission-grid">
                            <div class="permission-item-new" onclick="togglePermission('hr_system_management')">
                                <input type="checkbox" id="hr_system_management" name="hr_system_management" class="custom-checkbox-new select-all-organizational-structure permission-main-checkbox crm-checkbox">
                                <label for="hr_system_management" class="permission-label-new">إدارة نظام الموارد البشرية</label>
                            </div>
                        </div>
                    </div>

                    <!-- المرتبات -->
                    <div class="permission-section">
                        <div class="permission-header">
                            <div class="permission-title">
                                <input type="checkbox" id="SelectAllSalaries" class="custom-checkbox-new permission-main-checkbox account-checkbox" onclick="toggleSectionPermissions('salaries')">
                                <h6 class="mb-0 ms-2">💳 المرتبات</h6>
                            </div>
                            <span class="permission-count">
                                الصلاحيات النشطة: <span id="activeCountSalaries">0</span>/17
                            </span>
                        </div>
                        <div class="permission-grid">
                            <div class="permission-item-new" onclick="togglePermission('salaries_loans_manage')">
                                <input type="checkbox" id="salaries_loans_manage" name="salaries_loans_manage" class="custom-checkbox-new select-all-salaries permission-main-checkbox account-checkbox">
                                <label for="salaries_loans_manage" class="permission-label-new">إدارة السلفيات والأقساط</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('salaries_payroll_view')">
                                <input type="checkbox" id="salaries_payroll_view" name="salaries_payroll_view" class="custom-checkbox-new select-all-salaries permission-main-checkbox account-checkbox">
                                <label for="salaries_payroll_view" class="permission-label-new">عرض مسير الرواتب</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('salaries_payroll_create')">
                                <input type="checkbox" id="salaries_payroll_create" name="salaries_payroll_create" class="custom-checkbox-new select-all-salaries permission-main-checkbox account-checkbox">
                                <label for="salaries_payroll_create" class="permission-label-new">إنشاء مسير رواتب</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('salaries_payroll_approve')">
                                <input type="checkbox" id="salaries_payroll_approve" name="salaries_payroll_approve" class="custom-checkbox-new select-all-salaries permission-main-checkbox account-checkbox">
                                <label for="salaries_payroll_approve" class="permission-label-new">موافقة قسائم الرواتب</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('salaries_payroll_edit')">
                                <input type="checkbox" id="salaries_payroll_edit" name="salaries_payroll_edit" class="custom-checkbox-new select-all-salaries permission-main-checkbox account-checkbox">
                                <label for="salaries_payroll_edit" class="permission-label-new">تعديل قسائم الرواتب</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('salaries_payroll_delete')">
                                <input type="checkbox" id="salaries_payroll_delete" name="salaries_payroll_delete" class="custom-checkbox-new select-all-salaries permission-main-checkbox account-checkbox">
                                <label for="salaries_payroll_delete" class="permission-label-new">مسح مدفوعات مسير الرواتب</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('salaries_contracts_notifications')">
                                <input type="checkbox" id="salaries_contracts_notifications" name="salaries_contracts_notifications" class="custom-checkbox-new select-all-salaries permission-main-checkbox account-checkbox">
                                <label for="salaries_contracts_notifications" class="permission-label-new">إشعارات العقد</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('salaries_contracts_edit_delete_own')">
                                <input type="checkbox" id="salaries_contracts_edit_delete_own" name="salaries_contracts_edit_delete_own" class="custom-checkbox-new select-all-salaries permission-main-checkbox account-checkbox">
                                <label for="salaries_contracts_edit_delete_own" class="permission-label-new">تعديل / مسح العقود الخاصة به</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('salaries_payroll_settings_manage')">
                                <input type="checkbox" id="salaries_payroll_settings_manage" name="salaries_payroll_settings_manage" class="custom-checkbox-new select-all-salaries permission-main-checkbox account-checkbox">
                                <label for="salaries_payroll_settings_manage" class="permission-label-new">إدارة إعدادات المرتبات</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('salaries_payroll_view_own')">
                                <input type="checkbox" id="salaries_payroll_view_own" name="salaries_payroll_view_own" class="custom-checkbox-new select-all-salaries permission-main-checkbox account-checkbox">
                                <label for="salaries_payroll_view_own" class="permission-label-new">عرض قسيمة الراتب الخاصة به</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('salaries_payroll_delete_all')">
                                <input type="checkbox" id="salaries_payroll_delete_all" name="salaries_payroll_delete_all" class="custom-checkbox-new select-all-salaries permission-main-checkbox account-checkbox">
                                <label for="salaries_payroll_delete_all" class="permission-label-new">مسح مسير الرواتب</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('salaries_payroll_payment')">
                                <input type="checkbox" id="salaries_payroll_payment" name="salaries_payroll_payment" class="custom-checkbox-new select-all-salaries permission-main-checkbox account-checkbox">
                                <label for="salaries_payroll_payment" class="permission-label-new">دفع قسائم الرواتب</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('salaries_contracts_receive_notifications')">
                                <input type="checkbox" id="salaries_contracts_receive_notifications" name="salaries_contracts_receive_notifications" class="custom-checkbox-new select-all-salaries permission-main-checkbox account-checkbox">
                                <label for="salaries_contracts_receive_notifications" class="permission-label-new">استلام إشعارات العقود</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('salaries_contracts_view_all')">
                                <input type="checkbox" id="salaries_contracts_view_all" name="salaries_contracts_view_all" class="custom-checkbox-new select-all-salaries permission-main-checkbox account-checkbox">
                                <label for="salaries_contracts_view_all" class="permission-label-new">عرض جميع العقود</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('salaries_contracts_edit_delete_all')">
                                <input type="checkbox" id="salaries_contracts_edit_delete_all" name="salaries_contracts_edit_delete_all" class="custom-checkbox-new select-all-salaries permission-main-checkbox account-checkbox">
                                <label for="salaries_contracts_edit_delete_all" class="permission-label-new">تعديل /مسح جميع العقود</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('salaries_contracts_create')">
                                <input type="checkbox" id="salaries_contracts_create" name="salaries_contracts_create" class="custom-checkbox-new select-all-salaries permission-main-checkbox account-checkbox">
                                <label for="salaries_contracts_create" class="permission-label-new">إنشاء عقود</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('salaries_contracts_view_own')">
                                <input type="checkbox" id="salaries_contracts_view_own" name="salaries_contracts_view_own" class="custom-checkbox-new select-all-salaries permission-main-checkbox account-checkbox">
                                <label for="salaries_contracts_view_own" class="permission-label-new">عرض العقود الخاصة به</label>
                            </div>
                        </div>
                    </div>

                    <!-- حضور الموظفين -->
                    <div class="permission-section">
                        <div class="permission-header">
                            <div class="permission-title">
                                <input type="checkbox" id="SelectAllStaffAttendance" class="custom-checkbox-new permission-main-checkbox crm-checkbox" onclick="toggleSectionPermissions('staffAttendance')">
                                <h6 class="mb-0 ms-2">📋 حضور الموظفين</h6>
                            </div>
                            <span class="permission-count">
                                الصلاحيات النشطة: <span id="activeCountStaffAttendance">0</span>/23
                            </span>
                        </div>
                        <div class="permission-grid">
                            <div class="permission-item-new" onclick="togglePermission('staff_attendance_online')">
                                <input type="checkbox" id="staff_attendance_online" name="staff_attendance_online" class="custom-checkbox-new select-all-staff-attendance permission-main-checkbox crm-checkbox">
                                <label for="staff_attendance_online" class="permission-label-new">تسجيل حضور الموظفين (أونلاين)</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('staff_attendance_pull_from_device')">
                                <input type="checkbox" id="staff_attendance_pull_from_device" name="staff_attendance_pull_from_device" class="custom-checkbox-new select-all-staff-attendance permission-main-checkbox crm-checkbox">
                                <label for="staff_attendance_pull_from_device" class="permission-label-new">سحب سجل الحضور من الجهاز</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('staff_attendance_view_all')">
                                <input type="checkbox" id="staff_attendance_view_all" name="staff_attendance_view_all" class="custom-checkbox-new select-all-staff-attendance permission-main-checkbox crm-checkbox">
                                <label for="staff_attendance_view_all" class="permission-label-new">عرض كل سجلات الحضور</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('staff_attendance_settings_manage')">
                                <input type="checkbox" id="staff_attendance_settings_manage" name="staff_attendance_settings_manage" class="custom-checkbox-new select-all-staff-attendance permission-main-checkbox crm-checkbox">
                                <label for="staff_attendance_settings_manage" class="permission-label-new">إدارة إعدادات الحضور</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('staff_attendance_delete')">
                                <input type="checkbox" id="staff_attendance_delete" name="staff_attendance_delete" class="custom-checkbox-new select-all-staff-attendance permission-main-checkbox crm-checkbox">
                                <label for="staff_attendance_delete" class="permission-label-new">مسح سجل الحضور</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('staff_attendance_edit_days')">
                                <input type="checkbox" id="staff_attendance_edit_days" name="staff_attendance_edit_days" class="custom-checkbox-new select-all-staff-attendance permission-main-checkbox crm-checkbox">
                                <label for="staff_attendance_edit_days" class="permission-label-new">تعديل أيام الحضور</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('staff_attendance_view_own')">
                                <input type="checkbox" id="staff_attendance_view_own" name="staff_attendance_view_own" class="custom-checkbox-new select-all-staff-attendance permission-main-checkbox crm-checkbox">
                                <label for="staff_attendance_view_own" class="permission-label-new">عرض دفاتر الحضور الخاصة به</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('staff_attendance_change_status')">
                                <input type="checkbox" id="staff_attendance_change_status" name="staff_attendance_change_status" class="custom-checkbox-new select-all-staff-attendance permission-main-checkbox crm-checkbox">
                                <label for="staff_attendance_change_status" class="permission-label-new">تغيير حالة دفاتر الحضور</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('staff_attendance_report_view')">
                                <input type="checkbox" id="staff_attendance_report_view" name="staff_attendance_report_view" class="custom-checkbox-new select-all-staff-attendance permission-main-checkbox crm-checkbox">
                                <label for="staff_attendance_report_view" class="permission-label-new">عرض تقرير الحضور</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('staff_leave_requests_edit_delete_own')">
                                <input type="checkbox" id="staff_leave_requests_edit_delete_own" name="staff_leave_requests_edit_delete_own" class="custom-checkbox-new select-all-staff-attendance permission-main-checkbox crm-checkbox">
                                <label for="staff_leave_requests_edit_delete_own" class="permission-label-new">تعديل / حذف طلبات إجازاته فقط</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('staff_leave_requests_view_own')">
                                <input type="checkbox" id="staff_leave_requests_view_own" name="staff_leave_requests_view_own" class="custom-checkbox-new select-all-staff-attendance permission-main-checkbox crm-checkbox">
                                <label for="staff_leave_requests_view_own" class="permission-label-new">عرض طلبات إجازاته فقط</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('staff_leave_requests_approve_reject')">
                                <input type="checkbox" id="staff_leave_requests_approve_reject" name="staff_leave_requests_approve_reject" class="custom-checkbox-new select-all-staff-attendance permission-main-checkbox crm-checkbox">
                                <label for="staff_leave_requests_approve_reject" class="permission-label-new">الموافقة على / رفض طلبات الإجازة</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('staff_attendance_self_registration')">
                                <input type="checkbox" id="staff_attendance_self_registration" name="staff_attendance_self_registration" class="custom-checkbox-new select-all-staff-attendance permission-main-checkbox crm-checkbox">
                                <label for="staff_attendance_self_registration" class="permission-label-new">تسجيل الحضور بنفسه أون لاين</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('staff_attendance_import')">
                                <input type="checkbox" id="staff_attendance_import" name="staff_attendance_import" class="custom-checkbox-new select-all-staff-attendance permission-main-checkbox crm-checkbox">
                                <label for="staff_attendance_import" class="permission-label-new">استيراد سجل الحضور</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('staff_attendance_view_own_records')">
                                <input type="checkbox" id="staff_attendance_view_own_records" name="staff_attendance_view_own_records" class="custom-checkbox-new select-all-staff-attendance permission-main-checkbox crm-checkbox">
                                <label for="staff_attendance_view_own_records" class="permission-label-new">عرض كل سجلات الحضور الخاصة به</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('staff_inventory_permissions_manage')">
                                <input type="checkbox" id="staff_inventory_permissions_manage" name="staff_inventory_permissions_manage" class="custom-checkbox-new select-all-staff-attendance permission-main-checkbox crm-checkbox">
                                <label for="staff_inventory_permissions_manage" class="permission-label-new">إدارة الأذونات المخزنية</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('staff_attendance_calculate_days')">
                                <input type="checkbox" id="staff_attendance_calculate_days" name="staff_attendance_calculate_days" class="custom-checkbox-new select-all-staff-attendance permission-main-checkbox crm-checkbox">
                                <label for="staff_attendance_calculate_days" class="permission-label-new">حساب أيام الحضور</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('staff_attendance_create_book')">
                                <input type="checkbox" id="staff_attendance_create_book" name="staff_attendance_create_book" class="custom-checkbox-new select-all-staff-attendance permission-main-checkbox crm-checkbox">
                                <label for="staff_attendance_create_book" class="permission-label-new">إنشاء دفتر حضور</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('staff_attendance_view_other_books')">
                                <input type="checkbox" id="staff_attendance_view_other_books" name="staff_attendance_view_other_books" class="custom-checkbox-new select-all-staff-attendance permission-main-checkbox crm-checkbox">
                                <label for="staff_attendance_view_other_books" class="permission-label-new">عرض دفاتر الحضور الأخرى</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('staff_attendance_delete_books')">
                                <input type="checkbox" id="staff_attendance_delete_books" name="staff_attendance_delete_books" class="custom-checkbox-new select-all-staff-attendance permission-main-checkbox crm-checkbox">
                                <label for="staff_attendance_delete_books" class="permission-label-new">مسح دفاتر الحضور</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('staff_leave_requests_add')">
                                <input type="checkbox" id="staff_leave_requests_add" name="staff_leave_requests_add" class="custom-checkbox-new select-all-staff-attendance permission-main-checkbox crm-checkbox">
                                <label for="staff_leave_requests_add" class="permission-label-new">إضافة طلب إجازة</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('staff_leave_requests_edit_delete_all')">
                                <input type="checkbox" id="staff_leave_requests_edit_delete_all" name="staff_leave_requests_edit_delete_all" class="custom-checkbox-new select-all-staff-attendance permission-main-checkbox crm-checkbox">
                                <label for="staff_leave_requests_edit_delete_all" class="permission-label-new">تعديل وحذف جميع طلبات الإجازات</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('staff_leave_requests_view_all')">
                                <input type="checkbox" id="staff_leave_requests_view_all" name="staff_leave_requests_view_all" class="custom-checkbox-new select-all-staff-attendance permission-main-checkbox crm-checkbox">
                                <label for="staff_leave_requests_view_all" class="permission-label-new">عرض جميع طلبات الإجازة</label>
                            </div>
                        </div>
                    </div>

                    <!-- الطلبات -->
                    <div class="permission-section">
                        <div class="permission-header">
                            <div class="permission-title">
                                <input type="checkbox" id="SelectAllOrders" class="custom-checkbox-new permission-main-checkbox sales-checkbox" onclick="toggleSectionPermissions('orders')">
                                <h6 class="mb-0 ms-2">📋 الطلبات</h6>
                            </div>
                            <span class="permission-count">
                                الصلاحيات النشطة: <span id="activeCountOrders">0</span>/2
                            </span>
                        </div>
                        <div class="permission-grid">
                            <div class="permission-item-new" onclick="togglePermission('orders_management')">
                                <input type="checkbox" id="orders_management" name="orders_management" class="custom-checkbox-new select-all-orders permission-main-checkbox sales-checkbox">
                                <label for="orders_management" class="permission-label-new">إدارة الطلبات</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('orders_setting_management')">
                                <input type="checkbox" id="orders_setting_management" name="orders_setting_management" class="custom-checkbox-new select-all-orders permission-main-checkbox sales-checkbox">
                                <label for="orders_setting_management" class="permission-label-new">إدارة إعدادات الطلبات</label>
                            </div>
                        </div>
                    </div>

                    <!-- إدارة المخزون -->
                    <div class="permission-section">
                        <div class="permission-header">
                            <div class="permission-title">
                                <input type="checkbox" id="SelectAllInventoryManagement" class="custom-checkbox-new permission-main-checkbox store-checkbox" onclick="toggleSectionPermissions('inventoryManagement')">
                                <h6 class="mb-0 ms-2">📦 إدارة المخزون</h6>
                            </div>
                            <span class="permission-count">
                                الصلاحيات النشطة: <span id="activeCountInventoryManagement">0</span>/19
                            </span>
                        </div>
                        <div class="permission-grid">
                            <div class="permission-item-new" onclick="togglePermission('inv_manage_inventory_permission_add')">
                                <input type="checkbox" id="inv_manage_inventory_permission_add" name="inv_manage_inventory_permission_add" class="custom-checkbox-new select-all-inventory-management permission-main-checkbox store-checkbox">
                                <label for="inv_manage_inventory_permission_add" class="permission-label-new">إضافة إذن مخزني</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('inv_manage_inventory_permission_view')">
                                <input type="checkbox" id="inv_manage_inventory_permission_view" name="inv_manage_inventory_permission_view" class="custom-checkbox-new select-all-inventory-management permission-main-checkbox store-checkbox">
                                <label for="inv_manage_inventory_permission_view" class="permission-label-new">عرض الإذن المخزني</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('inv_manage_inventory_price_edit')">
                                <input type="checkbox" id="inv_manage_inventory_price_edit" name="inv_manage_inventory_price_edit" class="custom-checkbox-new select-all-inventory-management permission-main-checkbox store-checkbox">
                                <label for="inv_manage_inventory_price_edit" class="permission-label-new">تعديل سعر حركة المخزون</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('inv_manage_purchase_invoices_view_own')">
                                <input type="checkbox" id="inv_manage_purchase_invoices_view_own" name="inv_manage_purchase_invoices_view_own" class="custom-checkbox-new select-all-inventory-management permission-main-checkbox store-checkbox">
                                <label for="inv_manage_purchase_invoices_view_own" class="permission-label-new">عرض فواتير الشراء الخاصة به</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('inv_manage_suppliers_add')">
                                <input type="checkbox" id="inv_manage_suppliers_add" name="inv_manage_suppliers_add" class="custom-checkbox-new select-all-inventory-management permission-main-checkbox store-checkbox">
                                <label for="inv_manage_suppliers_add" class="permission-label-new">إضافة موردين جدد</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('inv_manage_suppliers_view_all')">
                                <input type="checkbox" id="inv_manage_suppliers_view_all" name="inv_manage_suppliers_view_all" class="custom-checkbox-new select-all-inventory-management permission-main-checkbox store-checkbox">
                                <label for="inv_manage_suppliers_view_all" class="permission-label-new">عرض كل الموردين</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('inv_manage_suppliers_edit_delete_all')">
                                <input type="checkbox" id="inv_manage_suppliers_edit_delete_all" name="inv_manage_suppliers_edit_delete_all" class="custom-checkbox-new select-all-inventory-management permission-main-checkbox store-checkbox">
                                <label for="inv_manage_suppliers_edit_delete_all" class="permission-label-new">تعديل وحذف كل الموردين</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('inv_manage_inventory_edit_quantity')">
                                <input type="checkbox" id="inv_manage_inventory_edit_quantity" name="inv_manage_inventory_edit_quantity" class="custom-checkbox-new select-all-inventory-management permission-main-checkbox store-checkbox">
                                <label for="inv_manage_inventory_edit_quantity" class="permission-label-new">تعديل عدد المنتجات بالمخزون</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('inv_manage_inventory_transfer')">
                                <input type="checkbox" id="inv_manage_inventory_transfer" name="inv_manage_inventory_transfer" class="custom-checkbox-new select-all-inventory-management permission-main-checkbox store-checkbox">
                                <label for="inv_manage_inventory_transfer" class="permission-label-new">نقل المخزون</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('inv_manage_inventory_permission_edit')">
                                <input type="checkbox" id="inv_manage_inventory_permission_edit" name="inv_manage_inventory_permission_edit" class="custom-checkbox-new select-all-inventory-management permission-main-checkbox store-checkbox">
                                <label for="inv_manage_inventory_permission_edit" class="permission-label-new">تعديل الإذن المخزني</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('inv_manage_inventory_view_price')">
                                <input type="checkbox" id="inv_manage_inventory_view_price" name="inv_manage_inventory_view_price" class="custom-checkbox-new select-all-inventory-management permission-main-checkbox store-checkbox">
                                <label for="inv_manage_inventory_view_price" class="permission-label-new">عرض سعر حركة المخزون</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('inv_manage_purchase_invoice_add')">
                                <input type="checkbox" id="inv_manage_purchase_invoice_add" name="inv_manage_purchase_invoice_add" class="custom-checkbox-new select-all-inventory-management permission-main-checkbox store-checkbox">
                                <label for="inv_manage_purchase_invoice_add" class="permission-label-new">إضافة فاتورة شراء جديدة</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('inv_manage_purchase_invoice_edit_delete_own')">
                                <input type="checkbox" id="inv_manage_purchase_invoice_edit_delete_own" name="inv_manage_purchase_invoice_edit_delete_own" class="custom-checkbox-new select-all-inventory-management permission-main-checkbox store-checkbox">
                                <label for="inv_manage_purchase_invoice_edit_delete_own" class="permission-label-new">تعديل أو حذف فواتير الشراء الخاصة به</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('inv_manage_purchase_invoice_edit_delete_all')">
                                <input type="checkbox" id="inv_manage_purchase_invoice_edit_delete_all" name="inv_manage_purchase_invoice_edit_delete_all" class="custom-checkbox-new select-all-inventory-management permission-main-checkbox store-checkbox">
                                <label for="inv_manage_purchase_invoice_edit_delete_all" class="permission-label-new">تعديل أو حذف كل فواتير الشراء</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('inv_manage_purchase_invoices_view_all')">
                                <input type="checkbox" id="inv_manage_purchase_invoices_view_all" name="inv_manage_purchase_invoices_view_all" class="custom-checkbox-new select-all-inventory-management permission-main-checkbox store-checkbox">
                                <label for="inv_manage_purchase_invoices_view_all" class="permission-label-new">عرض كل فواتير الشراء</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('inv_manage_suppliers_view_created')">
                                <input type="checkbox" id="inv_manage_suppliers_view_created" name="inv_manage_suppliers_view_created" class="custom-checkbox-new select-all-inventory-management permission-main-checkbox store-checkbox">
                                <label for="inv_manage_suppliers_view_created" class="permission-label-new">عرض الموردين الذين تم إنشاؤهم</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('inv_manage_allow_sale_below_min_price')">
                                <input type="checkbox" id="inv_manage_allow_sale_below_min_price" name="inv_manage_allow_sale_below_min_price" class="custom-checkbox-new select-all-inventory-management permission-main-checkbox store-checkbox">
                                <label for="inv_manage_allow_sale_below_min_price" class="permission-label-new">السماح للبيع بأقل من السعر الأدنى للمنتج</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('inv_manage_inventory_monitor')">
                                <input type="checkbox" id="inv_manage_inventory_monitor" name="inv_manage_inventory_monitor" class="custom-checkbox-new select-all-inventory-management permission-main-checkbox store-checkbox">
                                <label for="inv_manage_inventory_monitor" class="permission-label-new">متابعة المخزون</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('inv_manage_suppliers_edit_delete_own')">
                                <input type="checkbox" id="inv_manage_suppliers_edit_delete_own" name="inv_manage_suppliers_edit_delete_own" class="custom-checkbox-new select-all-inventory-management permission-main-checkbox store-checkbox">
                                <label for="inv_manage_suppliers_edit_delete_own" class="permission-label-new">تعديل وحذف الموردين الخاصين به</label>
                            </div>
                        </div>
                    </div>

                    <!-- دورة المشتريات -->
                    <div class="permission-section">
                        <div class="permission-header">
                            <div class="permission-title">
                                <input type="checkbox" id="SelectAllProcurementCycle" class="custom-checkbox-new permission-main-checkbox store-checkbox" onclick="toggleSectionPermissions('procurementCycle')">
                                <h6 class="mb-0 ms-2">🔄 دورة المشتريات</h6>
                            </div>
                            <span class="permission-count">
                                الصلاحيات النشطة: <span id="activeCountProcurementCycle">0</span>/7
                            </span>
                        </div>
                        <div class="permission-grid">
                            <div class="permission-item-new" onclick="togglePermission('purchase_cycle_orders_manage')">
                                <input type="checkbox" id="purchase_cycle_orders_manage" name="purchase_cycle_orders_manage" class="custom-checkbox-new select-all-procurement-cycle permission-main-checkbox store-checkbox">
                                <label for="purchase_cycle_orders_manage" class="permission-label-new">إدارة طلبات الشراء</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('purchase_cycle_quotes_manage')">
                                <input type="checkbox" id="purchase_cycle_quotes_manage" name="purchase_cycle_quotes_manage" class="custom-checkbox-new select-all-procurement-cycle permission-main-checkbox store-checkbox">
                                <label for="purchase_cycle_quotes_manage" class="permission-label-new">إدارة عروض أسعار المشتريات</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('purchase_cycle_quotes_to_orders')">
                                <input type="checkbox" id="purchase_cycle_quotes_to_orders" name="purchase_cycle_quotes_to_orders" class="custom-checkbox-new select-all-procurement-cycle permission-main-checkbox store-checkbox">
                                <label for="purchase_cycle_quotes_to_orders" class="permission-label-new">تحويل عروض أسعار المشتريات إلى أوامر الشراء</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('purchase_cycle_order_to_invoice')">
                                <input type="checkbox" id="purchase_cycle_order_to_invoice" name="purchase_cycle_order_to_invoice" class="custom-checkbox-new select-all-procurement-cycle permission-main-checkbox store-checkbox">
                                <label for="purchase_cycle_order_to_invoice" class="permission-label-new">تحويل أمر الشراء إلى فاتورة شراء</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('purchase_cycle_orders_approve_reject')">
                                <input type="checkbox" id="purchase_cycle_orders_approve_reject" name="purchase_cycle_orders_approve_reject" class="custom-checkbox-new select-all-procurement-cycle permission-main-checkbox store-checkbox">
                                <label for="purchase_cycle_orders_approve_reject" class="permission-label-new">موافقة/رفض طلبات الشراء</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('purchase_cycle_quotes_approve_reject')">
                                <input type="checkbox" id="purchase_cycle_quotes_approve_reject" name="purchase_cycle_quotes_approve_reject" class="custom-checkbox-new select-all-procurement-cycle permission-main-checkbox store-checkbox">
                                <label for="purchase_cycle_quotes_approve_reject" class="permission-label-new">موافقة/رفض عروض أسعار المشتريات</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('purchase_cycle_orders_manage_orders')">
                                <input type="checkbox" id="purchase_cycle_orders_manage_orders" name="purchase_cycle_orders_manage_orders" class="custom-checkbox-new select-all-procurement-cycle permission-main-checkbox store-checkbox">
                                <label for="purchase_cycle_orders_manage_orders" class="permission-label-new">إدارة أوامر الشراء</label>
                            </div>
                        </div>
                    </div>

                    <!-- إدارة أوامر التوريد -->
                    <div class="permission-section">
                        <div class="permission-header">
                            <div class="permission-title">
                                <input type="checkbox" id="SelectAllSupplyOrderManagement" class="custom-checkbox-new permission-main-checkbox account-checkbox" onclick="toggleSectionPermissions('supplyOrderManagement')">
                                <h6 class="mb-0 ms-2">📋 إدارة أوامر التوريد</h6>
                            </div>
                            <span class="permission-count">
                                الصلاحيات النشطة: <span id="activeCountSupplyOrderManagement">0</span>/6
                            </span>
                        </div>
                        <div class="permission-grid">
                            <div class="permission-item-new" onclick="togglePermission('supply_orders_view_all')">
                                <input type="checkbox" id="supply_orders_view_all" name="supply_orders_view_all" class="custom-checkbox-new select-all-supply-order-management permission-main-checkbox account-checkbox">
                                <label for="supply_orders_view_all" class="permission-label-new">عرض جميع أوامر التوريد</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('supply_orders_add')">
                                <input type="checkbox" id="supply_orders_add" name="supply_orders_add" class="custom-checkbox-new select-all-supply-order-management permission-main-checkbox account-checkbox">
                                <label for="supply_orders_add" class="permission-label-new">إضافة أوامر شغل</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('supply_orders_edit_delete_all')">
                                <input type="checkbox" id="supply_orders_edit_delete_all" name="supply_orders_edit_delete_all" class="custom-checkbox-new select-all-supply-order-management permission-main-checkbox account-checkbox">
                                <label for="supply_orders_edit_delete_all" class="permission-label-new">تعديل وحذف جميع أوامر التوريد</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('supply_orders_edit_delete_own')">
                                <input type="checkbox" id="supply_orders_edit_delete_own" name="supply_orders_edit_delete_own" class="custom-checkbox-new select-all-supply-order-management permission-main-checkbox account-checkbox">
                                <label for="supply_orders_edit_delete_own" class="permission-label-new">تعديل وحذف أوامر التوريد الخاصة به</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('supply_orders_update_status')">
                                <input type="checkbox" id="supply_orders_update_status" name="supply_orders_update_status" class="custom-checkbox-new select-all-supply-order-management permission-main-checkbox account-checkbox">
                                <label for="supply_orders_update_status" class="permission-label-new">تحديث حالة أمر التوريد</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('supply_orders_view_own')">
                                <input type="checkbox" id="supply_orders_view_own" name="supply_orders_view_own" class="custom-checkbox-new select-all-supply-order-management permission-main-checkbox account-checkbox">
                                <label for="supply_orders_view_own" class="permission-label-new">عرض أوامر التوريد الخاصة به</label>
                            </div>
                        </div>
                    </div>

                    <!-- تتبع الوقت -->
                    <div class="permission-section">
                        <div class="permission-header">
                            <div class="permission-title">
                                <input type="checkbox" id="SelectAllTrackTime" class="custom-checkbox-new permission-main-checkbox operating-checkbox" onclick="toggleSectionPermissions('trackTime')">
                                <h6 class="mb-0 ms-2">⏰ تتبع الوقت</h6>
                            </div>
                            <span class="permission-count">
                                الصلاحيات النشطة: <span id="activeCountTrackTime">0</span>/7
                            </span>
                        </div>
                        <div class="permission-grid">
                            <div class="permission-item-new" onclick="togglePermission('track_time_add_employee_work_hours')">
                                <input type="checkbox" id="track_time_add_employee_work_hours" name="track_time_add_employee_work_hours" class="custom-checkbox-new select-all-track-time permission-main-checkbox operating-checkbox">
                                <label for="track_time_add_employee_work_hours" class="permission-label-new">إضافة ساعات عمله</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('track_time_edit_other_employees_work_hours')">
                                <input type="checkbox" id="track_time_edit_other_employees_work_hours" name="track_time_edit_other_employees_work_hours" class="custom-checkbox-new select-all-track-time permission-main-checkbox operating-checkbox">
                                <label for="track_time_edit_other_employees_work_hours" class="permission-label-new">تعديل ساعات عمل الموظفين الآخرين</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('track_time_edit_delete_all_projects')">
                                <input type="checkbox" id="track_time_edit_delete_all_projects" name="track_time_edit_delete_all_projects" class="custom-checkbox-new select-all-track-time permission-main-checkbox operating-checkbox">
                                <label for="track_time_edit_delete_all_projects" class="permission-label-new">تعديل وحذف كل المشاريع</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('track_time_edit_delete_all_activities')">
                                <input type="checkbox" id="track_time_edit_delete_all_activities" name="track_time_edit_delete_all_activities" class="custom-checkbox-new select-all-track-time permission-main-checkbox operating-checkbox">
                                <label for="track_time_edit_delete_all_activities" class="permission-label-new">تعديل وحذف كل الأنشطة</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('track_time_view_other_employees_work_hours')">
                                <input type="checkbox" id="track_time_view_other_employees_work_hours" name="track_time_view_other_employees_work_hours" class="custom-checkbox-new select-all-track-time permission-main-checkbox operating-checkbox">
                                <label for="track_time_view_other_employees_work_hours" class="permission-label-new">عرض ساعات عمل الموظفين الآخرين</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('track_time_add_new_project')">
                                <input type="checkbox" id="track_time_add_new_project" name="track_time_add_new_project" class="custom-checkbox-new select-all-track-time permission-main-checkbox operating-checkbox">
                                <label for="track_time_add_new_project" class="permission-label-new">إضافة مشروع جديد</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('track_time_add_new_activity')">
                                <input type="checkbox" id="track_time_add_new_activity" name="track_time_add_new_activity" class="custom-checkbox-new select-all-track-time permission-main-checkbox operating-checkbox">
                                <label for="track_time_add_new_activity" class="permission-label-new">إضافة نشاط جديد</label>
                            </div>
                        </div>
                    </div>

                    <!-- إدارة الوحدات والإيجارات -->
                    <div class="permission-section">
                        <div class="permission-header">
                            <div class="permission-title">
                                <input type="checkbox" id="SelectAllRentalUnitManagement" class="custom-checkbox-new permission-main-checkbox account-checkbox" onclick="toggleSectionPermissions('rentalUnitManagement')">
                                <h6 class="mb-0 ms-2">🏠 إدارة الإيجارات والوحدات</h6>
                            </div>
                            <span class="permission-count">
                                الصلاحيات النشطة: <span id="activeCountRentalUnitManagement">0</span>/3
                            </span>
                        </div>
                        <div class="permission-grid">
                            <div class="permission-item-new" onclick="togglePermission('rental_unit_manage_rental_settings')">
                                <input type="checkbox" id="rental_unit_manage_rental_settings" name="rental_unit_manage_rental_settings" class="custom-checkbox-new select-all-rental-unit-management permission-main-checkbox account-checkbox">
                                <label for="rental_unit_manage_rental_settings" class="permission-label-new">إدارة وإعدادات الإيجارات</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('rental_unit_manage_booking_orders')">
                                <input type="checkbox" id="rental_unit_manage_booking_orders" name="rental_unit_manage_booking_orders" class="custom-checkbox-new select-all-rental-unit-management permission-main-checkbox account-checkbox">
                                <label for="rental_unit_manage_booking_orders" class="permission-label-new">إدارة أوامر الحجز</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('rental_unit_view_booking_orders')">
                                <input type="checkbox" id="rental_unit_view_booking_orders" name="rental_unit_view_booking_orders" class="custom-checkbox-new select-all-rental-unit-management permission-main-checkbox account-checkbox">
                                <label for="rental_unit_view_booking_orders" class="permission-label-new">عرض أوامر الحجز</label>
                            </div>
                        </div>
                    </div>

                    <!-- الحسابات العامة والقيود اليومية -->
                    <div class="permission-section">
                        <div class="permission-header">
                            <div class="permission-title">
                                <input type="checkbox" id="SelectAllGeneralAccountsDailyRestrictions" class="custom-checkbox-new permission-main-checkbox account-checkbox" onclick="toggleSectionPermissions('generalAccountsDailyRestrictions')">
                                <h6 class="mb-0 ms-2">📊 الحسابات العامة والقيود اليومية</h6>
                            </div>
                            <span class="permission-count">
                                الصلاحيات النشطة: <span id="activeCountGeneralAccountsDailyRestrictions">0</span>/11
                            </span>
                        </div>
                        <div class="permission-grid">
                            <div class="permission-item-new" onclick="togglePermission('g_a_d_r_add_new_assets')">
                                <input type="checkbox" id="g_a_d_r_add_new_assets" name="g_a_d_r_add_new_assets" class="custom-checkbox-new select-all-general-accounts-daily-restrictions permission-main-checkbox account-checkbox">
                                <label for="g_a_d_r_add_new_assets" class="permission-label-new">إضافة أصول جديدة</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('g_a_d_r_view_cost_centers')">
                                <input type="checkbox" id="g_a_d_r_view_cost_centers" name="g_a_d_r_view_cost_centers" class="custom-checkbox-new select-all-general-accounts-daily-restrictions permission-main-checkbox account-checkbox">
                                <label for="g_a_d_r_view_cost_centers" class="permission-label-new">عرض مراكز التكلفة</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('g_a_d_r_manage_cost_centers')">
                                <input type="checkbox" id="g_a_d_r_manage_cost_centers" name="g_a_d_r_manage_cost_centers" class="custom-checkbox-new select-all-general-accounts-daily-restrictions permission-main-checkbox account-checkbox">
                                <label for="g_a_d_r_manage_cost_centers" class="permission-label-new">إدارة مراكز التكلفة</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('g_a_d_r_manage_closed_periods')">
                                <input type="checkbox" id="g_a_d_r_manage_closed_periods" name="g_a_d_r_manage_closed_periods" class="custom-checkbox-new select-all-general-accounts-daily-restrictions permission-main-checkbox account-checkbox">
                                <label for="g_a_d_r_manage_closed_periods" class="permission-label-new">إدارة الفترات المقفلة</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('g_a_d_r_view_closed_periods')">
                                <input type="checkbox" id="g_a_d_r_view_closed_periods" name="g_a_d_r_view_closed_periods" class="custom-checkbox-new select-all-general-accounts-daily-restrictions permission-main-checkbox account-checkbox">
                                <label for="g_a_d_r_view_closed_periods" class="permission-label-new">عرض الفترات المقفلة</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('g_a_d_r_manage_journal_entries')">
                                <input type="checkbox" id="g_a_d_r_manage_journal_entries" name="g_a_d_r_manage_journal_entries" class="custom-checkbox-new select-all-general-accounts-daily-restrictions permission-main-checkbox account-checkbox">
                                <label for="g_a_d_r_manage_journal_entries" class="permission-label-new">إدارة حسابات القيود</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('g_a_d_r_view_all_journal_entries')">
                                <input type="checkbox" id="g_a_d_r_view_all_journal_entries" name="g_a_d_r_view_all_journal_entries" class="custom-checkbox-new select-all-general-accounts-daily-restrictions permission-main-checkbox account-checkbox">
                                <label for="g_a_d_r_view_all_journal_entries" class="permission-label-new">عرض جميع القيود</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('g_a_d_r_view_own_journal_entries')">
                                <input type="checkbox" id="g_a_d_r_view_own_journal_entries" name="g_a_d_r_view_own_journal_entries" class="custom-checkbox-new select-all-general-accounts-daily-restrictions permission-main-checkbox account-checkbox">
                                <label for="g_a_d_r_view_own_journal_entries" class="permission-label-new">عرض القيود الخاصة به</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('g_a_d_r_add_edit_delete_all_journal_entries')">
                                <input type="checkbox" id="g_a_d_r_add_edit_delete_all_journal_entries" name="g_a_d_r_add_edit_delete_all_journal_entries" class="custom-checkbox-new select-all-general-accounts-daily-restrictions permission-main-checkbox account-checkbox">
                                <label for="g_a_d_r_add_edit_delete_all_journal_entries" class="permission-label-new">إضافة/تعديل/مسح جميع القيود</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('g_a_d_r_add_edit_delete_own_journal_entries')">
                                <input type="checkbox" id="g_a_d_r_add_edit_delete_own_journal_entries" name="g_a_d_r_add_edit_delete_own_journal_entries" class="custom-checkbox-new select-all-general-accounts-daily-restrictions permission-main-checkbox account-checkbox">
                                <label for="g_a_d_r_add_edit_delete_own_journal_entries" class="permission-label-new">إضافة/تعديل/مسح القيود الخاصة به</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('g_a_d_r_add_edit_delete_draft_journal_entries')">
                                <input type="checkbox" id="g_a_d_r_add_edit_delete_draft_journal_entries" name="g_a_d_r_add_edit_delete_draft_journal_entries" class="custom-checkbox-new select-all-general-accounts-daily-restrictions permission-main-checkbox account-checkbox">
                                <label for="g_a_d_r_add_edit_delete_draft_journal_entries" class="permission-label-new">إضافة/تعديل/مسح القيود المسودة</label>
                            </div>
                        </div>
                    </div>

                    <!-- المالية -->
                    <div class="permission-section">
                        <div class="permission-header">
                            <div class="permission-title">
                                <input type="checkbox" id="SelectAllFinance" class="custom-checkbox-new permission-main-checkbox account-checkbox" onclick="toggleSectionPermissions('finance')">
                                <h6 class="mb-0 ms-2">💰 المالية</h6>
                            </div>
                            <span class="permission-count">
                                الصلاحيات النشطة: <span id="activeCountFinance">0</span>/15
                            </span>
                        </div>
                        <div class="permission-grid">
                            <div class="permission-item-new" onclick="togglePermission('finance_add_expense')">
                                <input type="checkbox" id="finance_add_expense" name="finance_add_expense" class="custom-checkbox-new select-all-finance permission-main-checkbox account-checkbox">
                                <label for="finance_add_expense" class="permission-label-new">إضافة مصروف</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('finance_edit_delete_all_expenses')">
                                <input type="checkbox" id="finance_edit_delete_all_expenses" name="finance_edit_delete_all_expenses" class="custom-checkbox-new select-all-finance permission-main-checkbox account-checkbox">
                                <label for="finance_edit_delete_all_expenses" class="permission-label-new">تعديل وحذف كل المصروفات</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('finance_edit_delete_own_expenses')">
                                <input type="checkbox" id="finance_edit_delete_own_expenses" name="finance_edit_delete_own_expenses" class="custom-checkbox-new select-all-finance permission-main-checkbox account-checkbox">
                                <label for="finance_edit_delete_own_expenses" class="permission-label-new">تعديل وحذف المصروفات الخاصة به</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('finance_view_all_expenses')">
                                <input type="checkbox" id="finance_view_all_expenses" name="finance_view_all_expenses" class="custom-checkbox-new select-all-finance permission-main-checkbox account-checkbox">
                                <label for="finance_view_all_expenses" class="permission-label-new">مشاهدة كل المصروفات</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('finance_view_own_expenses')">
                                <input type="checkbox" id="finance_view_own_expenses" name="finance_view_own_expenses" class="custom-checkbox-new select-all-finance permission-main-checkbox account-checkbox">
                                <label for="finance_view_own_expenses" class="permission-label-new">مشاهدة المصروفات التي أنشأها</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('finance_add_edit_delete_draft_expenses')">
                                <input type="checkbox" id="finance_add_edit_delete_draft_expenses" name="finance_add_edit_delete_draft_expenses" class="custom-checkbox-new select-all-finance permission-main-checkbox account-checkbox">
                                <label for="finance_add_edit_delete_draft_expenses" class="permission-label-new">إضافة/تعديل/مسح مصروفات مسودة</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('finance_edit_default_cashbox')">
                                <input type="checkbox" id="finance_edit_default_cashbox" name="finance_edit_default_cashbox" class="custom-checkbox-new select-all-finance permission-main-checkbox account-checkbox">
                                <label for="finance_edit_default_cashbox" class="permission-label-new">تعديل الخزينة الافتراضية</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('finance_view_own_cashboxes')">
                                <input type="checkbox" id="finance_view_own_cashboxes" name="finance_view_own_cashboxes" class="custom-checkbox-new select-all-finance permission-main-checkbox account-checkbox">
                                <label for="finance_view_own_cashboxes" class="permission-label-new">عرض خزائنه الخاصة</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('finance_add_revenue')">
                                <input type="checkbox" id="finance_add_revenue" name="finance_add_revenue" class="custom-checkbox-new select-all-finance permission-main-checkbox account-checkbox">
                                <label for="finance_add_revenue" class="permission-label-new">إضافة إيراد</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('finance_edit_delete_all_receipts')">
                                <input type="checkbox" id="finance_edit_delete_all_receipts" name="finance_edit_delete_all_receipts" class="custom-checkbox-new select-all-finance permission-main-checkbox account-checkbox">
                                <label for="finance_edit_delete_all_receipts" class="permission-label-new">تعديل وحذف كل سندات القبض</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('finance_edit_delete_own_receipts')">
                                <input type="checkbox" id="finance_edit_delete_own_receipts" name="finance_edit_delete_own_receipts" class="custom-checkbox-new select-all-finance permission-main-checkbox account-checkbox">
                                <label for="finance_edit_delete_own_receipts" class="permission-label-new">تعديل وحذف سندات القبض الخاص به</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('finance_view_all_receipts')">
                                <input type="checkbox" id="finance_view_all_receipts" name="finance_view_all_receipts" class="custom-checkbox-new select-all-finance permission-main-checkbox account-checkbox">
                                <label for="finance_view_all_receipts" class="permission-label-new">عرض كل سندات القبض</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('finance_view_own_receipts')">
                                <input type="checkbox" id="finance_view_own_receipts" name="finance_view_own_receipts" class="custom-checkbox-new select-all-finance permission-main-checkbox account-checkbox">
                                <label for="finance_view_own_receipts" class="permission-label-new">عرض سندات القبض التي أنشأها</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('finance_add_edit_delete_draft_revenue')">
                                <input type="checkbox" id="finance_add_edit_delete_draft_revenue" name="finance_add_edit_delete_draft_revenue" class="custom-checkbox-new select-all-finance permission-main-checkbox account-checkbox">
                                <label for="finance_add_edit_delete_draft_revenue" class="permission-label-new">إضافة/تعديل/مسح إيرادات مسودة</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('finance_add_revenue_expense_category')">
                                <input type="checkbox" id="finance_add_revenue_expense_category" name="finance_add_revenue_expense_category" class="custom-checkbox-new select-all-finance permission-main-checkbox account-checkbox">
                                <label for="finance_add_revenue_expense_category" class="permission-label-new">إضافة تصنيف إيرادات/مصروفات</label>
                            </div>
                        </div>
                    </div>

                    <!-- دورة الشيكات -->
                    <div class="permission-section">
                        <div class="permission-header">
                            <div class="permission-title">
                                <input type="checkbox" id="SelectAllCheckCycle" class="custom-checkbox-new permission-main-checkbox account-checkbox" onclick="toggleSectionPermissions('checkCycle')">
                                <h6 class="mb-0 ms-2">💳 دورة الشيكات</h6>
                            </div>
                            <span class="permission-count">
                                الصلاحيات النشطة: <span id="activeCountCheckCycle">0</span>/4
                            </span>
                        </div>
                        <div class="permission-grid">
                            <div class="permission-item-new" onclick="togglePermission('check_cycle_add_checkbook')">
                                <input type="checkbox" id="check_cycle_add_checkbook" name="check_cycle_add_checkbook" class="custom-checkbox-new select-all-check-cycle permission-main-checkbox account-checkbox">
                                <label for="check_cycle_add_checkbook" class="permission-label-new">إضافة دفتر الشيكات</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('check_cycle_view_checkbook')">
                                <input type="checkbox" id="check_cycle_view_checkbook" name="check_cycle_view_checkbook" class="custom-checkbox-new select-all-check-cycle permission-main-checkbox account-checkbox">
                                <label for="check_cycle_view_checkbook" class="permission-label-new">عرض دفتر الشيكات</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('check_cycle_edit_delete_checkbook')">
                                <input type="checkbox" id="check_cycle_edit_delete_checkbook" name="check_cycle_edit_delete_checkbook" class="custom-checkbox-new select-all-check-cycle permission-main-checkbox account-checkbox">
                                <label for="check_cycle_edit_delete_checkbook" class="permission-label-new">تعديل وحذف دفتر الشيكات</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('check_cycle_manage_received_checks')">
                                <input type="checkbox" id="check_cycle_manage_received_checks" name="check_cycle_manage_received_checks" class="custom-checkbox-new select-all-check-cycle permission-main-checkbox account-checkbox">
                                <label for="check_cycle_manage_received_checks" class="permission-label-new">إدارة الشيكات المستلمة</label>
                            </div>
                        </div>
                    </div>

                    <!-- الإعدادات -->
                    <div class="permission-section">
                        <div class="permission-header">
                            <div class="permission-title">
                                <input type="checkbox" id="SelectAllSettings" class="custom-checkbox-new permission-main-checkbox setting-checkbox" onclick="toggleSectionPermissions('settings')">
                                <h6 class="mb-0 ms-2">⚙️ الإعدادات</h6>
                            </div>
                            <span class="permission-count">
                                الصلاحيات النشطة: <span id="activeCountSettings">0</span>/6
                            </span>
                        </div>
                        <div class="permission-grid">
                            <div class="permission-item-new" onclick="togglePermission('settings_edit_general_settings')">
                                <input type="checkbox" id="settings_edit_general_settings" name="settings_edit_general_settings" class="custom-checkbox-new select-all-settings permission-main-checkbox setting-checkbox">
                                <label for="settings_edit_general_settings" class="permission-label-new">تعديل الإعدادات العامة</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('settings_edit_tax_settings')">
                                <input type="checkbox" id="settings_edit_tax_settings" name="settings_edit_tax_settings" class="custom-checkbox-new select-all-settings permission-main-checkbox setting-checkbox">
                                <label for="settings_edit_tax_settings" class="permission-label-new">تعديل إعدادات الضرائب</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('settings_view_own_reports')">
                                <input type="checkbox" id="settings_view_own_reports" name="settings_view_own_reports" class="custom-checkbox-new select-all-settings permission-main-checkbox setting-checkbox">
                                <label for="settings_view_own_reports" class="permission-label-new">عرض تقاريره الخاصة</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('branches')">
                                <input type="checkbox" id="branches" name="branches" class="custom-checkbox-new select-all-settings permission-main-checkbox setting-checkbox">
                                <label for="branches" class="permission-label-new">عرض إشعارات النظام</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('templates')">
                                <input type="checkbox" id="templates" name="templates" class="custom-checkbox-new select-all-settings permission-main-checkbox setting-checkbox">
                                <label for="templates" class="permission-label-new">القوالب</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('work_cycle')">
                                <input type="checkbox" id="work_cycle" name="work_cycle" class="custom-checkbox-new select-all-settings permission-main-checkbox setting-checkbox">
                                <label for="work_cycle" class="permission-label-new">دورة العمل</label>
                            </div>
                        </div>
                    </div>

                    <!-- المتجر الإلكتروني -->
                    <div class="permission-section">
                        <div class="permission-header">
                            <div class="permission-title">
                                <input type="checkbox" id="SelectAllOnlineStore" class="custom-checkbox-new permission-main-checkbox setting-checkbox" onclick="toggleSectionPermissions('onlineStore')">
                                <h6 class="mb-0 ms-2">🛒 المتجر الإلكتروني</h6>
                            </div>
                            <span class="permission-count">
                                الصلاحيات النشطة: <span id="activeCountOnlineStore">0</span>/1
                            </span>
                        </div>
                        <div class="permission-grid">
                            <div class="permission-item-new" onclick="togglePermission('online_store_content_management')">
                                <input type="checkbox" id="online_store_content_management" name="online_store_content_management" class="custom-checkbox-new select-all-online-store permission-main-checkbox setting-checkbox">
                                <label for="online_store_content_management" class="permission-label-new">إدارة محتوى المتجر الإلكتروني</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- محتوى الموظف -->
                <div id="employeeContent" class="role-content">

                    <!-- المرتبات (للموظفين) -->
                    <div class="permission-section">
                        <div class="permission-header">
                            <div class="permission-title">
                                <input type="checkbox" id="SelectAllSalariesEmployee" class="custom-checkbox-new permission-main-checkbox account-checkbox" onclick="toggleSectionPermissions('salariesEmployee')">
                                <h6 class="mb-0 ms-2">💳 المرتبات</h6>
                            </div>
                            <span class="permission-count">
                                الصلاحيات النشطة: <span id="activeCountSalariesEmployee">0</span>/1
                            </span>
                        </div>
                        <div class="permission-grid">
                            <div class="permission-item-new" onclick="togglePermission('employee_view_his_salary_slip')">
                                <input type="checkbox" id="employee_view_his_salary_slip" name="employee_view_his_salary_slip" class="custom-checkbox-new select-all-salaries-employee account-checkbox">
                                <label for="employee_view_his_salary_slip" class="permission-label-new">عرض قسيمة الراتب الخاصة به</label>
                            </div>
                        </div>
                    </div>

                    <!-- حضور الموظفين (للموظفين) -->
                    <div class="permission-section">
                        <div class="permission-header">
                            <div class="permission-title">
                                <input type="checkbox" id="SelectAllStaffAttendanceEmployee" class="custom-checkbox-new permission-main-checkbox" onclick="toggleSectionPermissions('staffAttendanceEmployee')">
                                <h6 class="mb-0 ms-2">📋 حضور الموظفين</h6>
                            </div>
                            <span class="permission-count">
                                الصلاحيات النشطة: <span id="activeCountStaffAttendanceEmployee">0</span>/6
                            </span>
                        </div>
                        <div class="permission-grid">
                            <div class="permission-item-new" onclick="togglePermission('employee_staffmark_own_attendance_online')">
                                <input type="checkbox" id="employee_staffmark_own_attendance_online" name="employee_staffmark_own_attendance_online" class="custom-checkbox-new select-all-staff-attendance-employee">
                                <label for="employee_staffmark_own_attendance_online" class="permission-label-new">تسجيل الحضور بنفسه (أونلاين)</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('employee_staffview_own_attendance_books')">
                                <input type="checkbox" id="employee_staffview_own_attendance_books" name="employee_staffview_own_attendance_books" class="custom-checkbox-new select-all-staff-attendance-employee">
                                <label for="employee_staffview_own_attendance_books" class="permission-label-new">عرض دفاتر الحضور الخاصة به</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('employee_staffedit_delete_own_leave_requests')">
                                <input type="checkbox" id="employee_staffedit_delete_own_leave_requests" name="employee_staffedit_delete_own_leave_requests" class="custom-checkbox-new select-all-staff-attendance-employee">
                                <label for="employee_staffedit_delete_own_leave_requests" class="permission-label-new">تعديل/ حذف طلبات إجازاته فقط</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('employee_staffview_own_attendance_logs')">
                                <input type="checkbox" id="employee_staffview_own_attendance_logs" name="employee_staffview_own_attendance_logs" class="custom-checkbox-new select-all-staff-attendance-employee">
                                <label for="employee_staffview_own_attendance_logs" class="permission-label-new">عرض كل سجلات الحضور الخاصة به</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('employee_staffadd_leave_request')">
                                <input type="checkbox" id="employee_staffadd_leave_request" name="employee_staffadd_leave_request" class="custom-checkbox-new select-all-staff-attendance-employee">
                                <label for="employee_staffadd_leave_request" class="permission-label-new">إضافة طلب إجازة</label>
                            </div>

                            <div class="permission-item-new" onclick="togglePermission('employee_staffview_own_leave_requests')">
                                <input type="checkbox" id="employee_staffview_own_leave_requests" name="employee_staffview_own_leave_requests" class="custom-checkbox-new select-all-staff-attendance-employee">
                                <label for="employee_staffview_own_leave_requests" class="permission-label-new">عرض طلبات إجازاته فقط</label>
                            </div>
                        </div>
                    </div>

                    <!-- الطلبات (للموظفين) -->
                    <div class="permission-section">
                        <div class="permission-header">
                            <div class="permission-title">
                                <input type="checkbox" id="SelectAllOrdersEmployee" class="custom-checkbox-new permission-main-checkbox" onclick="toggleSectionPermissions('ordersEmployee')">
                                <h6 class="mb-0 ms-2">📋 الطلبات</h6>
                            </div>
                            <span class="permission-count">
                                الصلاحيات النشطة: <span id="activeCountOrdersEmployee">0</span>/1
                            </span>
                        </div>
                        <div class="permission-grid">
                            <div class="permission-item-new" onclick="togglePermission('employee_orders_management')">
                                <input type="checkbox" id="employee_orders_management" name="employee_orders_management" class="custom-checkbox-new select-all-orders-employee">
                                <label for="employee_orders_management" class="permission-label-new">إدارة الطلبات</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
@section('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // تعريف الأقسام مع معرفات العناصر الخاصة بها
            const sections = [{
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
                selectAll.addEventListener('change', function() {
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                    updateCheckedCount(checkboxes, activeCountElement);
                });

                // إضافة حدث عند تغيير أي checkbox
                checkboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        updateCheckedCount(checkboxes, activeCountElement);
                    });
                });

                // استدعاء الوظيفة لتحديث العدد عند التحميل
                updateCheckedCount(checkboxes, activeCountElement);
            });

            // مدير (أدمن)
            const adminCheckbox = document.getElementById('adminCheckbox');
            const permissionCheckboxes = document.querySelectorAll('.permission-main-checkbox');

            adminCheckbox.addEventListener('change', function() {
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
        document.addEventListener('DOMContentLoaded', function() {
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
    <!-- سكريبت جافاسكريبت -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
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
                let allChecked = [...checkboxes].every(checkbox => checkbox
                .checked); // فحص إذا كانت كل الشيك بوكس محددة
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
        document.addEventListener("DOMContentLoaded", function() {
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
                let allChecked = [...checkboxes].every(checkbox => checkbox
                .checked); // فحص إذا كانت كل الشيك بوكس محددة
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
        document.addEventListener("DOMContentLoaded", function() {
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
                let allChecked = [...checkboxes].every(checkbox => checkbox
                .checked); // فحص إذا كانت كل الشيك بوكس محددة
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
        document.addEventListener("DOMContentLoaded", function() {
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
                let allChecked = [...checkboxes].every(checkbox => checkbox
                .checked); // فحص إذا كانت كل الشيك بوكس محددة
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
        document.addEventListener("DOMContentLoaded", function() {
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
                let allChecked = [...checkboxes].every(checkbox => checkbox
                .checked); // فحص إذا كانت كل الشيك بوكس محددة
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
        document.addEventListener("DOMContentLoaded", function() {
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
                let allChecked = [...checkboxes].every(checkbox => checkbox
                .checked); // فحص إذا كانت كل الشيك بوكس محددة
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
        document.addEventListener("DOMContentLoaded", function() {
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
                let allChecked = [...checkboxes].every(checkbox => checkbox
                .checked); // فحص إذا كانت كل الشيك بوكس محددة
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
    // settingsButton الاعدادات العامة
    <script>
        document.addEventListener("DOMContentLoaded", function() {
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
                let allChecked = [...checkboxes].every(checkbox => checkbox
                .checked); // فحص إذا كانت كل الشيك بوكس محددة
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
