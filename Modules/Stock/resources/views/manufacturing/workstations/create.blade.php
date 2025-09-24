@extends('master')

@section('title')
محطة العمل
@stop

@section('css')
    <style>
        .section-header {
            cursor: pointer;
            font-weight: bold;
        }

        .is-valid {
            border-color: #28a745 !important;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25) !important;
        }

        .is-invalid {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
        }

        .invalid-feedback {
            display: block;
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            font-weight: 500;
        }

        .field-success {
            border-left: 4px solid #28a745;
            background-color: #f8fff9;
        }

        .field-error {
            border-left: 4px solid #dc3545;
            background-color: #fff8f8;
        }

        .total-cost-container {
            transition: all 0.3s ease;
        }

        .loading-spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #3498db;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
            display: inline-block;
            margin-left: 10px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
@endsection

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">محطة العمل</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a></li>
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

            <form class="form-horizontal" action="{{ route('manufacturing.workstations.store') }}" method="POST" id="workstationForm">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div>
                                <label>الحقول التي عليها علامة <span style="color: red">*</span> الزامية</label>
                            </div>

                            <div>
                                <a href="{{ route('manufacturing.workstations.index') }}" class="btn btn-outline-danger" id="cancelBtn">
                                    <i class="fa fa-ban"></i> الغاء
                                </a>
                                <button type="submit" class="btn btn-outline-primary" id="submitBtn">
                                    <i class="fa fa-save"></i> حفظ
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-content">
                        <div class="card-body">
                            <h4 class="card-title">معلومات محطة العمل</h4>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="">الاسم <span style="color: red">*</span></label>
                                    <input type="text" class="form-control" name="name" value="{{ old('name') }}" placeholder="أدخل اسم محطة العمل">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="">كود <span style="color: red">*</span></label>
                                    <input type="text" class="form-control" name="code" value="{{ $serial_number }}" placeholder="كود محطة العمل">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="">الوحدة</label>
                                    <input type="text" class="form-control" name="unit" value="{{ old('unit') }}" placeholder="الوحدة">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="">الوصف</label>
                                    <textarea name="description" class="form-control" rows="2" placeholder="وصف محطة العمل">{{ old('description') }}</textarea>
                                </div>

                                <br>

                                <div class="form-group col-md-12">
                                    <p onclick="toggleSection('rawMaterials')" class="d-flex justify-content-between section-header" style="background: #DBDEE2; width: 100%;">
                                        <span class="p-1 font-weight-bold"><i class="fa fa-money"></i> المصروفات (<span id="rawMaterialCount">1</span>)</span>
                                        <i class="feather icon-plus-circle p-1"></i>
                                    </p>
                                    <div id="rawMaterials">
                                        <table class="table table-striped" id="itemsTable">
                                            <thead style="background: #f8f8f8">
                                                <tr>
                                                    <th>التكلفة</th>
                                                    <th>الحساب</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><input type="number" name="cost_expenses[]" class="form-control unit-price" oninput="calculateTotalCost()" placeholder="0.00" step="0.01" min="0"></td>
                                                    <td>
                                                        <select name="account_expenses[]" class="form-control select2 product-select">
                                                            <option value="" disabled selected>-- اختر الحساب --</option>
                                                            @foreach ($accounts as $account)
                                                                <option value="{{ $account->id }}" {{ old('account_expenses') == $account->id ? 'selected' : '' }}>{{ $account->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td style="width: 10px">
                                                        <button type="button" class="btn btn-outline-danger btn-sm removeRow"><i class="fa fa-trash"></i></button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <hr>
                                        <div class="d-flex justify-content-between mt-2">
                                            <button type="button" class="btn btn-outline-success btn-sm" id="addRow"><i class="fa fa-plus"></i> إضافة</button>
                                        </div>
                                    </div>
                                </div>
                                <br><hr>

                                <div class="form-group col-md-12">
                                    <p onclick="toggleSection('expenses')" class="d-flex justify-content-between section-header" style="background: #DBDEE2; width: 100%;">
                                        <span class="p-1 font-weight-bold"><i class="fa fa-money"></i> الاجور</span>
                                        <i class="feather icon-plus-circle p-1"></i>
                                    </p>
                                    <div id="expenses" style="display: none">
                                        <table class="table table-striped" id="ExpensesTable">
                                            <thead style="background: #f8f8f8">
                                                <tr>
                                                    <th>التكلفة</th>
                                                    <th>الحساب</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><input type="number" name="cost_wages" class="form-control unit-price" oninput="calculateTotalCost()" placeholder="0.00" step="0.01" min="0"></td>
                                                    <td>
                                                        <select name="account_wages" class="form-control select2 product-select">
                                                            <option value="" disabled selected>-- اختر الحساب --</option>
                                                            @foreach ($accounts as $account)
                                                                <option value="{{ $account->id }}" {{ old('account_wages') == $account->id ? 'selected' : '' }}>{{ $account->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <br><hr>

                                <div class="form-group col-md-12">
                                    <p onclick="toggleSection('manufacturing')" class="d-flex justify-content-between section-header" style="background: #DBDEE2; width: 100%;">
                                        <span class="p-1 font-weight-bold"><i class="feather icon-folder"></i> أصل</span>
                                        <i class="feather icon-plus-circle p-1"></i>
                                    </p>
                                    <div id="manufacturing" style="display: none">
                                        <table class="table table-striped" id="manufacturingTable">
                                            <thead style="background: #f8f8f8">
                                                <tr>
                                                    <th>التكلفة</th>
                                                    <th>أصل</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><input type="number" name="cost_origin" class="form-control unit-price" oninput="calculateTotalCost()" placeholder="0.00" step="0.01" min="0"></td>
                                                    <td>
                                                        <select name="origin" class="form-control select2 product-select">
                                                            <option value="" disabled selected>-- اختر الحساب --</option>
                                                            @foreach ($accounts as $account)
                                                                <option value="{{ $account->id }}" {{ old('origin') == $account->id ? 'selected' : '' }}>{{ $account->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td style="width: 15%">
                                                        <div class="custom-control custom-switch custom-switch-success custom-control-inline">
                                                            <input type="checkbox" class="custom-control-input" id="customSwitch1" name="automatic_depreciation" value="1">
                                                            <label class="custom-control-label" for="customSwitch1"></label>
                                                            <span class="switch-label">إهلاك تلقائي</span>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <br><hr>

                                <div class="form-group col-md-6"></div>

                                <div class="form-group col-md-6">
                                    <div class="d-flex justify-content-between p-1 total-cost-container" style="background: #f8f9fa; border-left: 4px solid #dc3545;">
                                        <strong>إجمالي التكلفة : </strong>
                                        <strong class="total-cost">0.00 ر.س</strong>
                                        <input type="hidden" name="total_cost" value="0">
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
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // نظام التحقق من صحة البيانات
        function validateForm() {
            const errors = [];

            // التحقق من الحقول الإجبارية
            const requiredFields = {
                'name': 'اسم محطة العمل',
                'code': 'كود محطة العمل'
            };

            // فحص الحقول الإجبارية
            Object.keys(requiredFields).forEach(fieldName => {
                const field = document.querySelector(`[name="${fieldName}"]`);
                if (!field || !field.value.trim()) {
                    errors.push(`حقل ${requiredFields[fieldName]} مطلوب`);
                }
            });

            // التحقق من كود محطة العمل
            const codeField = document.querySelector('[name="code"]');
            if (codeField && codeField.value.trim()) {
                const codePattern = /^[a-zA-Z0-9]+$/;
                if (!codePattern.test(codeField.value.trim())) {
                    errors.push('كود محطة العمل يجب أن يحتوي على أرقام وحروف إنجليزية فقط');
                }
            }

            // التحقق من المصروفات
            const expenseRows = document.querySelectorAll('#itemsTable tbody tr');
            let hasValidExpense = false;

            expenseRows.forEach((row, index) => {
                const costInput = row.querySelector('[name="cost_expenses[]"]');
                const accountSelect = row.querySelector('[name="account_expenses[]"]');

                if (costInput && costInput.value.trim()) {
                    const cost = parseFloat(costInput.value);
                    if (isNaN(cost) || cost <= 0) {
                        errors.push(`تكلفة المصروف في الصف ${index + 1} يجب أن تكون رقم موجب`);
                    } else {
                        hasValidExpense = true;
                        if (!accountSelect || !accountSelect.value) {
                            errors.push(`يجب اختيار حساب للمصروف في الصف ${index + 1}`);
                        }
                    }
                }
            });

            // التحقق من الأجور
            const wagesInput = document.querySelector('[name="cost_wages"]');
            const wagesAccountSelect = document.querySelector('[name="account_wages"]');

            if (wagesInput && wagesInput.value.trim()) {
                const wages = parseFloat(wagesInput.value);
                if (isNaN(wages) || wages <= 0) {
                    errors.push('تكلفة الأجور يجب أن تكون رقم موجب');
                } else if (!wagesAccountSelect || !wagesAccountSelect.value) {
                    errors.push('يجب اختيار حساب للأجور');
                }
            }

            // التحقق من الأصل
            const originInput = document.querySelector('[name="cost_origin"]');
            const originSelect = document.querySelector('[name="origin"]');

            if (originInput && originInput.value.trim()) {
                const origin = parseFloat(originInput.value);
                if (isNaN(origin) || origin <= 0) {
                    errors.push('تكلفة الأصل يجب أن تكون رقم موجب');
                } else if (!originSelect || !originSelect.value) {
                    errors.push('يجب اختيار حساب للأصل');
                }
            }

            // التحقق من وجود تكلفة واحدة على الأقل
            const totalCost = parseFloat(document.querySelector('[name="total_cost"]').value) || 0;
            if (totalCost <= 0) {
                errors.push('يجب إدخال تكلفة واحدة على الأقل (مصروفات، أجور، أو أصل)');
            }

            return errors;
        }

        // حساب التكلفة الإجمالية
        function calculateTotalCost() {
            let totalCost = 0;
            let hasValidData = false;

            // حساب المصروفات
            document.querySelectorAll('[name="cost_expenses[]"]').forEach(input => {
                const value = parseFloat(input.value) || 0;
                if (value > 0) {
                    totalCost += value;
                    hasValidData = true;
                }
            });

            // حساب الأجور
            const wages = parseFloat(document.querySelector('[name="cost_wages"]').value) || 0;
            if (wages > 0) {
                totalCost += wages;
                hasValidData = true;
            }

            // حساب الأصل
            const origin = parseFloat(document.querySelector('[name="cost_origin"]').value) || 0;
            if (origin > 0) {
                totalCost += origin;
                hasValidData = true;
            }

            // تحديث عرض التكلفة الإجمالية
            const totalCostElement = document.querySelector('.total-cost');
            const totalCostInput = document.querySelector('input[name="total_cost"]');
            const totalCostContainer = document.querySelector('.total-cost-container');

            totalCostElement.textContent = totalCost.toFixed(2) + ' ر.س';
            totalCostInput.value = totalCost.toFixed(2);

            // تغيير لون الخلفية حسب وجود البيانات
            if (hasValidData && totalCost > 0) {
                totalCostContainer.style.backgroundColor = '#CCF5FA';
                totalCostContainer.style.borderLeft = '4px solid #28a745';
            } else {
                totalCostContainer.style.backgroundColor = '#f8f9fa';
                totalCostContainer.style.borderLeft = '4px solid #dc3545';
            }
        }

        // تبديل عرض الأقسام
        function toggleSection(sectionId) {
            const section = document.getElementById(sectionId);
            if (section.style.display === "none") {
                section.style.display = "block";
            } else {
                section.style.display = "none";
            }
        }

        // تحديث عدد المواد الخام
        function updateRawMaterialCount() {
            const rowCount = document.querySelectorAll('#itemsTable tbody tr').length;
            document.getElementById('rawMaterialCount').textContent = rowCount;
        }

        // التحقق من حقل واحد
        function validateSingleField(field) {
            const fieldName = field.getAttribute('name');
            const fieldValue = field.value.trim();

            clearFieldError(field);

            if ((fieldName === 'name' || fieldName === 'code') && !fieldValue) {
                showFieldError(field, 'هذا الحقل مطلوب');
                return false;
            }

            if (fieldName === 'code' && fieldValue) {
                const codePattern = /^[a-zA-Z0-9]+$/;
                if (!codePattern.test(fieldValue)) {
                    showFieldError(field, 'الكود يجب أن يحتوي على أرقام وحروف إنجليزية فقط');
                    return false;
                }
            }

            if (fieldValue) {
                showFieldSuccess(field);
            }
            return true;
        }

        // التحقق من الحقول الرقمية
        function validateNumberField(field) {
            const value = parseFloat(field.value);

            clearFieldError(field);

            if (field.value.trim() && (isNaN(value) || value < 0)) {
                showFieldError(field, 'يجب إدخال رقم صحيح');
                return false;
            }

            if (field.value.trim() && value >= 0) {
                showFieldSuccess(field);
            }

            return true;
        }

        // التحقق من القوائم المنسدلة
        function validateSelectField(field) {
            clearFieldError(field);

            const row = field.closest('tr');
            if (row) {
                const costField = row.querySelector('input[type="number"]');
                if (costField && costField.value.trim() && parseFloat(costField.value) > 0) {
                    if (!field.value) {
                        showFieldError(field, 'يجب اختيار حساب');
                        return false;
                    }
                }
            }

            if (field.value) {
                showFieldSuccess(field);
            }

            return true;
        }

        // عرض رسالة خطأ للحقل
        function showFieldError(field, message) {
            field.classList.add('is-invalid');
            field.classList.remove('is-valid');

            let errorDiv = field.parentNode.querySelector('.invalid-feedback');
            if (!errorDiv) {
                errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback';
                field.parentNode.appendChild(errorDiv);
            }
            errorDiv.textContent = message;
        }

        // عرض نجاح التحقق للحقل
        function showFieldSuccess(field) {
            field.classList.add('is-valid');
            field.classList.remove('is-invalid');
        }

        // مسح رسائل الخطأ للحقل
        function clearFieldError(field) {
            field.classList.remove('is-invalid', 'is-valid');
            const errorDiv = field.parentNode.querySelector('.invalid-feedback');
            if (errorDiv) {
                errorDiv.remove();
            }
        }

        // إعداد التحقق الفوري
        function setupRealTimeValidation() {
            // التحقق من الحقول النصية
            document.querySelectorAll('input[type="text"], textarea').forEach(input => {
                input.addEventListener('blur', function() {
                    validateSingleField(this);
                });

                input.addEventListener('input', function() {
                    if (this.classList.contains('is-invalid')) {
                        validateSingleField(this);
                    }
                });
            });

            // التحقق من الحقول الرقمية
            document.querySelectorAll('input[type="number"]').forEach(input => {
                input.addEventListener('input', function() {
                    validateNumberField(this);
                    calculateTotalCost();
                });

                input.addEventListener('blur', function() {
                    validateNumberField(this);
                });
            });

            // التحقق من القوائم المنسدلة
            document.querySelectorAll('select').forEach(select => {
                select.addEventListener('change', function() {
                    validateSelectField(this);
                });
            });
        }

        // إعداد إرسال النموذج
        function setupFormSubmission() {
            document.getElementById('workstationForm').addEventListener('submit', function(e) {
                e.preventDefault();

                const errors = validateForm();

                if (errors.length > 0) {
                    Swal.fire({
                        title: 'خطأ في البيانات',
                        html: `
                            <div style="text-align: right; max-height: 300px; overflow-y: auto;">
                                <ul style="list-style: none; padding: 0;">
                                    ${errors.map(error => `<li style="color: #dc3545; margin: 8px 0; padding: 5px; background: #f8d7da; border-radius: 4px;"><i class="fa fa-exclamation-triangle"></i> ${error}</li>`).join('')}
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
                    return;
                }

                Swal.fire({
                    title: 'تأكيد الحفظ',
                    text: 'هل أنت متأكد من حفظ محطة العمل؟',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'نعم، احفظ',
                    cancelButtonText: 'إلغاء'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // تعطيل الزر وإظهار التحميل
                        const submitBtn = document.getElementById('submitBtn');
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<div class="loading-spinner"></div> جاري الحفظ...';

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

                        this.submit();
                    }
                });
            });
        }

        // إعداد الصفوف الديناميكية
        function setupDynamicRows() {
            // إضافة صف جديد
            document.getElementById('addRow').addEventListener('click', function () {
                Swal.fire({
                    title: 'إضافة مصروف جديد',
                    text: 'هل تريد إضافة مصروف جديد؟',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'نعم، أضف',
                    cancelButtonText: 'إلغاء',
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#d33'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const newRow = document.createElement('tr');
                        newRow.innerHTML = `
                            <td><input type="number" name="cost_expenses[]" class="form-control unit-price" oninput="calculateTotalCost()" placeholder="0.00" step="0.01" min="0"></td>
                            <td>
                                <select name="account_expenses[]" class="form-control select2 product-select">
                                    <option value="" disabled selected>-- اختر الحساب --</option>
                                    @foreach ($accounts as $account)
                                        <option value="{{ $account->id }}">{{ $account->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td style="width: 10px">
                                <button type="button" class="btn btn-outline-danger btn-sm removeRow"><i class="fa fa-trash"></i></button>
                            </td>
                        `;
                        document.querySelector('#itemsTable tbody').appendChild(newRow);
                        updateRawMaterialCount();

                        // إعداد التحقق للصف الجديد
                        const newCostInput = newRow.querySelector('input[type="number"]');
                        const newSelectInput = newRow.querySelector('select');

                        newCostInput.addEventListener('input', function() {
                            validateNumberField(this);
                            calculateTotalCost();
                        });

                        newSelectInput.addEventListener('change', function() {
                            validateSelectField(this);
                        });

                        Swal.fire({
                            title: 'تم بنجاح!',
                            text: 'تم إضافة المصروف الجديد',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                });
            });

            // حذف صف
            document.querySelector('#itemsTable').addEventListener('click', function (e) {
                if (e.target.classList.contains('fa-trash') || e.target.classList.contains('removeRow')) {
                    const button = e.target.classList.contains('removeRow') ? e.target : e.target.closest('.removeRow');
                    const row = button.closest('tr');

                    if (document.querySelectorAll('#itemsTable tbody tr').length > 1) {
                        Swal.fire({
                            title: 'تأكيد الحذف',
                            text: 'هل أنت متأكد من حذف هذا المصروف؟',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'نعم، احذف',
                            cancelButtonText: 'إلغاء',
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#6c757d'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                row.remove();
                                updateRawMaterialCount();
                                calculateTotalCost();

                                Swal.fire({
                                    title: 'تم الحذف!',
                                    text: 'تم حذف المصروف بنجاح',
                                    icon: 'success',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            }
                        });
                    } else {
                        Swal.fire({
                            title: 'تحذير!',
                            text: 'لا يمكنك حذف جميع الصفوف!',
                            icon: 'warning',
                            confirmButtonText: 'حسناً',
                            confirmButtonColor: '#d33'
                        });
                    }
                }
            });
        }

        // إعداد زر الإلغاء
        function setupCancelButton() {
            document.getElementById('cancelBtn').addEventListener('click', function(e) {
                e.preventDefault();
                const href = this.href;

                Swal.fire({
                    title: 'تأكيد الإلغاء',
                    text: 'هل أنت متأكد من إلغاء العملية؟ سيتم فقدان جميع البيانات المدخلة.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'نعم، ألغي',
                    cancelButtonText: 'لا، استمر',
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = href;
                    }
                });
            });
        }

        // تشغيل جميع الوظائف عند تحميل الصفحة
        document.addEventListener('DOMContentLoaded', function() {
            setupRealTimeValidation();
            setupFormSubmission();
            setupDynamicRows();
            setupCancelButton();
            calculateTotalCost();

            console.log('✅ تم تحميل نظام التحقق من صحة البيانات');
        });

        // عرض رسائل النجاح والأخطاء
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'تم بنجاح',
                text: '{{ session('success') }}',
                confirmButtonText: 'حسناً',
                confirmButtonColor: '#28a745'
            });
        @endif

        @if($errors->any())
            Swal.fire({
                title: 'خطأ في البيانات',
                html: `
                    <ul style="text-align: right;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                `,
                icon: 'error',
                confirmButtonText: 'حسناً',
                confirmButtonColor: '#d33'
            });
        @endif
    </script>
@endsection
