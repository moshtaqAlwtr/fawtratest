@extends('master')

@section('title')
    فئات وكلاء التأمين
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">تعديل فئات وكيل تأمين</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسية</a></li>
                            <li class="breadcrumb-item active">تعديل</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('layouts.alerts.success')
    @include('layouts.alerts.error')

    <div class="card">
        <div class="card-content">
            <form id="insuranceForm" action="{{ route('InsuranceAgentsClass.update', $insuranceAgentCategory->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <input type="hidden" name="insurance_agent_id" value="{{ $insurance_agent_id }}">

                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-rtl flex-wrap">
                            <div></div>
                            <div>
                                <button type="button" class="btn btn-outline-danger" onclick="window.history.back();">
                                    <i class="fa fa-times me-2"></i>إلغاء
                                </button>
                                <button type="submit" class="btn btn-outline-success">
                                    <i class="fa fa-save me-2"></i>حفظ
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="form-group col-md-12">
                        <label for="name" class="">الاسم</label>
                        <input type="text" id="name" class="form-control" placeholder="ادخل اسم الفئة" name="name" value="{{ old('name', $insuranceAgentCategory->name) }}" required>
                    </div>

                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>التصنيفات</th>
                                <th>الخصم %</th>
                                <th>Company copayment %</th>
                                <th>Client copayment %</th>
                                <th>الحد الأقصى للدفع المشترك $</th>
                                <th>إجراءات</th>
                            </tr>
                        </thead>
                        <tbody id="statusTable">
                            @if ($insuranceAgentCategory->category)
                                <tr data-status-id="{{ $insuranceAgentCategory->category->id }}">
                                    <td>
                                        <div class="form-group">
                                            <select class="form-control" name="category_id[]" required>
                                                <option value="">اختر التصنيف</option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}" {{ $category->id == $insuranceAgentCategory->category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <input type="number" class="form-control discount-input" placeholder="0" name="discount[]" value="{{ $insuranceAgentCategory->discount }}" min="0" step="0.01">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <input type="number" class="form-control company-copayment" placeholder="0" name="company_copayment[]" value="{{ $insuranceAgentCategory->company_copayment }}" min="0" max="100" step="0.01">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <input type="number" class="form-control client-copayment" placeholder="0" name="client_copayment[]" value="{{ $insuranceAgentCategory->client_copayment }}" min="0" max="100" step="0.01" readonly>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-6">
                                                    <input type="number" class="form-control numeric" step="any" name="max_copayment[]" value="{{ $insuranceAgentCategory->max_copayment }}" />
                                                </div>
                                                <div class="col-6">
                                                    <select name="type[]" class="form-control">
                                                        <option value="1" {{ $insuranceAgentCategory->type == 1 ? 'selected' : '' }}>العميل</option>
                                                        <option value="2" {{ $insuranceAgentCategory->type == 2 ? 'selected' : '' }}>شركه تأمين</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="delete-product-cell notEditable">
                                        <a href="#" class="removeItem delete-ico btn btn-circle btn-danger btn-sm" tabindex="-1">
                                            <i class="fa fa-remove"></i>
                                        </a>
                                    </td>
                                </tr>
                            @else
                                <tr data-status-id="1">
                                    <td>
                                        <div class="form-group">
                                            <select class="form-control" name="category_id[]" required>
                                                <option value="">اختر التصنيف</option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <input type="number" class="form-control discount-input" placeholder="0" name="discount[]" min="0" step="0.01">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <input type="number" class="form-control company-copayment" placeholder="0" name="company_copayment[]" min="0" max="100" step="0.01">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <input type="number" class="form-control client-copayment" placeholder="0" name="client_copayment[]" min="0" max="100" step="0.01" readonly>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-6">
                                                    <input type="number" class="form-control numeric" step="any" name="max_copayment[]" />
                                                </div>
                                                <div class="col-6">
                                                    <select name="type[]" class="form-control">
                                                        <option value="1">العميل</option>
                                                        <option value="2">شركه تأمين</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="delete-product-cell notEditable">
                                        <a href="#" class="removeItem delete-ico btn btn-circle btn-danger btn-sm" tabindex="-1">
                                            <i class="fa fa-remove"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>

                    <button class="btn btn-success mt-2" id="addNewStatus" type="button">
                        <i class="feather icon-plus"></i> إضافة سطر
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // وظيفة لحساب نسبة العميل
    function calculateClientCopayment(row) {
        const companyCopaymentInput = row.querySelector('.company-copayment');
        const clientCopaymentInput = row.querySelector('.client-copayment');

        if (companyCopaymentInput && clientCopaymentInput) {
            const companyCopayment = parseFloat(companyCopaymentInput.value) || 0;
            const clientCopayment = Math.max(0, 100 - companyCopayment);
            clientCopaymentInput.value = clientCopayment.toFixed(2);
        }
    }

    // وظيفة لإضافة أحداث الحقول للصف الجديد
    function addCopaymentEvents(row) {
        const companyCopaymentInput = row.querySelector('.company-copayment');

        // عند تغيير قيمة نسبة الشركة
        companyCopaymentInput.addEventListener('input', function() {
            const value = parseFloat(this.value);

            if (value < 0) {
                this.value = 0;
                Swal.fire({
                    icon: 'warning',
                    title: 'تحذير',
                    text: 'لا يمكن أن تكون النسبة أقل من 0%',
                    confirmButtonText: 'موافق'
                });
            } else if (value > 100) {
                this.value = 100;
                Swal.fire({
                    icon: 'warning',
                    title: 'تحذير',
                    text: 'لا يمكن أن تكون النسبة أكثر من 100%',
                    confirmButtonText: 'موافق'
                });
            }

            calculateClientCopayment(row);
        });

        // إضافة حدث لحذف الصف
        const removeButton = row.querySelector('.removeItem');
        if (removeButton) {
            removeButton.addEventListener('click', function(e) {
                e.preventDefault();

                const tableBody = document.getElementById('statusTable');
                const rows = tableBody.querySelectorAll('tr');

                if (rows.length > 1) {
                    Swal.fire({
                        title: 'هل أنت متأكد؟',
                        text: 'سيتم حذف هذا الصف نهائياً',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'نعم، احذف',
                        cancelButtonText: 'إلغاء'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            row.remove();
                            Swal.fire({
                                title: 'تم الحذف!',
                                text: 'تم حذف الصف بنجاح',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: 'لا يمكن حذف الصف الوحيد المتبقي',
                        confirmButtonText: 'موافق'
                    });
                }
            });
        }
    }

    // تطبيق الأحداث على الصفوف الموجودة
    document.addEventListener('DOMContentLoaded', function() {
        const existingRows = document.querySelectorAll('#statusTable tr');
        existingRows.forEach(row => {
            addCopaymentEvents(row);
            calculateClientCopayment(row);
        });
    });

    // إضافة صف جديد
    document.getElementById('addNewStatus').addEventListener('click', function() {
        const tableBody = document.getElementById('statusTable');
        const newRow = document.createElement('tr');

        newRow.innerHTML = `
            <td>
                <div class="form-group">
                    <select class="form-control" name="category_id[]" required>
                        <option value="">اختر التصنيف</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </td>
            <td>
                <div class="form-group">
                    <input type="number" class="form-control discount-input" placeholder="0" name="discount[]" min="0" step="0.01">
                </div>
            </td>
            <td>
                <div class="form-group">
                    <input type="number" class="form-control company-copayment" placeholder="0" name="company_copayment[]" min="0" max="100" step="0.01">
                </div>
            </td>
            <td>
                <div class="form-group">
                    <input type="number" class="form-control client-copayment" placeholder="0" name="client_copayment[]" min="0" max="100" step="0.01" readonly>
                </div>
            </td>
            <td>
                <div class="form-group">
                    <div class="row">
                        <div class="col-6">
                            <input type="number" class="form-control numeric" step="any" name="max_copayment[]" />
                        </div>
                        <div class="col-6">
                            <select name="type[]" class="form-control">
                                <option value="1">العميل</option>
                                <option value="2">شركه تأمين</option>
                            </select>
                        </div>
                    </div>
                </div>
            </td>
            <td class="delete-product-cell notEditable">
                <a href="#" class="removeItem delete-ico btn btn-circle btn-danger btn-sm" tabindex="-1">
                    <i class="fa fa-remove"></i>
                </a>
            </td>
        `;

        tableBody.appendChild(newRow);
        addCopaymentEvents(newRow);
        calculateClientCopayment(newRow);
    });

    // التحقق من صحة النموذج قبل الإرسال
    document.getElementById('insuranceForm').addEventListener('submit', function(e) {
        const nameInput = document.getElementById('name');
        const categorySelects = document.querySelectorAll('select[name="category_id[]"]');
        const discountInputs = document.querySelectorAll('input[name="discount[]"]');
        const companyCopaymentInputs = document.querySelectorAll('input[name="company_copayment[]"]');
        const maxCopaymentInputs = document.querySelectorAll('input[name="max_copayment[]"]');

        let isValid = true;
        let errorMessage = '';

        // التحقق من اسم الفئة
        if (!nameInput.value.trim()) {
            isValid = false;
            errorMessage = 'يرجى إدخال اسم الفئة';
        }

        // التحقق من التصنيفات
        let hasValidCategory = false;
        categorySelects.forEach(select => {
            if (select.value) {
                hasValidCategory = true;
            }
        });

        if (!hasValidCategory) {
            isValid = false;
            errorMessage = 'يرجى اختيار تصنيف واحد على الأقل';
        }

        // التحقق من القيم الرقمية
        discountInputs.forEach(input => {
            if (input.value && (isNaN(input.value) || parseFloat(input.value) < 0)) {
                isValid = false;
                errorMessage = 'يرجى إدخال قيم صحيحة للخصم';
            }
        });

        companyCopaymentInputs.forEach(input => {
            if (input.value && (isNaN(input.value) || parseFloat(input.value) < 0 || parseFloat(input.value) > 100)) {
                isValid = false;
                errorMessage = 'يرجى إدخال قيم صحيحة لنسبة الشركة (0-100)';
            }
        });

        maxCopaymentInputs.forEach(input => {
            if (input.value && (isNaN(input.value) || parseFloat(input.value) < 0)) {
                isValid = false;
                errorMessage = 'يرجى إدخال قيم صحيحة للحد الأقصى';
            }
        });

        if (!isValid) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'خطأ في البيانات',
                text: errorMessage,
                confirmButtonText: 'موافق'
            });
        } else {
            // إظهار رسالة التحميل
            Swal.fire({
                title: 'جاري الحفظ...',
                text: 'يرجى الانتظار',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        }
    });

    // إعادة حساب نسب العميل عند تحميل الصفحة
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            const rows = document.querySelectorAll('#statusTable tr');
            rows.forEach(row => {
                calculateClientCopayment(row);
            });
        });
    } else {
        const rows = document.querySelectorAll('#statusTable tr');
        rows.forEach(row => {
            calculateClientCopayment(row);
        });
    }
