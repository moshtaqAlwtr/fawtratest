@extends('master')

@section('title', 'تعديل طلب إجازة')

@section('content')
    <!-- Header Section -->
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">تعديل طلب إجازة</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('attendance.leave_requests.index') }}">طلبات الإجازة</a></li>
                            <li class="breadcrumb-item active">تعديل طلب #{{ $leaveRequest->id }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form id="leave-request-form" method="POST" action="{{ route('attendance.leave_requests.update', $leaveRequest->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        @if ($errors->any())
            <div class="alert alert-danger">
                <h6 class="alert-heading">يرجى التحقق من الأخطاء التالية:</h6>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                {{ session('error') }}
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
        @endif

        <!-- Status Alert -->
        <div class="alert alert-info">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h6 class="alert-heading mb-1">
                        <i class="fas fa-info-circle mr-1"></i>معلومات الطلب
                    </h6>
                    <p class="mb-0">
                        <strong>رقم الطلب:</strong> #{{ $leaveRequest->id }} |
                        <strong>الحالة:</strong>
                        <span class="badge badge-{{ $leaveRequest->status === 'pending' ? 'warning' : ($leaveRequest->status === 'approved' ? 'success' : 'danger') }}">
                            {{ $leaveRequest->status_text }}
                        </span> |
                        <strong>تاريخ الإنشاء:</strong> {{ $leaveRequest->created_at->format('Y-m-d H:i') }}
                    </p>
                </div>
                <div class="col-md-4 text-right">
                    @if(!$leaveRequest->isPending())
                        <span class="text-muted">
                            <i class="fas fa-lock mr-1"></i>لا يمكن تعديل الطلبات المعتمدة أو المرفوضة
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <span class="text-muted">الحقول المميزة بعلامة <span class="text-danger">*</span> إلزامية</span>
                    </div>
                    <div class="btn-group" role="group">
                        <a href="{{ route('attendance.leave_requests.index') }}" class="btn btn-outline-danger">
                            <i class="fas fa-times"></i> إلغاء
                        </a>
                        @if($leaveRequest->isPending())
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> حفظ التعديلات
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Form Card -->
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="card-title mb-0">
                    <i class="fas fa-edit mr-2"></i>تعديل معلومات طلب الإجازة
                </h4>
            </div>

            <div class="card-body">
                <div class="row g-3 mb-4">
                    <!-- الموظف -->
                    <div class="col-md-6">
                        <label for="employee_id" class="form-label fw-bold">
                            <i class="fas fa-user text-primary mr-1"></i>الموظف <span class="text-danger">*</span>
                        </label>
                        <select class="form-control select2 @error('employee_id') is-invalid @enderror"
                                id="employee_id" name="employee_id" required {{ !$leaveRequest->isPending() ? 'disabled' : '' }}>
                            <option value="" disabled>اختر الموظف</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}"
                                        {{ old('employee_id', $leaveRequest->employee_id) == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->full_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('employee_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <div class="mt-2">
                            <a href="#" class="text-primary text-decoration-none" data-toggle="modal" data-target="#leaveBalanceModal">
                                <i class="fas fa-info-circle"></i> تفقد رصيد الإجازات
                            </a>
                        </div>
                    </div>

                    <!-- نوع الطلب -->
                    <div class="col-md-6">
                        <label for="request_type" class="form-label fw-bold">
                            <i class="fas fa-tag text-primary mr-1"></i>نوع الطلب <span class="text-danger">*</span>
                        </label>
                        <select class="form-control @error('request_type') is-invalid @enderror"
                                id="request_type" name="request_type" required {{ !$leaveRequest->isPending() ? 'disabled' : '' }}>
                            <option value="leave" {{ old('request_type', $leaveRequest->request_type) == 'leave' ? 'selected' : '' }}>إجازة عادية</option>
                            <option value="emergency" {{ old('request_type', $leaveRequest->request_type) == 'emergency' ? 'selected' : '' }}>إجازة طارئة</option>
                            <option value="sick" {{ old('request_type', $leaveRequest->request_type) == 'sick' ? 'selected' : '' }}>إجازة مرضية</option>
                        </select>
                        @error('request_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <!-- عدد الأيام -->
                    <div class="col-md-4">
                        <label for="days" class="form-label fw-bold">
                            <i class="fas fa-calendar-day text-primary mr-1"></i>عدد الأيام <span class="text-danger">*</span>
                        </label>
                        <input type="number" class="form-control @error('days') is-invalid @enderror"
                               id="days" name="days" min="1" max="365"
                               value="{{ old('days', $leaveRequest->days) }}" required
                               placeholder="أدخل عدد الأيام" {{ !$leaveRequest->isPending() ? 'readonly' : '' }}>
                        @error('days')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">سيتم حساب تاريخ الانتهاء تلقائيًا</small>
                    </div>

                    <!-- تاريخ البدء -->
                    <div class="col-md-4">
                        <label for="start_date" class="form-label fw-bold">
                            <i class="fas fa-calendar-plus text-primary mr-1"></i>تاريخ البدء <span class="text-danger">*</span>
                        </label>
                        <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                               id="start_date" name="start_date"
                               value="{{ old('start_date', \Carbon\Carbon::parse($leaveRequest->start_date)->format('Y-m-d')) }}"
                               required {{ !$leaveRequest->isPending() ? 'readonly' : '' }}>
                        @error('start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- تاريخ الانتهاء -->
                    <div class="col-md-4">
                        <label for="end_date" class="form-label fw-bold">
                            <i class="fas fa-calendar-minus text-primary mr-1"></i>تاريخ الانتهاء
                        </label>
                        <input type="date" class="form-control" id="end_date" name="end_date"
                               value="{{ old('end_date', \Carbon\Carbon::parse($leaveRequest->end_date)->format('Y-m-d')) }}"
                               readonly>
                        <small class="text-muted">يتم حسابه تلقائيًا</small>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <!-- نوع الإجازة -->
                    <div class="col-md-6">
                        <label for="leave_type_id" class="form-label fw-bold">
                            <i class="fas fa-list text-primary mr-1"></i>نوع الإجازة <span class="text-danger">*</span>
                        </label>
                        <select class="form-control @error('leave_type_id') is-invalid @enderror"
                                id="leave_type_id" name="leave_type_id" required {{ !$leaveRequest->isPending() ? 'disabled' : '' }}>
                            <option value="" disabled>اختر نوع الإجازة</option>
                            @foreach ($leaveTypes as $leaveType)
                                <option value="{{ $leaveType->id }}"
                                        {{ old('leave_type_id', $leaveRequest->leave_type_id) == $leaveType->id ? 'selected' : '' }}>
                                    {{ $leaveType->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('leave_type_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <div class="mt-2">
                            <div class="alert alert-info py-2 mb-0">
                                <i class="fas fa-info-circle mr-1"></i>
                                <span class="text-primary fw-bold">رصيد الإجازات: <span id="leave-balance">0</span> يوم</span>
                            </div>
                        </div>
                    </div>

                    <!-- المرفقات -->
                    <div class="col-md-6">
                        <label for="attachments" class="form-label fw-bold">
                            <i class="fas fa-paperclip text-primary mr-1"></i>المرفقات
                        </label>

                        <!-- المرفقات الحالية -->
                        @if($leaveRequest->attachments)
                            <div class="mb-3">
                                <label class="form-label text-muted">المرفقات الحالية:</label>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach(json_decode($leaveRequest->attachments) as $attachment)
                                        <div class="border rounded p-2 bg-light">
                                            <a href="{{ asset('storage/' . $attachment) }}" target="_blank" class="text-decoration-none">
                                                <i class="fas fa-file-alt mr-2 text-primary"></i>
                                                <span>{{ basename($attachment) }}</span>
                                            </a>
                                            @if($leaveRequest->isPending())
                                                <button type="button" class="btn btn-sm btn-outline-danger ml-2 remove-attachment"
                                                        data-attachment="{{ $attachment }}">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if($leaveRequest->isPending())
                            <input type="file" name="attachments[]" id="attachments" class="d-none" multiple
                                   accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">

                            <div class="upload-area border border-2 border-dashed rounded p-4 text-center position-relative cursor-pointer hover-shadow"
                                 onclick="document.getElementById('attachments').click()">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="fas fa-cloud-upload-alt text-primary fs-2 mb-2"></i>
                                    <div class="mb-2">
                                        <span class="text-primary fw-bold">اضغط هنا لإضافة مرفقات جديدة</span>
                                        <span class="text-muted">أو اسحب الملفات إلى هنا</span>
                                    </div>
                                    <small class="text-muted">
                                        الملفات المسموحة: PDF, DOC, DOCX, JPG, PNG (حد أقصى 5 ميجابايت لكل ملف)
                                    </small>
                                </div>
                            </div>

                            <div id="file-list" class="mt-2"></div>
                        @else
                            <div class="alert alert-warning py-2">
                                <i class="fas fa-lock mr-1"></i>
                                لا يمكن تعديل المرفقات بعد اعتماد الطلب
                            </div>
                        @endif
                    </div>
                </div>

                <!-- الوصف -->
                <div class="mb-3">
                    <label for="description" class="form-label fw-bold">
                        <i class="fas fa-align-left text-primary mr-1"></i>الوصف والملاحظات
                    </label>
                    <textarea class="form-control @error('description') is-invalid @enderror"
                              id="description" name="description" rows="4"
                              placeholder="أدخل وصف أو ملاحظات إضافية حول طلب الإجازة (اختياري)"
                              {{ !$leaveRequest->isPending() ? 'readonly' : '' }}>{{ old('description', $leaveRequest->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- معلومات الموافقة/الرفض -->
                @if(!$leaveRequest->isPending())
                    <div class="alert alert-{{ $leaveRequest->status === 'approved' ? 'success' : 'danger' }}">
                        <h6 class="alert-heading">
                            <i class="fas fa-{{ $leaveRequest->status === 'approved' ? 'check-circle' : 'times-circle' }} mr-1"></i>
                            {{ $leaveRequest->status === 'approved' ? 'تمت الموافقة على الطلب' : 'تم رفض الطلب' }}
                        </h6>
                        <p class="mb-1">
                            <strong>بواسطة:</strong> {{ $leaveRequest->approver->name ?? 'غير محدد' }} |
                            <strong>في:</strong> {{ $leaveRequest->approved_at ? $leaveRequest->approved_at->format('Y-m-d H:i') : 'غير محدد' }}
                        </p>
                        @if($leaveRequest->rejection_reason)
                            <p class="mb-0">
                                <strong>سبب الرفض:</strong> {{ $leaveRequest->rejection_reason }}
                            </p>
                        @endif
                    </div>
                @endif

            </div>
        </div>
    </form>

    <!-- مودال رصيد الإجازات -->
    <div class="modal fade" id="leaveBalanceModal" tabindex="-1" role="dialog" aria-labelledby="leaveBalanceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="leaveBalanceModalLabel">
                        <i class="fas fa-chart-pie mr-2"></i>رصيد الإجازات
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">جاري التحميل...</span>
                        </div>
                        <p class="mt-2">جاري تحميل بيانات الموظف...</p>
                    </div>

                    <div class="table-responsive d-none" id="balance-table-container">
                        <table class="table table-striped table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th><i class="fas fa-tag mr-1"></i>نوع الإجازة</th>
                                    <th><i class="fas fa-plus mr-1"></i>المستحق</th>
                                    <th><i class="fas fa-minus mr-1"></i>المستخدم</th>
                                    <th><i class="fas fa-balance-scale mr-1"></i>المتبقي</th>
                                </tr>
                            </thead>
                            <tbody id="leave-balance-details">
                                <!-- سيتم ملؤها بالجافاسكريبت -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i>إغلاق
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // عرض تنبيه SweetAlert عند وجود رسائل من السيرفر
            @if(session('sweet_alert'))
                const alert = @json(session('sweet_alert'));
                Swal.fire({
                    icon: alert.type,
                    title: alert.title,
                    text: alert.message,
                    confirmButtonText: 'موافق',
                    confirmButtonColor: '#3085d6'
                });
            @endif

            // تفعيل Select2
            $('.select2').select2({
                placeholder: 'اختر من القائمة',
                allowClear: true
            });

            // تأكيد الحفظ عند إرسال النموذج (فقط إذا كان الطلب في حالة انتظار)
            @if($leaveRequest->isPending())
                $('#leave-request-form').on('submit', function(e) {
                    e.preventDefault();

                    Swal.fire({
                        title: 'تأكيد التعديل',
                        text: 'هل أنت متأكد من حفظ التعديلات على طلب الإجازة؟',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'نعم، احفظ التعديلات',
                        cancelButtonText: 'إلغاء',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // عرض تنبيه التحميل
                            Swal.fire({
                                title: 'جاري حفظ التعديلات...',
                                text: 'يرجى الانتظار',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                showConfirmButton: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            // إرسال النموذج
                            this.submit();
                        }
                    });
                });

                // عرض الملفات المرفوعة الجديدة
                $('#attachments').change(function() {
                    let files = $(this)[0].files;
                    let fileList = $('#file-list');
                    fileList.empty();

                    if (files.length > 0) {
                        let filesHtml = '<div class="alert alert-success py-2 mb-0"><i class="fas fa-check mr-1"></i><strong>ملفات جديدة للإضافة:</strong><ul class="mb-0 mt-1">';

                        for (let i = 0; i < files.length; i++) {
                            filesHtml += `<li><i class="fas fa-file mr-2"></i>${files[i].name} (${(files[i].size / 1024 / 1024).toFixed(2)} MB)</li>`;
                        }

                        filesHtml += '</ul></div>';
                        fileList.html(filesHtml);
                    } else {
                        fileList.empty();
                    }
                });

                // حساب التواريخ عند تغيير تاريخ البدء أو عدد الأيام
                $('#start_date, #days').on('change', function() {
                    calculateDates();
                });

                // حذف مرفق موجود
                $('.remove-attachment').click(function() {
                    let attachment = $(this).data('attachment');
                    let attachmentElement = $(this).closest('.border');

                    Swal.fire({
                        title: 'حذف المرفق',
                        text: 'هل أنت متأكد من حذف هذا المرفق؟',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'نعم، احذف',
                        cancelButtonText: 'إلغاء'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // هنا يمكنك إضافة منطق حذف المرفق من الخادم
                            attachmentElement.fadeOut(300, function() {
                                $(this).remove();
                            });

                            Swal.fire({
                                icon: 'success',
                                title: 'تم الحذف',
                                text: 'تم حذف المرفق بنجاح',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        }
                    });
                });

                // تحسين تصميم منطقة رفع الملفات
                $('.upload-area').on('dragover', function(e) {
                    e.preventDefault();
                    $(this).addClass('border-primary bg-light');
                });

                $('.upload-area').on('dragleave', function(e) {
                    e.preventDefault();
                    $(this).removeClass('border-primary bg-light');
                });

                $('.upload-area').on('drop', function(e) {
                    e.preventDefault();
                    $(this).removeClass('border-primary bg-light');

                    let files = e.originalEvent.dataTransfer.files;
                    document.getElementById('attachments').files = files;
                    $('#attachments').trigger('change');
                });
            @endif

            // جلب رصيد الإجازات عند تحميل الصفحة أو تغيير الموظف
            function loadEmployeeBalance() {
                let employeeId = $('#employee_id').val();
                if (employeeId) {
                    $.ajax({
                        url: `/employees/${employeeId}/leave-balance`,
                        method: 'GET',
                        success: function(data) {
                            $('#leave-balance').text(data.balance);

                            // تحديث جدول رصيد الإجازات في المودال
                            let balanceDetails = '';
                            if (data.details && data.details.length > 0) {
                                data.details.forEach(function(item) {
                                    balanceDetails += `
                                        <tr>
                                            <td>${item.type}</td>
                                            <td><span class="badge badge-info">${item.entitled}</span></td>
                                            <td><span class="badge badge-warning">${item.used}</span></td>
                                            <td><span class="badge badge-${item.remaining > 0 ? 'success' : 'danger'}">${item.remaining}</span></td>
                                        </tr>
                                    `;
                                });
                            } else {
                                balanceDetails = '<tr><td colspan="4" class="text-center text-muted">لا يوجد بيانات متاحة</td></tr>';
                            }

                            $('#leave-balance-details').html(balanceDetails);
                            $('#balance-table-container').removeClass('d-none');
                            $('.spinner-border').parent().addClass('d-none');
                        },
                        error: function() {
                            $('#leave-balance').text('0');
                            $('#leave-balance-details').html('<tr><td colspan="4" class="text-center text-muted">حدث خطأ في تحميل البيانات</td></tr>');
                            $('#balance-table-container').removeClass('d-none');
                            $('.spinner-border').parent().addClass('d-none');
                        }
                    });
                }
            }

            // تحميل رصيد الإجازات عند تحميل الصفحة
            loadEmployeeBalance();

            // جلب رصيد الإجازات عند تغيير الموظف
            $('#employee_id').change(function() {
                $('#balance-table-container').addClass('d-none');
                $('.spinner-border').parent().removeClass('d-none');
                loadEmployeeBalance();
            });

            // دالة حساب تاريخ الانتهاء بناءً على تاريخ البدء وعدد الأيام
            function calculateDates() {
                let startDate = new Date($('#start_date').val());
                let days = parseInt($('#days').val());

                if (startDate && !isNaN(days) && days > 0) {
                    let endDate = new Date(startDate);
                    endDate.setDate(endDate.getDate() + days - 1);

                    // تحويل التاريخ إلى صيغة YYYY-MM-DD
                    let formattedDate = endDate.toISOString().split('T')[0];
                    $('#end_date').val(formattedDate);
                }
            }

            // تشغيل حساب التواريخ عند تحميل الصفحة إذا كانت هناك قيم موجودة
            if ($('#start_date').val() && $('#days').val()) {
                calculateDates();
            }
        });
    </script>

    <style>
        .hover-shadow:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: box-shadow 0.3s ease;
        }

        .cursor-pointer {
            cursor: pointer;
        }

        .upload-area {
            transition: all 0.3s ease;
        }

        .upload-area:hover {
            border-color: #007bff !important;
            background-color: #f8f9fa;
        }

        .fw-bold {
            font-weight: 600;
        }

        .gap-2 > * {
            margin: 0.25rem;
        }
    </style>
@endsection
