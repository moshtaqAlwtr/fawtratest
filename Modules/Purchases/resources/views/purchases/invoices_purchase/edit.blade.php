@extends('master')

@section('title')
    تعديل فاتورة مشتريات
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
        .modified-badge {
            background: rgba(255, 193, 7, 0.1);
            border-left: 3px solid #ffc107;
            padding: 8px 12px;
            border-radius: 4px;
            margin-top: 5px;
        }
    </style>
@endsection

@section('content')
    <div class="content-body">
        <form id="invoice-form" action="{{ route('invoicePurchases.update', $invoice->id) }}" method="post"
            onsubmit="return confirmSubmit(event)">
            @csrf
            @method('PUT')

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
                            <small class="text-muted d-block">
                                <i class="fa fa-clock"></i> آخر تعديل: {{ $invoice->updated_at->format('Y-m-d H:i') }}
                            </small>
                        </div>

                        <div class="d-flex">
                            <div class="btn-group mr-2" style="margin-left: 10px;">
                                <button type="button" class="btn btn-outline-info btn-sm" onclick="saveAsDraft()"
                                    title="حفظ كمسودة">
                                    <i class="fa fa-save"></i> مسودة
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="resetToOriginal()"
                                    title="إعادة تعيين">
                                    <i class="fa fa-undo"></i> إعادة تعيين
                                </button>
                                <button type="button" class="btn btn-outline-warning btn-sm" onclick="clearAllItems()"
                                    title="مسح العناصر">
                                    <i class="fa fa-trash"></i> مسح العناصر
                                </button>
                                <button type="button" class="btn btn-outline-success btn-sm" onclick="showQuickPreview()"
                                    title="معاينة سريعة">
                                    <i class="fa fa-eye"></i> معاينة
                                </button>
                            </div>
                            <div>
                                <a href="{{ route('invoicePurchases.index') }}" class="btn btn-outline-danger">
                                    <i class="fa fa-ban"></i> الغاء
                                </a>
                                <a href="{{ route('invoicePurchases.show', $invoice->id) }}" class="btn btn-outline-info">
                                    <i class="fa fa-eye"></i> عرض
                                </a>
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="fa fa-save"></i> حفظ التعديلات
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
                                    الإعدادات المفعلة حالياً - وضع التعديل
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
                                <div class="modified-badge mt-2">
                                    <small class="text-warning">
                                        <i class="fas fa-edit me-1"></i>
                                        <strong>تنبيه:</strong> بعض الإعدادات قد لا تؤثر على الفواتير الموجودة، وستطبق على التعديلات الجديدة فقط
                                    </small>
                                </div>
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
                                لا توجد إعدادات مفعلة حالياً - سيتم التعديل يدوياً
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

            <!-- قسم المورد والبيانات الأساسية -->
            <div class="row">
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
                                                            {{ $supplier->id == $invoice->supplier_id ? 'selected' : '' }}>
                                                            {{ $supplier->trade_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <a href="{{ route('SupplierManagement.create') }}" type="button"
                                                    class="btn btn-secondary mr-1 mb-1 waves-effect waves-light">
                                                    <i class="fa fa-user-plus"></i> جديد
                                                </a>
                                            </div>
                                        </div>

                                        <!-- كارد رصيد المورد -->
                                        <div class="row" id="supplierBalanceCard"
                                            style="{{ $invoice->supplier_id ? '' : 'display: none;' }}">
                                            <div class="col-12">
                                                <div class="card"
                                                    style="background: #E3F2FD; border-radius: 8px; border: 1px solid #BBDEFB;">
                                                    <div class="card-body p-4">
                                                        <div class="row align-items-center">
                                                            <div class="col-8">
                                                                <a href="{{ route('SupplierManagement.edit', $invoice->supplier_id ?? 0) }}"
                                                                    class="text-decoration-none" style="color: inherit;">
                                                                    <h5 class="card-title mb-2" id="supplierName"
                                                                        style="font-weight: 600; color: #333;">
                                                                        {{ $invoice->supplier->trade_name ?? 'اسم المورد' }}
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
                                                                        id="supplierBalance">
                                                                        {{ number_format($invoice->supplier->account->balance ?? 0, 2) }}
                                                                    </span>
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
                                        <div class="row" id="autoPaymentAlert" style="{{ $invoice->supplier_id ? '' : 'display: none;' }}">
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
                                                    value="{{ $invoice->invoice_number }}" readonly>
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
                                                    value="{{ $invoice->date ? \Carbon\Carbon::parse($invoice->date)->format('Y-m-d') : date('Y-m-d') }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-md-3">
                                                <span>شروط الدفع :</span>
                                            </div>
                                            <div class="col-md-6">
                                                <input class="form-control" type="text" name="terms"
                                                    value="{{ $invoice->terms }}">
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

            <!-- جدول العناصر -->
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
                                    @foreach ($invoice->items as $index => $invoiceItem)
                                        <tr class="item-row">
                                            <td style="width:18%">
                                                <select name="items[{{ $index }}][product_id]"
                                                    class="form-control product-select">
                                                    <option value="">اختر المنتج</option>
                                                    @foreach ($items as $item)
                                                        <option value="{{ $item->id }}"
                                                            data-price="{{ $item->price }}"
                                                            {{ $item->id == $invoiceItem->product_id ? 'selected' : '' }}>
                                                            {{ $item->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" name="items[{{ $index }}][description]"
                                                    class="form-control item-description"
                                                    value="{{ $invoiceItem->description }}">
                                            </td>
                                            <td>
                                                <input type="number" name="items[{{ $index }}][quantity]"
                                                    class="form-control quantity" value="{{ $invoiceItem->quantity }}"
                                                    min="1" required>
                                            </td>
                                            <td>
                                                <input type="number" name="items[{{ $index }}][unit_price]"
                                                    class="form-control price" step="0.01"
                                                    value="{{ $invoiceItem->unit_price }}" required>
                                                @if(in_array('update_product_prices', $purchaseSettings ?? []))
                                                    <small class="text-info">
                                                        <i class="fas fa-sync fa-xs"></i> تحديث تلقائي
                                                    </small>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <input type="number" name="items[{{ $index }}][discount]"
                                                        class="form-control discount-amount"
                                                        value="{{ $invoiceItem->discount ?? 0 }}" min="0"
                                                        step="0.01"
                                                        style="{{ ($invoiceItem->discount_type ?? 'amount') == 'percentage' ? 'display: none;' : '' }}">
                                                    <input type="number"
                                                        name="items[{{ $index }}][discount_percentage]"
                                                        class="form-control discount-percentage"
                                                        value="{{ $invoiceItem->discount ?? 0 }}" min="0"
                                                        max="100" step="0.01"
                                                        style="{{ ($invoiceItem->discount_type ?? 'amount') == 'amount' ? 'display: none;' : '' }}">
                                                    <div class="input-group-append">
                                                        <select name="items[{{ $index }}][discount_type]"
                                                            class="form-control discount-type">
                                                            <option value="amount"
                                                                {{ ($invoiceItem->discount_type ?? 'amount') == 'amount' ? 'selected' : '' }}>
                                                                ريال</option>
                                                            <option value="percentage"
                                                                {{ ($invoiceItem->discount_type ?? 'amount') == 'percentage' ? 'selected' : '' }}>
                                                                %</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </td>
                                            <td data-label="الضريبة 1">
                                                <div class="input-group">
                                                    <select name="items[{{ $index }}][tax_1]"
                                                        class="form-control tax-select" data-target="tax_1"
                                                        style="width: 150px;" onchange="updateHiddenInput(this)">
                                                        <option value=""></option>
                                                        @foreach ($taxs as $tax)
                                                            <option value="{{ $tax->tax }}"
                                                                data-id="{{ $tax->id }}"
                                                                data-name="{{ $tax->name }}"
                                                                data-type="{{ $tax->type }}"
                                                                {{ $invoiceItem->tax_1 == $tax->tax ? 'selected' : '' }}>
                                                                {{ $tax->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <input type="hidden" name="items[{{ $index }}][tax_1_id]"
                                                        value="{{ $invoiceItem->tax_1_id }}">
                                                </div>
                                            </td>

                                            <td data-label="الضريبة 2">
                                                <div class="input-group">
                                                    <select name="items[{{ $index }}][tax_2]"
                                                        class="form-control tax-select" data-target="tax_2"
                                                        style="width: 150px;" onchange="updateHiddenInput(this)">
                                                        <option value=""></option>
                                                        @foreach ($taxs as $tax)
                                                            <option value="{{ $tax->tax }}"
                                                                data-id="{{ $tax->id }}"
                                                                data-name="{{ $tax->name }}"
                                                                data-type="{{ $tax->type }}"
                                                                {{ $invoiceItem->tax_2 == $tax->tax ? 'selected' : '' }}>
                                                                {{ $tax->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <input type="hidden" name="items[{{ $index }}][tax_2_id]"
                                                        value="{{ $invoiceItem->tax_2_id }}">
                                                </div>
                                            </td>

                                            <td>
                                                <span class="row-total">{{ number_format($invoiceItem->total, 2) }}</span>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm remove-row">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
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
                                        <td><span id="subtotal">{{ number_format($invoice->subtotal, 2) }}</span>
                                            {!! $currencySymbol !!}</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="7" class="text-right">مجموع الخصومات</td>
                                        <td><span
                                                id="total-discount">{{ number_format($invoice->total_discount ?? 0, 2) }}</span>
                                            {!! $currencySymbol !!}</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <small id="tax-details"></small>
                                    </tr>
                                    <tr>
                                        <td colspan="7" class="text-right">تكلفة الشحن</td>
                                        <td><span
                                                id="shipping-cost">{{ number_format($invoice->shipping_cost ?? 0, 2) }}</span>
                                            {!! $currencySymbol !!}</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="7" class="text-right">الدفعة المقدمة</td>
                                        <td><span
                                                id="advance-payment">{{ number_format($invoice->advance_payment ?? 0, 2) }}</span>
                                            {!! $currencySymbol !!}</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="7" class="text-right">المبلغ المدفوع</td>
                                        <td><span id="paid-amount-display">{{ number_format($invoice->paid_amount ?? 0, 2) }}</span> {!! $currencySymbol !!}</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="7" class="text-right">المجموع الكلي</td>
                                        <td><span id="grand-total">{{ number_format($invoice->total, 2) }}</span>
                                            {!! $currencySymbol !!}</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="7" class="text-right">المبلغ المتبقي</td>
                                        <td><span id="remaining-amount">{{ number_format(($invoice->total - ($invoice->paid_amount ?? 0)), 2) }}</span> {!! $currencySymbol !!}</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- التبويبات الرئيسية -->
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
                                    <input type="number" name="discount_amount" class="form-control"
                                        value="{{ $invoice->discount_amount ?? 0 }}" min="0" step="0.01">
                                    <select name="discount_type" class="form-control">
                                        <option value="amount"
                                            {{ ($invoice->discount_type ?? 'amount') == 'amount' ? 'selected' : '' }}>ريال
                                        </option>
                                        <option value="percentage"
                                            {{ ($invoice->discount_type ?? 'amount') == 'percentage' ? 'selected' : '' }}>
                                            نسبة مئوية</option>
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
                                        placeholder="اسم التسوية (مثال: خصم نقدي)"
                                        value="{{ $invoice->adjustment_label }}">
                                    <select name="adjustment_type" class="form-control">
                                        <option value="discount"
                                            {{ ($invoice->adjustment_type ?? 'discount') == 'discount' ? 'selected' : '' }}>
                                            خصم</option>
                                        <option value="addition"
                                            {{ ($invoice->adjustment_type ?? 'discount') == 'addition' ? 'selected' : '' }}>
                                            إضافة</option>
                                    </select>
                                    <input type="number" name="adjustment_value" step="0.01" class="form-control"
                                        placeholder="قيمة التسوية" value="{{ $invoice->adjustment_value ?? 0 }}">
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
                                    <input type="number" id="paid-amount-input" class="form-control" value="{{ $invoice->advance_payment ?? 0 }}"
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
                                        <option value="{{ $tax->id }}" data-rate="{{ $tax->tax }}"
                                            {{ ($invoice->shipping_tax_id ?? '') == $tax->id ? 'selected' : '' }}>
                                            {{ $tax->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">تكلفة الشحن</label>
                                <input type="number" class="form-control" name="shipping_cost" id="shipping"
                                    value="{{ $invoice->shipping_cost ?? 0 }}" min="0" step="0.01">
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
                            <li class="nav-item">
                                <a class="nav-link" id="tab-current-documents" href="#">المستندات الحالية</a>
                            </li>
                        </ul>

                        <div class="tab-content mt-3">
                            <!-- رفع مستند جديد -->
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

                            <!-- بحث في الملفات -->
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

                            <!-- المستندات الحالية -->
                            <div id="content-current-documents" class="tab-pane d-none">
                                @if ($invoice->attachments && $invoice->attachments->count() > 0)
                                    <div class="row">
                                        @foreach ($invoice->attachments as $attachment)
                                            <div class="col-md-4 mb-3" id="attachment-{{ $attachment->id }}">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h6 class="card-title">
                                                            <i class="fa fa-file text-primary"></i>
                                                            {{ $attachment->name }}
                                                        </h6>
                                                        <p class="card-text small text-muted">
                                                            <strong>الحجم:</strong>
                                                            {{ number_format($attachment->size / 1024, 2) }} KB<br>
                                                            <strong>النوع:</strong> {{ $attachment->type }}<br>
                                                            <strong>تاريخ الرفع:</strong>
                                                            {{ $attachment->created_at->format('Y-m-d H:i') }}
                                                        </p>
                                                        <div class="btn-group btn-group-sm w-100">
                                                            <a href="{{ $attachment->url }}"
                                                                class="btn btn-outline-primary" target="_blank">
                                                                <i class="fa fa-eye"></i> عرض
                                                            </a>
                                                            <a href="{{ $attachment->url }}"
                                                                class="btn btn-outline-success" download>
                                                                <i class="fa fa-download"></i> تحميل
                                                            </a>
                                                            <button type="button" class="btn btn-outline-danger"
                                                                onclick="removeAttachment({{ $attachment->id }})">
                                                                <i class="fa fa-trash"></i> حذف
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="alert alert-info">
                                        <i class="fa fa-info-circle"></i>
                                        لا توجد مستندات مرفقة بهذه الفاتورة
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- الملاحظات والشروط -->
            <div class="card shadow-sm border-0">
                <div class="card-header border-bottom" style="background-color: transparent;">
                    <h5 class="mb-0 fw-bold text-dark" style="font-size: 1.2rem;">
                        📝 الملاحظات / الشروط
                    </h5>
                </div>
                <div class="card-body">
                    <textarea id="tinyMCE" name="notes" class="form-control" rows="6" style="font-size: 1.05rem;">{{ $invoice->notes }}</textarea>
                </div>
            </div>

            <!-- حالة الدفع الكامل -->
            <div class="card">
                <div class="card-body py-2">
                    <div class="d-flex justify-content-start" style="direction: rtl;">
                        <div class="form-check">
                            <input class="form-check-input payment-toggle" type="checkbox" name="is_paid"
                                value="1" id="full-payment-check"
                                {{ $invoice->is_paid ? 'checked' : '' }}
                                @if(in_array('default_paid_invoices', $purchaseSettings ?? [])) disabled @endif>
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
                                <strong>إعداد مفعل:</strong> سيتم الدفع بالكامل تلقائياً عند حفظ التعديلات
                            </small>
                        </div>
                    @endif

                    <div class="full-payment-fields mt-3" style="{{ $invoice->is_paid ? '' : 'display: none;' }}">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="payment_method">وسيلة الدفع</label>
                                <select class="form-control" name="payment_method">
                                    <option value="">اختر وسيلة الدفع</option>
                                    <option value="cash"
                                        {{ ($invoice->payment_method ?? '') == 'cash' ? 'selected' : '' }}>نقداً
                                    </option>
                                    <option value="credit_card"
                                        {{ ($invoice->payment_method ?? '') == 'credit_card' ? 'selected' : '' }}>بطاقة
                                        ائتمان</option>
                                    <option value="bank_transfer"
                                        {{ ($invoice->payment_method ?? '') == 'bank_transfer' ? 'selected' : '' }}>
                                        تحويل بنكي</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">رقم المعرف</label>
                                <input type="text" class="form-control" name="reference_number"
                                    value="{{ $invoice->reference_number }}">
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

            <!-- حالة الاستلام -->
            <div class="card">
                <div class="card-body py-2 align-items-right">
                    <div class="d-flex justify-content-start" style="direction: rtl;">
                        <div class="form-check">
                            <input class="form-check-input toggle-check" type="checkbox" name="is_received"
                                value="1" id="received-check"
                                {{ $invoice->is_received ? 'checked' : '' }}
                                @if(in_array('default_received_invoices', $purchaseSettings ?? [])) disabled @endif>
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
                                <strong>إعداد مفعل:</strong> سيتم تسجيل الاستلام تلقائياً عند حفظ التعديلات
                            </small>
                        </div>
                    @endif

                    <div class="payment-fields mt-3" style="{{ $invoice->is_received ? '' : 'display: none;' }}">
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label">تاريخ الاستلام</label>
                                <input type="date" class="form-control" name="received_date"
                                    value="{{ $invoice->received_date ? \Carbon\Carbon::parse($invoice->received_date)->format('Y-m-d') : '' }}">
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
                                <input type="number" id="paid-amount-input-extra" class="form-control"
                                    value="{{ $invoice->paid_amount ?? 0 }}" name="paid_amount"
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
                                    <input class="form-check-input advance-payment-toggle" type="checkbox"
                                        name="is_advance_paid" value="1" id="advance-payment-check"
                                        {{ ($invoice->is_advance_paid ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="advance-payment-check">
                                        دفعة مقدمة (جزئية)
                                    </label>
                                </div>
                            </div>

                            <div class="advance-payment-fields mt-3"
                                style="{{ ($invoice->is_advance_paid ?? false) ? '' : 'display: none;' }}">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="advance_payment_method">وسيلة الدفع</label>
                                        <select class="form-control" name="advance_payment_method">
                                            <option value="">اختر وسيلة الدفع</option>
                                            <option value="cash"
                                                {{ ($invoice->advance_payment_method ?? '') == 'cash' ? 'selected' : '' }}>نقداً</option>
                                            <option value="credit_card"
                                                {{ ($invoice->advance_payment_method ?? '') == 'credit_card' ? 'selected' : '' }}>بطاقة ائتمان</option>
                                            <option value="bank_transfer"
                                                {{ ($invoice->advance_payment_method ?? '') == 'bank_transfer' ? 'selected' : '' }}>تحويل بنكي</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">رقم المعرف</label>
                                        <input type="text" class="form-control" name="advance_reference_number"
                                            value="{{ $invoice->advance_reference_number ?? '' }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </form>
    </div>

    <!-- نموذج مخفي لحفظ البيانات الأصلية -->
    <script type="application/json" id="original-invoice-data">
        {!! json_encode($invoice->toArray()) !!}
    </script>

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

        // تهيئة الصفحة
        document.addEventListener('DOMContentLoaded', function() {
            console.log('صفحة تعديل فاتورة المشتريات جاهزة');
            console.log('الإعدادات المفعلة:', activeSettings);

            // إعادة حساب الإجماليات عند تحميل الصفحة
            if (typeof invoiceCalculator !== 'undefined') {
                invoiceCalculator.calculateTotals();

                // إظهار رصيد المورد إذا كان محدداً
                const supplierSelect = document.getElementById('clientSelect');
                if (supplierSelect && supplierSelect.value) {
                    invoiceCalculator.showSupplierBalance(supplierSelect);
                }
            }

            // إعداد معالجات الأحداث للتبويبات
            setupTabHandlers();
            setupFormHandlers();
            setupAutomaticSettings();
        });

        // إعداد معالجات التبويبات
        function setupTabHandlers() {
            // التبويبات الرئيسية
            const tabLinks = document.querySelectorAll('#tab-discount, #tab-deposit, #tab-shipping, #tab-documents');
            tabLinks.forEach(tab => {
                tab.addEventListener('click', function(e) {
                    e.preventDefault();
                    const sectionName = this.id.replace('tab-', '');
                    showTabSection(sectionName);
                });
            });

            // تبويبات المستندات
            const docTabLinks = document.querySelectorAll(
                '#tab-new-document, #tab-uploaded-documents, #tab-current-documents');
            docTabLinks.forEach(tab => {
                tab.addEventListener('click', function(e) {
                    e.preventDefault();
                    const sectionName = this.id.replace('tab-', '');
                    showDocumentTab(sectionName);
                });
            });
        }

        // إعداد الإعدادات التلقائية
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
        }

        // إعداد معالجات النموذج
        function setupFormHandlers() {
            // معالج تغيير نوع الخصم
            document.querySelectorAll('.discount-type').forEach(select => {
                select.addEventListener('change', function() {
                    const row = this.closest('.item-row');
                    const discountAmount = row.querySelector('.discount-amount');
                    const discountPercentage = row.querySelector('.discount-percentage');

                    if (this.value === 'percentage') {
                        discountAmount.style.display = 'none';
                        discountPercentage.style.display = 'block';
                    } else {
                        discountAmount.style.display = 'block';
                        discountPercentage.style.display = 'none';
                    }

                    if (typeof invoiceCalculator !== 'undefined') {
                        invoiceCalculator.calculateTotals();
                    }
                });
            });

            // معالج تبديل حقول الدفع (إذا لم يكن مفعل تلقائياً)
            const paymentToggle = document.querySelector('.payment-toggle');
            const paymentFields = document.querySelector('.full-payment-fields');

            if (paymentToggle && paymentFields && !defaultPaidEnabled) {
                paymentToggle.addEventListener('change', function() {
                    paymentFields.style.display = this.checked ? 'block' : 'none';
                });
            }

            // معالج تبديل حقول الاستلام (إذا لم يكن مفعل تلقائياً)
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

        // دالة إظهار التبويبات الرئيسية
        function showTabSection(sectionName) {
            // إخفاء جميع الأقسام
            document.querySelectorAll('.tab-section').forEach(section => {
                section.classList.add('d-none');
            });

            // إزالة الفئة النشطة من جميع التبويبات
            document.querySelectorAll('.nav-tabs .nav-link').forEach(tab => {
                tab.classList.remove('active');
            });

            // إظهار القسم المحدد
            const targetSection = document.getElementById('section-' + sectionName);
            if (targetSection) {
                targetSection.classList.remove('d-none');
            }

            // تفعيل التبويب المحدد
            const targetTab = document.getElementById('tab-' + sectionName);
            if (targetTab) {
                targetTab.classList.add('active');
            }
        }

        // دالة إظهار تبويبات المستندات
        function showDocumentTab(tabName) {
            // إخفاء جميع محتويات المستندات
            document.querySelectorAll('.tab-pane').forEach(pane => {
                pane.classList.add('d-none');
                pane.classList.remove('active');
            });

            // إزالة الفئة النشطة من تبويبات المستندات
            document.querySelectorAll('#section-documents .nav-link').forEach(tab => {
                tab.classList.remove('active');
            });

            // إظهار المحتوى المحدد
            const targetContent = document.getElementById('content-' + tabName);
            if (targetContent) {
                targetContent.classList.remove('d-none');
                targetContent.classList.add('active');
            }

            // تفعيل التبويب المحدد
            const targetTab = document.getElementById('tab-' + tabName);
            if (targetTab) {
                targetTab.classList.add('active');
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
                title: 'تأكيد حفظ التعديلات',
                html: `<p>هل أنت متأكد من حفظ التعديلات على الفاتورة؟</p>${settingsMessage}`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'نعم، احفظ التعديلات',
                cancelButtonText: 'إلغاء',
                reverseButtons: true,
                customClass: {
                    confirmButton: 'btn btn-success ms-1',
                    cancelButton: 'btn btn-outline-danger ms-1'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    // إضافة مؤشر التحميل
                    Swal.fire({
                        title: 'جاري الحفظ...',
                        text: 'يرجى الانتظار',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    document.getElementById('invoice-form').submit();
                }
            });
        }

        // دالة حفظ كمسودة
        function saveAsDraft() {
            Swal.fire({
                title: 'حفظ كمسودة',
                text: 'هل تريد حفظ التعديلات كمسودة؟',
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'نعم، احفظ كمسودة',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    // إضافة حقل مخفي للإشارة إلى أن هذه مسودة
                    const draftInput = document.createElement('input');
                    draftInput.type = 'hidden';
                    draftInput.name = 'is_draft';
                    draftInput.value = '1';
                    document.getElementById('invoice-form').appendChild(draftInput);

                    // إضافة مؤشر التحميل
                    Swal.fire({
                        title: 'جاري الحفظ...',
                        text: 'يرجى الانتظار',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    document.getElementById('invoice-form').submit();
                }
            });
        }

        // دالة إعادة تعيين البيانات
        function resetToOriginal() {
            Swal.fire({
                title: 'إعادة تعيين البيانات',
                text: 'هل تريد إعادة تعيين جميع البيانات للحالة الأصلية؟ سيتم فقدان جميع التعديلات الحالية.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'نعم، أعد التعيين',
                cancelButtonText: 'إلغاء',
                confirmButtonColor: '#d33'
            }).then((result) => {
                if (result.isConfirmed) {
                    location.reload();
                }
            });
        }

        // دالة مسح جميع العناصر
        function clearAllItems() {
            Swal.fire({
                title: 'مسح جميع العناصر',
                text: 'هل تريد مسح جميع عناصر الفاتورة؟',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'نعم، امسح الكل',
                cancelButtonText: 'إلغاء',
                confirmButtonColor: '#d33'
            }).then((result) => {
                if (result.isConfirmed) {
                    // مسح جميع الصفوف عدا الأول
                    const rows = document.querySelectorAll('.item-row');
                    for (let i = 1; i < rows.length; i++) {
                        rows[i].remove();
                    }

                    // إعادة تعيين الصف الأول
                    const firstRow = document.querySelector('.item-row');
                    if (firstRow) {
                        firstRow.querySelectorAll('input, select').forEach(field => {
                            if (field.type === 'number') {
                                field.value = field.name.includes('quantity') ? '1' : '0';
                            } else {
                                field.value = '';
                            }
                        });
                    }

                    if (typeof invoiceCalculator !== 'undefined') {
                        invoiceCalculator.calculateTotals();
                    }

                    Swal.fire('تم المسح!', 'تم مسح جميع العناصر بنجاح', 'success');
                }
            });
        }

        // دالة المعاينة السريعة
        function showQuickPreview() {
            const supplierName = document.querySelector('#clientSelect option:checked')?.textContent || 'غير محدد';
            const invoiceNumber = document.querySelector('input[name="invoice_number"]')?.value || '';
            const total = document.querySelector('#grand-total')?.textContent || '0.00';
            const itemCount = document.querySelectorAll('.item-row').length;

            let previewHTML = `
                <div class="text-right" style="direction: rtl;">
                    <h6>معاينة سريعة للفاتورة</h6>
                    <hr>
                    <p><strong>رقم الفاتورة:</strong> ${invoiceNumber}</p>
                    <p><strong>المورد:</strong> ${supplierName}</p>
                    <p><strong>المجموع الكلي:</strong> ${total} ر.س</p>
                    <p><strong>عدد البنود:</strong> ${itemCount}</p>
            `;

            if (activeSettings.length > 0) {
                previewHTML += `<hr><p><strong>الإعدادات المفعلة:</strong></p><ul class="text-right">`;
                activeSettings.forEach(setting => {
                    const settingNames = {
                        'default_paid_invoices': 'دفع تلقائي بالكامل',
                        'default_received_invoices': 'استلام تلقائي',
                        'auto_payment': 'دفع حسب رصيد المورد',
                        'update_product_prices': 'تحديث أسعار المنتجات',
                        'total_discounts': 'إجمالي الخصومات',
                        'enable_settlement': 'نظام التسوية'
                    };
                    if (settingNames[setting]) {
                        previewHTML += `<li>${settingNames[setting]}</li>`;
                    }
                });
                previewHTML += `</ul>`;
            }

            previewHTML += `
                    <hr>
                    <small class="text-muted">هذه معاينة سريعة. للحصول على معاينة كاملة، احفظ الفاتورة أولاً.</small>
                </div>
            `;

            Swal.fire({
                title: 'معاينة الفاتورة',
                html: previewHTML,
                width: 800,
                confirmButtonText: 'إغلاق'
            });
        }

        // دالة حذف المرفقات
        function removeAttachment(attachmentId) {
            Swal.fire({
                title: 'حذف المستند',
                text: 'هل أنت متأكد من حذف هذا المستند؟ لا يمكن التراجع عن هذا الإجراء.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'نعم، احذف',
                cancelButtonText: 'إلغاء',
                confirmButtonColor: '#d33'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/attachments/${attachmentId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                                    'content'),
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // إزالة العنصر من الواجهة
                                const attachmentElement = document.getElementById(`attachment-${attachmentId}`);
                                if (attachmentElement) {
                                    attachmentElement.remove();
                                }

                                Swal.fire('تم الحذف!', 'تم حذف المستند بنجاح', 'success');
                            } else {
                                Swal.fire('خطأ!', data.message || 'حدث خطأ أثناء حذف المستند', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire('خطأ!', 'حدث خطأ أثناء حذف المستند', 'error');
                        });
                }
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

        // رسائل النجاح والخطأ
        @if (session('success'))
            Swal.fire({
                title: 'تم التحديث بنجاح!',
                text: '{{ session('success') }}',
                icon: 'success',
                confirmButtonText: 'حسناً'
            });
        @endif

        @if (session('warning'))
            Swal.fire({
                title: 'تنبيه!',
                text: '{{ session('warning') }}',
                icon: 'warning',
                confirmButtonText: 'حسناً'
            });
        @endif

        @if ($errors->any())
            Swal.fire({
                title: 'خطأ في التحديث!',
                html: `
                    <ul style="text-align: right;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                `,
                icon: 'error',
                confirmButtonText: 'حسناً'
            });
        @endif
    </script>
@endsection
