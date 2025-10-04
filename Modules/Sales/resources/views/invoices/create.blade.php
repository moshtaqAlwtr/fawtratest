@extends('master')

@section('title')
    انشاء فاتورة مبيعات
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/purch.css') }}">
@endsection

@section('content')
    <div class="content-body">
        <form id="invoice-form" action="{{ route('invoices.store') }}" method="post" onsubmit="return confirmSubmit(event)">
            @csrf
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <!-- كارد الأزرار الرئيسية -->
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <label>الحقول التي عليها علامة <span style="color: red">*</span> الزامية</label>
                        </div>

                        <div class="d-flex">
                            <div class="btn-group mr-2" style="margin-left: 10px;">
                                <button type="button" class="btn btn-outline-info btn-sm" onclick="saveAsDraft()"
                                    title="حفظ كمسودة">
                                    <i class="fa fa-save"></i> مسودة
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="copyLastInvoice()"
                                    title="نسخ آخر فاتورة">
                                    <i class="fa fa-copy"></i> نسخ
                                </button>
                                <button type="button" class="btn btn-outline-warning btn-sm" onclick="clearAllItems()"
                                    title="مسح الكل">
                                    <i class="fa fa-trash"></i> مسح
                                </button>
                                <button type="button" class="btn btn-outline-success btn-sm" onclick="showQuickPreview()"
                                    title="معاينة سريعة">
                                    <i class="fa fa-eye"></i> معاينة
                                </button>
                            </div>
                            <div>
                                <a href="{{ route('invoices.index') }}" class="btn btn-outline-danger">
                                    <i class="fa fa-ban"></i>الغاء
                                </a>
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="fa fa-save"></i>حفظ
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- كارد الإعدادات المفعلة -->
            @if (isset($salesSettings) && count($salesSettings) > 0)
                <div class="card mb-3">
                    <div class="card-body py-3">
                        <div class="settings-card p-3">
                            <div class="d-flex align-items-start">
                                <div class="me-3">
                                    <i class="fas fa-cogs text-primary pulse-animation" style="font-size: 1.5rem;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-2 text-primary font-weight-bold">
                                        <i class="fas fa-check-circle me-1"></i>
                                        الإعدادات المفعلة حالياً
                                    </h6>
                                    <div class="d-flex flex-wrap">
                                        @if (in_array('auto_apply_offers', $salesSettings))
                                            <span class="setting-badge bg-success text-white">
                                                <i class="fas fa-percentage"></i>
                                                تطبيق العروض تلقائياً
                                            </span>
                                        @endif

                                        @if (in_array('default_paid_invoices', $salesSettings))
                                            <span class="setting-badge bg-primary text-white">
                                                <i class="fas fa-credit-card"></i>
                                                دفع تلقائي بالكامل
                                            </span>
                                        @endif

                                        @if (in_array('auto_inventory_update', $salesSettings))
                                            <span class="setting-badge bg-warning text-dark">
                                                <i class="fas fa-boxes"></i>
                                                تحديث المخزون تلقائياً
                                            </span>
                                        @endif

                                        @if (in_array('commission_calculation', $salesSettings))
                                            <span class="setting-badge bg-info text-white">
                                                <i class="fas fa-calculator"></i>
                                                حساب العمولة
                                            </span>
                                        @endif

                                        @if (in_array('client_notifications', $salesSettings))
                                            <span class="setting-badge bg-secondary text-white">
                                                <i class="fas fa-bell"></i>
                                                إشعارات العملاء
                                            </span>
                                        @endif
                                    </div>
                                    @if (in_array('auto_apply_offers', $salesSettings) || in_array('default_paid_invoices', $salesSettings))
                                        <div class="auto-note mt-2">
                                            <small class="text-success">
                                                <i class="fas fa-magic me-1"></i>
                                                <strong>ملاحظة:</strong> ستتم معالجة الفاتورة تلقائياً حسب الإعدادات المفعلة
                                            </small>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <a href="" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-cog me-1"></i>
                                        تعديل الإعدادات
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- إذا لم تكن هناك إعدادات مفعلة -->
                <div class="card mb-3">
                    <div class="card-body py-2">
                        <div class="alert alert-light border mb-0" style="border-radius: 8px;">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">
                                    <i class="fas fa-cog me-2"></i>
                                    لا توجد إعدادات مفعلة حالياً - ستتم معالجة الفاتورة يدوياً
                                </span>
                                <a href="" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-cogs me-1"></i>
                                    تفعيل الإعدادات
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- صف بيانات العميل والفاتورة -->
            <div class="row">
                <!-- بيانات العميل -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-md-2">
                                                <span>العميل :</span>
                                            </div>
                                            <div class="col-md-6">
                                                <select class="form-control select2" id="clientSelect" name="client_id"
    required onchange="showClientBalance(this)">
    <option value="">اختر العميل</option>
    @foreach ($clients as $c)
        <option value="{{ $c->id }}"
            data-balance="{{ $c->account->balance ?? 0 }}"
            data-name="{{ $c->trade_name }}"
            @if (isset($selectedClient) && $c->id == $selectedClient) selected @endif>
            {{ $c->trade_name }} - {{ $c->code }}
        </option>
    @endforeach
