@extends('master')

@section('title')
تعديل مسار الإنتاج
@stop

@section('css')
    <style>
        .section-header {
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .section-header:hover {
            background-color: #c8ccd1 !important;
        }

        .card {
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            border: none;
            margin-bottom: 20px;
        }

        .card-title {
            color: #2c3e50;
            font-weight: 600;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid #e1e5e9;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .btn {
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .table {
            border-radius: 8px;
            overflow: hidden;
        }

        .table thead th {
            background: #f8f9fa;
            border: none;
            font-weight: 600;
            color: #495057;
            padding: 15px;
        }

        .table tbody td {
            padding: 12px 15px;
            border-color: #e9ecef;
            vertical-align: middle;
        }

        .alert {
            border-radius: 8px;
            border: none;
        }

        .breadcrumb {
            background: transparent;
            padding: 0;
        }

        .required-asterisk {
            color: #e74c3c;
            font-weight: bold;
        }

        .fade-in {
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .section-collapsed {
            display: none;
        }

        .rotate-icon {
            transition: transform 0.3s ease;
        }

        .rotate-icon.rotated {
            transform: rotate(45deg);
        }
    </style>
@endsection

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">تعديل مسار الإنتاج</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسية</a></li>
                            <li class="breadcrumb-item"><a href="">مسارات الإنتاج</a></li>
                            <li class="breadcrumb-item active">تعديل</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">
        <div class="container-fluid">
            @if ($errors->any())
                <div class="alert alert-danger fade-in">
                    <div class="d-flex align-items-center">
                        <i class="feather icon-alert-triangle mr-2"></i>
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
                <div class="alert alert-danger fade-in">
                    <div class="d-flex align-items-center">
                        <i class="feather icon-alert-triangle mr-2"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success fade-in">
                    <div class="d-flex align-items-center">
                        <i class="feather icon-check-circle mr-2"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <form class="form-horizontal" id="pathForm" action="{{ route('manufacturing.paths.update', $path->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Action Buttons Card -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div>
                                <label class="mb-0">
                                    <i class="feather icon-info mr-1"></i>
                                    الحقول التي عليها علامة <span class="required-asterisk">*</span> إلزامية
                                </label>
                            </div>
                            <div>
                                <a href="{{ route('manufacturing.paths.index') }}" class="btn btn-outline-secondary ml-2">
                                    <i class="feather icon-x mr-1"></i> إلغاء
                                </a>
                                <button type="submit" class="btn btn-primary" id="saveBtn">
                                    <i class="feather icon-save mr-1"></i> تحديث المسار
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
                                <i class="feather icon-settings mr-2"></i>
                                معلومات مسار الإنتاج الأساسية
                            </h4>
                        </div>

                        <div class="card-body pt-0">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="name">
                                        <i class="feather icon-type mr-1"></i>
                                        اسم المسار <span class="required-asterisk">*</span>
                                    </label>
                                    <input type="text"
                                           class="form-control @error('name') is-invalid @enderror"
                                           id="name"
                                           name="name"
                                           value="{{ old('name', $path->name) }}"
                                           placeholder="أدخل اسم مسار الإنتاج"
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="code">
                                        <i class="feather icon-hash mr-1"></i>
                                        كود المسار <span class="required-asterisk">*</span>
                                    </label>
                                    <input type="text"
                                           class="form-control @error('code') is-invalid @enderror"
                                           id="code"
                                           name="code"
                                           value="{{ old('code', $path->code) }}"
                                           placeholder="أدخل كود مسار الإنتاج"
                                           required>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Production Stages Card -->
                <div class="card">
                    <div class="card-content">
                        <div class="card-body pb-0">
                            <div class="form-group col-md-12 px-0">
                                <div onclick="toggleSection('rawMaterials')"
                                     class="section-header d-flex justify-content-between align-items-center"
                                     style="background: #DBDEE2; padding: 12px 15px;">
                                    <span class="font-weight-bold">
                                        <i class="feather icon-layers mr-2"></i>
                                        المراحل الإنتاجية (<span id="rawMaterialCount">{{ count($path->stages) }}</span>)
                                    </span>
                                    <i class="feather icon-plus-circle rotate-icon" id="toggleIcon"></i>
                                </div>

                                <div id="rawMaterials" class="mt-3">
                                    <div class="table-responsive">
                                        <table class="table table-striped" id="itemsTable">
                                            <thead>
                                                <tr>
                                                    <th style="width: 10%">
                                                        <i class="feather icon-hash mr-1"></i>
                                                        رقم
                                                    </th>
                                                    <th>
                                                        <i class="feather icon-type mr-1"></i>
                                                        اسم المرحلة
                                                    </th>
                                                    <th style="width: 10%" class="text-center">
                                                        <i class="feather icon-settings mr-1"></i>
                                                        إجراءات
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($path->stages as $index => $stage)
                                                    <tr class="fade-in">
                                                        <td class="row-number">{{ $index + 1 }}</td>
                                                        <td>
                                                            <input type="text"
                                                                   name="stage_name[]"
                                                                   class="form-control"
                                                                   value="{{ old("stage_name.$index", $stage->stage_name) }}"
                                                                   placeholder="أدخل اسم المرحلة"
                                                                   required>
                                                        </td>
                                                        <td class="text-center">
                                                            <button type="button"
                                                                    class="btn btn-outline-danger btn-sm removeRow"
                                                                    title="حذف المرحلة">
                                                                <i class="feather icon-trash-2"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr class="fade-in">
                                                        <td class="row-number">1</td>
                                                        <td>
                                                            <input type="text"
                                                                   name="stage_name[]"
                                                                   class="form-control"
                                                                   placeholder="أدخل اسم المرحلة"
                                                                   required>
                                                        </td>
                                                        <td class="text-center">
                                                            <button type="button"
                                                                    class="btn btn-outline-danger btn-sm removeRow"
                                                                    title="حذف المرحلة">
                                                                <i class="feather icon-trash-2"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mt-3 mb-3">
                                        <button type="button" class="btn btn-outline-success btn-sm" id="addRow">
                                            <i class="feather icon-plus mr-1"></i>
                                            إضافة مرحلة جديدة
                                        </button>
                                        <small class="text-muted">
                                            <i class="feather icon-info mr-1"></i>
                                            يجب إضافة مرحلة واحدة على الأقل
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
        // تحديث عدد المراحل
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

            if (section.style.display === "none" || section.classList.contains('section-collapsed')) {
                section.style.display = "block";
                section.classList.remove('section-collapsed');
                icon.classList.add('rotated');
            } else {
                section.style.display = "none";
                section.classList.add('section-collapsed');
                icon.classList.remove('rotated');
            }
        }

        // التحقق من صحة النموذج
        function validateForm() {
            const name = document.getElementById('name').value.trim();
            const code = document.getElementById('code').value.trim();
            const stages = document.querySelectorAll('input[name="stage_name[]"]');

            if (!name) {
                Swal.fire({
                    icon: 'warning',
                    title: 'تنبيه',
                    text: 'يرجى إدخال اسم المسار',
                    confirmButtonText: 'حسناً',
                    confirmButtonColor: '#667eea'
                });
                return false;
            }

            if (!code) {
                Swal.fire({
                    icon: 'warning',
                    title: 'تنبيه',
                    text: 'يرجى إدخال كود المسار',
                    confirmButtonText: 'حسناً',
                    confirmButtonColor: '#667eea'
                });
                return false;
            }

            let hasEmptyStage = false;
            stages.forEach(stage => {
                if (!stage.value.trim()) {
                    hasEmptyStage = true;
                }
            });

            if (hasEmptyStage) {
                Swal.fire({
                    icon: 'warning',
                    title: 'تنبيه',
                    text: 'يرجى ملء جميع أسماء المراحل',
                    confirmButtonText: 'حسناً',
                    confirmButtonColor: '#667eea'
                });
                return false;
            }

            return true;
        }

        document.addEventListener('DOMContentLoaded', function () {
            const itemsTable = document.getElementById('itemsTable').querySelector('tbody');
            const addRowButton = document.getElementById('addRow');
            const pathForm = document.getElementById('pathForm');

            // إضافة صف جديد
            addRowButton.addEventListener('click', function (e) {
                e.preventDefault();

                const newRow = document.createElement('tr');
                newRow.className = 'fade-in';
                newRow.innerHTML = `
                    <td class="row-number"></td>
                    <td>
                        <input type="text"
                               name="stage_name[]"
                               class="form-control"
                               placeholder="أدخل اسم المرحلة"
                               required>
                    </td>
                    <td class="text-center">
                        <button type="button"
                                class="btn btn-outline-danger btn-sm removeRow"
                                title="حذف المرحلة">
                            <i class="feather icon-trash-2"></i>
                        </button>
                    </td>
                `;

                itemsTable.appendChild(newRow);
                updateRawMaterialCount();
                updateRowNumbers();

                // تركيز على الحقل الجديد
                const newInput = newRow.querySelector('input[name="stage_name[]"]');
                newInput.focus();
            });

            // حذف صف
            itemsTable.addEventListener('click', function (e) {
                if (e.target.closest('.removeRow')) {
                    const row = e.target.closest('tr');
                    const rowCount = itemsTable.rows.length;

                    if (rowCount > 1) {
                        Swal.fire({
                            title: 'تأكيد الحذف',
                            text: 'هل أنت متأكد من حذف هذه المرحلة؟',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#e74c3c',
                            cancelButtonColor: '#95a5a6',
                            confirmButtonText: 'نعم، احذف',
                            cancelButtonText: 'إلغاء'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                row.remove();
                                updateRawMaterialCount();
                                updateRowNumbers();

                                Swal.fire({
                                    title: 'تم الحذف!',
                                    text: 'تم حذف المرحلة بنجاح.',
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'تنبيه',
                            text: 'لا يمكنك حذف جميع المراحل! يجب وجود مرحلة واحدة على الأقل.',
                            confirmButtonText: 'حسناً',
                            confirmButtonColor: '#667eea'
                        });
                    }
                }
            });

            // التحقق من النموذج قبل الإرسال
            pathForm.addEventListener('submit', function(e) {
                e.preventDefault();

                if (validateForm()) {
                    Swal.fire({
                        title: 'تأكيد التحديث',
                        text: 'هل أنت متأكد من تحديث مسار الإنتاج؟',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#667eea',
                        cancelButtonColor: '#95a5a6',
                        confirmButtonText: 'نعم، حدث',
                        cancelButtonText: 'إلغاء',
                        showLoaderOnConfirm: true,
                        preConfirm: () => {
                            return new Promise((resolve) => {
                                setTimeout(resolve, 1000);
                            });
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // إظهار مؤشر التحميل على الزر
                            const saveBtn = document.getElementById('saveBtn');
                            const originalText = saveBtn.innerHTML;
                            saveBtn.innerHTML = '<i class="feather icon-loader mr-1"></i> جاري التحديث...';
                            saveBtn.disabled = true;

                            // إرسال النموذج
                            this.submit();
                        }
                    });
                }
            });

            // إضافة تأثير hover للصفوف
            itemsTable.addEventListener('mouseenter', function(e) {
                if (e.target.closest('tr')) {
                    e.target.closest('tr').style.backgroundColor = '#f8f9fa';
                }
            }, true);

            itemsTable.addEventListener('mouseleave', function(e) {
                if (e.target.closest('tr')) {
                    e.target.closest('tr').style.backgroundColor = '';
                }
            }, true);

            // التحقق من النموذج في الوقت الفعلي
            const inputs = document.querySelectorAll('input[required]');
            inputs.forEach(input => {
                input.addEventListener('blur', function() {
                    if (this.value.trim() === '') {
                        this.classList.add('is-invalid');
                    } else {
                        this.classList.remove('is-invalid');
                        this.classList.add('is-valid');
                    }
                });

                input.addEventListener('input', function() {
                    if (this.classList.contains('is-invalid') && this.value.trim() !== '') {
                        this.classList.remove('is-invalid');
                        this.classList.add('is-valid');
                    }
                });
            });
        });

        // إظهار رسالة نجاح عند تحميل الصفحة إذا كانت موجودة
        @if(session('success'))
            window.addEventListener('load', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'تم بنجاح!',
                    text: '{{ session('success') }}',
                    timer: 3000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            });
        @endif
    </script>
@endsection
