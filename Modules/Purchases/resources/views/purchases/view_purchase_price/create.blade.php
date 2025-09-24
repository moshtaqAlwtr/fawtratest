@extends('master')

@section('title')
    انشاء عرض سعر مشتريات
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/purch.css') }}">
@endsection
@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0"></h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a>
                            </li>
                            <li class="breadcrumb-item active">عرض
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body">
        <form id="invoice-form" action="{{ route('pricesPurchase.store') }}" method="post">
            @csrf
            @if (isset($quotation) && $quotation)
                <input type="hidden" name="quotation_id" value="{{ $quotation->id }}">
            @endif
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

            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
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
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body">

                                <div class="row">
                                    <div class="col-12">

                                    </div>
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
                                                            {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                                            {{ $supplier->trade_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <a href="{{ route('SupplierManagement.create') }}" type="button"
                                                    class="btn btn-primary mr-1 mb-1 waves-effect waves-light">
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
                                                                <a href="{{ route('SupplierManagement.edit', ['id' => $supplier->id]) }}"
                                                                    class="text-decoration-none" style="color: inherit;">
                                                                    <h5 class="card-title mb-2" id="supplierName"
                                                                        style="font-weight: 600; color: #333;">
                                                                        {{ $supplier->name }}</h5>
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
                                                                        id="supplierBalance">{{ $supplier->account->balance ?? 0 }}</span>
                                                                    <small style="color: #666; margin-top: -5px;">ر.س
                                                                        SAR</small>
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
                                                <span>رقم عرض الشراء:</span>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" class="form-control" id="purchase_price_number"
                                                    name="purchase_price_number" value="{{ $purchasePriceNumber }}"
                                                    readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-md-3">
                                                <span>التاريخ:</span>
                                            </div>
                                            <div class="col-md-8">
                                                <input class="form-control" type="date" name="date" value="{{ date('Y-m-d',) }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-md-3">
                                                <span>صالح حتى :</span>
                                            </div>
                                            <div class="col-md-6">
                                                <input class="form-control" type="text" name="valid_days">
                                            </div>
                                            <div class="col-md-2">
                                                <span class="form-control-plaintext">أيام</span>
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
                                    <tr class="item-row">
                                        <td style="width:18%">
                                            <select name="items[0][product_id]"
                                                class="form-control product-select select2">
                                                <option value="">اختر المنتج</option>
                                                @foreach ($items as $item)
                                                    <option value="{{ $item->id }}"
                                                        data-price="{{ $item->price }}">
                                                        {{ $item->name }}</option>
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
                                                step="0.01" required>
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <input type="number" name="items[0][discount]"
                                                    class="form-control discount-amount" value="0" min="0"
                                                    step="0.01">
                                                <input type="number" name="items[0][discount_percentage]"
                                                    class="form-control discount-percentage" value="0"
                                                    min="0" max="100" step="0.01">
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
                                                <i class="fa fa-plus"></i> إضافة
                                            </button>
                                        </td>
                                    </tr>
                                    @php
                                        $currencySymbol =
                                            '<img src="' .
                                            asset('assets/images/Saudi_Riyal.svg') .
                                            '" alt="ريال سعودي" width="13" style="display: inline-block; margin-left: 5px; vertical-align: middle;">';
                                    @endphp
                                    <tr>
                                        <td colspan="7" class="text-right">المجموع الفرعي</td>
                                        <td><span id="subtotal">0.00</span></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="7" class="text-right">مجموع الخصومات</td>
                                        <td><span id="total-discount">0.00</span></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <small id="tax-details"></small> <!-- مكان عرض تفاصيل الضرائب -->
                                    </tr>
                                    <tr>
                                        <td colspan="7" class="text-right">المجموع الكلي</td>
                                        <td><span id="grand-total">0.00</span></td>
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
                    <!-- التبويبات الرئيسية -->
                    <ul class="nav nav-tabs card-header-tabs align-items-center">

                        <li class="nav-item">
                            <a class="nav-link active" id="tab-discount" href="#">الخصم والتسوية</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab-deposit" href="#">إيداع</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab-shipping" href="#"> التوصيل </a>
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
                                    <!-- اسم التسوية -->
                                    <input type="text" name="adjustment_label" class="form-control"
                                        placeholder="اسم التسوية (مثال: خصم نقدي)">

                                    <!-- نوع التسوية: خصم أو إضافة -->
                                    <select name="adjustment_type" class="form-control">
                                        <option value="discount">خصم</option>
                                        <option value="addition">إضافة</option>
                                    </select>

                                    <!-- قيمة التسوية -->
                                    <input type="number" name="adjustment_value" step="0.01" class="form-control"
                                        placeholder="قيمة التسوية">

                                </div>
                            </div>
                        </div>
                    </div>




                    <!-- القسم الثالث:      التوصيل -->
                    <div id="section-shipping" class="tab-section d-none">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">نوع الضريبة</label>
                                <select class="form-control" id="methodSelect" name="tax_id">
                                    <option value="">اختر الضريبة </option>
                                    @foreach ($taxs as $tax)
                                        <option value="{{ $tax->tax }}" data-id="{{ $tax->id }}">
                                            {{ $tax->name }}</option>
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
                        <!-- التبويبات الداخلية -->
                        <ul class="nav nav-tabs">
                            <li class="nav-item">
                                <a class="nav-link active" id="tab-new-document" href="#">رفع مستند جديد</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab-uploaded-documents" href="#">بحث في الملفات</a>
                            </li>
                        </ul>

                        <!-- محتوى التبويبات -->
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
                                            aria-describedby="uploadButton">
                                        <button class="btn btn-primary" id="uploadButton">
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
                                                    <option selected>Select Document</option>
                                                    <option value="1">مستند 1</option>
                                                    <option value="2">مستند 2</option>
                                                    <option value="3">مستند 3</option>
                                                </select>
                                                <button type="button" class="btn btn-success">
                                                    أرفق
                                                </button>
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
                    <textarea id="tinyMCE" name="notes" class="form-control" rows="6" style="font-size: 1.05rem;"></textarea>
                </div>
            </div>

        </form>
    </div>
    </div>
    </div>

    <!------------------------->

    <div style="visibility: hidden;">
        <div class="whole_extra_item_add" id="whole_extra_item_add">
            <div class="delete_whole_extra_item_add" id="delete_whole_extra_item_add">

                <div class="col-12">
                    <div class="form-group row">
                        <div class="col-md-3">
                            <input class="form-control" type="text" name="" id=""
                                placeholder="عنوان اضافي">
                        </div>
                        <div class="col-md-6">
                            <input class="form-control" type="text" name="" id=""
                                placeholder="بيانات اضافيه">
                        </div>
                        <div class="form-label-group">
                            <span
                                class="btn btn-icon btn-icon rounded-circle btn-outline-success mr-1 mb-1 waves-effect waves-light addeventmore"><i
                                    class="fa fa-plus-circle"></i></span>
                            <span
                                class="btn btn-icon btn-icon rounded-circle btn-outline-danger mr-1 mb-1 waves-effect waves-light removeeventmore"><i
                                    class="fa fa-minus-circle"></i></span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    </div>
@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('assets/js/invoice.js') }}"></script>
    <script>
        function updateHiddenInput(selectElement) {
            // البحث عن أقرب صف يحتوي على العنصر المحدد
            var row = selectElement.closest('.item-row');

            // استخراج نوع الضريبة (tax_1 أو tax_2) من data-target
            var taxType = selectElement.getAttribute('data-target');

            // البحث عن الحقل المخفي داخل نفس الصف المرتبط بهذه الضريبة
            var hiddenInput = row.querySelector('input[name^="items"][name$="[' + taxType + '_id]"]');

            // تحديث قيمة الحقل المخفي بناءً على الضريبة المختارة
            if (hiddenInput) {
                hiddenInput.value = selectElement.options[selectElement.selectedIndex].getAttribute('data-id');
            }
        }
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            function calculateTotals() {
                let subtotal = 0; // المجموع الفرعي (قبل كل شيء)
                let totalItemDiscount = 0; // إجمالي خصومات العناصر
                let totalItemTax = 0; // إجمالي ضرائب العناصر
                let taxDetails = {}; // تفاصيل الضرائب المختارة

                // مسح صفوف الضرائب السابقة
                document.querySelectorAll(".dynamic-tax-row").forEach(row => row.remove());

                // حساب إجماليات العناصر
                document.querySelectorAll(".item-row").forEach(function(row) {
                    let quantity = parseFloat(row.querySelector(".quantity").value) || 0;
                    let unitPrice = parseFloat(row.querySelector(".price").value) || 0;
                    let itemSubtotal = quantity * unitPrice; // المجموع الفرعي للعنصر
                    subtotal += itemSubtotal;

                    // حساب خصم العنصر
                    let itemDiscount = 0;
                    let discountType = row.querySelector(".discount-type").value;
                    if (discountType === 'percentage') {
                        let discountPercentage = parseFloat(row.querySelector(".discount-percentage")
                            .value) || 0;
                        itemDiscount = (itemSubtotal * discountPercentage) / 100;
                    } else {
                        itemDiscount = parseFloat(row.querySelector(".discount-amount").value) || 0;
                    }
                    totalItemDiscount += itemDiscount;

                    // حساب ضرائب العنصر
                    let tax1Value = parseFloat(row.querySelector("[name^='items'][name$='[tax_1]']")
                        .value) || 0;
                    let tax1Type = row.querySelector("[name^='items'][name$='[tax_1]']").options[
                        row.querySelector("[name^='items'][name$='[tax_1]']").selectedIndex
                    ].dataset.type;
                    let tax1Name = row.querySelector("[name^='items'][name$='[tax_1]']").options[
                        row.querySelector("[name^='items'][name$='[tax_1]']").selectedIndex
                    ].dataset.name;

                    let tax2Value = parseFloat(row.querySelector("[name^='items'][name$='[tax_2]']")
                        .value) || 0;
                    let tax2Type = row.querySelector("[name^='items'][name$='[tax_2]']").options[
                        row.querySelector("[name^='items'][name$='[tax_2]']").selectedIndex
                    ].dataset.type;
                    let tax2Name = row.querySelector("[name^='items'][name$='[tax_2]']").options[
                        row.querySelector("[name^='items'][name$='[tax_2]']").selectedIndex
                    ].dataset.name;

                    // حساب الضريبة الأولى
                    if (tax1Value > 0 && tax1Name) {
                        let itemTax = 0;
                        if (tax1Type === 'included') {
                            // الضريبة متضمنة: نستخرجها من المجموع الكلي
                            itemTax = itemSubtotal - (itemSubtotal / (1 + (tax1Value / 100)));
                        } else {
                            // الضريبة غير متضمنة: نضيفها إلى المجموع الفرعي
                            itemTax = (itemSubtotal * tax1Value) / 100;
                        }

                        if (!taxDetails[tax1Name]) {
                            taxDetails[tax1Name] = 0;
                        }
                        taxDetails[tax1Name] += itemTax;
                        totalItemTax += itemTax;
                    }

                    // حساب الضريبة الثانية
                    if (tax2Value > 0 && tax2Name) {
                        let itemTax = 0;
                        if (tax2Type === 'included') {
                            // الضريبة متضمنة: نستخرجها من المجموع الكلي
                            itemTax = itemSubtotal - (itemSubtotal / (1 + (tax2Value / 100)));
                        } else {
                            // الضريبة غير متضمنة: نضيفها إلى المجموع الفرعي
                            itemTax = (itemSubtotal * tax2Value) / 100;
                        }

                        if (!taxDetails[tax2Name]) {
                            taxDetails[tax2Name] = 0;
                        }
                        taxDetails[tax2Name] += itemTax;
                        totalItemTax += itemTax;
                    }

                    // تحديث إجمالي الصف
                    let rowTotal = itemSubtotal - itemDiscount;
                    row.querySelector(".row-total").innerText = rowTotal.toFixed(2);
                });

                // حساب الخصم الإضافي
                let additionalDiscount = 0;
                let discountAmount = parseFloat(document.querySelector("[name='discount_amount']")?.value) || 0;
                let discountType = document.querySelector("[name='discount_type']")?.value;

                if (discountAmount > 0) {
                    if (discountType === 'percentage') {
                        additionalDiscount = (subtotal * discountAmount) / 100;
                    } else {
                        additionalDiscount = discountAmount;
                    }
                }

                // حساب التسوية
                let adjustmentAmount = 0;
                let adjustmentValue = parseFloat(document.querySelector("[name='adjustment_value']")?.value) || 0;
                let adjustmentType = document.querySelector("[name='adjustment_type']")?.value;

                if (adjustmentValue > 0) {
                    if (adjustmentType === 'discount') {
                        adjustmentAmount = -adjustmentValue; // خصم
                    } else {
                        adjustmentAmount = adjustmentValue; // إضافة
                    }
                }

                // حساب تكلفة الشحن وضريبتها
                let shippingCost = parseFloat(document.querySelector("[name='shipping_cost']")?.value) || 0;
                let shippingTax = 0;
                let shippingTaxSelect = document.querySelector("[name='tax_id']");

                if (shippingCost > 0 && shippingTaxSelect && shippingTaxSelect.value) {
                    let selectedOption = shippingTaxSelect.options[shippingTaxSelect.selectedIndex];
                    let taxRate = parseFloat(selectedOption.value) || 0;
                    let taxName = selectedOption.text;

                    if (taxRate > 0) {
                        shippingTax = (shippingCost * taxRate) / 100;

                        // إضافة ضريبة الشحن إلى تفاصيل الضرائب
                        let shippingTaxName = taxName + " (شحن)";
                        if (!taxDetails[shippingTaxName]) {
                            taxDetails[shippingTaxName] = 0;
                        }
                        taxDetails[shippingTaxName] += shippingTax;
                    }
                }

                // إضافة صفوف الضرائب ديناميكيًا
                let taxRowsContainer = document.getElementById("tax-rows");

                // إضافة صف الخصم الإضافي إذا وجد
                if (additionalDiscount > 0) {
                    let discountRow = document.createElement("tr");
                    discountRow.classList.add("dynamic-tax-row");
                    discountRow.innerHTML = `
                <td colspan="7" class="text-right">خصم إضافي</td>
                <td><span class="text-danger">-${additionalDiscount.toFixed(2)}</span></td>
                <td></td>
            `;
                    taxRowsContainer.insertBefore(discountRow, document.querySelector("#tax-rows tr:last-child"));
                }

                // إضافة صف التسوية إذا وجد
                if (adjustmentAmount !== 0) {
                    let adjustmentLabel = document.querySelector("[name='adjustment_label']")?.value || "تسوية";
                    let adjustmentRow = document.createElement("tr");
                    adjustmentRow.classList.add("dynamic-tax-row");
                    let adjustmentClass = adjustmentAmount > 0 ? "text-success" : "text-danger";
                    let adjustmentSign = adjustmentAmount > 0 ? "+" : "";

                    adjustmentRow.innerHTML = `
                <td colspan="7" class="text-right">${adjustmentLabel}</td>
                <td><span class="${adjustmentClass}">${adjustmentSign}${adjustmentAmount.toFixed(2)}</span></td>
                <td></td>
            `;
                    taxRowsContainer.insertBefore(adjustmentRow, document.querySelector("#tax-rows tr:last-child"));
                }

                // إضافة صف تكلفة الشحن إذا وجد
                if (shippingCost > 0) {
                    let shippingRow = document.createElement("tr");
                    shippingRow.classList.add("dynamic-tax-row");
                    shippingRow.innerHTML = `
                <td colspan="7" class="text-right">تكلفة الشحن</td>
                <td><span>${shippingCost.toFixed(2)}</span></td>
                <td></td>
            `;
                    taxRowsContainer.insertBefore(shippingRow, document.querySelector("#tax-rows tr:last-child"));
                }

                // إضافة صفوف الضرائب
                for (let taxName in taxDetails) {
                    let taxRow = document.createElement("tr");
                    taxRow.classList.add("dynamic-tax-row");
                    taxRow.innerHTML = `
                <td colspan="7" class="text-right">${taxName}</td>
                <td><span>${taxDetails[taxName].toFixed(2)}</span></td>
                <td></td>
            `;
                    taxRowsContainer.insertBefore(taxRow, document.querySelector("#tax-rows tr:last-child"));
                }

                // حساب الإجماليات
                let totalDiscount = totalItemDiscount + additionalDiscount;
                let totalTax = totalItemTax + shippingTax;
                let grandTotal = subtotal - totalDiscount + adjustmentAmount + shippingCost + totalTax;

                // تحديث القيم في الواجهة
                document.getElementById("subtotal").innerText = subtotal.toFixed(2);
                document.getElementById("total-discount").innerText = totalDiscount.toFixed(2);
                document.getElementById("grand-total").innerText = grandTotal.toFixed(2);
            }

            // ربط الأحداث
            document.addEventListener("input", function(event) {
                if (event.target.matches(
                        ".quantity, .price, .discount-amount, .discount-percentage, [name='discount_amount'], [name='adjustment_value'], [name='shipping_cost']"
                        )) {
                    calculateTotals();
                }
            });

            document.addEventListener("change", function(event) {
                if (event.target.matches(
                        ".tax-select, .discount-type, [name='discount_type'], [name='adjustment_type'], [name='tax_id']"
                        )) {
                    calculateTotals();
                }
            });

            // حساب القيم عند تحميل الصفحة
            calculateTotals();
        });

        // إضافة وحذف الصفوف
        document.addEventListener('click', function(e) {
            if (e.target.closest('#add-row')) {
                e.preventDefault();
                addNewRow();
            }

            if (e.target.closest('.remove-row')) {
                e.preventDefault();
                removeRow(e.target.closest('.item-row'));
            }
        });

        function addNewRow() {
            let table = document.querySelector('#items-table tbody');
            let rowCount = table.children.length;
            let newRow = table.children[0].cloneNode(true);

            // تحديث أسماء الحقول
            newRow.querySelectorAll('input, select').forEach(function(input) {
                if (input.name) {
                    input.name = input.name.replace(/\[\d+\]/, `[${rowCount}]`);
                    if (input.type !== 'hidden') {
                        input.value = input.type === 'number' ? (input.classList.contains('quantity') ? '1' : '0') :
                            '';
                    }
                }
            });

            // إعادة تهيئة Select2 للصف الجديد
            $(newRow).find('.select2').select2();

            table.appendChild(newRow);
            calculateTotals();
        }

        function removeRow(row) {
            if (document.querySelectorAll('.item-row').length > 1) {
                row.remove();
                calculateTotals();
            } else {
                alert('لا يمكن حذف جميع العناصر');
            }
        }

        // معالجة التبويبات
        document.addEventListener('click', function(e) {
            // التبويبات الرئيسية
            if (e.target.matches('#tab-discount, #tab-deposit, #tab-shipping, #tab-documents')) {
                e.preventDefault();

                // إزالة الكلاس النشط من جميع التبويبات
                document.querySelectorAll('.nav-link').forEach(tab => tab.classList.remove('active'));
                document.querySelectorAll('.tab-section').forEach(section => section.classList.add('d-none'));

                // تفعيل التبويب المحدد
                e.target.classList.add('active');

                let targetSection = '';
                switch (e.target.id) {
                    case 'tab-discount':
                        targetSection = 'section-discount';
                        break;
                    case 'tab-deposit':
                        targetSection = 'section-deposit';
                        break;
                    case 'tab-shipping':
                        targetSection = 'section-shipping';
                        break;
                    case 'tab-documents':
                        targetSection = 'section-documents';
                        break;
                }

                if (targetSection) {
                    document.getElementById(targetSection).classList.remove('d-none');
                }
            }

            // التبويبات الفرعية للمستندات
            if (e.target.matches('#tab-new-document, #tab-uploaded-documents')) {
                e.preventDefault();

                // إزالة الكلاس النشط من التبويبات الفرعية
                document.querySelectorAll('#tab-new-document, #tab-uploaded-documents').forEach(tab =>
                    tab.classList.remove('active'));
                document.querySelectorAll('#content-new-document, #content-uploaded-documents').forEach(content =>
                    content.classList.add('d-none'));

                // تفعيل التبويب المحدد
                e.target.classList.add('active');

                if (e.target.id === 'tab-new-document') {
                    document.getElementById('content-new-document').classList.remove('d-none');
                } else {
                    document.getElementById('content-uploaded-documents').classList.remove('d-none');
                }
            }
        });
    </script>

    <script>
        function showSupplierBalance(selectElement) {
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            const balanceCard = document.getElementById('supplierBalanceCard');

            if (selectedOption.value && selectedOption.value !== '') {
                // إظهار الكارد
                balanceCard.style.display = 'block';

                // تحديث بيانات المورد
                const supplierName = selectedOption.text;
                const supplierBalance = parseFloat(selectedOption.getAttribute('data-balance')) || 0;

                document.getElementById('supplierName').textContent = supplierName;
                document.getElementById('supplierBalance').textContent = Math.abs(supplierBalance).toFixed(2);

                // تحديد حالة الرصيد (دائن/مدين)
                const balanceStatus = document.getElementById('balanceStatus');
                const balanceElement = document.getElementById('supplierBalance');

                if (supplierBalance > 0) {
                    balanceStatus.textContent = 'دائن';
                    balanceStatus.style.color = '#4CAF50';
                    balanceElement.style.color = '#4CAF50';
                } else if (supplierBalance < 0) {
                    balanceStatus.textContent = 'مدين';
                    balanceStatus.style.color = '#f44336';
                    balanceElement.style.color = '#f44336';
                } else {
                    balanceStatus.textContent = 'متوازن';
                    balanceStatus.style.color = '#FFC107';
                    balanceElement.style.color = '#FFC107';
                }

                // إضافة تأثير الظهور
                balanceCard.style.opacity = '0';
                balanceCard.style.transform = 'translateY(-20px)';

                setTimeout(() => {
                    balanceCard.style.transition = 'all 0.3s ease';
                    balanceCard.style.opacity = '1';
                    balanceCard.style.transform = 'translateY(0)';
                }, 10);

            } else {
                // إخفاء الكارد
                balanceCard.style.display = 'none';
            }
        }

        // إظهار الكارد إذا كان هناك مورد محدد مسبقاً
        document.addEventListener('DOMContentLoaded', function() {
            const selectElement = document.getElementById('clientSelect');
            if (selectElement.value) {
                showSupplierBalance(selectElement);
            }
        });
    </script>


@endsection




@endsection