</select>
                                            </div>
                                            <div class="col-md-4">
                                                <a href="{{ route('clients.create') }}" type="button"
                                                    class="btn btn-secondary mr-1 mb-1 waves-effect waves-light">
                                                    <i class="fa fa-user-plus"></i>جديد
                                                </a>
                                            </div>
                                        </div>

                                        <!-- كارد رصيد العميل -->
                                        <div class="row" id="clientBalanceCard" style="display: none;">
                                            <div class="col-12">
                                                <div class="card"
                                                    style="background: #E3F2FD; border-radius: 8px; border: 1px solid #BBDEFB;">
                                                    <div class="card-body p-4">
                                                        <div class="row align-items-center">
                                                            <div class="col-8">
                                                                <a href="#" class="text-decoration-none"
                                                                    style="color: inherit;">
                                                                    <h5 class="card-title mb-2" id="clientName"
                                                                        style="font-weight: 600; color: #333;">
                                                                        اسم العميل
                                                                    </h5>
                                                                    <p class="mb-0"
                                                                        style="color: #666; font-size: 0.9rem;">
                                                                        <i class="fas fa-edit ml-1"
                                                                            style="color: #2196F3;"></i>
                                                                        <span>تعديل البيانات</span>
                                                                    </p>
                                                                </a>
                                                            </div>
                                                            <div class="col-4 text-left">
                                                                <div class="d-flex flex-column align-items-end">
                                                                    <span
                                                                        style="font-size: 1.8rem; font-weight: 700; color: #333;"
                                                                        id="clientBalance">0.00</span>
                                                                    <small style="color: #666; margin-top: -5px;">ر.س
                                                                        SAR</small>
                                                                    <span id="balanceStatus"
                                                                        style="font-size: 0.8rem; margin-top: 5px;"></span>
                                                                    <div
                                                                        style="width: 4px; height: 40px; background: #4CAF50; border-radius: 2px; margin-top: 10px;">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- قوائم الأسعار -->
                                        <div class="col-12">
                                            <div class="form-group row">
                                                <div class="col-md-2">
                                                    <span>قوائم الاسعار :</span>
                                                </div>
                                                <div class="col-md-6">
                                                    <select class="form-control" id="price-list-select"
                                                        name="price_list_id">
                                                        <option value="">اختر قائمة اسعار</option>
                                                        @foreach ($price_lists as $price_list)
                                                            <option value="{{ $price_list->id }}">
                                                                {{ $price_list->name ?? '' }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- تنبيه العروض التلقائية -->
                                        @if (in_array('auto_apply_offers', $salesSettings ?? []))
                                            <div class="row" id="autoOffersAlert" style="display: none;">
                                                <div class="col-12">
                                                    <div class="alert alert-success" style="border-radius: 8px;">
                                                        <i class="fas fa-percentage me-2"></i>
                                                        <strong>عروض تلقائية مفعلة!</strong> سيتم تطبيق العروض المناسبة
                                                        تلقائياً
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- بيانات الفاتورة -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="row add_item">
                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-md-3">
                                                <span>رقم الفاتورة :</span>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" class="form-control" name="invoice_number"
                                                    value="{{ $invoice_number }}" readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-md-3">
                                                <span>تاريخ الفاتورة:</span>
                                            </div>
                                            <div class="col-md-8">
                                                <input class="form-control" type="date" name="invoice_date"
                                                    value="{{ old('invoice_date', date('Y-m-d')) }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-md-3">
                                                <span>مسئول المبيعات :</span>
                                            </div>
                                            <div class="col-md-8">
                                                <select name="employee_id" class="form-control select2">
                                                    @foreach ($employees as $employee)
                                                        <option value="{{ $employee->id }}">{{ $employee->full_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-md-3">
                                                <span>تاريخ الاصدار :</span>
                                            </div>
                                            <div class="col-md-8">
                                                <input class="form-control" type="date" name="issue_date"
                                                    value="{{ old('issue_date', date('Y-m-d')) }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-md-3">
                                                <span>شروط الدفع :</span>
                                            </div>
                                            <div class="col-md-6">
                                                <input class="form-control" type="text" name="terms">
                                            </div>
                                            <div class="col-md-2">
                                                <span class="form-control-plaintext">أيام</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-md-3">
                                                <input class="form-control" type="text" placeholder="عنوان إضافي">
                                            </div>
                                            <div class="col-md-8">
                                                <div class="input-group">
                                                    <input class="form-control" type="text"
                                                        placeholder="بيانات إضافية">
                                                    <div class="input-group-append">
                                                        <button type="button"
                                                            class="btn btn-outline-success waves-effect waves-light addeventmore">
                                                            <i class="fa fa-plus-circle"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- جدول البنود -->
            <div class="card">
                <div class="card-content">
                    <div class="card-body">
                        <input type="hidden" id="products-data" value="{{ json_encode($items) }}">
                        <div class="table-responsive">
                            <table class="table" id="items-table">
                                <thead>
                                    <tr>
                                        <th>المنتج</th>
                                        <th>الوصف</th>
                                        <th>الكمية</th>
                                        <th>السعر
                                            @if (in_array('auto_inventory_update', $salesSettings ?? []))
                                                <small class="text-warning d-block">
                                                    <i class="fas fa-boxes fa-sm"></i> سيتم خصم المخزون
                                                </small>
                                            @endif
                                        </th>
                                        <th>الخصم
                                            @if (in_array('auto_apply_offers', $salesSettings ?? []))
                                                <small class="text-success d-block">
                                                    <i class="fas fa-percentage fa-sm"></i> تطبيق تلقائي
                                                </small>
                                            @endif
                                        </th>
                                        <th>الضريبة 1</th>
                                        <th>الضريبة 2</th>
                                        <th>المجموع</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="item-row">
                                        <td style="width:18%" data-label="المنتج">
                                            <select name="items[0][product_id]" class="form-control product-select"
                                                required>
                                                <option value="">اختر المنتج</option>
                                                @foreach ($items as $item)
                                                    <option value="{{ $item->id }}"
                                                        data-price="{{ $item->sale_price }}">{{ $item->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td data-label="الوصف">
                                            <input type="text" name="items[0][description]"
                                                class="form-control item-description" placeholder="أدخل الوصف">
                                        </td>
                                        <td data-label="الكمية">
                                            <input type="number" name="items[0][quantity]" class="form-control quantity"
                                                value="1" min="1" required>
                                        </td>
                                        <td data-label="السعر">
                                            <input type="number" name="items[0][unit_price]" class="form-control price"
                                                value="" step="0.01" required placeholder="0.00">
                                            @if (in_array('auto_inventory_update', $salesSettings ?? []))
                                                <small class="text-warning">
                                                    <i class="fas fa-boxes fa-xs"></i> خصم تلقائي
                                                </small>
                                            @endif
                                        </td>
                                        <td data-label="الخصم">
                                            <div class="input-group">
                                                <input type="number" name="items[0][discount]"
                                                    class="form-control discount-amount" value="0" min="0"
                                                    step="0.01">
                                                <input type="number" name="items[0][discount_percentage]"
                                                    class="form-control discount-percentage" value="0"
                                                    min="0" max="100" step="0.01" style="display: none;">
                                                <div class="input-group-append">
                                                    <select name="items[0][discount_type]"
                                                        class="form-control discount-type">
                                                        <option value="amount">ريال</option>
                                                        <option value="percentage">%</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </td>
                                        <td data-label="الضريبة 1">
                                            <div class="input-group">
                                                <select name="items[0][tax_1]" class="form-control tax-select"
                                                    data-target="tax_1" onchange="updateHiddenInput(this)">
                                                    <option value="">لا يوجد</option>
                                                    @foreach ($taxs as $tax)
                                                        <option value="{{ $tax->tax }}"
                                                            data-id="{{ $tax->id }}"
                                                            data-name="{{ $tax->name }}"
                                                            data-type="{{ $tax->type }}">
                                                            {{ $tax->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <input type="hidden" name="items[0][tax_1_id]">
                                            </div>
                                        </td>

                                        <td data-label="الضريبة 2">
                                            <div class="input-group">
                                                <select name="items[0][tax_2]" class="form-control tax-select"
                                                    data-target="tax_2" onchange="updateHiddenInput(this)">
                                                    <option value="">لا يوجد</option>
                                                    @foreach ($taxs as $tax)
                                                        <option value="{{ $tax->tax }}"
                                                            data-id="{{ $tax->id }}"
                                                            data-name="{{ $tax->name }}"
                                                            data-type="{{ $tax->type }}">
                                                            {{ $tax->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <input type="hidden" name="items[0][tax_2_id]">
                                            </div>
                                        </td>

                                        <input type="hidden" name="items[0][store_house_id]" value="">
                                        <td data-label="المجموع">
                                            <span class="row-total">0.00</span>
                                        </td>
                                        <td data-label="الإجراءات">
                                            <button type="button" class="btn btn-danger btn-sm remove-row">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot id="tax-rows">
                                    <tr>
                                        <td colspan="9" class="text-right">
                                            <button type="button" id="add-row" class="btn btn-success">
                                                <i class="fa fa-plus"></i> إضافة صف
                                            </button>
                                        </td>
                                    </tr>
                                    @php
                                        $currency = $account_setting->currency ?? 'SAR';
                                        $currencySymbol =
                                            $currency == 'SAR' || empty($currency)
                                                ? '<img src="' .
                                                    asset('assets/images/Saudi_Riyal.svg') .
                                                    '" alt="ريال سعودي" width="13" style="display: inline-block; margin-left: 5px; vertical-align: middle;">'
                                                : $currency;
                                    @endphp
                                    <tr>
                                        <td colspan="7" class="text-right">المجموع الفرعي</td>
                                        <td><span id="subtotal">0.00</span> {!! $currencySymbol !!}</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="7" class="text-right">مجموع الخصومات</td>
                                        <td><span id="total-discount">0.00</span> {!! $currencySymbol !!}</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <small id="tax-details"></small>
                                    </tr>
                                    <tr>
                                        <td colspan="7" class="text-right">تكلفة الشحن</td>
                                        <td><span id="shipping-cost">0.00</span> {!! $currencySymbol !!}</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="7" class="text-right">الدفعة المقدمة</td>
                                        <td><span id="advance-payment">0.00</span> {!! $currencySymbol !!}</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="7" class="text-right">المبلغ المدفوع</td>
                                        <td><span id="paid-amount-display">0.00</span> {!! $currencySymbol !!}</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="7" class="text-right">المجموع الكلي</td>
                                        <td><span id="grand-total">0.00</span> {!! $currencySymbol !!}</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="7" class="text-right">المبلغ المتبقي</td>
                                        <td><span id="remaining-amount">0.00</span> {!! $currencySymbol !!}</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- كارد التفاصيل الإضافية -->
            <div class="card">
                <div class="card-header bg-white">
                    <ul class="nav nav-tabs card-header-tabs align-items-center">
                        <li class="nav-item">
                            <a class="nav-link active" id="tab-discount" href="#">الخصم والتسوية</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab-deposit" href="#">إيداع</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab-shipping" href="#">التوصيل</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab-documents" href="#">إرفاق المستندات</a>
                        </li>
                    </ul>
                </div>

                <div class="card-body">
                    <!-- القسم الأول: الخصم والتسوية -->
                    <div id="section-discount" class="tab-section">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">قيمة الخصم</label>
                                <div class="input-group">
                                    <input type="number" name="discount_amount" class="form-control" value="0"
                                        min="0" step="0.01">
                                    <select name="discount_type" class="form-control">
                                        <option value="amount">ريال</option>
                                        <option value="percentage">نسبة مئوية</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">التسوية</label>
                                <div class="input-group">
                                    <input type="text" name="adjustment_label" class="form-control"
                                        placeholder="اسم التسوية (مثال: خصم نقدي)">
                                    <select name="adjustment_type" class="form-control">
                                        <option value="discount">خصم</option>
                                        <option value="addition">إضافة</option>
                                    </select>
                                    <input type="number" name="adjustment_value" step="0.01" class="form-control"
                                        placeholder="قيمة التسوية" value="0">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- القسم الثاني: الإيداع -->
                    <div id="section-deposit" class="tab-section d-none">
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label">المبلغ المدفوع</label>
                                <div class="input-group">
                                    <input type="number" id="paid-amount-input" class="form-control" value="0"
                                        name="advance_payment" step="0.01" min="0"
                                        placeholder="المبلغ المدفوع">
                                    <select name="advance_payment_type" class="form-control">
                                        <option value="amount">ريال</option>
                                        <option value="percentage">نسبة مئوية</option>
                                    </select>
                                </div>
                                @if (in_array('default_paid_invoices', $salesSettings ?? []))
                                    <small class="text-success">
                                        <i class="fas fa-magic"></i> سيتم الدفع تلقائياً حسب الإعدادات
                                    </small>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- القسم الثالث: التوصيل -->
                    <div id="section-shipping" class="tab-section d-none">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">نوع الضريبة</label>
                                <select class="form-control" id="methodSelect" name="shipping_tax_id">
                                    <option value="">اختر الضريبة</option>
                                    @foreach ($taxs as $tax)
                                        <option value="{{ $tax->id }}" data-rate="{{ $tax->tax }}">
                                            {{ $tax->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">تكلفة الشحن</label>
                                <input type="number" class="form-control" name="shipping_cost" id="shipping"
                                    value="0" min="0" step="0.01">
                            </div>
                        </div>
                    </div>

                    <!-- القسم الرابع: إرفاق المستندات -->
                    <div id="section-documents" class="tab-section d-none">
                        <ul class="nav nav-tabs">
                            <li class="nav-item">
                                <a class="nav-link active" id="tab-new-document" href="#">رفع مستند جديد</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab-uploaded-documents" href="#">بحث في الملفات</a>
                            </li>
                        </ul>

                        <div class="tab-content mt-3">
                            <div id="content-new-document" class="tab-pane active">
                                <div class="col-12 mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-file-upload text-primary me-2"></i>
                                        رفع مستند جديد:
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-primary text-white">
                                            <i class="fas fa-upload"></i>
                                        </span>
                                        <input type="file" class="form-control" id="uploadFile"
                                            aria-describedby="uploadButton" name="attachments[]" multiple>
                                        <button class="btn btn-primary" type="button" id="uploadButton">
                                            <i class="fas fa-cloud-upload-alt me-1"></i>
                                            رفع
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div id="content-uploaded-documents" class="tab-pane d-none">
                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center gap-2" style="width: 80%;">
                                                <label class="form-label mb-0"
                                                    style="white-space: nowrap;">المستند:</label>
                                                <select class="form-select">
                                                    <option selected>اختر مستند</option>
                                                    <option value="1">مستند 1</option>
                                                    <option value="2">مستند 2</option>
                                                    <option value="3">مستند 3</option>
                                                </select>
                                                <button type="button" class="btn btn-success">أرفق</button>
                                            </div>
                                            <button type="button" class="btn btn-primary">
                                                <i class="fas fa-search me-1"></i>
                                                بحث متقدم
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- كارد الملاحظات -->
            <div class="card shadow-sm border-0">
                <div class="card-header border-bottom" style="background-color: transparent;">
                    <h5 class="mb-0 fw-bold text-dark" style="font-size: 1.2rem;">
                        📝 الملاحظات / الشروط
                    </h5>
                </div>
                <div class="card-body">
                    <textarea id="tinyMCE" name="notes" class="form-control" rows="6" style="font-size: 1.05rem;"></textarea>
                </div>
            </div>

            <!-- كارد الدفع -->
            <div class="card">
                <div class="card-body py-2">
                    <div class="d-flex justify-content-start" style="direction: rtl;">
                        <div class="form-check">
                            <input class="form-check-input payment-toggle" type="checkbox" name="is_paid" value="1"
                                id="full-payment-check" @if (in_array('default_paid_invoices', $salesSettings ?? [])) checked disabled @endif>
                            <label class="form-check-label" for="full-payment-check">
                                تم الدفع بالكامل من العميل؟
                                @if (in_array('default_paid_invoices', $salesSettings ?? []))
                                    <span class="text-success">
                                        <i class="fas fa-magic"></i> (تلقائي)
                                    </span>
                                @endif
                            </label>
                        </div>
                    </div>

                    @if (in_array('default_paid_invoices', $salesSettings ?? []))
                        <div class="auto-note mt-2">
                            <small class="text-success">
                                <i class="fas fa-info-circle"></i>
                                <strong>إعداد مفعل:</strong> سيتم الدفع بالكامل تلقائياً عند حفظ الفاتورة
                            </small>
                        </div>
                    @endif


                </div>
            </div>

            <!-- كارد إضافي للدفعة المقدمة -->
            <div id="section-deposit-extra" class="card" style="display: none;">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label">المبلغ المدفوع</label>
                            <div class="input-group">
                                <input type="number" id="paid-amount-input-extra" class="form-control" value="0"
                                    name="paid_amount" step="0.01" min="0" placeholder="المبلغ المدفوع">
                                <select name="payment_amount_type" class="form-control">
                                    <option value="amount">ريال</option>
                                    <option value="percentage">نسبة مئوية</option>
                                </select>
                            </div>
                            <small class="text-muted">أدخل المبلغ المدفوع مقدماً أو كاملاً</small>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-body py-2">
                            <div class="d-flex justify-content-start" style="direction: rtl;">
                                <div class="form-check">
                                    <input class="form-check-input advance-payment-toggle" type="checkbox"
                                        name="is_advance_paid" value="1" id="advance-payment-check">
                                    <label class="form-check-label" for="advance-payment-check">
                                        دفعة مقدمة (جزئية)
                                    </label>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- استدعاء ملف الجافا سكربت المنفصل -->
    <script src="{{ asset('assets/js/invoice-calculator.js') }}"></script>

    <script>
        // متغير الإعدادات المفعلة للاستخدام في الجافا سكريبت
        const activeSettings = @json($salesSettings ?? []);
        const autoOffersEnabled = activeSettings.includes('auto_apply_offers');
        const defaultPaidEnabled = activeSettings.includes('default_paid_invoices');
        const autoInventoryEnabled = activeSettings.includes('auto_inventory_update');
        const commissionEnabled = activeSettings.includes('commission_calculation');

        document.addEventListener('DOMContentLoaded', function() {
            console.log('صفحة إنشاء فاتورة المبيعات جاهزة');
            console.log('الإعدادات المفعلة:', activeSettings);

            // إعداد تبديل التبويبات
            setupTabs();

            // إعداد الإعدادات التلقائية
            setupAutomaticSettings();

            // إعداد معالجات الأحداث
            setupEventHandlers();

            // التحقق من العميل المحدد مسبقاً
            const clientSelect = document.getElementById('clientSelect');
            if (clientSelect && clientSelect.value) {
                showClientBalance(clientSelect);
            }
        });

        // دالة إعداد التبويبات
        function setupTabs() {
            // معالج تبديل التبويبات
            document.querySelectorAll('[id^="tab-"]').forEach(tab => {
                tab.addEventListener('click', function(e) {
                    e.preventDefault();

                    // إزالة الكلاس النشط من جميع التبويبات
                    document.querySelectorAll('.nav-link').forEach(link => {
                        link.classList.remove('active');
                    });

                    // إخفاء جميع الأقسام
                    document.querySelectorAll('.tab-section').forEach(section => {
                        section.classList.add('d-none');
                    });

                    // تفعيل التبويب المحدد
                    this.classList.add('active');

                    // إظهار القسم المطابق
                    const targetSection = document.getElementById('section-' + this.id.replace('tab-', ''));
                    if (targetSection) {
                        targetSection.classList.remove('d-none');
                    }
                });
            });
        }

        // دالة إعداد الإعدادات التلقائية
        function setupAutomaticSettings() {
            // إذا كانت العروض التلقائية مفعلة، أظهر التنبيه للعميل
            if (autoOffersEnabled) {
                const clientSelect = document.getElementById('clientSelect');
                if (clientSelect) {
                    clientSelect.addEventListener('change', function() {
                        const autoOffersAlert = document.getElementById('autoOffersAlert');
                        if (autoOffersAlert && this.value) {
                            autoOffersAlert.style.display = 'block';
                        } else if (autoOffersAlert) {
                            autoOffersAlert.style.display = 'none';
                        }
                    });
                }
            }

            // إعداد الدفع التلقائي
            if (defaultPaidEnabled) {
                const paymentCheckbox = document.getElementById('full-payment-check');
                if (paymentCheckbox) {
                    paymentCheckbox.checked = true;
                    paymentCheckbox.disabled = true;
                }
            }
        }

        // دالة إعداد معالجات الأحداث
        function setupEventHandlers() {
            // معالج تبديل حقول الدفع
            const paymentToggle = document.querySelector('.payment-toggle');
            const paymentFields = document.querySelector('.full-payment-fields');

            if (paymentToggle && paymentFields && !defaultPaidEnabled) {
                paymentToggle.addEventListener('change', function() {
                    paymentFields.style.display = this.checked ? 'block' : 'none';
                });
            }

            // معالج تبديل الدفعة المقدمة
            const advanceToggle = document.querySelector('.advance-payment-toggle');
            const advanceFields = document.querySelector('.advance-payment-fields');

            if (advanceToggle && advanceFields) {
                advanceToggle.addEventListener('change', function() {
                    advanceFields.style.display = this.checked ? 'block' : 'none';
                });
            }
        }

        // دالة إظهار رصيد العميل
        window.showClientBalance = function(selectElement) {
    const balanceCard = document.getElementById('clientBalanceCard');

    // التحقق من أن العميل تم اختياره فعلياً
    if (!selectElement ||
        !selectElement.value ||
        selectElement.value === '' ||
        selectElement.value === '0' ||
        selectElement.selectedIndex === 0) {
        if (balanceCard) {
            balanceCard.style.display = 'none';
        }
        return;
    }

    const selectedOption = selectElement.options[selectElement.selectedIndex];
    const clientName = selectedOption.text.split(' - ')[0];
    const clientBalance = parseFloat(selectedOption.getAttribute('data-balance')) || 0;

    const nameElement = document.getElementById('clientName');
    const balanceElement = document.getElementById('clientBalance');
    const statusElement = document.getElementById('balanceStatus');

    if (nameElement) nameElement.textContent = clientName;
    if (balanceElement) balanceElement.textContent = Math.abs(clientBalance).toFixed(2);

    if (statusElement && balanceElement) {
        if (clientBalance > 0) {
            statusElement.textContent = 'دائن';
            statusElement.style.color = '#4CAF50';
            balanceElement.style.color = '#4CAF50';
        } else if (clientBalance < 0) {
            statusElement.textContent = 'مدين';
            statusElement.style.color = '#f44336';
            balanceElement.style.color = '#f44336';
        } else {
            statusElement.textContent = 'متوازن';
            statusElement.style.color = '#FFC107';
            balanceElement.style.color = '#FFC107';
        }
    }

    // إظهار الكارد مع تأثير انيميشن
    if (balanceCard) {
        balanceCard.style.display = 'block';
        balanceCard.style.opacity = '0';
        balanceCard.style.transform = 'translateY(-20px)';

        setTimeout(() => {
            balanceCard.style.transition = 'all 0.3s ease';
            balanceCard.style.opacity = '1';
            balanceCard.style.transform = 'translateY(0)';
        }, 10);
    }
};

        // دالة نسخ آخر فاتورة
        window.copyLastInvoice = function() {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'نسخ آخر فاتورة',
                    text: 'هل تريد نسخ بيانات آخر فاتورة؟',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'نعم، انسخ',
                    cancelButtonText: 'إلغاء'
                }).then((result) => {
                    if (result.isConfirmed) {
                        performCopyLastInvoice();
                    }
                });
            } else {
                if (confirm('هل تريد نسخ بيانات آخر فاتورة؟')) {
                    performCopyLastInvoice();
                }
            }
        };

        function performCopyLastInvoice() {
            fetch('/sales/invoices/get-last-invoice', {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.invoice) {
                        fillInvoiceData(data.invoice);
                        showNotification('تم نسخ بيانات آخر فاتورة بنجاح', 'success');
                    } else {
                        showNotification('لم يتم العثور على فواتير سابقة', 'info');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('لم يتم العثور على فواتير سابقة', 'info');
                });
        }

        function fillInvoiceData(invoiceData) {
            // ملء بيانات العميل
            if (invoiceData.client_id) {
                const clientSelect = document.getElementById('clientSelect');
                if (clientSelect) {
                    clientSelect.value = invoiceData.client_id;
                    showClientBalance(clientSelect);
                }
            }

            // ملء الحقول الأساسية
            const basicFields = [
                'terms', 'discount_amount', 'discount_type', 'adjustment_value',
                'adjustment_type', 'adjustment_label', 'advance_payment',
                'advance_payment_type', 'shipping_cost', 'shipping_tax_id'
            ];

            basicFields.forEach(fieldName => {
                if (invoiceData[fieldName] !== undefined) {
                    const field = document.querySelector(`[name="${fieldName}"]`);
                    if (field) {
                        field.value = invoiceData[fieldName];
                    }
                }
            });

            // ملء الملاحظات
            if (invoiceData.notes) {
                const notesField = document.getElementById('tinyMCE');
                if (notesField) {
                    notesField.value = invoiceData.notes;
                }
            }

            // إعادة حساب الإجماليات
            if (typeof calculateTotals === 'function') {
                calculateTotals();
            }
        }

        // دالة تأكيد الحفظ مع عرض الإعدادات
        function confirmSubmit(event) {
            event.preventDefault();

            let settingsMessage = '';
            if (activeSettings.length > 0) {
                settingsMessage = '<div class="alert alert-info mt-3 text-start"><strong>الإعدادات المفعلة:</strong><br>';

                if (defaultPaidEnabled) {
                    settingsMessage += '• سيتم الدفع بالكامل تلقائياً<br>';
                }
                if (autoOffersEnabled) {
                    settingsMessage += '• سيتم تطبيق العروض تلقائياً<br>';
                }
                if (autoInventoryEnabled) {
                    settingsMessage += '• سيتم تحديث المخزون تلقائياً<br>';
                }
                if (commissionEnabled) {
                    settingsMessage += '• سيتم حساب العمولة تلقائياً<br>';
                }

                settingsMessage += '</div>';
            }

            Swal.fire({
                title: 'تأكيد الحفظ',
                html: `<p>هل أنت متأكد من حفظ الفاتورة؟</p>${settingsMessage}`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'نعم، احفظه!',
                cancelButtonText: 'إلغاء',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('invoice-form').submit();
                }
            });
        }

        // دالة حفظ كمسودة
        function saveAsDraft() {
            const draftInput = document.createElement('input');
            draftInput.type = 'hidden';
            draftInput.name = 'is_draft';
            draftInput.value = '1';
            document.getElementById('invoice-form').appendChild(draftInput);
            document.getElementById('invoice-form').submit();
        }
    </script>
@endsection
