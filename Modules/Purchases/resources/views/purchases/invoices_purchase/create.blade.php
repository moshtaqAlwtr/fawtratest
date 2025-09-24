@extends('master')

@section('title')
    انشاء فاتورة مشتريات
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/purch.css') }}">
    <style>
        .settings-card {
            background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .setting-badge {
            font-size: 0.85rem;
            padding: 8px 12px;
            border-radius: 20px;
            margin: 3px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .auto-note {
            background: rgba(40, 167, 69, 0.1);
            border-left: 3px solid #28a745;
            padding: 8px 12px;
            border-radius: 4px;
            margin-top: 5px;
        }
        .pulse-animation {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.7; }
            100% { opacity: 1; }
        }
    </style>
@endsection

@section('content')
    <div class="content-body">
        <form id="invoice-form" action="{{ route('invoicePurchases.store') }}" method="post"
            onsubmit="return confirmSubmit(event)">
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
                                <a href="{{ route('invoicePurchases.index') }}" class="btn btn-outline-danger">
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
            @if(isset($purchaseSettings) && count($purchaseSettings) > 0)
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
                                    @if(in_array('default_paid_invoices', $purchaseSettings))
                                        <span class="setting-badge bg-success text-white">
                                            <i class="fas fa-credit-card"></i>
                                            دفع تلقائي بالكامل
                                        </span>
                                    @endif

                                    @if(in_array('default_received_invoices', $purchaseSettings))
                                        <span class="setting-badge bg-primary text-white">
                                            <i class="fas fa-check-circle"></i>
                                            استلام تلقائي
                                        </span>
                                    @endif

                                    @if(in_array('auto_payment', $purchaseSettings))
                                        <span class="setting-badge bg-warning text-dark">
                                            <i class="fas fa-wallet"></i>
                                            دفع حسب رصيد المورد
                                        </span>
                                    @endif

                                    @if(in_array('update_product_prices', $purchaseSettings))
                                        <span class="setting-badge bg-info text-white">
                                            <i class="fas fa-tags"></i>
                                            تحديث أسعار المنتجات
                                        </span>
                                    @endif

                                    @if(in_array('total_discounts', $purchaseSettings))
                                        <span class="setting-badge bg-secondary text-white">
                                            <i class="fas fa-percentage"></i>
                                            إجمالي الخصومات
                                        </span>
                                    @endif

                                    @if(in_array('enable_settlement', $purchaseSettings))
                                        <span class="setting-badge bg-dark text-white">
                                            <i class="fas fa-balance-scale"></i>
                                            نظام التسوية
                                        </span>
                                    @endif
                                </div>
                                @if(in_array('default_paid_invoices', $purchaseSettings) || in_array('auto_payment', $purchaseSettings))
                                    <div class="auto-note mt-2">
                                        <small class="text-success">
                                            <i class="fas fa-magic me-1"></i>
                                            <strong>ملاحظة:</strong> ستتم معالجة الدفع تلقائياً حسب الإعدادات المفعلة
                                        </small>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <a href="{{ route('purchase_invoices.settings.index') }}" class="btn btn-outline-primary btn-sm">
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
                            <a href="{{ route('purchase_invoices.settings') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-cogs me-1"></i>
                                تفعيل الإعدادات
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- صف بيانات المورد والفاتورة -->
            <div class="row">
                <!-- بيانات المورد -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-md-2">
                                                <span>المورد :</span>
                                            </div>
                                            <div class="col-md-6">
                                                <select class="form-control select2" id="clientSelect" name="supplier_id"
                                                    required onchange="showSupplierBalance(this)">
                                                    <option value="">اختر المورد</option>
                                                    @foreach ($suppliers as $supplier)
                                                        <option value="{{ $supplier->id }}"
                                                            data-balance="{{ $supplier->account->balance ?? 0 }}"
                                                            @if (isset($selectedSupplier) && $supplier->id == $selectedSupplier) selected @endif>
                                                            {{ $supplier->trade_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <a href="{{ route('SupplierManagement.create') }}" type="button"
                                                    class="btn btn-secondary mr-1 mb-1 waves-effect waves-light">
                                                    <i class="fa fa-user-plus"></i>جديد
                                                </a>
                                            </div>
                                        </div>

                                        <!-- كارد رصيد المورد -->
                                        <div class="row" id="supplierBalanceCard" style="display: none;">
                                            <div class="col-12">
                                                <div class="card"
                                                    style="background: #E3F2FD; border-radius: 8px; border: 1px solid #BBDEFB;">
                                                    <div class="card-body p-4">
                                                        <div class="row align-items-center">
                                                            <div class="col-8">
                                                                <a href="#" class="text-decoration-none"
                                                                    style="color: inherit;">
                                                                    <h5 class="card-title mb-2" id="supplierName"
                                                                        style="font-weight: 600; color: #333;">
                                                                        اسم المورد
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
                                                                        id="supplierBalance">0.00</span>
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

                                        <!-- تنبيه الدفع التلقائي حسب رصيد المورد -->
                                        @if(in_array('auto_payment', $purchaseSettings ?? []))
                                        <div class="row" id="autoPaymentAlert" style="display: none;">
                                            <div class="col-12">
                                                <div class="alert alert-success" style="border-radius: 8px;">
                                                    <i class="fas fa-magic me-2"></i>
                                                    <strong>دفع تلقائي مفعل!</strong> سيتم الدفع تلقائياً إذا كان لدى المورد رصيد صالح
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
                                                    value="{{ $code }}" readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-md-3">
                                                <span>التاريخ:</span>
                                            </div>
                                            <div class="col-md-8">
                                                <input class="form-control" type="date" name="date"
                                                    value="{{ date('Y-m-d') }}">
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
                                            @if(in_array('update_product_prices', $purchaseSettings ?? []))
                                                <small class="text-info d-block">
                                                    <i class="fas fa-sync fa-sm"></i> سيتم التحديث
                                                </small>
                                            @endif
                                        </th>
                                        <th>الخصم</th>
                                        <th>الضريبة 1</th>
                                        <th>الضريبة 2</th>
                                        <th>المجموع</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="item-row">
                                        <td style="width:18%">
                                            <select name="items[0][product_id]" class="form-control product-select">
                                                <option value="">اختر المنتج</option>
                                                @foreach ($items as $item)
                                                    <option value="{{ $item->id }}"
                                                        data-price="{{ $item->price }}">
                                                        {{ $item->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" name="items[0][description]"
                                                class="form-control item-description">
                                        </td>
                                        <td>
                                            <input type="number" name="items[0][quantity]" class="form-control quantity"
                                                value="1" min="1" required>
                                        </td>
                                        <td>
                                            <input type="number" name="items[0][unit_price]" class="form-control price"
                                                step="0.01" value="{{ $item->purchase_price ?? 0 }}" required>
                                            @if(in_array('update_product_prices', $purchaseSettings ?? []))
                                                <small class="text-info">
                                                    <i class="fas fa-sync fa-xs"></i> تحديث تلقائي
                                                </small>
                                            @endif
                                        </td>
                                        <td>
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
                                                    data-target="tax_1" style="width: 150px;"
                                                    onchange="updateHiddenInput(this)">
                                                    <option value=""></option>
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
                                                    data-target="tax_2" style="width: 150px;"
                                                    onchange="updateHiddenInput(this)">
                                                    <option value=""></option>
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

                                        <td>
                                            <span class="row-total">0.00</span>
                                        </td>
                                        <td>
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
                                @if(in_array('total_discounts', $purchaseSettings ?? []))
                                    <small class="text-info">
                                        <i class="fas fa-info-circle"></i> سيتم احتساب إجمالي الخصومات تلقائياً
                                    </small>
                                @endif
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
                                @if(in_array('enable_settlement', $purchaseSettings ?? []))
                                    <small class="text-success">
                                        <i class="fas fa-check-circle"></i> نظام التسوية مفعل
                                    </small>
                                @endif
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
                                @if(in_array('default_paid_invoices', $purchaseSettings ?? []) || in_array('auto_payment', $purchaseSettings ?? []))
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
                            <input class="form-check-input payment-toggle" type="checkbox" name="is_paid"
                                value="1" id="full-payment-check"
                                @if(in_array('default_paid_invoices', $purchaseSettings ?? [])) checked disabled @endif>
                            <label class="form-check-label" for="full-payment-check">
                                تم الدفع بالكامل إلى المورد؟
                                @if(in_array('default_paid_invoices', $purchaseSettings ?? []))
                                    <span class="text-success">
                                        <i class="fas fa-magic"></i> (تلقائي)
                                    </span>
                                @endif
                            </label>
                        </div>
                    </div>

                    @if(in_array('default_paid_invoices', $purchaseSettings ?? []))
                        <div class="auto-note mt-2">
                            <small class="text-success">
                                <i class="fas fa-info-circle"></i>
                                <strong>إعداد مفعل:</strong> سيتم الدفع بالكامل تلقائياً عند حفظ الفاتورة
                            </small>
                        </div>
                    @endif

                    <div class="full-payment-fields mt-3" style="display: none;">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="payment_method">وسيلة الدفع</label>
                                <select class="form-control" name="payment_method">
                                    <option value="">اختر وسيلة الدفع</option>
                                    <option value="cash">نقداً</option>
                                    <option value="credit_card">بطاقة ائتمان</option>
                                    <option value="bank_transfer">تحويل بنكي</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">رقم المعرف</label>
                                <input type="text" class="form-control" name="reference_number">
                            </div>
                        </div>
                        <div class="alert alert-info mt-2">
                            <small>
                                <i class="fa fa-info-circle"></i>
                                عند اختيار "دفع كامل" سيتم تعيين المبلغ المدفوع تلقائياً لكامل المبلغ
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- كارد الاستلام -->
            <div class="card">
                <div class="card-body py-2 align-items-right">
                    <div class="d-flex justify-content-start" style="direction: rtl;">
                        <div class="form-check">
                            <input class="form-check-input toggle-check" type="checkbox" name="is_received"
                                value="1" id="received-check"
                                @if(in_array('default_received_invoices', $purchaseSettings ?? [])) checked disabled @endif>
                            <label class="form-check-label" for="received-check">
                                مستلم
                                @if(in_array('default_received_invoices', $purchaseSettings ?? []))
                                    <span class="text-primary">
                                        <i class="fas fa-magic"></i> (تلقائي)
                                    </span>
                                @endif
                            </label>
                        </div>
                    </div>

                    @if(in_array('default_received_invoices', $purchaseSettings ?? []))
                        <div class="auto-note mt-2">
                            <small class="text-primary">
                                <i class="fas fa-info-circle"></i>
                                <strong>إعداد مفعل:</strong> سيتم تسجيل الاستلام تلقائياً عند حفظ الفاتورة
                            </small>
                        </div>
                    @endif

                    <div class="payment-fields mt-3" style="display: none;">
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label">تاريخ الاستلام</label>
                                <input type="date" class="form-control" name="received_date">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- كارد إضافي للدفعة المقدمة -->
            <div id="section-deposit-extra" class="card" style="display: none;">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label">المبلغ المدفوع</label>
                            <div class="input-group">
                                <input type="number" id="paid-amount-input-extra" class="form-control" value="0" name="paid_amount"
                                    step="0.01" min="0" placeholder="المبلغ المدفوع">
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
                                    <input class="form-check-input advance-payment-toggle" type="checkbox" name="is_advance_paid"
                                        value="1" id="advance-payment-check">
                                    <label class="form-check-label" for="advance-payment-check">
                                        دفعة مقدمة (جزئية)
                                    </label>
                                </div>
                            </div>

                            <div class="advance-payment-fields mt-3" style="display: none;">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="advance_payment_method">وسيلة الدفع</label>
                                        <select class="form-control" name="advance_payment_method">
                                            <option value="">اختر وسيلة الدفع</option>
                                            <option value="cash">نقداً</option>
                                            <option value="credit_card">بطاقة ائتمان</option>
                                            <option value="bank_transfer">تحويل بنكي</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">رقم المعرف</label>
                                        <input type="text" class="form-control" name="advance_reference_number">
                                    </div>
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
        const activeSettings = @json($purchaseSettings ?? []);
        const autoPaymentEnabled = activeSettings.includes('auto_payment');
        const defaultPaidEnabled = activeSettings.includes('default_paid_invoices');
        const defaultReceivedEnabled = activeSettings.includes('default_received_invoices');
        const updatePricesEnabled = activeSettings.includes('update_product_prices');

        document.addEventListener('DOMContentLoaded', function() {
            console.log('صفحة إنشاء فاتورة المشتريات جاهزة');
            console.log('الإعدادات المفعلة:', activeSettings);

            // إعداد تبديل التبويبات
            setupTabs();

            // إعداد الإعدادات التلقائية
            setupAutomaticSettings();

            // إعداد معالجات الأحداث
            setupEventHandlers();

            // التحقق من المورد المحدد مسبقاً
            const supplierSelect = document.getElementById('clientSelect');
            if (supplierSelect && supplierSelect.value) {
                showSupplierBalance(supplierSelect);
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
            // إذا كان الدفع التلقائي مفعل، أظهر التنبيه للمورد
            if (autoPaymentEnabled) {
                const supplierSelect = document.getElementById('clientSelect');
                if (supplierSelect) {
                    supplierSelect.addEventListener('change', function() {
                        const autoPaymentAlert = document.getElementById('autoPaymentAlert');
                        if (autoPaymentAlert && this.value) {
                            autoPaymentAlert.style.display = 'block';
                        } else if (autoPaymentAlert) {
                            autoPaymentAlert.style.display = 'none';
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

            // إعداد الاستلام التلقائي
            if (defaultReceivedEnabled) {
                const receivedCheckbox = document.getElementById('received-check');
                if (receivedCheckbox) {
                    receivedCheckbox.checked = true;
                    receivedCheckbox.disabled = true;
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

            // معالج تبديل حقول الاستلام
            const receivedToggle = document.querySelector('.toggle-check');
            const receivedFields = document.querySelector('.payment-fields');

            if (receivedToggle && receivedFields && !defaultReceivedEnabled) {
                receivedToggle.addEventListener('change', function() {
                    receivedFields.style.display = this.checked ? 'block' : 'none';
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
            fetch('/invoices/get-last-invoice', {
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
            // ملء بيانات المورد
            if (invoiceData.supplier_id) {
                const supplierSelect = document.getElementById('clientSelect');
                if (supplierSelect) {
                    supplierSelect.value = invoiceData.supplier_id;
                    showSupplierBalance(supplierSelect);
                }
            }

            // ملء الحقول الأساسية
            const basicFields = [
                'terms', 'discount_amount', 'discount_type', 'adjustment_value',
                'adjustment_type', 'adjustment_label', 'paid_amount',
                'payment_amount_type', 'shipping_cost', 'shipping_tax_id'
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
                if (defaultReceivedEnabled) {
                    settingsMessage += '• سيتم الاستلام تلقائياً<br>';
                }
                if (autoPaymentEnabled) {
                    settingsMessage += '• دفع تلقائي حسب رصيد المورد<br>';
                }
                if (updatePricesEnabled) {
                    settingsMessage += '• سيتم تحديث أسعار المنتجات<br>';
                }

                settingsMessage += '</div>';
            }

            Swal.fire({
                title: 'تأكيد الحفظ',
                html: `<p>هل أنت متأكد من حفظ الفاتورة؟</p>${settingsMessage}`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'نعم، احفظ',
                cancelButtonText: 'إلغاء',
                reverseButtons: true,
                customClass: {
                    confirmButton: 'btn btn-success ms-1',
                    cancelButton: 'btn btn-outline-danger ms-1'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('invoice-form').submit();
                }
            });
        }

        // دالة حفظ كمسودة
        function saveAsDraft() {
            Swal.fire({
                title: 'حفظ كمسودة',
                text: 'هل تريد حفظ الفاتورة كمسودة؟',
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'نعم، احفظ',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    const draftInput = document.createElement('input');
                    draftInput.type = 'hidden';
                    draftInput.name = 'is_draft';
                    draftInput.value = '1';
                    document.getElementById('invoice-form').appendChild(draftInput);
                    document.getElementById('invoice-form').submit();
                }
            });
        }

        // دالة مسح جميع البنود
        function clearAllItems() {
            Swal.fire({
                title: 'مسح جميع البنود',
                text: 'هل تريد مسح جميع بنود الفاتورة؟',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'نعم، امسح',
                cancelButtonText: 'إلغاء',
                confirmButtonColor: '#dc3545'
            }).then((result) => {
                if (result.isConfirmed) {
                    // مسح جميع الصفوف ما عدا الأول
                    const rows = document.querySelectorAll('.item-row');
                    for (let i = 1; i < rows.length; i++) {
                        rows[i].remove();
                    }

                    // إعادة تعيين الصف الأول
                    const firstRow = document.querySelector('.item-row');
                    if (firstRow) {
                        firstRow.querySelectorAll('input, select').forEach(input => {
                            if (input.type === 'number') {
                                input.value = input.name.includes('quantity') ? '1' : '0';
                            } else {
                                input.value = '';
                            }
                        });
                    }

                    // إعادة حساب الإجماليات
                    if (typeof calculateTotals === 'function') {
                        calculateTotals();
                    }

                    showNotification('تم مسح جميع البنود', 'success');
                }
            });
        }

        // دالة معاينة سريعة
        function showQuickPreview() {
            const grandTotal = document.getElementById('grand-total').textContent || '0.00';
            const supplierName = document.getElementById('clientSelect').selectedOptions[0]?.text || 'غير محدد';

            let previewHTML = `
                <div class="text-right">
                    <h6>معاينة سريعة للفاتورة</h6>
                    <hr>
                    <p><strong>المورد:</strong> ${supplierName}</p>
                    <p><strong>المجموع الكلي:</strong> ${grandTotal} ر.س</p>
                    <p><strong>عدد البنود:</strong> ${document.querySelectorAll('.item-row').length}</p>
            `;

            if (activeSettings.length > 0) {
                previewHTML += `<hr><p><strong>الإعدادات المفعلة:</strong></p><ul class="text-right">`;
                activeSettings.forEach(setting => {
                    const settingNames = {
                        'default_paid_invoices': 'دفع تلقائي بالكامل',
                        'default_received_invoices': 'استلام تلقائي',
                        'auto_payment': 'دفع حسب رصيد المورد',
                        'update_product_prices': 'تحديث أسعار المنتجات'
                    };
                    if (settingNames[setting]) {
                        previewHTML += `<li>${settingNames[setting]}</li>`;
                    }
                });
                previewHTML += `</ul>`;
            }

            previewHTML += `</div>`;

            Swal.fire({
                title: 'معاينة الفاتورة',
                html: previewHTML,
                icon: 'info',
                confirmButtonText: 'حسناً'
            });
        }

        // دالة إظهار الإشعارات
        function showNotification(message, type = 'info') {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: type,
                    title: message,
                    showConfirmButton: false,
                    timer: 3000
                });
            } else {
                alert(message);
            }
        }

        // رسائل النجاح والأخطاء
        @if (session('success'))
            Swal.fire({
                title: 'تم الحفظ!',
                text: '{{ session('success') }}',
                icon: 'success',
                confirmButtonText: 'حسناً'
            });
        @endif

        @if ($errors->any())
            Swal.fire({
                title: 'خطأ!',
                html: `<ul class="text-right">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>`,
                icon: 'error',
                confirmButtonText: 'حسناً'
            });
        @endif
    </script>

@endsection
