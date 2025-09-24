@extends('master')

@section('title')
أوامر التصنيع
@stop

@section('css')
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-5-theme/1.3.0/select2-bootstrap-5-theme.min.css" rel="stylesheet">

    <!-- SweetAlert2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.7.32/sweetalert2.min.css" rel="stylesheet">

    <style>
        .section-header {
            cursor: pointer;
            font-weight: bold;
            background: linear-gradient(135deg, #ddd6fe 0%, #e0e7ff 100%) !important;
            border-radius: 10px;
            padding: 15px !important;
            margin: 10px 0;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .section-header:hover {
            background: linear-gradient(135deg, #c4b5fd 0%, #ddd6fe 100%) !important;
            border-color: #8b5cf6;
            transform: translateX(5px);
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            margin-bottom: 25px;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }



        .btn {
            border-radius: 10px;
            padding: 12px 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }

        .table {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }

        .table thead th {
            background: linear-gradient(135deg, #ffffff 0%, #fefefe 100%);
            color: black;
            border: none;
            padding: 20px 15px;
            font-weight: 600;
        }

        .table tbody tr:hover {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            transform: scale(1.01);
            transition: all 0.3s ease;
        }

        .total-cost-box {
            background: linear-gradient(135deg, #00cec9 0%, #00b894 100%);
            border-radius: 15px;
            padding: 20px;
            color: white;
            box-shadow: 0 10px 30px rgba(0,203,201,0.3);
        }

        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .alert {
            border-radius: 15px;
            border: none;
            padding: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .date-input-group {
            position: relative;
        }

        .date-input-group  {
            padding-left: 45px;
        }

        .date-input-group .date-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #74b9ff;
            z-index: 3;
        }

        /* تخصيص Select2 */
        .select2-container--bootstrap-5 .select2-selection {
            border-radius: 10px !important;
            border: 2px solid #e9ecef !important;
            min-height: 48px !important;
        }

        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
            line-height: 28px !important;
            padding-left: 12px !important;
            padding-right: 20px !important;
        }

        .select2-container--bootstrap-5.select2-container--focus .select2-selection {
            border-color: #74b9ff !important;
            box-shadow: 0 0 20px rgba(116, 185, 255, 0.3) !important;
        }

        .select2-dropdown {
            border-radius: 10px !important;
            border: 2px solid #74b9ff !important;
        }

        .count-badge {
            background: #74b9ff;
            color: white;
            border-radius: 50%;
            padding: 2px 8px;
            font-size: 0.8rem;
            font-weight: bold;
            margin-right: 5px;
        }

        .section-collapsed {
            display: none;
        }

        .section-expanded {
            display: block;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
@endsection

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">
                        <i class="fas fa-cogs me-2"></i>أوامر التصنيع
                    </h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href=""><i class="fas fa-home me-2"></i>الرئيسيه</a>
                            </li>
                            <li class="breadcrumb-item active">اضافة</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">
        <div class="container-fluid">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li><i class="fas fa-exclamation-circle me-2"></i>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                </div>
            @endif

            <form class="form-horizontal" action="{{ route('manufacturing.orders.store') }}" method="POST" enctype="multipart/form-data" id="manufacturingOrderForm">
                @csrf

                <!-- Header Actions Card -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div>
                                <label class="text-info">
                                    <i class="fas fa-info-circle me-2"></i>الحقول التي عليها علامة
                                    <span style="color: red">*</span> الزامية
                                </label>
                            </div>
                            <div>
                                <button type="button" class="btn btn-outline-danger" onclick="cancelForm()">
                                    <i class="fa fa-ban me-2"></i>الغاء
                                </button>
                                <button type="submit" class="btn btn-outline-primary" id="submitBtn">
                                    <i class="fa fa-save me-2"></i>حفظ
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Basic Information Card -->
                <div class="card">
                    <div class="card-content">
                        <div class="card-body">
                            <h4 class="card-title">
                                <i class="fas fa-info-circle me-2"></i>معلومات أمر التصنيع
                            </h4>
                        </div>

                        <div class="card-body">
                            <div class="row g-4">
                                <div class="form-group col-md-4">
                                    <label for="name">الاسم <span style="color: red">*</span></label>
                                    <input type="text" class="form-control" name="name" id="name" value="{{ old('name') }}" required>
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="code">كود <span style="color: red">*</span></label>
                                    <input type="text" class="form-control" name="code" id="code" value="{{ $serial_number }}" required>
                                </div>

                                <div class="form-group col-md-2">
                                    <label for="from_date">التاريخ من<span style="color: red">*</span></label>
                                    <div class="date-input-group">
                                        <input type="date" class="form-control" name="from_date" id="from_date" value="{{ old('from_date') }}" required>
                                        <i class="fas fa-calendar-alt date-icon"></i>
                                    </div>
                                </div>

                                <div class="form-group col-md-2">
                                    <label for="to_date">التاريخ الى<span style="color: red">*</span></label>
                                    <div class="date-input-group">
                                        <input type="date" class="form-control" name="to_date" id="to_date" value="{{ old('to_date') }}" required>
                                        <i class="fas fa-calendar-alt date-icon"></i>
                                    </div>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="account_id">الحساب <span class="text-danger">*</span></label>
                                    <select class="form-control select2" name="account_id" id="account_id" required>
                                        <option value="" disabled selected>-- اختر الحساب --</option>
                                        @foreach ($accounts as $account)
                                            <option value="{{ $account->id }}" {{ old('account_id') == $account->id ? 'selected' : '' }}>{{ $account->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="employee_id">الموظفين <span class="text-danger">*</span></label>
                                    <select class="form-control select2" name="employee_id" id="employee_id" required>
                                        <option value="" disabled selected>-- اختر الموظف --</option>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>{{ $employee->full_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="client_id">العميل <span class="text-danger">*</span></label>
                                    <select class="form-control select2" name="client_id" id="client_id" required>
                                        <option value="" disabled selected>-- اختر العميل --</option>
                                        @foreach ($clients as $client)
                                            <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>{{ $client->trade_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="product_id">المنتجات <span class="text-danger">*</span></label>
                                    <select class="form-control select2" name="product_id" id="product_id" required>
                                        <option value="" disabled selected>-- اختر المنتج --</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="quantity">الكمية المطلوبة <span style="color: red">*</span></label>
                                    <input type="number" class="form-control" name="quantity" id="quantity" value="{{ old('quantity') }}" min="1" required>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="production_material_id">قائمة مواد الانتاج <span class="text-danger">*</span></label>
                                    <select class="form-control select2" name="production_material_id" id="production_material_id" required>
                                        <option value="" disabled selected>-- اختر قائمة مواد الانتاج --</option>
                                        @foreach ($production_materials as $material)
                                            <option value="{{ $material->id }}" {{ old('production_material_id') == $material->id ? 'selected' : '' }}>{{ $material->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="production_path_id">مسار الانتاج <span class="text-danger">*</span></label>
                                    <select class="form-control select2" name="production_path_id" id="production_path_id" required>
                                        <option value="" disabled selected>-- اختر مسار الانتاج --</option>
                                        @foreach ($paths as $path)
                                            <option value="{{ $path->id }}" {{ old('production_path_id') == $path->id ? 'selected' : '' }}>{{ $path->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Raw Materials Section -->
                                <div class="form-group col-md-12">
                                    <div onclick="toggleSection('rawMaterials')" class="section-header d-flex justify-content-between">
                                        <span>
                                            <i class="feather icon-package me-2"></i> المواد الخام
                                            <span class="count-badge" id="rawMaterialCount">0</span>
                                        </span>
                                        <i class="feather icon-plus-circle" id="rawMaterialsIcon"></i>
                                    </div>
                                    <div id="rawMaterials" class="section-collapsed">
                                        <table class="table table-striped" id="itemsTable">
                                            <thead style="background: #f8f8f8">
                                                <tr>
                                                    <th>المنتجات</th>
                                                    <th>سعر الوحدة</th>
                                                    <th>الكمية</th>
                                                    <th>المرحلة الإنتاجية</th>
                                                    <th>الإجمالي</th>
                                                    <th width="100">العمليات</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                        <hr>
                                        <div class="d-flex justify-content-between mt-2">
                                            <button type="button" class="btn btn-outline-success btn-sm" id="addRow">
                                                <i class="fa fa-plus me-2"></i> إضافة
                                            </button>
                                            <strong style="margin-left: 13rem;">
                                                <small class="text-muted">الإجمالي الكلي : </small>
                                                <span class="grand-total">0.00</span>
                                            </strong>
                                        </div>
                                    </div>
                                </div>

                                <br><hr>

                                <!-- Expenses Section -->
                                <div class="form-group col-md-12">
                                    <div onclick="toggleSection('expenses')" class="section-header d-flex justify-content-between">
                                        <span>
                                            <i class="fa fa-money me-2"></i> المصروفات
                                            <span class="count-badge" id="rowExpensesCount">0</span>
                                        </span>
                                        <i class="feather icon-plus-circle" id="expensesIcon"></i>
                                    </div>
                                    <div id="expenses" class="section-collapsed">
                                        <table class="table table-striped" id="ExpensesTable">
                                            <thead style="background: #f8f8f8">
                                                <tr>
                                                    <th>الحساب</th>
                                                    <th>نوع التكلفة</th>
                                                    <th>المبلغ</th>
                                                    <th>الوصف</th>
                                                    <th>المرحلة الإنتاجية</th>
                                                    <th>الإجمالي</th>
                                                    <th width="100">العمليات</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                        <hr>
                                        <div class="d-flex justify-content-between mt-2">
                                            <button type="button" class="btn btn-outline-success btn-sm" id="ExpensesAddRow">
                                                <i class="fa fa-plus me-2"></i> إضافة
                                            </button>
                                            <strong style="margin-left: 13rem;">
                                                <small class="text-muted">الإجمالي الكلي : </small>
                                                <span class="expenses-grand-total">0.00</span>
                                            </strong>
                                        </div>
                                    </div>
                                </div>

                                <br><hr>

                                <!-- Manufacturing Section -->
                                <div class="form-group col-md-12">
                                    <div onclick="toggleSection('manufacturing')" class="section-header d-flex justify-content-between">
                                        <span>
                                            <i class="feather icon-settings me-2"></i> عمليات التصنيع
                                            <span class="count-badge" id="manufacturingCount">0</span>
                                        </span>
                                        <i class="feather icon-plus-circle" id="manufacturingIcon"></i>
                                    </div>
                                    <div id="manufacturing" class="section-collapsed">
                                        <table class="table table-striped" id="manufacturingTable">
                                            <thead style="background: #f8f8f8">
                                                <tr>
                                                    <th>محطة العمل</th>
                                                    <th>نوع التكلفة</th>
                                                    <th>وقت التشغيل</th>
                                                    <th>التكلفة</th>
                                                    <th>الوصف</th>
                                                    <th>المرحلة الإنتاجية</th>
                                                    <th>الإجمالي</th>
                                                    <th width="100">العمليات</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                        <hr>
                                        <div class="d-flex justify-content-between mt-2">
                                            <button type="button" class="btn btn-outline-success btn-sm" id="manufacturingAddRow">
                                                <i class="fa fa-plus me-2"></i> إضافة
                                            </button>
                                            <strong style="margin-left: 13rem;">
                                                <small class="text-muted">الإجمالي الكلي : </small>
                                                <span class="manufacturing-grand-total">0.00</span>
                                            </strong>
                                        </div>
                                    </div>
                                </div>

                                <br><hr>

                                <!-- End Life Materials Section -->
                                <div class="form-group col-md-12">
                                    <div onclick="toggleSection('endLife')" class="section-header d-flex justify-content-between">
                                        <span>
                                            <i class="feather icon-trash-2 me-2"></i> المواد الهالكة
                                            <span class="count-badge" id="EndLifeCount">0</span>
                                        </span>
                                        <i class="feather icon-plus-circle" id="endLifeIcon"></i>
                                    </div>
                                    <div id="endLife" class="section-collapsed">
                                        <table class="table table-striped" id="EndLifeTable">
                                            <thead style="background: #f8f8f8">
                                                <tr>
                                                    <th>المنتجات</th>
                                                    <th>السعر</th>
                                                    <th>الكمية</th>
                                                    <th>المرحلة الإنتاجية</th>
                                                    <th>الإجمالي</th>
                                                    <th width="100">العمليات</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                        <hr>
                                        <div class="d-flex justify-content-between mt-2">
                                            <button type="button" class="btn btn-outline-success btn-sm" id="EndLifeAddRow">
                                                <i class="fa fa-plus me-2"></i> إضافة
                                            </button>
                                            <strong style="margin-left: 13rem;">
                                                <small class="text-muted">الإجمالي الكلي : </small>
                                                <span class="end-life-grand-total">0.00</span>
                                            </strong>
                                        </div>
                                    </div>
                                </div>

                                <br><hr>

                                <!-- Total Cost Summary -->
                                <div class="form-group col-md-6"></div>
                                <div class="form-group col-md-6">
                                    <div class="total-cost-box text-center">
                                        <strong>
                                            <i class="fas fa-calculator me-2"></i>
                                            إجمالي التكلفة : <span class="total-cost">0.00</span> ر.س
                                        </strong>
                                        <input type="hidden" name="last_total_cost" id="last_total_cost">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection

@section('scripts')
    <!-- Select2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <!-- SweetAlert2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.7.32/sweetalert2.all.min.js"></script>

    <script>
        $(document).ready(function() {
            // تهيئة Select2
            $('.select2').select2({
                theme: 'bootstrap-5',
                dir: 'rtl',
                placeholder: 'اختر من القائمة...',
                allowClear: true
            });

            // إعداد النموذج
            initializeForm();
        });

        // تهيئة النموذج
        function initializeForm() {
            // إضافة أول صف للمواد الخام
            addInitialRawMaterialRow();

            // عرض رسالة ترحيب
            showWelcomeMessage();
        }

        // عرض رسالة ترحيب
        function showWelcomeMessage() {
            @if(session('success'))
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: '{{ session('error') }}',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            @endif
        }

        // التعامل مع إرسال النموذج
        document.getElementById('manufacturingOrderForm').addEventListener('submit', function(e) {
            e.preventDefault();

            if (!validateManufacturingOrderForm()) {
                return;
            }

            Swal.fire({
                title: 'تأكيد الحفظ',
                text: 'هل أنت متأكد من حفظ أمر التصنيع؟',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'نعم، احفظ',
                cancelButtonText: 'إلغاء',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    submitForm();
                }
            });
        });

        // إرسال النموذج
        function submitForm() {
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<div class="loading-spinner me-2"></div> جاري الحفظ...';

            Swal.fire({
                title: 'جاري الحفظ...',
                text: 'الرجاء الانتظار',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            // إرسال النموذج
            document.getElementById('manufacturingOrderForm').submit();
        }

        // التحقق من صحة النموذج
        function validateManufacturingOrderForm() {
            const errors = [];

            // التحقق من الحقول المطلوبة
            const name = document.querySelector('input[name="name"]').value.trim();
            const code = document.querySelector('input[name="code"]').value.trim();
            const fromDate = document.querySelector('input[name="from_date"]').value;
            const toDate = document.querySelector('input[name="to_date"]').value;
            const accountId = document.querySelector('select[name="account_id"]').value;
            const employeeId = document.querySelector('select[name="employee_id"]').value;
            const clientId = document.querySelector('select[name="client_id"]').value;
            const productId = document.querySelector('select[name="product_id"]').value;
            const quantity = document.querySelector('input[name="quantity"]').value;
            const productionMaterialId = document.querySelector('select[name="production_material_id"]').value;
            const productionPathId = document.querySelector('select[name="production_path_id"]').value;

            if (!name) errors.push('اسم أمر التصنيع مطلوب');
            if (!code) errors.push('كود أمر التصنيع مطلوب');
            if (!fromDate) errors.push('التاريخ من مطلوب');
            if (!toDate) errors.push('التاريخ إلى مطلوب');
            if (!accountId) errors.push('يجب اختيار الحساب');
            if (!employeeId) errors.push('يجب اختيار الموظف');
            if (!clientId) errors.push('يجب اختيار العميل');
            if (!productId) errors.push('يجب اختيار المنتج');
            if (!quantity || quantity <= 0) errors.push('الكمية المطلوبة مطلوبة ويجب أن تكون أكبر من صفر');
            if (!productionMaterialId) errors.push('يجب اختيار قائمة مواد الإنتاج');
            if (!productionPathId) errors.push('يجب اختيار مسار الإنتاج');

            // التحقق من التواريخ
            if (fromDate && toDate && new Date(fromDate) > new Date(toDate)) {
                errors.push('تاريخ البداية يجب أن يكون قبل تاريخ النهاية');
            }

            // التحقق من وجود مواد خام
            const rawMaterialRows = document.querySelectorAll('#itemsTable tbody tr').length;
            if (rawMaterialRows === 0) {
                errors.push('يجب إضافة مادة خام واحدة على الأقل');
            }

            if (errors.length > 0) {
                showValidationErrors(errors);
                return false;
            }

            return true;
        }

        // عرض أخطاء التحقق
        function showValidationErrors(errors) {
            const errorHtml = errors.map(error =>
                `<li style="color: #dc3545; margin: 8px 0; padding: 10px; background: #f8d7da; border-radius: 8px; border-right: 4px solid #dc3545;">
                    <i class="fa fa-exclamation-triangle me-2"></i> ${error}
                </li>`
            ).join('');

            Swal.fire({
                title: 'خطأ في البيانات',
                html: `
                    <div style="text-align: right; max-height: 300px; overflow-y: auto;">
                        <ul style="list-style: none; padding: 0;">
                            ${errorHtml}
                        </ul>
                    </div>
                `,
                icon: 'error',
                confirmButtonText: 'حسناً',
                confirmButtonColor: '#d33',
                customClass: {
                    htmlContainer: 'text-right'
                }
            });
        }

        // إلغاء النموذج
        function cancelForm() {
            Swal.fire({
                title: 'تأكيد الإلغاء',
                text: 'هل أنت متأكد من إلغاء العملية؟ سيتم فقدان جميع البيانات المدخلة.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'نعم، إلغاء',
                cancelButtonText: 'العودة للنموذج',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '{{ route("manufacturing.orders.index") }}';
                }
            });
        }

        // تبديل عرض الأقسام
        function toggleSection(sectionId) {
            const section = document.getElementById(sectionId);
            const icon = document.getElementById(sectionId + 'Icon');

            if (section.classList.contains('section-collapsed')) {
                section.classList.remove('section-collapsed');
                section.classList.add('section-expanded');
                icon.classList.remove('icon-plus-circle');
                icon.classList.add('icon-minus-circle');
            } else {
                section.classList.remove('section-expanded');
                section.classList.add('section-collapsed');
                icon.classList.remove('icon-minus-circle');
                icon.classList.add('icon-plus-circle');
            }
        }

        // إضافة الصف الأولي للمواد الخام
        function addInitialRawMaterialRow() {
            const tbody = document.querySelector('#itemsTable tbody');
            if (tbody.children.length === 0) {
                addRawMaterialRow();
            }
        }

        // تحديث العدادات
        function updateRawMaterialCount() {
            const rowCount = document.querySelectorAll('#itemsTable tbody tr').length;
            document.getElementById('rawMaterialCount').textContent = rowCount;
        }

        function updateRawExpensesCount() {
            const rowCount = document.querySelectorAll('#ExpensesTable tbody tr').length;
            document.getElementById('rowExpensesCount').textContent = rowCount;
        }

        function updateManufacturingCount() {
            const rowCount = document.querySelectorAll('#manufacturingTable tbody tr').length;
            document.getElementById('manufacturingCount').textContent = rowCount;
        }

        function updateEndLifeCount() {
            const rowCount = document.querySelectorAll('#EndLifeTable tbody tr').length;
            document.getElementById('EndLifeCount').textContent = rowCount;
        }

        // حساب التكلفة الإجمالية
        function updateTotalCost() {
            let totalCost = 0;

            // إضافة إجمالي المواد الخام
            const rawMaterialsTotal = parseFloat(document.querySelector('.grand-total').textContent) || 0;
            totalCost += rawMaterialsTotal;

            // إضافة إجمالي المصروفات
            const expensesTotal = parseFloat(document.querySelector('.expenses-grand-total').textContent) || 0;
            totalCost += expensesTotal;

            // إضافة إجمالي التصنيع
            const manufacturingTotal = parseFloat(document.querySelector('.manufacturing-grand-total').textContent) || 0;
            totalCost += manufacturingTotal;

            // طرح إجمالي المواد الهالكة
            const endLifeTotal = parseFloat(document.querySelector('.end-life-grand-total').textContent) || 0;
            totalCost -= endLifeTotal;

            // تحديث عرض التكلفة الإجمالية
            document.querySelector('.total-cost').textContent = totalCost.toFixed(2);
            document.getElementById('last_total_cost').value = totalCost.toFixed(2);
        }
    </script>

    <!-- Raw Materials Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const itemsTable = document.getElementById('itemsTable').querySelector('tbody');
            const addRowButton = document.getElementById('addRow');

            // Function to calculate total for a row
            function calculateTotal(row) {
                const unitPrice = parseFloat(row.querySelector('.unit-price').value) || 0;
                const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
                const total = unitPrice * quantity;
                row.querySelector('.total').value = total.toFixed(2);
                updateGrandTotal();
                updateTotalCost();
            }

            // Function to update grand total
            function updateGrandTotal() {
                let grandTotal = 0;
                document.querySelectorAll('.total').forEach(totalInput => {
                    grandTotal += parseFloat(totalInput.value) || 0;
                });
                document.querySelector('.grand-total').textContent = grandTotal.toFixed(2);
            }

            // Function to attach event listeners to a row
            function attachRowEvents(row) {
                const productSelect = row.querySelector('.product-select');
                const quantityInput = row.querySelector('.quantity');

                if (productSelect) {
                    productSelect.addEventListener('change', function () {
                        const selectedOption = productSelect.options[productSelect.selectedIndex];
                        const unitPrice = selectedOption.getAttribute('data-price');
                        row.querySelector('.unit-price').value = unitPrice || 0;
                        calculateTotal(row);
                    });
                }

                if (quantityInput) {
                    quantityInput.addEventListener('input', function () {
                        calculateTotal(row);
                    });
                }
            }

            // Add Row function
            function addRawMaterialRow() {
                const newRow = document.createElement('tr');
                newRow.innerHTML = `
                    <td>
                        <select name="raw_product_id[]" class="form-control select2 product-select" required>
                            <option value="" disabled selected>-- اختر البند --</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" data-price="{{ $product->sale_price }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="number" name="raw_unit_price[]" class="form-control unit-price" ></td>
                    <td><input type="number" name="raw_quantity[]" class="form-control quantity" value="1" min="1" required></td>
                    <td>
                        <select name="raw_production_stage_id[]" class="form-control select2" required>
                            @foreach ($stages as $stage)
                                <option value="{{ $stage->id }}">{{ $stage->stage_name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="number" name="raw_total[]" class="form-control total" ></td>
                    <td>
                        <button type="button" class="btn btn-outline-danger btn-sm removeRow">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                `;

                itemsTable.appendChild(newRow);

                // تهيئة Select2 للصف الجديد
                $(newRow).find('.select2').select2({
                    theme: 'bootstrap-5',
                    dir: 'rtl'
                });

                attachRowEvents(newRow);
                updateRawMaterialCount();
                updateTotalCost();

                // عرض رسالة نجاح
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'تم إضافة مادة خام جديدة',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });
            }

            // Add Row with SweetAlert confirmation
            addRowButton.addEventListener('click', function (e) {
                e.preventDefault();
                addRawMaterialRow();
            });

            // تعيين دالة addRawMaterialRow لتكون متاحة عالمياً
            window.addRawMaterialRow = addRawMaterialRow;

            // Remove Row with confirmation
            itemsTable.addEventListener('click', function (e) {
                if (e.target.classList.contains('removeRow') || e.target.closest('.removeRow')) {
                    const row = e.target.closest('tr');

                    if (itemsTable.rows.length > 1) {
                        Swal.fire({
                            title: 'تأكيد الحذف',
                            text: 'هل أنت متأكد من حذف هذه المادة الخام؟',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'نعم، احذف',
                            cancelButtonText: 'إلغاء',
                            reverseButtons: true
                        }).then((result) => {
                            if (result.isConfirmed) {
                                row.remove();
                                updateGrandTotal();
                                updateRawMaterialCount();
                                updateTotalCost();

                                Swal.fire({
                                    toast: true,
                                    position: 'top-end',
                                    icon: 'success',
                                    title: 'تم حذف المادة الخام',
                                    showConfirmButton: false,
                                    timer: 2000
                                });
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'تحذير',
                            text: 'لا يمكنك حذف جميع الصفوف! يجب وجود مادة خام واحدة على الأقل.',
                            confirmButtonText: 'حسناً',
                            confirmButtonColor: '#3085d6'
                        });
                    }
                }
            });
        });
    </script>

    <!-- Expenses Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ExpensesTable = document.getElementById('ExpensesTable').querySelector('tbody');
            const ExpensesAddRowButton = document.getElementById('ExpensesAddRow');

            // Function to calculate total for a row
            function calculateExpensesTotal(row) {
                const expensesPrice = parseFloat(row.querySelector('.expenses-price').value) || 0;
                row.querySelector('.expenses-total').value = expensesPrice.toFixed(2);
                updateExpensesGrandTotal();
                updateTotalCost();
            }

            // Function to update grand total
            function updateExpensesGrandTotal() {
                let expensesGrandTotal = 0;
                document.querySelectorAll('.expenses-total').forEach(totalInput => {
                    expensesGrandTotal += parseFloat(totalInput.value) || 0;
                });
                document.querySelector('.expenses-grand-total').textContent = expensesGrandTotal.toFixed(2);
            }

            // Attach events to a row
            function attachExpensesRowEvents(row) {
                const priceInput = row.querySelector('.expenses-price');
                if (priceInput) {
                    priceInput.addEventListener('input', function () {
                        calculateExpensesTotal(row);
                    });
                }
            }

            // Add Row
            ExpensesAddRowButton.addEventListener('click', function (e) {
                e.preventDefault();

                const exNewRow = document.createElement('tr');
                exNewRow.innerHTML = `
                    <td>
                        <select name="expenses_account_id[]" class="form-control select2" required>
                            <option value="" disabled selected>-- اختر الحساب --</option>
                            @foreach ($accounts as $account)
                                <option value="{{ $account->id }}">{{ $account->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <select name="expenses_cost_type[]" class="form-control select2">
                            <option value="1">مبلغ ثابت</option>
                            <option value="2">بناءً على الكمية</option>
                            <option value="3">معادلة</option>
                        </select>
                    </td>
                    <td><input type="number" name="expenses_price[]" class="form-control expenses-price" required></td>
                    <td><textarea name="expenses_description[]" class="form-control" rows="2"></textarea></td>
                    <td>
                        <select name="expenses_production_stage_id[]" class="form-control select2" required>
                            @foreach ($stages as $stage)
                                <option value="{{ $stage->id }}">{{ $stage->stage_name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="number" name="expenses_total[]" class="form-control expenses-total" ></td>
                    <td>
                        <button type="button" class="btn btn-outline-danger btn-sm removeRow">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                `;

                ExpensesTable.appendChild(exNewRow);

                // تهيئة Select2 للصف الجديد
                $(exNewRow).find('.select2').select2({
                    theme: 'bootstrap-5',
                    dir: 'rtl'
                });

                attachExpensesRowEvents(exNewRow);
                updateRawExpensesCount();
                updateTotalCost();

                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'تم إضافة مصروف جديد',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });
            });

            // Remove Row
            ExpensesTable.addEventListener('click', function (e) {
                if (e.target.classList.contains('removeRow') || e.target.closest('.removeRow')) {
                    const row = e.target.closest('tr');

                    Swal.fire({
                        title: 'تأكيد الحذف',
                        text: 'هل أنت متأكد من حذف هذا المصروف؟',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'نعم، احذف',
                        cancelButtonText: 'إلغاء',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            row.remove();
                            updateExpensesGrandTotal();
                            updateRawExpensesCount();
                            updateTotalCost();

                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: 'تم حذف المصروف',
                                showConfirmButton: false,
                                timer: 2000
                            });
                        }
                    });
                }
            });
        });
    </script>

    <!-- Manufacturing Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const manufacturingTable = document.getElementById('manufacturingTable').querySelector('tbody');
            const manufacturingAddRowButton = document.getElementById('manufacturingAddRow');

            // Function to calculate total for a row
            function calculateManufacturingTotal(row) {
                const totalCost = parseFloat(row.querySelector('.total_cost').value) || 0;
                const operatingTime = parseFloat(row.querySelector('.operating_time').value) || 0;
                const manufacturingTotal = totalCost * operatingTime;

                row.querySelector('.manufacturing-total').value = manufacturingTotal.toFixed(2);
                updateManufacturingGrandTotal();
                updateTotalCost();
            }

            // Function to update grand total
            function updateManufacturingGrandTotal() {
                let manufacturingGrandTotal = 0;
                document.querySelectorAll('.manufacturing-total').forEach(totalInput => {
                    manufacturingGrandTotal += parseFloat(totalInput.value) || 0;
                });
                document.querySelector('.manufacturing-grand-total').textContent = manufacturingGrandTotal.toFixed(2);
            }

            // Function to fetch total_cost from the server
            function fetchTotalCost(workstationId, row) {
                if (!workstationId) return;

                fetch(`/api/workstations/${workstationId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.total_cost !== undefined) {
                            row.querySelector('.total_cost').value = data.total_cost;
                            calculateManufacturingTotal(row);
                        } else {
                            console.error("total_cost غير موجود في الاستجابة:", data);
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching total cost:', error);
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: 'خطأ في جلب بيانات محطة العمل',
                            showConfirmButton: false,
                            timer: 3000
                        });
                    });
            }

            // Attach events to a row
            function attachManufacturingRowEvents(row) {
                const totalCostInput = row.querySelector('.total_cost');
                const operatingTimeInput = row.querySelector('.operating_time');
                const workstationSelect = row.querySelector('select[name="workstation_id[]"]');

                if (totalCostInput) {
                    totalCostInput.addEventListener('input', function () {
                        calculateManufacturingTotal(row);
                    });
                }

                if (operatingTimeInput) {
                    operatingTimeInput.addEventListener('input', function () {
                        calculateManufacturingTotal(row);
                    });
                }

                if (workstationSelect) {
                    workstationSelect.addEventListener('change', function () {
                        const workstationId = this.value;
                        fetchTotalCost(workstationId, row);
                    });
                }
            }

            // Add Row
            manufacturingAddRowButton.addEventListener('click', function (e) {
                e.preventDefault();

                const newRow = document.createElement('tr');
                newRow.innerHTML = `
                    <td>
                        <select name="workstation_id[]" class="form-control select2" required>
                            <option value="" disabled selected>-- اختر محطة العمل --</option>
                            @foreach ($workstations as $workstation)
                                <option value="{{ $workstation->id }}">{{ $workstation->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <select name="manu_cost_type[]" class="form-control select2">
                            <option value="1">مبلغ ثابت</option>
                            <option value="2">بناءً على الكمية</option>
                            <option value="3">معادلة</option>
                        </select>
                    </td>
                    <td><input type="number" name="operating_time[]" class="form-control operating_time" required></td>
                    <td><input type="number" name="manu_total_cost[]" class="form-control total_cost"></td>
                    <td><textarea name="manu_description[]" class="form-control" rows="2"></textarea></td>
                    <td>
                        <select name="manu_production_stage_id[]" class="form-control select2" required>
                            @foreach ($stages as $stage)
                                <option value="{{ $stage->id }}">{{ $stage->stage_name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="number" name="manu_total[]" class="form-control manufacturing-total" ></td>
                    <td>
                        <button type="button" class="btn btn-outline-danger btn-sm removeRow">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                `;

                manufacturingTable.appendChild(newRow);

                // تهيئة Select2 للصف الجديد
                $(newRow).find('.select2').select2({
                    theme: 'bootstrap-5',
                    dir: 'rtl'
                });

                attachManufacturingRowEvents(newRow);
                updateManufacturingCount();
                updateTotalCost();

                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'تم إضافة عملية تصنيع جديدة',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });
            });

            // Remove Row
            manufacturingTable.addEventListener('click', function (e) {
                if (e.target.classList.contains('removeRow') || e.target.closest('.removeRow')) {
                    const row = e.target.closest('tr');

                    Swal.fire({
                        title: 'تأكيد الحذف',
                        text: 'هل أنت متأكد من حذف عملية التصنيع هذه؟',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'نعم، احذف',
                        cancelButtonText: 'إلغاء',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            row.remove();
                            updateManufacturingGrandTotal();
                            updateManufacturingCount();
                            updateTotalCost();

                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: 'تم حذف عملية التصنيع',
                                showConfirmButton: false,
                                timer: 2000
                            });
                        }
                    });
                }
            });
        });
    </script>

    <!-- End Life Materials Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const EndLifeTable = document.getElementById('EndLifeTable').querySelector('tbody');
            const EndLifeAddRowButton = document.getElementById('EndLifeAddRow');

            // Function to calculate total for a row
            function calculateEndLifeTotal(row) {
                const EndLifeUnitPrice = parseFloat(row.querySelector('.end-life-unit-price').value) || 0;
                const EndLifeQuantity = parseFloat(row.querySelector('.end-life-quantity').value) || 0;
                const total = EndLifeUnitPrice * EndLifeQuantity;
                row.querySelector('.end-life-total').value = total.toFixed(2);
                updateEndLifeGrandTotal();
                updateTotalCost();
            }

            // Function to update grand total
            function updateEndLifeGrandTotal() {
                let grandTotal = 0;
                document.querySelectorAll('.end-life-total').forEach(totalInput => {
                    grandTotal += parseFloat(totalInput.value) || 0;
                });
                document.querySelector('.end-life-grand-total').textContent = grandTotal.toFixed(2);
            }

            // Function to attach event listeners to a row
            function attachEndLifeRowEvents(row) {
                const productSelect = row.querySelector('.end-life-product-select');
                const quantityInput = row.querySelector('.end-life-quantity');

                if (productSelect) {
                    productSelect.addEventListener('change', function () {
                        const selectedOption = productSelect.options[productSelect.selectedIndex];
                        const unitPrice = selectedOption.getAttribute('data-price');
                        row.querySelector('.end-life-unit-price').value = unitPrice || 0;
                        calculateEndLifeTotal(row);
                    });
                }

                if (quantityInput) {
                    quantityInput.addEventListener('input', function () {
                        calculateEndLifeTotal(row);
                    });
                }
            }

            // Add Row
            EndLifeAddRowButton.addEventListener('click', function (e) {
                e.preventDefault();

                const newRow = document.createElement('tr');
                newRow.innerHTML = `
                    <td>
                        <select name="end_life_product_id[]" class="form-control select2 end-life-product-select" required>
                            <option value="" disabled selected>-- اختر البند --</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" data-price="{{ $product->sale_price }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="number" name="end_life_unit_price[]" class="form-control end-life-unit-price"></td>
                    <td><input type="number" name="end_life_quantity[]" class="form-control end-life-quantity" value="1" min="1" required></td>
                    <td>
                        <select name="end_life_production_stage_id[]" class="form-control select2" required>
                            @foreach ($stages as $stage)
                                <option value="{{ $stage->id }}">{{ $stage->stage_name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="number" name="end_life_total[]" class="form-control end-life-total" ></td>
                    <td>
                        <button type="button" class="btn btn-outline-danger btn-sm removeRow">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                `;

                EndLifeTable.appendChild(newRow);

                // تهيئة Select2 للصف الجديد
                $(newRow).find('.select2').select2({
                    theme: 'bootstrap-5',
                    dir: 'rtl'
                });

                attachEndLifeRowEvents(newRow);
                updateEndLifeCount();
                updateTotalCost();

                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'تم إضافة مادة هالكة جديدة',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });
            });

            // Remove Row
            EndLifeTable.addEventListener('click', function (e) {
                if (e.target.classList.contains('removeRow') || e.target.closest('.removeRow')) {
                    const row = e.target.closest('tr');

                    Swal.fire({
                        title: 'تأكيد الحذف',
                        text: 'هل أنت متأكد من حذف هذه المادة الهالكة؟',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'نعم، احذف',
                        cancelButtonText: 'إلغاء',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            row.remove();
                            updateEndLifeGrandTotal();
                            updateEndLifeCount();
                            updateTotalCost();

                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: 'تم حذف المادة الهالكة',
                                showConfirmButton: false,
                                timer: 2000
                            });
                        }
                    });
                }
            });
        });
    </script>

@endsection