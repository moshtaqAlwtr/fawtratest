@extends('master')

@section('title')
    فئات وكلاء التأمين
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">إضافة فئات وكيل تأمين</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسية</a></li>
                            <li class="breadcrumb-item active">إضافة</li>
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
            <form id="insuranceForm" method="POST" enctype="multipart/form-data">
                @csrf

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
                        <input type="text" id="name" class="form-control" placeholder="ادخل اسم الفئة" name="name" required>
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
                                        <input type="number" class="form-control company-copayment" placeholder="0" name="company_copayment[]" min="0" max="100" step="0.01" value="0">
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <input type="number" class="form-control client-copayment" placeholder="100" name="client_copayment[]" min="0" max="100" step="0.01" value="100.00" readonly>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-6">
                                                <input name="max_copayment[]" type="number" class="form-control numeric" step="0.01" min="0" placeholder="0" />
                                            </div>
                                            <div class="col-6">
                                                <select name="type[]" class="form-control">
                                                    <option value="1">العميل</option>
                                                    <option value="2">شركة تأمين</option>
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

        const companyCopayment = parseFloat(companyCopaymentInput.value) || 0;

        // حساب نسبة العميل = 100 - نسبة الشركة
        const clientCopayment = Math.max(0, 100 - companyCopayment);
        clientCopaymentInput.value = clientCopayment.toFixed(2);
    }

    // وظيفة لإضافة أحداث الحقول للصف الجديد
    function addCopaymentEvents(row) {
        const companyCopaymentInput = row.querySelector('.company-copayment');

        // عند تغيير قيمة نسبة الشركة
        companyCopaymentInput.addEventListener('input', function() {
            const companyValue = parseFloat(this.value) || 0;

            // التأكد من أن نسبة الشركة لا تتجاوز 100
            if (companyValue > 100) {
                this.value = 100;
                Swal.fire({
                    icon: 'warning',
                    title: 'تحذير',
                    text: 'نسبة الشركة لا يمكن أن تكون أكبر من 100%',
                    confirmButtonText: 'موافق'
                });
            }
            if (companyValue < 0) {
                this.value = 0;
                Swal.fire({
                    icon: 'warning',
                    title: 'تحذير',
                    text: 'نسبة الشركة لا يمكن أن تكون أقل من 0%',
                    confirmButtonText: 'موافق'
                });
            }

            calculateClientCopayment(row);
        });

        // إضافة حدث لحذف الصف
        const removeButton = row.querySelector('.removeItem');
        removeButton.addEventListener('click', function(e) {
            e.preventDefault();
            if (document.querySelectorAll('#statusTable tr').length > 1) {
                Swal.fire({
                    title: 'هل أنت متأكد؟',
                    text: "سيتم حذف هذا الصف نهائياً",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'نعم، احذف!',
                    cancelButtonText: 'إلغاء'
                }).then((result) => {
                    if (result.isConfirmed) {
                        row.remove();
                        Swal.fire({
                            icon: 'success',
                            title: 'تم الحذف!',
                            text: 'تم حذف الصف بنجاح.',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: 'لا يمكن حذف جميع الصفوف. يجب أن يبقى صف واحد على الأقل.',
                    confirmButtonText: 'موافق'
                });
            }
        });
    }

    // إضافة الأحداث للصف الموجود مسبقاً
    document.addEventListener('DOMContentLoaded', function() {
        const existingRows = document.querySelectorAll('#statusTable tr');
        existingRows.forEach(row => {
            // تعيين القيمة الافتراضية للعميل
            const clientCopaymentInput = row.querySelector('.client-copayment');
            if (clientCopaymentInput && !clientCopaymentInput.value) {
                clientCopaymentInput.value = '100.00';
            }
            addCopaymentEvents(row);
        });
    });

    // إضافة صف جديد
    document.getElementById('addNewStatus').addEventListener('click', function() {
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
                    <input type="number" class="form-control company-copayment" placeholder="0" name="company_copayment[]" min="0" max="100" step="0.01" value="0">
                </div>
            </td>
            <td>
                <div class="form-group">
                    <input type="number" class="form-control client-copayment" placeholder="100" name="client_copayment[]" min="0" max="100" step="0.01" value="100.00" readonly>
                </div>
            </td>
            <td>
                <div class="form-group">
                    <div class="row">
                        <div class="col-6">
                            <input type="number" class="form-control numeric" step="0.01" min="0" name="max_copayment[]" placeholder="0" />
                        </div>
                        <div class="col-6">
                            <select name="type[]" class="form-control">
                                <option value="1">العميل</option>
                                <option value="2">شركة تأمين</option>
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

        document.getElementById('statusTable').appendChild(newRow);
        addCopaymentEvents(newRow);
    });

    // التحقق من صحة البيانات والإرسال عبر Ajax
    document.getElementById('insuranceForm').addEventListener('submit', function(e) {
        e.preventDefault();

        let valid = true;
        const rows = document.querySelectorAll('#statusTable tr');

        // التحقق من اسم الفئة
        const nameInput = document.getElementById('name');
        if (!nameInput.value.trim()) {
            valid = false;
            Swal.fire({
                icon: 'error',
                title: 'خطأ في البيانات',
                text: 'الرجاء إدخال اسم الفئة',
                confirmButtonText: 'موافق'
            });
            nameInput.focus();
            return;
        }

        // التحقق من صحة البيانات في كل صف
        rows.forEach((row, index) => {
            const categorySelect = row.querySelector('select[name="category_id[]"]');
            const discountInput = row.querySelector('input[name="discount[]"]');
            const companyCopaymentInput = row.querySelector('input[name="company_copayment[]"]');
            const clientCopaymentInput = row.querySelector('input[name="client_copayment[]"]');
            const maxCopaymentInput = row.querySelector('input[name="max_copayment[]"]');
            const typeSelect = row.querySelector('select[name="type[]"]');

            // التحقق من اختيار التصنيف
            if (!categorySelect.value) {
                valid = false;
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ في البيانات',
                    text: `الرجاء اختيار التصنيف في الصف ${index + 1}`,
                    confirmButtonText: 'موافق'
                });
                return;
            }

            // التحقق من قيمة الخصم
            const discountValue = parseFloat(discountInput.value) || 0;
            if (discountValue < 0) {
                valid = false;
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ في البيانات',
                    text: `قيمة الخصم يجب أن تكون رقم موجب في الصف ${index + 1}`,
                    confirmButtonText: 'موافق'
                });
                return;
            }

            // التحقق من نسبة الشركة
            const companyValue = parseFloat(companyCopaymentInput.value) || 0;
            if (companyValue < 0 || companyValue > 100) {
                valid = false;
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ في البيانات',
                    text: `نسبة الشركة يجب أن تكون بين 0 و 100 في الصف ${index + 1}`,
                    confirmButtonText: 'موافق'
                });
                return;
            }

            // التحقق من نسبة العميل
            const clientValue = parseFloat(clientCopaymentInput.value) || 0;
            if (clientValue < 0 || clientValue > 100) {
                valid = false;
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ في البيانات',
                    text: `نسبة العميل يجب أن تكون بين 0 و 100 في الصف ${index + 1}`,
                    confirmButtonText: 'موافق'
                });
                return;
            }

            // التأكد من أن مجموع نسبة الشركة والعميل = 100
            if (Math.abs((companyValue + clientValue) - 100) > 0.01) {
                valid = false;
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ في البيانات',
                    text: `مجموع نسبة الشركة والعميل يجب أن يساوي 100% في الصف ${index + 1}`,
                    confirmButtonText: 'موافق'
                });
                return;
            }

            // التحقق من الحد الأقصى
            const maxValue = parseFloat(maxCopaymentInput.value) || 0;
            if (maxValue < 0) {
                valid = false;
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ في البيانات',
                    text: `الحد الأقصى للدفع المشترك يجب أن يكون رقم موجب في الصف ${index + 1}`,
                    confirmButtonText: 'موافق'
                });
                return;
            }

            // التحقق من نوع الدفع
            if (!['1', '2'].includes(typeSelect.value)) {
                valid = false;
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ في البيانات',
                    text: `الرجاء اختيار نوع الدفع في الصف ${index + 1}`,
                    confirmButtonText: 'موافق'
                });
                return;
            }
        });

        if (!valid) {
            return;
        }

        // عرض رسالة تأكيد قبل الحفظ
        Swal.fire({
            title: 'تأكيد الحفظ',
            text: "هل أنت متأكد من حفظ البيانات؟",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'نعم، احفظ!',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                submitForm();
            }
        });
    });

    // دالة لإرسال النموذج
    function submitForm() {
        const formData = new FormData(document.getElementById('insuranceForm'));
        const submitButton = document.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;

        // تعطيل الزر وإظهار حالة التحميل
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i>جاري الحفظ...';

        fetch('{{ route("InsuranceAgentsClass.store") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            submitButton.disabled = false;
            submitButton.innerHTML = originalText;

            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'تم بنجاح!',
                    text: data.message || 'تم حفظ البيانات بنجاح',
                    confirmButtonText: 'موافق',
                    timer: 3000,
                    timerProgressBar: true
                }).then(() => {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        // إعادة تعيين النموذج
                        document.getElementById('insuranceForm').reset();
                        // إعادة تعيين الجدول إلى صف واحد
                        resetTable();
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ!',
                    text: data.message || 'حدث خطأ أثناء حفظ البيانات',
                    confirmButtonText: 'موافق'
                });

                // عرض أخطاء التحقق إن وجدت
                if (data.errors) {
                    let errorMessages = '';
                    Object.values(data.errors).forEach(errorArray => {
                        errorArray.forEach(error => {
                            errorMessages += error + '\n';
                        });
                    });

                    Swal.fire({
                        icon: 'error',
                        title: 'أخطاء في البيانات',
                        text: errorMessages,
                        confirmButtonText: 'موافق'
                    });
                }
            }
        })
        .catch(error => {
            submitButton.disabled = false;
            submitButton.innerHTML = originalText;

            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'خطأ في الشبكة!',
                text: 'حدث خطأ في الاتصال، الرجاء المحاولة مرة أخرى',
                confirmButtonText: 'موافق'
            });
        });
    }

    // دالة لإعادة تعيين الجدول
    function resetTable() {
        const tableBody = document.getElementById('statusTable');
        tableBody.innerHTML = `
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
                        <input type="number" class="form-control company-copayment" placeholder="0" name="company_copayment[]" min="0" max="100" step="0.01" value="0">
                    </div>
                </td>
                <td>
                    <div class="form-group">
                        <input type="number" class="form-control client-copayment" placeholder="100" name="client_copayment[]" min="0" max="100" step="0.01" value="100.00" readonly>
                    </div>
                </td>
                <td>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-6">
                                <input name="max_copayment[]" type="number" class="form-control numeric" step="0.01" min="0" placeholder="0" />
                            </div>
                            <div class="col-6">
                                <select name="type[]" class="form-control">
                                    <option value="1">العميل</option>
                                    <option value="2">شركة تأمين</option>
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
        `;

        // إعادة إضافة الأحداث للصف الجديد
        const newRow = tableBody.querySelector('tr');
        addCopaymentEvents(newRow);
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
