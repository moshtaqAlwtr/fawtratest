@extends('master')

@section('title')
    انشاء مرتجع مشتريات
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/purch.css') }}">
@endsection

@section('content')
    <div class="content-body">
        <form id="invoice-form" action="{{ route('ReturnsInvoice.store') }}" method="post" onsubmit="return confirmSubmit(event)">
            @csrf
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
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

            <!-- إضافة معرف الفاتورة الأصلية إذا كانت موجودة -->
            @if(isset($originalInvoice))
                <input type="hidden" name="original_invoice_id" value="{{ $originalInvoice->id }}">
                <input type="hidden" name="reference_id" value="{{ $originalInvoice->id }}">
            @endif

            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <label>الحقول التي عليها علامة <span style="color: red">*</span> الزامية</label>
                            @if(isset($originalInvoice))
                                <div class="mt-2">
                                    <span class="badge badge-info">
                                        <i class="fa fa-info-circle"></i>
                                        مرتجع من الفاتورة رقم: {{ $originalInvoice->invoice_number ?? $originalInvoice->id }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        <div class="d-flex">
                            <div class="btn-group mr-2" style="margin-left: 10px;">
                                <button type="button" class="btn btn-outline-info btn-sm" onclick="saveAsDraft()" title="حفظ كمسودة">
                                    <i class="fa fa-save"></i> مسودة
                                </button>
                                @if(!isset($originalInvoice))
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="copyLastInvoice()" title="نسخ آخر فاتورة">
                                    <i class="fa fa-copy"></i> نسخ
                                </button>
                                @endif
                                <button type="button" class="btn btn-outline-warning btn-sm" onclick="clearAllItems()" title="مسح الكل">
                                    <i class="fa fa-trash"></i> مسح
                                </button>
                                <button type="button" class="btn btn-outline-success btn-sm" onclick="showQuickPreview()" title="معاينة سريعة">
                                    <i class="fa fa-eye"></i> معاينة
                                </button>
                            </div>
                            <div>
                                <a href="{{ route('ReturnsInvoice.index') }}" class="btn btn-outline-danger">
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
                                                <select class="form-control select2" id="clientSelect" name="supplier_id" required
                                                    @if(isset($originalInvoice)) disabled @endif>
                                                    <option value="">اختر المورد</option>
                                                    @foreach ($suppliers as $supplier)
                                                        <option value="{{ $supplier->id }}"
                                                            data-balance="{{ $supplier->account->balance ?? 0 }}"
                                                            @if(isset($originalInvoice) && $originalInvoice->supplier_id == $supplier->id) selected
                                                            @elseif(old('supplier_id') == $supplier->id) selected @endif>
                                                            {{ $supplier->trade_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @if(isset($originalInvoice))
                                                    <input type="hidden" name="supplier_id" value="{{ $originalInvoice->supplier_id }}">
                                                @endif
                                            </div>
                                            <div class="col-md-4">
                                                <a href="{{ route('SupplierManagement.create') }}" type="button"
                                                    class="btn btn-secondary mr-1 mb-1 waves-effect waves-light">
                                                    <i class="fa fa-user-plus"></i>جديد
                                                </a>
                                            </div>
                                        </div>

                                        <!-- كارد رصيد المورد -->
                                        <div class="row" id="supplierBalanceCard" style="display: {{ isset($originalInvoice) ? 'block' : 'none' }};">
                                            <div class="col-12">
                                                <div class="card" style="background: #E3F2FD; border-radius: 8px; border: 1px solid #BBDEFB;">
                                                    <div class="card-body p-4">
                                                        <div class="row align-items-center">
                                                            <div class="col-8">
                                                                <a href="#" class="text-decoration-none" style="color: inherit;">
                                                                    <h5 class="card-title mb-2" id="supplierName" style="font-weight: 600; color: #333;">
                                                                        @if(isset($originalInvoice))
                                                                            {{ $suppliers->where('id', $originalInvoice->supplier_id)->first()->trade_name ?? 'غير محدد' }}
                                                                        @else
                                                                            اسم المورد
                                                                        @endif
                                                                    </h5>
                                                                    <p class="mb-0" style="color: #666; font-size: 0.9rem;">
                                                                        <i class="fas fa-edit ml-1" style="color: #2196F3;"></i>
                                                                        <span>تعديل البيانات</span>
                                                                    </p>
                                                                </a>
                                                            </div>
                                                            <div class="col-4 text-left">
                                                                <div class="d-flex flex-column align-items-end">
                                                                    <span style="font-size: 1.8rem; font-weight: 700; color: #333;" id="supplierBalance">
                                                                        @if(isset($originalInvoice))
                                                                            {{ number_format($suppliers->where('id', $originalInvoice->supplier_id)->first()->account->balance ?? 0, 2) }}
                                                                        @else
                                                                            0.00
                                                                        @endif
                                                                    </span>
                                                                    <small style="color: #666; margin-top: -5px;">ر.س SAR</small>
                                                                    <span id="balanceStatus" style="font-size: 0.8rem; margin-top: 5px;"></span>
                                                                    <div style="width: 4px; height: 40px; background: #4CAF50; border-radius: 2px; margin-top: 10px;"></div>
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
                                                <span>رقم المرتجع :</span>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" class="form-control" name="invoice_number"
                                                    value="{{ old('invoice_number') }}" readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-md-3">
                                                <span>تاريخ الارتجاع :</span>
                                            </div>
                                            <div class="col-md-8">
                                                <input class="form-control" type="date" name="date"
                                                    value="{{ old('date', isset($originalInvoice) ? $originalInvoice->date : date('Y-m-d')) }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-md-3">
                                                <span>سبب الارجاع :</span>
                                            </div>
                                            <div class="col-md-8">
                                                <input class="form-control" type="text" name="return_reason"
                                                    value="{{ old('return_reason', isset($originalInvoice) ? 'ارجاع من الفاتورة رقم: ' . ($originalInvoice->invoice_number ?? $originalInvoice->id) : '') }}"
                                                    placeholder="سبب الارجاع">
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
                                                    <input class="form-control" type="text" placeholder="بيانات إضافية">
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-outline-success waves-effect waves-light addeventmore">
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
                                        <th>السعر</th>
                                        <th>الخصم</th>
                                        <th>الضريبة 1</th>
                                        <th>الضريبة 2</th>
                                        <th>المجموع</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($originalInvoice) && $originalInvoice->items && count($originalInvoice->items) > 0)
                                        @foreach($originalInvoice->items as $index => $item)
                                            <tr class="item-row">
                                                <td style="width:18%">
                                                    <select name="items[{{ $index }}][product_id]" class="form-control product-select">
                                                        <option value="">اختر المنتج</option>
                                                        @foreach ($items as $product)
                                                            <option value="{{ $product->id }}" data-price="{{ $product->price }}"
                                                                @if($item->product_id == $product->id) selected @endif>
                                                                {{ $product->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="text" name="items[{{ $index }}][description]" class="form-control item-description"
                                                        value="{{ $item->description ?? $item->product->name ?? '' }}">
                                                </td>
                                                <td>
                                                    <input type="number" name="items[{{ $index }}][quantity]" class="form-control quantity"
                                                        value="{{ $item->quantity ?? 1 }}" min="1" required>
                                                </td>
                                                <td>
                                                    <input type="number" name="items[{{ $index }}][unit_price]" class="form-control price"
                                                        value="{{ $item->unit_price ?? $item->price ?? '' }}" step="0.01" required>
                                                </td>
                                                <td>
                                                    <div class="input-group">
                                                        <input type="number" name="items[{{ $index }}][discount]" class="form-control discount-amount"
                                                            value="{{ ($item->discount_type ?? 'amount') == 'amount' ? ($item->discount ?? 0) : 0 }}"
                                                            min="0" step="0.01"
                                                            style="{{ ($item->discount_type ?? 'amount') == 'percentage' ? 'display: none;' : '' }}">
                                                        <input type="number" name="items[{{ $index }}][discount_percentage]" class="form-control discount-percentage"
                                                            value="{{ ($item->discount_type ?? 'amount') == 'percentage' ? ($item->discount ?? 0) : 0 }}"
                                                            min="0" max="100" step="0.01"
                                                            style="{{ ($item->discount_type ?? 'amount') == 'amount' ? 'display: none;' : '' }}">
                                                        <div class="input-group-append">
                                                            <select name="items[{{ $index }}][discount_type]" class="form-control discount-type">
                                                                <option value="amount" @if(($item->discount_type ?? 'amount') == 'amount') selected @endif>ريال</option>
                                                                <option value="percentage" @if(($item->discount_type ?? 'amount') == 'percentage') selected @endif>%</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td data-label="الضريبة 1">
                                                    <div class="input-group">
                                                        <select name="items[{{ $index }}][tax_1]" class="form-control tax-select" data-target="tax_1"
                                                            style="width: 150px;" onchange="updateHiddenInput(this)">
                                                            <option value=""></option>
                                                            @foreach ($taxs as $tax)
                                                                <option value="{{ $tax->tax }}" data-id="{{ $tax->id }}"
                                                                    data-name="{{ $tax->name }}" data-type="{{ $tax->type }}"
                                                                    @if(($item->tax_1_id ?? '') == $tax->id || ($item->tax_1 ?? '') == $tax->tax) selected @endif>
                                                                    {{ $tax->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <input type="hidden" name="items[{{ $index }}][tax_1_id]" value="{{ $item->tax_1_id ?? '' }}">
                                                    </div>
                                                </td>

                                                <td data-label="الضريبة 2">
                                                    <div class="input-group">
                                                        <select name="items[{{ $index }}][tax_2]" class="form-control tax-select" data-target="tax_2"
                                                            style="width: 150px;" onchange="updateHiddenInput(this)">
                                                            <option value=""></option>
                                                            @foreach ($taxs as $tax)
                                                                <option value="{{ $tax->tax }}" data-id="{{ $tax->id }}"
                                                                    data-name="{{ $tax->name }}" data-type="{{ $tax->type }}"
                                                                    @if(($item->tax_2_id ?? '') == $tax->id || ($item->tax_2 ?? '') == $tax->tax) selected @endif>
                                                                    {{ $tax->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <input type="hidden" name="items[{{ $index }}][tax_2_id]" value="{{ $item->tax_2_id ?? '' }}">
                                                    </div>
                                                </td>

                                                <td>
                                                    <span class="row-total">{{ number_format(($item->quantity ?? 1) * ($item->unit_price ?? $item->price ?? 0), 2) }}</span>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-danger btn-sm remove-row">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr class="item-row">
                                            <td style="width:18%">
                                                <select name="items[0][product_id]" class="form-control product-select">
                                                    <option value="">اختر المنتج</option>
                                                    @foreach ($items as $item)
                                                        <option value="{{ $item->id }}" data-price="{{ $item->price }}">
                                                            {{ $item->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" name="items[0][description]" class="form-control item-description">
                                            </td>
                                            <td>
                                                <input type="number" name="items[0][quantity]" class="form-control quantity"
                                                    value="1" min="1" required>
                                            </td>
                                            <td>
                                                <input type="number" name="items[0][unit_price]" class="form-control price"
                                                    step="0.01" required>
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <input type="number" name="items[0][discount]" class="form-control discount-amount"
                                                        value="0" min="0" step="0.01">
                                                    <input type="number" name="items[0][discount_percentage]" class="form-control discount-percentage"
                                                        value="0" min="0" max="100" step="0.01" style="display: none;">
                                                    <div class="input-group-append">
                                                        <select name="items[0][discount_type]" class="form-control discount-type">
                                                            <option value="amount">ريال</option>
                                                            <option value="percentage">%</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </td>
                                            <td data-label="الضريبة 1">
                                                <div class="input-group">
                                                    <select name="items[0][tax_1]" class="form-control tax-select" data-target="tax_1"
                                                        style="width: 150px;" onchange="updateHiddenInput(this)">
                                                        <option value=""></option>
                                                        @foreach ($taxs as $tax)
                                                            <option value="{{ $tax->tax }}" data-id="{{ $tax->id }}"
                                                                data-name="{{ $tax->name }}" data-type="{{ $tax->type }}">
                                                                {{ $tax->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <input type="hidden" name="items[0][tax_1_id]">
                                                </div>
                                            </td>

                                            <td data-label="الضريبة 2">
                                                <div class="input-group">
                                                    <select name="items[0][tax_2]" class="form-control tax-select" data-target="tax_2"
                                                        style="width: 150px;" onchange="updateHiddenInput(this)">
                                                        <option value=""></option>
                                                        @foreach ($taxs as $tax)
                                                            <option value="{{ $tax->tax }}" data-id="{{ $tax->id }}"
                                                                data-name="{{ $tax->name }}" data-type="{{ $tax->type }}">
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
                                    @endif
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
                                        $currencySymbol = $currency == 'SAR' || empty($currency) ?
                                            '<img src="' . asset('assets/images/Saudi_Riyal.svg') . '" alt="ريال سعودي" width="13" style="display: inline-block; margin-left: 5px; vertical-align: middle;">' :
                                            $currency;
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
                                        <td colspan="7" class="text-right">المبلغ المرتجع</td>
                                        <td><span id="refund-amount">0.00</span> {!! $currencySymbol !!}</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="7" class="text-right">المجموع الكلي</td>
                                        <td><span id="grand-total">0.00</span> {!! $currencySymbol !!}</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-white">
                    <ul class="nav nav-tabs card-header-tabs align-items-center">
                        <li class="nav-item">
                            <a class="nav-link active" id="tab-discount" href="#section-discount">الخصم والتسوية</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab-deposit" href="#section-deposit">إيداع</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab-shipping" href="#section-shipping">التوصيل</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab-documents" href="#section-documents">إرفاق المستندات</a>
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
                                        value="{{ old('discount_amount', isset($originalInvoice) ? $originalInvoice->discount_amount : 0) }}"
                                        min="0" step="0.01">
                                    <select name="discount_type" class="form-control">
                                        <option value="amount" @if(old('discount_type', isset($originalInvoice) ? $originalInvoice->discount_type : 'amount') == 'amount') selected @endif>ريال</option>
                                        <option value="percentage" @if(old('discount_type', isset($originalInvoice) ? $originalInvoice->discount_type : 'amount') == 'percentage') selected @endif>نسبة مئوية</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">التسوية</label>
                                <div class="input-group">
                                    <input type="text" name="adjustment_label" class="form-control"
                                        value="{{ old('adjustment_label', isset($originalInvoice) ? $originalInvoice->adjustment_label : '') }}"
                                        placeholder="اسم التسوية (مثال: خصم نقدي)">
                                    <select name="adjustment_type" class="form-control">
                                        <option value="discount" @if(old('adjustment_type', isset($originalInvoice) ? $originalInvoice->adjustment_type : 'discount') == 'discount') selected @endif>خصم</option>
                                        <option value="addition" @if(old('adjustment_type', isset($originalInvoice) ? $originalInvoice->adjustment_type : 'discount') == 'addition') selected @endif>إضافة</option>
                                    </select>
                                    <input type="number" name="adjustment_value" step="0.01" class="form-control"
                                        value="{{ old('adjustment_value', isset($originalInvoice) ? $originalInvoice->adjustment_value : 0) }}"
                                        placeholder="قيمة التسوية">
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
                <input type="number" id="paid-amount-input" class="form-control" value="{{ old('advance_payment', isset($originalInvoice) ? $originalInvoice->advance_payment : 0) }}"
                    name="advance_payment" step="0.01" min="0" placeholder="المبلغ المدفوع">
                <select name="advance_payment_type" class="form-control">
                    <option value="amount">ريال</option>
                    <option value="percentage">نسبة مئوية</option>
                </select>
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
                                            @if(old('shipping_tax_id', isset($originalInvoice) ? $originalInvoice->shipping_tax_id : '') == $tax->id) selected @endif>
                                            {{ $tax->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">تكلفة الشحن</label>
                                <input type="number" class="form-control" name="shipping_cost" id="shipping"
                                    value="{{ old('shipping_cost', isset($originalInvoice) ? $originalInvoice->shipping_cost : 0) }}"
                                    min="0" step="0.01">
                            </div>
                        </div>
                    </div>

                    <!-- القسم الرابع: إرفاق المستندات -->
                    <div id="section-documents" class="tab-section d-none">
                        <ul class="nav nav-tabs">
                            <li class="nav-item">
                                <a class="nav-link active" id="tab-new-document" href="#content-new-document">رفع مستند جديد</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab-uploaded-documents" href="#content-uploaded-documents">بحث في الملفات</a>
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
                                                <label class="form-label mb-0" style="white-space: nowrap;">المستند:</label>
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

            <div class="card shadow-sm border-0">
                <div class="card-header border-bottom" style="background-color: transparent;">
                    <h5 class="mb-0 fw-bold text-dark" style="font-size: 1.2rem;">
                        📝 الملاحظات / الشروط
                    </h5>
                </div>
                <div class="card-body">
                    <textarea id="tinyMCE" name="notes" class="form-control" rows="6"
                        style="font-size: 1.05rem;">{{ old('notes', isset($originalInvoice) ? $originalInvoice->notes : '') }}</textarea>
                </div>
            </div>

            <div class="card">
                <div class="card-body py-2">
                    <div class="d-flex justify-content-start" style="direction: rtl;">
                        <div class="form-check">
                            <input class="form-check-input payment-toggle" type="checkbox"
                                   name="is_paid" value="{{ old('is_paid', isset($originalInvoice) ? $originalInvoice->is_paid : 0) }}" id="full-payment-check">
                            <label class="form-check-label" for="full-payment-check">
                                تم استرداد المبلغ بالكامل للمورد؟
                            </label>
                        </div>
                    </div>

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
                                عند اختيار "استرداد كامل" سيتم تعيين المبلغ المدفوع تلقائياً لكامل المبلغ المرتجع
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body py-2 align-items-right">
                    <div class="d-flex justify-content-start" style="direction: rtl;">
                        <div class="form-check">
                            <input class="form-check-input toggle-check" type="checkbox" name="is_received" value="1">
                            <label class="form-check-label">تم استلام البضاعة</label>
                        </div>
                    </div>

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
        </form>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('assets/js/invoice-calculator.js') }}"></script>
<script src="{{ asset('assets/js/invoice.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // تحديث فهارس الصفوف عند إضافة أو حذف صف
            function updateRowIndexes() {
                const rows = document.querySelectorAll('#items-table tbody .item-row');
                rows.forEach((row, index) => {
                    // تحديث أسماء الحقول
                    const inputs = row.querySelectorAll('input, select');
                    inputs.forEach(input => {
                        if (input.name && input.name.includes('items[')) {
                            input.name = input.name.replace(/items\[\d+\]/, `items[${index}]`);
                        }
                    });
                });
            }

            // إضافة صف جديد
            document.getElementById('add-row').addEventListener('click', function() {
                const tbody = document.querySelector('#items-table tbody');
                const rowCount = tbody.querySelectorAll('.item-row').length;
                addNewRow(rowCount);
                updateRowIndexes();
                calculateTotals();
            });

            // حذف صف
            document.addEventListener('click', function(e) {
                if (e.target.closest('.remove-row')) {
                    const row = e.target.closest('.item-row');
                    const tbody = document.querySelector('#items-table tbody');

                    if (tbody.querySelectorAll('.item-row').length > 1) {
                        row.remove();
                        updateRowIndexes();
                        calculateTotals();
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'تنبيه',
                            text: 'يجب أن يكون هناك صف واحد على الأقل'
                        });
                    }
                }
            });

            function addNewRow(index) {
                const tbody = document.querySelector('#items-table tbody');
                const newRow = document.createElement('tr');
                newRow.className = 'item-row';

                newRow.innerHTML = `
                    <td style="width:18%">
                        <select name="items[${index}][product_id]" class="form-control product-select">
                            <option value="">اختر المنتج</option>
                            @foreach ($items as $item)
                                <option value="{{ $item->id }}" data-price="{{ $item->price }}">
                                    {{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="text" name="items[${index}][description]" class="form-control item-description">
                    </td>
                    <td>
                        <input type="number" name="items[${index}][quantity]" class="form-control quantity"
                            value="1" min="1" required>
                    </td>
                    <td>
                        <input type="number" name="items[${index}][unit_price]" class="form-control price"
                            step="0.01" required>
                    </td>
                    <td>
                        <div class="input-group">
                            <input type="number" name="items[${index}][discount]" class="form-control discount-amount"
                                value="0" min="0" step="0.01">
                            <input type="number" name="items[${index}][discount_percentage]" class="form-control discount-percentage"
                                value="0" min="0" max="100" step="0.01" style="display: none;">
                            <div class="input-group-append">
                                <select name="items[${index}][discount_type]" class="form-control discount-type">
                                    <option value="amount">ريال</option>
                                    <option value="percentage">%</option>
                                </select>
                            </div>
                        </div>
                    </td>
                    <td data-label="الضريبة 1">
                        <div class="input-group">
                            <select name="items[${index}][tax_1]" class="form-control tax-select" data-target="tax_1"
                                style="width: 150px;" onchange="updateHiddenInput(this)">
                                <option value=""></option>
                                @foreach ($taxs as $tax)
                                    <option value="{{ $tax->tax }}" data-id="{{ $tax->id }}"
                                        data-name="{{ $tax->name }}" data-type="{{ $tax->type }}">
                                        {{ $tax->name }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="items[${index}][tax_1_id]">
                        </div>
                    </td>
                    <td data-label="الضريبة 2">
                        <div class="input-group">
                            <select name="items[${index}][tax_2]" class="form-control tax-select" data-target="tax_2"
                                style="width: 150px;" onchange="updateHiddenInput(this)">
                                <option value=""></option>
                                @foreach ($taxs as $tax)
                                    <option value="{{ $tax->tax }}" data-id="{{ $tax->id }}"
                                        data-name="{{ $tax->name }}" data-type="{{ $tax->type }}">
                                        {{ $tax->name }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="items[${index}][tax_2_id]">
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
                `;

                tbody.appendChild(newRow);

                // ربط الأحداث للصف الجديد
                bindRowEvents(newRow);
            }

            function bindRowEvents(row) {
                // عند تغيير المنتج
                const productSelect = row.querySelector('.product-select');
                if (productSelect) {
                    productSelect.addEventListener('change', function() {
                        const selectedOption = this.options[this.selectedIndex];
                        const price = selectedOption.getAttribute('data-price');
                        const priceInput = row.querySelector('.price');
                        const descriptionInput = row.querySelector('.item-description');

                        if (price && priceInput) {
                            priceInput.value = price;
                        }
                        if (selectedOption.text && descriptionInput && !descriptionInput.value) {
                            descriptionInput.value = selectedOption.text;
                        }

                        calculateRowTotal(row);
                    });
                }

                // عند تغيير الكمية أو السعر
                const quantityInput = row.querySelector('.quantity');
                const priceInput = row.querySelector('.price');
                const discountInput = row.querySelector('.discount-amount');
                const discountPercentageInput = row.querySelector('.discount-percentage');

                [quantityInput, priceInput, discountInput, discountPercentageInput].forEach(input => {
                    if (input) {
                        input.addEventListener('input', () => calculateRowTotal(row));
                    }
                });

                // عند تغيير نوع الخصم
                const discountTypeSelect = row.querySelector('.discount-type');
                if (discountTypeSelect) {
                    discountTypeSelect.addEventListener('change', function() {
                        if (this.value === 'percentage') {
                            discountInput.style.display = 'none';
                            discountPercentageInput.style.display = 'block';
                        } else {
                            discountInput.style.display = 'block';
                            discountPercentageInput.style.display = 'none';
                        }
                        calculateRowTotal(row);
                    });
                }

                // عند تغيير الضرائب
                const taxSelects = row.querySelectorAll('.tax-select');
                taxSelects.forEach(select => {
                    select.addEventListener('change', () => {
                        updateHiddenInput(select);
                        calculateRowTotal(row);
                    });
                });
            }

            function calculateRowTotal(row) {
                const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
                const price = parseFloat(row.querySelector('.price').value) || 0;
                const discountType = row.querySelector('.discount-type').value;

                let discount = 0;
                if (discountType === 'percentage') {
                    const discountPercentage = parseFloat(row.querySelector('.discount-percentage').value) || 0;
                    discount = (quantity * price * discountPercentage) / 100;
                } else {
                    discount = parseFloat(row.querySelector('.discount-amount').value) || 0;
                }

                let subtotal = (quantity * price) - discount;

                // حساب الضرائب
                const tax1Select = row.querySelector('select[name*="[tax_1]"]');
                const tax2Select = row.querySelector('select[name*="[tax_2]"]');

                let tax1 = 0, tax2 = 0;
                if (tax1Select && tax1Select.value) {
                    tax1 = (subtotal * parseFloat(tax1Select.value)) / 100;
                }
                if (tax2Select && tax2Select.value) {
                    tax2 = (subtotal * parseFloat(tax2Select.value)) / 100;
                }

                const total = subtotal + tax1 + tax2;
                row.querySelector('.row-total').textContent = total.toFixed(2);

                calculateTotals();
            }

            function calculateTotals() {
                let subtotal = 0;
                let totalDiscount = 0;
                let totalTax = 0;

                // حساب مجموع الصفوف
                document.querySelectorAll('.item-row').forEach(row => {
                    const rowTotal = parseFloat(row.querySelector('.row-total').textContent) || 0;
                    subtotal += rowTotal;

                    // حساب الخصومات
                    const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
                    const price = parseFloat(row.querySelector('.price').value) || 0;
                    const discountType = row.querySelector('.discount-type').value;

                    let rowDiscount = 0;
                    if (discountType === 'percentage') {
                        const discountPercentage = parseFloat(row.querySelector('.discount-percentage').value) || 0;
                        rowDiscount = (quantity * price * discountPercentage) / 100;
                    } else {
                        rowDiscount = parseFloat(row.querySelector('.discount-amount').value) || 0;
                    }
                    totalDiscount += rowDiscount;
                });

                // الخصم العام
                const generalDiscountAmount = parseFloat(document.querySelector('input[name="discount_amount"]').value) || 0;
                const generalDiscountType = document.querySelector('select[name="discount_type"]').value;

                let generalDiscount = 0;
                if (generalDiscountType === 'percentage') {
                    generalDiscount = (subtotal * generalDiscountAmount) / 100;
                } else {
                    generalDiscount = generalDiscountAmount;
                }

                // تكلفة الشحن
                const shippingCost = parseFloat(document.querySelector('input[name="shipping_cost"]').value) || 0;

                // التسوية
                const adjustmentValue = parseFloat(document.querySelector('input[name="adjustment_value"]').value) || 0;
                const adjustmentType = document.querySelector('select[name="adjustment_type"]').value;

                let adjustment = adjustmentType === 'addition' ? adjustmentValue : -adjustmentValue;

                // المجموع النهائي
                const grandTotal = subtotal - generalDiscount + shippingCost + adjustment;

                // تحديث العرض
                document.getElementById('subtotal').textContent = subtotal.toFixed(2);
                document.getElementById('total-discount').textContent = (totalDiscount + generalDiscount).toFixed(2);
                document.getElementById('shipping-cost').textContent = shippingCost.toFixed(2);
                document.getElementById('grand-total').textContent = grandTotal.toFixed(2);

                // تحديث المبلغ المرتجع
                const refundAmountInput = document.getElementById('refund-amount-input');
                if (refundAmountInput && !refundAmountInput.value) {
                    refundAmountInput.value = grandTotal.toFixed(2);
                }
                document.getElementById('refund-amount').textContent = (parseFloat(refundAmountInput?.value || 0)).toFixed(2);
            }

            // ربط الأحداث للصفوف الموجودة
            document.querySelectorAll('.item-row').forEach(row => {
                bindRowEvents(row);
            });

            // حساب المجاميع عند التحميل
            @if(isset($originalInvoice) && $originalInvoice->items && count($originalInvoice->items) > 0)
                setTimeout(() => {
                    calculateTotals();
                }, 500);
            @endif

            // تفعيل التبويبات
            document.querySelectorAll('.nav-tabs a').forEach(tab => {
                tab.addEventListener('click', function(e) {
                    e.preventDefault();

                    // إزالة active من جميع التبويبات
                    document.querySelectorAll('.nav-tabs a').forEach(t => t.classList.remove('active'));
                    document.querySelectorAll('.tab-section').forEach(s => s.classList.add('d-none'));

                    // إضافة active للتبويب المحدد
                    this.classList.add('active');
                    const targetSection = document.querySelector(this.getAttribute('href'));
                    if (targetSection) {
                        targetSection.classList.remove('d-none');
                    }
                });
            });

            // تفعيل التبويبات الداخلية للمستندات
            document.querySelectorAll('#section-documents .nav-tabs a').forEach(tab => {
                tab.addEventListener('click', function(e) {
                    e.preventDefault();

                    document.querySelectorAll('#section-documents .nav-tabs a').forEach(t => t.classList.remove('active'));
                    document.querySelectorAll('#section-documents .tab-pane').forEach(p => {
                        p.classList.remove('active');
                        p.classList.add('d-none');
                    });

                    this.classList.add('active');
                    const targetPane = document.querySelector(this.getAttribute('href'));
                    if (targetPane) {
                        targetPane.classList.add('active');
                        targetPane.classList.remove('d-none');
                    }
                });
            });

            // تفعيل toggle للمدفوعات
            document.querySelectorAll('.toggle-check, .payment-toggle, .refund-payment-toggle').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const targetClass = this.classList.contains('payment-toggle') ? '.full-payment-fields' :
                                       this.classList.contains('refund-payment-toggle') ? '.refund-payment-fields' : '.payment-fields';
                    const targetFields = this.closest('.card-body').querySelector(targetClass);
                    if (targetFields) {
                        targetFields.style.display = this.checked ? 'block' : 'none';
                    }
                });
            });

            // دالة تأكيد الحفظ
            window.confirmSubmit = function(event) {
                event.preventDefault();

                Swal.fire({
                    title: 'تأكيد الحفظ',
                    text: 'هل أنت متأكد من حفظ مرتجع المشتريات؟',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'نعم، احفظ',
                    cancelButtonText: 'إلغاء',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('invoice-form').submit();
                    }
                });
            };

            // دالة حفظ المسودة
            window.saveAsDraft = function() {
                Swal.fire({
                    title: 'حفظ كمسودة',
                    text: 'هل تريد حفظ مرتجع المشتريات كمسودة؟',
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
            };

            // دالة مسح جميع العناصر
            window.clearAllItems = function() {
                Swal.fire({
                    title: 'مسح جميع العناصر',
                    text: 'هل تريد مسح جميع عناصر الفاتورة؟',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'نعم، امسح',
                    cancelButtonText: 'إلغاء'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const tbody = document.querySelector('#items-table tbody');
                        tbody.innerHTML = '';
                        addNewRow(0);
                        calculateTotals();

                        Swal.fire({
                            icon: 'success',
                            title: 'تم المسح',
                            text: 'تم مسح جميع العناصر بنجاح',
                            timer: 2000
                        });
                    }
                });
            };

            // دالة المعاينة السريعة
            window.showQuickPreview = function() {
                // يمكن تطوير هذه الدالة لإظهار معاينة سريعة للفاتورة
                Swal.fire({
                    title: 'معاينة سريعة',
                    html: '<p>ستتم إضافة المعاينة السريعة قريباً</p>',
                    icon: 'info'
                });
            };

            // رسائل النجاح والخطأ
            @if(session('success'))
                Swal.fire({
                    title: 'تم الحفظ!',
                    text: '{{ session('success') }}',
                    icon: 'success',
                    confirmButtonText: 'حسناً'
                });
            @endif

            @if($errors->any())
                Swal.fire({
                    title: 'خطأ!',
                    html: `<ul style="text-align: right;">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>`,
                    icon: 'error',
                    confirmButtonText: 'حسناً'
                });
            @endif
        });

        // دالة تحديث الحقول المخفية للضرائب
        function updateHiddenInput(selectElement) {
            const row = selectElement.closest('.item-row');
            const taxType = selectElement.getAttribute('data-target');
            const hiddenInput = row.querySelector(`input[name*="[${taxType}_id]"]`);

            if (hiddenInput) {
                const selectedOption = selectElement.options[selectElement.selectedIndex];
                hiddenInput.value = selectedOption.getAttribute('data-id') || '';
            }
        }
    </script>
@endsection