@extends('master')

@section('title')
    انشاء عرض سعر مبيعات
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/purch.css') }}">
@endsection

@section('content')
    <div class="content-body">
        <form id="invoice-form" action="{{ route('questions.store') }}" method="post" onsubmit="return confirmSave(event)">
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
                                <button type="button" class="btn btn-outline-info btn-sm" onclick="saveAsDraft()" title="حفظ كمسودة">
                                    <i class="fa fa-save"></i> مسودة
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="copyLastQuote()" title="نسخ آخر عرض سعر">
                                    <i class="fa fa-copy"></i> نسخ
                                </button>
                                <button type="button" class="btn btn-outline-warning btn-sm" onclick="clearAllItems()" title="مسح الكل">
                                    <i class="fa fa-trash"></i> مسح
                                </button>
                                <button type="button" class="btn btn-outline-success btn-sm" onclick="showQuickPreview()" title="معاينة سريعة">
                                    <i class="fa fa-eye"></i> معاينة
                                </button>
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
            </div>

            <!-- صف بيانات العميل وعرض السعر -->
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
                                                <select class="form-control select2" id="clientSelect" name="client_id" required onchange="showClientBalance(this)">
                                                    <option value="">اختر العميل</option>
                                                    @foreach ($clients as $client)
                                                        <option value="{{ $client->id }}" data-balance="{{ $client->account->balance ?? 0 }}" data-name="{{ $client->trade_name }}">
                                                            {{ $client->trade_name }}-{{ $client->code ?? "" }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <a href="{{ route('clients.create') }}" type="button" class="btn btn-secondary mr-1 mb-1 waves-effect waves-light">
                                                    <i class="fa fa-user-plus"></i>جديد
                                                </a>
                                            </div>
                                        </div>

                                        <!-- كارد رصيد العميل -->
                                        <div class="row" id="clientBalanceCard" style="display: none;">
                                            <div class="col-12">
                                                <div class="card" style="background: #E8F5E8; border-radius: 8px; border: 1px solid #4CAF50;">
                                                    <div class="card-body p-4">
                                                        <div class="row align-items-center">
                                                            <div class="col-8">
                                                                <a href="#" class="text-decoration-none" style="color: inherit;">
                                                                    <h5 class="card-title mb-2" id="clientName" style="font-weight: 600; color: #333;">
                                                                        اسم العميل
                                                                    </h5>
                                                                    <p class="mb-0" style="color: #666; font-size: 0.9rem;">
                                                                        <i class="fas fa-edit ml-1" style="color: #4CAF50;"></i>
                                                                        <span>تعديل البيانات</span>
                                                                    </p>
                                                                </a>
                                                            </div>
                                                            <div class="col-4 text-left">
                                                                <div class="d-flex flex-column align-items-end">
                                                                    <span style="font-size: 1.8rem; font-weight: 700; color: #333;" id="clientBalance"></span>
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

                <!-- بيانات عرض السعر -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="row add_item">
                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-md-3">
                                                <span>رقم عرض السعر :</span>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" class="form-control" name="quote_number" value="{{ $quotes_number }}" readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-md-3">
                                                <span>تاريخ عرض السعر:</span>
                                            </div>
                                            <div class="col-md-8">
                                                <input class="form-control" type="date" name="quote_date" value="{{ old('quote_date', date('Y-m-d')) }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-md-3">
                                                <span>تاريخ الانتهاء:</span>
                                            </div>
                                            <div class="col-md-8">
                                                <input class="form-control" type="date" name="expiry_date" value="{{ old('expiry_date') }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-md-3">
                                                <span>شروط الدفع:</span>
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
                                        <td style="width:18%" data-label="المنتج">
                                            <select name="items[0][product_id]" class="form-control product-select" required>
                                                <option value="">اختر المنتج</option>
                                                @foreach ($items as $item)
                                                    <option value="{{ $item->id }}" data-price="{{ $item->price }}">{{ $item->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td data-label="الوصف">
                                            <input type="text" name="items[0][description]" class="form-control item-description" placeholder="أدخل الوصف">
                                        </td>
                                        <td data-label="الكمية">
                                            <input type="number" name="items[0][quantity]" class="form-control quantity" value="1" min="1" required>
                                        </td>
                                        <td data-label="السعر">
                                            <input type="number" name="items[0][unit_price]" class="form-control price" step="0.01" required placeholder="0.00">
                                        </td>
                                        <td data-label="الخصم">
                                            <div class="input-group">
                                                <input type="number" name="items[0][discount]" class="form-control discount-value" value="0" min="0" step="0.01">
                                                <select name="items[0][discount_type]" class="form-control discount-type">
                                                    <option value="amount">ريال</option>
                                                    <option value="percentage">نسبة %</option>
                                                </select>
                                            </div>
                                        </td>
                                        <td data-label="الضريبة 1">
                                            <div class="input-group">
                                                <select name="items[0][tax_1]" class="form-control tax-select" data-target="tax_1" onchange="updateHiddenInput(this)">
                                                    <option value="">لا يوجد</option>
                                                    @foreach ($taxs as $tax)
                                                        <option value="{{ $tax->tax }}" data-id="{{ $tax->id }}" data-name="{{ $tax->name }}" data-type="{{ $tax->type }}">
                                                            {{ $tax->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <input type="hidden" name="items[0][tax_1_id]">
                                            </div>
                                        </td>

                                        <td data-label="الضريبة 2">
                                            <div class="input-group">
                                                <select name="items[0][tax_2]" class="form-control tax-select" data-target="tax_2" onchange="updateHiddenInput(this)">
                                                    <option value="">لا يوجد</option>
                                                    @foreach ($taxs as $tax)
                                                        <option value="{{ $tax->tax }}" data-id="{{ $tax->id }}" data-name="{{ $tax->name }}" data-type="{{ $tax->type }}">
                                                            {{ $tax->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <input type="hidden" name="items[0][tax_2_id]">
                                            </div>
                                        </td>

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
                                        $currencySymbol = $currency == 'SAR' || empty($currency) ? '<img src="' . asset('assets/images/Saudi_Riyal.svg') . '" alt="ريال سعودي" width="13" style="display: inline-block; margin-left: 5px; vertical-align: middle;">' : $currency;
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

            <!-- كارد التفاصيل الإضافية -->
            <div class="card">
                <div class="card-header bg-white">
                    <ul class="nav nav-tabs card-header-tabs">
                        <li class="nav-item">
                            <a class="nav-link active" id="tab-discount" href="#">الخصم والتسوية</a>
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
                                    <input type="number" name="discount_amount" class="form-control" value="0" min="0" step="0.01">
                                    <select name="discount_type" class="form-control">
                                        <option value="amount">ريال</option>
                                        <option value="percentage">نسبة مئوية</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- القسم الثاني: التوصيل -->
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
                                <input type="number" class="form-control" name="shipping_cost" id="shipping" value="0" min="0" step="0.01">
                            </div>
                        </div>
                    </div>

                    <!-- القسم الثالث: إرفاق المستندات -->
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
                                        <input type="file" class="form-control" id="uploadFile" aria-describedby="uploadButton">
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
                                                    <option selected>Select Document</option>
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
        </form>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('assets/js/invoice.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('صفحة إنشاء عرض السعر جاهزة');

            // إعداد تبديل التبويبات
            setupTabs();

            // إعداد تبديل التبويبات الداخلية للمستندات
            setupDocumentTabs();

            // التحقق من العميل المحدد مسبقاً
            const clientSelect = document.getElementById('clientSelect');
            if (clientSelect && clientSelect.value) {
                showClientBalance(clientSelect);
            }
        });

        // دالة إعداد التبويبات
        function setupTabs() {
            document.querySelectorAll('[id^="tab-"]').forEach(tab => {
                tab.addEventListener('click', function(e) {
                    e.preventDefault();

                    // إزالة الكلاس النشط من جميع التبويبات
                    document.querySelectorAll('.card-header-tabs .nav-link').forEach(link => {
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

        // دالة إعداد تبويبات المستندات الداخلية
        function setupDocumentTabs() {
            const newDocTab = document.getElementById('tab-new-document');
            const uploadedDocTab = document.getElementById('tab-uploaded-documents');
            const newDocContent = document.getElementById('content-new-document');
            const uploadedDocContent = document.getElementById('content-uploaded-documents');

            if (newDocTab && uploadedDocTab) {
                newDocTab.addEventListener('click', function(e) {
                    e.preventDefault();
                    newDocTab.classList.add('active');
                    uploadedDocTab.classList.remove('active');
                    newDocContent.classList.remove('d-none');
                    uploadedDocContent.classList.add('d-none');
                });

                uploadedDocTab.addEventListener('click', function(e) {
                    e.preventDefault();
                    uploadedDocTab.classList.add('active');
                    newDocTab.classList.remove('active');
                    uploadedDocContent.classList.remove('d-none');
                    newDocContent.classList.add('d-none');
                });
            }
        }

        // دالة إظهار رصيد العميل
        window.showClientBalance = function(selectElement) {
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            const balance = parseFloat(selectedOption.dataset.balance) || 0;
            const clientName = selectedOption.text.split('-')[0].trim();

            const balanceCard = document.getElementById('clientBalanceCard');
            const clientNameElement = document.getElementById('clientName');
            const clientBalanceElement = document.getElementById('clientBalance');
            const balanceStatusElement = document.getElementById('balanceStatus');

            if (selectElement.value && balanceCard) {
                clientNameElement.textContent = clientName;
                clientBalanceElement.textContent = balance.toFixed(2);

                if (balance > 0) {
                    balanceStatusElement.textContent = 'رصيد دائن';
                    balanceStatusElement.className = 'text-success';
                } else if (balance < 0) {
                    balanceStatusElement.textContent = 'رصيد مدين';
                    balanceStatusElement.className = 'text-danger';
                } else {
                    balanceStatusElement.textContent = 'رصيد صفر';
                    balanceStatusElement.className = 'text-warning';
                }

                balanceCard.style.display = 'block';
            } else if (balanceCard) {
                balanceCard.style.display = 'none';
            }
        };

        // دالة تحديث الحقول المخفية للضرائب
        function updateHiddenInput(selectElement) {
            var row = selectElement.closest('.item-row');
            var taxType = selectElement.getAttribute('data-target');
            var hiddenInput = row.querySelector('input[name^="items"][name$="[' + taxType + '_id]"]');

            if (hiddenInput) {
                hiddenInput.value = selectElement.options[selectElement.selectedIndex].getAttribute('data-id');
            }
        }

        // دالة نسخ آخر عرض سعر
        function copyLastQuote() {
            Swal.fire({
                title: 'نسخ آخر عرض سعر',
                text: 'هل تريد نسخ بيانات آخر عرض سعر؟',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'نعم، انسخ',
                cancelButtonText: 'إلغاء',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33'
            }).then((result) => {
                if (result.isConfirmed) {
                    // يمكن إضافة كود AJAX هنا لجلب آخر عرض سعر
                    Swal.fire({
                        title: 'تم!',
                        text: 'تم نسخ البيانات بنجاح',
                        icon: 'success',
                        confirmButtonText: 'حسناً'
                    });
                }
            });
        }

        // دالة حفظ كمسودة
        function saveAsDraft() {
            Swal.fire({
                title: 'حفظ كمسودة',
                text: 'هل تريد حفظ عرض السعر كمسودة؟',
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'نعم، احفظ',
                cancelButtonText: 'إلغاء',
                confirmButtonColor: '#17a2b8',
                cancelButtonColor: '#6c757d'
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
                title: 'تأكيد المسح',
                text: 'هل أنت متأكد من مسح جميع البنود؟ هذا الإجراء لا يمكن التراجع عنه.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'نعم، امسح الكل',
                cancelButtonText: 'إلغاء',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    const tbody = document.querySelector('#items-table tbody');
                    const rows = tbody.querySelectorAll('.item-row');

                    // احتفظ بصف واحد فارغ
                    if (rows.length > 1) {
                        for (let i = 1; i < rows.length; i++) {
                            rows[i].remove();
                        }
                    }

                    // امسح محتويات الصف الأول
                    const firstRow = rows[0];
                    firstRow.querySelector('.product-select').value = '';
                    firstRow.querySelector('.item-description').value = '';
                    firstRow.querySelector('.quantity').value = '1';
                    firstRow.querySelector('.price').value = '';
                    firstRow.querySelector('.discount-value').value = '0';
                    firstRow.querySelector('.row-total').textContent = '0.00';

                    // إعادة حساب الإجماليات
                    if (typeof calculateTotals === 'function') {
                        calculateTotals();
                    }

                    Swal.fire({
                        title: 'تم المسح!',
                        text: 'تم مسح جميع البنود بنجاح',
                        icon: 'success',
                        confirmButtonText: 'حسناً'
                    });
                }
            });
        }

        // دالة معاينة سريعة
        function showQuickPreview() {
            Swal.fire({
                title: 'معاينة عرض السعر',
                html: '<p class="text-center">سيتم إضافة معاينة عرض السعر قريباً</p>',
                icon: 'info',
                confirmButtonText: 'حسناً',
                width: '600px'
            });
        }

        // دالة تأكيد الحفظ
        function confirmSave(event) {
            event.preventDefault();

            Swal.fire({
                title: 'تأكيد الحفظ',
                text: 'هل أنت متأكد من حفظ عرض السعر؟',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'نعم، احفظ',
                cancelButtonText: 'إلغاء',
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('invoice-form').submit();
                }
            });

            return false;
        }
    </script>
@endsection