</script>

<style>
    .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .btn-circle {
        border-radius: 50%;
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .table th {
        background-color: #f8f9fa;
        font-weight: bold;
        text-align: center;
        vertical-align: middle;
    }

    .table td {
        vertical-align: middle;
    }

    .client-copayment {
        background-color: #e9ecef;
        cursor: not-allowed;
    }

    .alert {
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .card {
        border: none;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }

    .breadcrumb {
        background: transparent;
        margin-bottom: 0;
    }

    .content-header-title {
        color: #5e5e5e;
        font-weight: 600;
    }

    .form-group {
        margin-bottom: 0;
    }

    .btn {
        border-radius: 6px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-outline-success:hover {
        background-color: #28a745;
        border-color: #28a745;
        transform: translateY(-1px);
    }

    .btn-outline-danger:hover {
        background-color: #dc3545;
        border-color: #dc3545;
        transform: translateY(-1px);
    }

    .btn:disabled {
        cursor: not-allowed;
        opacity: 0.6;
    }

    input[readonly] {
        background-color: #e9ecef !important;
        opacity: 1;
    }

    .swal2-popup {
        font-family: 'Arial', sans-serif;
        border-radius: 15px;
    }

    .swal2-title {
        font-size: 1.5rem;
        color: #495057;
    }

    .swal2-content {
        font-size: 1rem;
        color: #6c757d;
    }

    .swal2-confirm {
        border-radius: 8px !important;
        padding: 10px 25px !important;
        font-weight: 600 !important;
    }

    .swal2-cancel {
        border-radius: 8px !important;
        padding: 10px 25px !important;
        font-weight: 600 !important;
    }

    /* تحسين مظهر الجدول */
    .table {
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .table thead th {
        border-bottom: 2px solid #dee2e6;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }

    .table tbody tr:hover {
        background-color: #f8f9fa;
        transition: background-color 0.3s ease;
    }

    /* تحسين مظهر الأزرار */
    .btn-success {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border: none;
        box-shadow: 0 2px 4px rgba(40, 167, 69, 0.3);
    }

    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(40, 167, 69, 0.4);
    }

    /* تحسين animation للتحميل */
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .fa-spinner {
        animation: spin 1s linear infinite;
    }

    /* تحسين مظهر النموذج */
    .form-control {
        border-radius: 6px;
        border: 1px solid #ced4da;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        transform: translateY(-1px);
    }

    /* تحسين مظهر التحذيرات */
    .swal2-icon.swal2-warning {
        border-color: #f39c12 !important;
        color: #f39c12 !important;
    }

    .swal2-icon.swal2-success {
        border-color: #27ae60 !important;
        color: #27ae60 !important;
    }

    .swal2-icon.swal2-error {
        border-color: #e74c3c !important;
        color: #e74c3c !important;
    }
</style>
@endsection
