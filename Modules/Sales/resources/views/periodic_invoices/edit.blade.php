@extends('master')

@section('title')
    تعديل فاتورة دورية
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">تعديل فاتورة دورية </h2>
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
        <form id="" action="{{ route('periodic_invoices.update', ['id' => $periodicInvoice->id]) }}"
            method="POST">
            @csrf
            @method('PUT')
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

                <div class="card-content">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">

                                <div class="btn-group mb-1">
                                    <div class="dropdown">
                                        <button class="btn btn-info dropdown-toggle mr-1" type="button"
                                            id="dropdownMenuButton3" data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                            معاينة </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton3">
                                            <a class="dropdown-item" href="#">Option 1</a>
                                            <a class="dropdown-item" href="#">Option 2</a>
                                            <a class="dropdown-item" href="#">Option 3</a>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-success mr-1 mb-1">حفظ </button>
                                <div class="btn-group mb-1">
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-dark dropdown-toggle mr-1" type="button"
                                            id="dropdownMenuButton7" data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                            حفظ دون طباعة
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton7">
                                            <a class="dropdown-item" href="#">Option 1</a>
                                            <a class="dropdown-item" href="#">Option 2</a>
                                            <a class="dropdown-item" href="#">Option 3</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="card shadow-lg hover-shadow-lg transition-all duration-300"
                        style="border: none; border-radius: 15px;">
                        <div class="card-header bg-gradient-to-r from-blue-50 to-blue-100"
                            style="border-radius: 15px 15px 0 0; border-bottom: 1px solid rgba(0,0,0,0.05);">
                            <h5 class="mb-0 text-primary" style="color: #2c3e50; font-weight: 600;">
                                <i class="fas fa-cog me-2"></i>خيارات الإصدار الآلي
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <!-- الحقل الأول: الاشتراك -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label fw-bold mb-2" style="color: #34495e;">
                                            <i class="fas fa-file-contract me-1"></i>الاشتراك
                                        </label>
                                        <input type="text" class="form-control form-control-lg shadow-sm"
                                            placeholder="أدخل تفاصيل الاشتراك" name="details_subscription"
                                            style="border-radius: 10px; border: 1px solid #e2e8f0; padding: 12px;"
                                            value="{{ old('details_subscription', $periodicInvoice->details_subscription) }}">
                                    </div>
                                </div>

                                <!-- الحقل الثاني: إصدار فاتورة كل -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label fw-bold mb-2" style="color: #34495e;">
                                            <i class="fas fa-sync me-1"></i>إصدار فاتورة كل
                                        </label>
                                        <div class="input-group">
                                            <input type="number" class="form-control form-control-lg shadow-sm"
                                                value="1" min="1" name="repeat_interval"
                                                style="border-radius: 10px 0 0 10px; border: 1px solid #e2e8f0; border-right: none; padding: 12px;"
                                                value="{{ old('repeat_interval', $periodicInvoice->repeat_interval) }}">
                                            <select class="form-select form-select-lg shadow-sm" name="repeat_type"
                                                style="border-radius: 0 10px 10px 0; border: 1px solid #e2e8f0; padding: 12px;">
                                                <option value="1"
                                                    {{ old('repeat_type', $periodicInvoice->repeat_type) == 1 ? 'selected' : '' }}>
                                                    يوم</option>
                                                <option value="2"
                                                    {{ old('repeat_type', $periodicInvoice->repeat_type) == 2 ? 'selected' : '' }}>
                                                    أسبوع</option>
                                                <option value="3"
                                                    {{ old('repeat_type', $periodicInvoice->repeat_type) == 3 ? 'selected' : '' }}>
                                                    شهر</option>
                                                <option value="4"
                                                    {{ old('repeat_type', $periodicInvoice->repeat_type) == 4 ? 'selected' : '' }}>
                                                    سنة</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- الحقل الثالث: عدد مرات التكرار -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label fw-bold mb-2" style="color: #34495e;">
                                            <i class="fas fa-redo me-1"></i>عدد مرات التكرار
                                        </label>
                                        <input type="number" class="form-control form-control-lg shadow-sm"
                                            min="1" placeholder="أدخل عدد التكرار" name="repeat_count"
                                            style="border-radius: 10px; border: 1px solid #e2e8f0; padding: 12px;"
                                            value="{{ old('repeat_count', $periodicInvoice->repeat_count) }}">
                                    </div>
                                </div>

                                <!-- تاريخ أول فاتورة -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label fw-bold mb-2" style="color: #34495e;">
                                            <i class="fas fa-calendar-alt me-1"></i>تاريخ أول فاتورة
                                        </label>
                                        <input type="date" class="form-control form-control-lg shadow-sm"
                                            name="first_invoice_date"
                                            value="{{ old('first_invoice_date', $periodicInvoice->first_invoice_date) }}"
                                            style="border-radius: 10px; border: 1px solid #e2e8f0; padding: 12px;">
                                    </div>
                                </div>

                                <!-- أصدر الفاتورة قبل -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label fw-bold mb-2" style="color: #34495e;">
                                            <i class="fas fa-clock me-1"></i>أصدر الفاتورة قبل
                                        </label>
                                        <div class="d-flex align-items-center gap-3">
                                            <input type="number" class="form-control form-control-lg shadow-sm"
                                                value="0" name="invoice_days_offset"
                                                style="border-radius: 10px; border: 1px solid #e2e8f0; width: 120px; padding: 12px;"
                                                value="{{ old('invoice_days_offset', $periodicInvoice->invoice_days_offset) }}">
                                            <span class="mx-2 fw-bold" style="color: #34495e;">أيام</span>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="is_active"
                                                    value="{{ old('is_active', $periodicInvoice->is_active) }}" checked
                                                    style="width: 3rem; height: 1.5rem;">
                                                <label class="form-check-label fw-bold"
                                                    style="color: #34495e; margin-right: 20px">نشط</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Checkboxes Section -->
                            <div class="mt-4 p-4 bg-light"
                                style="border-radius: 15px; box-shadow: inset 0 2px 4px 0 rgba(0,0,0,0.05);">
                                <div class="form-check custom-checkbox mb-3">
                                    <input class="form-check-input shadow-sm" type="checkbox" name="auto_generate"
                                        style="width: 1.25rem; height: 1.25rem; border: 2px solid #e2e8f0; cursor: pointer;"
                                        value="{{ old('auto_generate', $periodicInvoice->auto_generate) }}">
                                    <label class="form-check-label fw-bold ms-2" style="color: #34495e; cursor: pointer;">
                                        <i class="fas fa-envelope me-1"></i>أرسل لي نسخة من الفاتورة المنشأة
                                    </label>
                                </div>
                                <div class="form-check custom-checkbox mb-3">
                                    <input class="form-check-input shadow-sm" type="checkbox" name="show_from_to_dates"
                                        style="width: 1.25rem; height: 1.25rem; border: 2px solid #e2e8f0; cursor: pointer;"
                                        value="{{ old('show_from_to_dates', $periodicInvoice->show_from_to_dates) }}">
                                    <label class="form-check-label fw-bold ms-2" style="color: #34495e; cursor: pointer;">
                                        <i class="fas fa-calendar-week me-1"></i>عرض تاريخ "منذ" و "حتى" في الفاتورة
                                    </label>
                                </div>
                                <div class="form-check custom-checkbox">
                                    <input class="form-check-input shadow-sm" type="checkbox"
                                        name="disable_partial_payment" value="{{ old('disable_partial_payment', $periodicInvoice->disable_partial_payment)}}"
                                        style="width: 1.25rem; height: 1.25rem; border: 2px solid #e2e8f0; cursor: pointer;">
                                    <label class="form-check-label fw-bold ms-2" style="color: #34495e; cursor: pointer;">
                                        <i class="fas fa-money-bill-wave me-1"></i>تفعيل الدفع التلقائي لهذه الفاتورة
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card shadow-sm mb-4">
                        <div class="card-body p-4">
                            <div class="col-12">
                                <div class="form-group row mb-3">
                                    <div class="col-md-3">
                                        <span class="fw-bold">الطريقه :</span>
                                    </div>
                                    <div class="col-md-9">
                                        <select class="form-control form-control-lg shadow-sm" id="methodSelect"
                                            name="printing_method">
                                            @foreach ($clients as $client)
                                                @if ($client->printing_method == '1')
                                                    <option selected value="طبا��ة">طباعة</option>
                                                @else
                                                    <option value="ارسل عبر البريد">ارسل عبر البريد</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group row">
                                    <div class="col-md-3">
                                        <span class="fw-bold">العميل :</span>
                                    </div>
                                    <div class="col-md-6">
                                        <select class="form-control form-control-lg shadow-sm select2" id="clientSelect"
                                            name="client_id">
                                            <option value="">اختر العميل</option>
                                            @foreach ($clients as $client)
                                                <option value="{{ $client->id }}">
                                                    {{ $client->trade_name ?: $client->first_name . ' ' . $client->last_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <a href="{{ route('clients.create') }}"
                                            class="btn btn-primary waves-effect waves-light">
                                            <i class="fa fa-user-plus me-1"></i>جديد
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm">
                        <div class="card-body p-4">
                            <div class="row add_item">
                                <div class="col-12">
                                    <div class="form-group row mb-3">
                                        <div class="col-md-3">
                                            <span class="fw-bold">شروط الدفع:</span>
                                        </div>
                                        <div class="col-md-7">
                                            <input class="form-control form-control-lg shadow-sm" type="text"
                                                name="payment_terms" value="{{old('payment_terms', $periodicInvoice->payment_terms)}}">
                                        </div>
                                        <div class="col-md-2">
                                            <span class="form-control-plaintext">أيام</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <input class="form-control form-control-lg shadow-sm" type="text"
                                                placeholder="عنوان إضافي">
                                        </div>
                                        <div class="col-md-8">
                                            <div class="input-group">
                                                <input class="form-control form-control-lg shadow-sm" type="text"
                                                    placeholder="بيانات إضافية">
                                                <button type="button"
                                                    class="btn btn-success waves-effect waves-light addeventmore">
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

                <!-- Right side content -->

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
                                                value="1" min="1">
                                        </td>
                                        <td>
                                            <input type="number" name="items[0][unit_price]" class="form-control price"
                                                step="0.01">
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <input type="number" name="items[0][discount]"
                                                    class="form-control discount-value" value="0" min="0"
                                                    step="0.01">
                                                <select name="items[0][discount_type]" class="form-control discount-type">
                                                    <option value="amount">ريال</option>
                                                    <option value="percentage">نسبة %</option>
                                                </select>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="number" name="items[0][tax_1]" class="form-control tax"
                                                value="15" min="0" max="100" step="0.01">
                                        </td>
                                        <td>
                                            <input type="number" name="items[0][tax_2]" class="form-control tax"
                                                value="0" min="0" max="100" step="0.01">
                                        </td>
                                        <input type="hidden" name="items[0][store_house_id]" value="2">
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
                                <tfoot>
                                    <tr>
                                        <td colspan="9" class="text-right">
                                            <button type="button" id="add-row" class="btn btn-success">
                                                <i class="fa fa-plus"></i> إضافة صف
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="7" class="text-right">المجموع الفرعي</td>
                                        <td><span id="subtotal">0.00</span> ر.س</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="7" class="text-right">مجموع الخصومات</td>
                                        <td>
                                            <span id="total-discount">0.00</span>
                                            <span id="discount-type-label"></span>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="7" class="text-right">مجموع الضرائب</td>
                                        <td><span id="total-tax">0.00</span> ر.س</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="7" class="text-right">تكلفة الشحن</td>
                                        <td><span id="shipping-cost">0.00</span> ر.س</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="7" class="text-right">الدفعة القادمة</td>
                                        <td><span id="next-payment">0.00</span> ر.س</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="7" class="text-right">المجموع الكلي</td>
                                        <td><span id="grand-total">0.00</span> ر.س</td>
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
                    <ul class="nav nav-tabs card-header-tabs">
                        <li class="nav-item">
                            <a class="nav-link active" id="tab-discount" href="#">الخصم والتسوية</a>
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
                        </div>
                    </div>
                    <!-- القسم الثالث:      التوصيل -->
                    <div id="section-shipping" class="tab-section d-none">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">نوع الضريبة</label>
                                <select class="form-control" id="methodSelect" name="tax_type">
                                    <option value="1">القيمة المضافة (15%)</option>
                                    <option value="2">صفرية</option>
                                    <option value="3">معفاة</option>
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
            <div class="card">
                <div class="card-header bg-white">
                    <h6 class="mb-0">الملاحظات/الشروط</h6>
                </div>
                <div class="card-body">
                    <textarea id="tinyMCE" name="notes"></textarea>
                </div>
            </div>

        </form>
    </div>

@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>>
    <script src="{{ asset('assets/js/invoice.js') }}"></script>


@endsection
