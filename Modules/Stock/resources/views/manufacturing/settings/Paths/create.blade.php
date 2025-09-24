@extends('master')

@section('title')
مسار الإنتاج
@stop

@section('css')
    <style>
        .section-header {
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s ease;
            border-radius: 8px;
            padding: 12px;
        }

        .section-header:hover {
            background: #C8CDD3 !important;
        }

        .card {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: none;
            border-radius: 10px;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .btn {
            border-radius: 6px;
            padding: 8px 16px;
        }

        .table th {
            border-top: none;
            font-weight: 600;
            color: #495057;
        }

        .animate-fade-in {
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .row-number {
            font-weight: bold;
            color: #6c757d;
        }

        .required-asterisk {
            color: #dc3545;
            font-weight: bold;
        }
    </style>
@endsection

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">إنشاء مسار إنتاج جديد</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('/') }}">الرئيسية</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('manufacturing.paths.index') }}">مسارات الإنتاج</a></li>
                            <li class="breadcrumb-item active">إضافة جديد</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">
        <div class="container-fluid">
            @if ($errors->any())
                <div class="alert alert-danger animate-fade-in">
                    <div class="d-flex align-items-center">
                        <i class="feather icon-alert-circle mr-2"></i>
                        <div>
                            <strong>يرجى تصحيح الأخطاء التالية:</strong>
                            <ul class="mb-0 mt-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger animate-fade-in">
                    <div class="d-flex align-items-center">
                        <i class="feather icon-alert-circle mr-2"></i>
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            <form class="form-horizontal" id="manufacturingPathForm" action="{{ route('manufacturing.paths.store') }}" method="POST">
                @csrf

                <!-- أزرار التحكم -->
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div class="d-flex align-items-center">
                                <i class="feather icon-info text-info mr-2"></i>
                                <label class="mb-0">الحقول التي عليها علامة <span class="required-asterisk">*</span> إلزامية</label>
                            </div>
                            <div>
                                <button type="button" class="btn btn-outline-secondary mr-2" id="cancelBtn">
                                    <i class="feather icon-x"></i> إلغاء
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="feather icon-save"></i> حفظ مسار الإنتاج
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- معلومات أساسية -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            <i class="feather icon-settings mr-2"></i>
                            معلومات مسار الإنتاج الأساسية
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="name" class="font-weight-bold">
                                    اسم مسار الإنتاج <span class="required-asterisk">*</span>
                                </label>
                                <input type="text" id="name" class="form-control" name="name"
                                       value="{{ old('name') }}" placeholder="أدخل اسم مسار الإنتاج">
                                <small class="text-muted">اختر اسماً وصفياً لمسار الإنتاج</small>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="code" class="font-weight-bold">
                                    الكود <span class="required-asterisk">*</span>
                                </label>
                                <input type="text" id="code" class="form-control" name="code"
                                       value="{{ $serial_number }}" readonly>
                                <small class="text-muted">كود تلقائي لمسار الإنتاج</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- المراحل الإنتاجية -->
                <div class="card">
                    <div class="card-body p-0">
                        <div class="form-group col-md-12 p-3">
                            <div onclick="toggleSection('rawMaterials')" class="section-header" style="background: #DBDEE2;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="font-weight-bold">
                                        <i class="feather icon-list mr-2"></i>
                                        المراحل الإنتاجية (<span id="rawMaterialCount">1</span>)
                                    </span>
                                    <i class="feather icon-chevron-down" id="toggleIcon"></i>
                                </div>
                            </div>

                            <div id="rawMaterials" class="mt-3">
                                <div class="table-responsive">
                                    <table class="table table-striped" id="itemsTable">
                                        <thead style="background: #f8f9fa">
                                            <tr>
                                                <th width="80px" class="text-center">رقم</th>
                                                <th>اسم المرحلة</th>
                                                <th width="80px" class="text-center">إجراءات</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="animate-fade-in">
                                                <td class="text-center row-number">1</td>
                                                <td>
                                                    <input type="text" name="stage_name[]" class="form-control"
                                                           placeholder="أدخل اسم المرحلة الإنتاجية">
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-outline-danger btn-sm removeRow"
                                                            data-toggle="tooltip" title="حذف المرحلة">
                                                        <i class="feather icon-trash-2"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="border-top pt-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <button type="button" class="btn btn-outline-success" id="addRow">
                                            <i class="feather icon-plus"></i> إضافة مرحلة جديدة
                                        </button>
                                        <small class="text-muted">
                                            <i class="feather icon-info"></i>
                                            يمكنك إضافة عدة مراحل لمسار الإنتاج
                                        </small>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // تحديث عدد المواد الخام
    function updateRawMaterialCount() {
        const rowCount = document.querySelectorAll('#itemsTable tbody tr').length;
        document.getElementById('rawMaterialCount').textContent = rowCount;
    }

    // تحديث أرقام الصفوف
    function updateRowNumbers() {
        document.querySelectorAll("#itemsTable tbody tr").forEach((row, index) => {
            row.querySelector(".row-number").textContent = index + 1;
        });
    }

    // تبديل عرض القسم
    function toggleSection(sectionId) {
        const section = document.getElementById(sectionId);
        const icon = document.getElementById('toggleIcon');

        if (section.style.display === "none") {
            section.style.display = "block";
            icon.className = 'feather icon-chevron-down';
        } else {
            section.style.display = "none";
            icon.className = 'feather icon-chevron-up';
        }
    }

    // إظهار رسالة تأكيد عند الحفظ
    function showSuccessMessage() {
        Swal.fire({
            title: 'تم بنجاح!',
            text: 'تم حفظ مسار الإنتاج بنجاح',
            icon: 'success',
            confirmButtonText: 'حسناً',
            confirmButtonColor: '#28a745',
            timer: 3000,
            timerProgressBar: true
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const itemsTable = document.getElementById('itemsTable').querySelector('tbody');
        const addRowButton = document.getElementById('addRow');
        const form = document.getElementById('manufacturingPathForm');
        const cancelBtn = document.getElementById('cancelBtn');

        // إضافة صف جديد
        addRowButton.addEventListener('click', function (e) {
            e.preventDefault();

            const newRow = document.createElement('tr');
            newRow.className = 'animate-fade-in';
            newRow.innerHTML = `
                <td class="text-center row-number"></td>
                <td>
                    <input type="text" name="stage_name[]" class="form-control"
                           placeholder="أدخل اسم المرحلة الإنتاجية">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-outline-danger btn-sm removeRow"
                            data-toggle="tooltip" title="حذف المرحلة">
                        <i class="feather icon-trash-2"></i>
                    </button>
                </td>
            `;

            itemsTable.appendChild(newRow);
            updateRawMaterialCount();
            updateRowNumbers();

            // إظهار رسالة نجاح
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'تمت إضافة مرحلة جديدة',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });
        });

        // حذف صف
        itemsTable.addEventListener('click', function (e) {
            if (e.target.closest('.removeRow')) {
                const row = e.target.closest('tr');

                if (itemsTable.rows.length > 1) {
                    Swal.fire({
                        title: 'هل أنت متأكد؟',
                        text: "سيتم حذف هذه المرحلة نهائياً",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'نعم، احذف',
                        cancelButtonText: 'إلغاء'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            row.remove();
                            updateRawMaterialCount();
                            updateRowNumbers();

                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: 'تم حذف المرحلة',
                                showConfirmButton: false,
                                timer: 2000
                            });
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'تنبيه',
                        text: 'يجب أن يحتوي مسار الإنتاج على مرحلة واحدة على الأقل!',
                        confirmButtonText: 'حسناً',
                        confirmButtonColor: '#007bff'
                    });
                }
            }
        });

        // تأكيد الإلغاء
        cancelBtn.addEventListener('click', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'هل تريد الخروج؟',
                text: "سيتم فقدان جميع البيانات غير المحفوظة",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'نعم، اخرج',
                cancelButtonText: 'البقاء هنا'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('manufacturing.paths.index') }}";
                }
            });
        });

        // تأكيد قبل إرسال النموذج
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            // التحقق من وجود مراحل
            const stages = document.querySelectorAll('input[name="stage_name[]"]');
            let hasEmptyStages = false;

            stages.forEach(stage => {
                if (!stage.value.trim()) {
                    hasEmptyStages = true;
                }
            });

            if (hasEmptyStages) {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ في البيانات',
                    text: 'يرجى ملء جميع أسماء المراحل',
                    confirmButtonText: 'حسناً',
                    confirmButtonColor: '#d33'
                });
                return;
            }

            Swal.fire({
                title: 'حفظ مسار الإنتاج؟',
                text: "هل تريد حفظ مسار الإنتاج بالبيانات المدخلة؟",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'نعم، احفظ',
                cancelButtonText: 'إلغاء',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return new Promise((resolve) => {
                        setTimeout(() => {
                            resolve();
                        }, 1000);
                    });
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // إرسال النموذج فعلياً
                    form.submit();
                }
            });
        });

        // تهيئة tooltips
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
@endsection
