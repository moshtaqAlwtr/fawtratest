@extends('master')

@section('title', 'تعديل نوع الإجازة')

@section('content')
    <!-- Header Section -->
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">تعديل نوع الإجازة</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">الرئيسية</a></li>
                            <li class="breadcrumb-item active">تعديل</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Actions -->
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <label>الحقول التي عليها علامة <span style="color: red">*</span> الزامية</label>
                </div>
                <div>
                    <button type="button" id="cancelBtn" class="btn btn-outline-danger">
                        <i class="fa fa-ban"></i> إلغاء
                    </button>
                    <button type="button" id="updateBtn" class="btn btn-outline-primary">
                        <i class="fa fa-save"></i> تحديث
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Section -->
    <div class="card">
        <div class="card-body">
            <h4 class="mb-4 p-1" style="background: #f8f8f8">معلومات نوع الإجازة</h4>
            <form id="editLeaveTypeForm" method="POST" action="{{ route('leave_types.update', $leaveType->id) }}">
                @csrf
                @method('PUT')
                <div class="row">
                    <!-- اسم نوع الإجازة -->
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">الاسم <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $leaveType->name) }}" required>
                        <div class="invalid-feedback">يرجى إدخال اسم نوع الإجازة</div>
                    </div>

                    <!-- اللون -->
                    <div class="col-md-6 mb-3">
                        <label for="colorDisplay" class="form-label">اللون <span class="text-danger">*</span></label>
                        <div class="row">
                            <div class="col-8">
                                <input type="text" disabled id="colorDisplay" class="form-control" value="{{ old('color', $leaveType->color) }}">
                                <input type="hidden" name="color" id="colorHidden" class="form-control" value="{{ old('color', $leaveType->color) }}">
                                <div class="invalid-feedback">يرجى اختيار لون للإجازة</div>
                            </div>
                            <div class="col-4">
                                <input type="text" id="colorPicker" class="form-control" value="{{ old('color', $leaveType->color) }}">
                            </div>
                        </div>
                    </div>

                    <!-- الحد الأقصى للأيام خلال العام -->
                    <div class="col-md-6 mb-3">
                        <label for="max_days_per_year" class="form-label">الحد الأقصى للأيام خلال العام <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="max_days_per_year" name="max_days_per_year" value="{{ old('max_days_per_year', $leaveType->max_days_per_year) }}" min="1" required>
                        <div class="invalid-feedback">يرجى إدخال عدد الأيام (أكبر من صفر)</div>
                    </div>

                    <!-- الحد الأقصى للأيام المتوالية -->
                    <div class="col-md-6 mb-3">
                        <label for="max_consecutive_days" class="form-label">الحد الأقصى للأيام المتوالية</label>
                        <input type="number" class="form-control" id="max_consecutive_days" name="max_consecutive_days" value="{{ old('max_consecutive_days', $leaveType->max_consecutive_days) }}" min="0">
                        <small class="text-muted">اتركه فارغاً إذا لم يكن هناك حد أقصى</small>
                    </div>

                    <!-- قابلة للتطبيق بعد -->
                    <div class="col-md-6 mb-3">
                        <label for="applicable_after" class="form-label">قابلة للتطبيق بعد (بالأيام)</label>
                        <input type="number" class="form-control" id="applicable_after" name="applicable_after" value="{{ old('applicable_after', $leaveType->applicable_after) }}" min="0">
                        <small class="text-muted">عدد الأيام المطلوبة قبل التمكن من طلب هذا النوع من الإجازة</small>
                    </div>

                    <!-- خانات الاختيار -->
                    <div class="col-md-6 mb-3">
                        <div class="row">
                            <div class="col-12 mb-2">
                                <div class="vs-checkbox-con vs-checkbox-primary">
                                    <input type="checkbox" id="requires_approval" name="requires_approval" value="1"
                                           {{ old('requires_approval', $leaveType->requires_approval) ? 'checked' : '' }}>
                                    <span class="vs-checkbox">
                                        <span class="vs-checkbox--check">
                                            <i class="vs-icon feather icon-check"></i>
                                        </span>
                                    </span>
                                    <label for="requires_approval">يحتاج إذن</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="vs-checkbox-con vs-checkbox-primary">
                                    <input type="checkbox" id="replace_weekends" name="replace_weekends" value="1"
                                           {{ old('replace_weekends', $leaveType->replace_weekends) ? 'checked' : '' }}>
                                    <span class="vs-checkbox">
                                        <span class="vs-checkbox--check">
                                            <i class="vs-icon feather icon-check"></i>
                                        </span>
                                    </span>
                                    <label for="replace_weekends">استبدال أيام عطلة نهاية الأسبوع</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- الوصف -->
                    <div class="col-md-12 mb-3">
                        <label for="description" class="form-label">الوصف</label>
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="أدخل وصف لنوع الإجازة...">{{ old('description', $leaveType->description) }}</textarea>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- معلومات إضافية إذا كان نوع الإجازة مستخدم -->
    @if(isset($usageInfo) && $usageInfo['total_requests'] > 0)
    <div class="card">
        <div class="card-body">
            <h4 class="mb-3 p-1" style="background: #fff3cd; color: #856404;">معلومات الاستخدام</h4>
            <div class="row">
                <div class="col-md-4">
                    <div class="alert alert-info">
                        <strong>إجمالي الطلبات:</strong> {{ $usageInfo['total_requests'] }}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="alert alert-success">
                        <strong>الطلبات المقبولة:</strong> {{ $usageInfo['approved_requests'] }}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="alert alert-warning">
                        <strong>الطلبات المعلقة:</strong> {{ $usageInfo['pending_requests'] }}
                    </div>
                </div>
            </div>
            <small class="text-warning">
                <i class="fa fa-exclamation-triangle"></i>
                تنبيه: هذا النوع من الإجازات قيد الاستخدام. قد تؤثر التغييرات على الطلبات الحالية.
            </small>
        </div>
    </div>
    @endif
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('editLeaveTypeForm');
        const originalValues = {
            name: '{{ $leaveType->name }}',
            color: '{{ $leaveType->color }}',
            max_days_per_year: '{{ $leaveType->max_days_per_year }}',
            max_consecutive_days: '{{ $leaveType->max_consecutive_days ?? '' }}',
            applicable_after: '{{ $leaveType->applicable_after ?? '' }}',
            requires_approval: {{ $leaveType->requires_approval ? 'true' : 'false' }},
            replace_weekends: {{ $leaveType->replace_weekends ? 'true' : 'false' }},
            description: '{{ $leaveType->description ?? '' }}'
        };

        // Initialize Color Picker
        $("#colorPicker").spectrum({
            showPalette: true,
            showInput: true,
            preferredFormat: "hex",
            palette: [
                ["#9a4d40", "#f44336", "#e91e63", "#9c27b0", "#673ab7"],
                ["#3f51b5", "#2196f3", "#03a9f4", "#00bcd4", "#009688"],
                ["#4caf50", "#8bc34a", "#cddc39", "#ffeb3b", "#ffc107"]
            ],
            i18n: {
                cancelText: "إلغاء",
                chooseText: "اختيار اللون",
                clearText: "مسح اللون",
                noColorSelectedText: "لم يتم اختيار لون"
            },
            change: function (color) {
                $("#colorDisplay").val(color.toHexString());
                $("#colorHidden").val(color.toHexString());
                $("#colorDisplay").removeClass('is-invalid');
            }
        });

        // Check if form has changes
        function hasChanges() {
            const currentValues = {
                name: document.getElementById('name').value,
                color: document.getElementById('colorHidden').value,
                max_days_per_year: document.getElementById('max_days_per_year').value,
                max_consecutive_days: document.getElementById('max_consecutive_days').value,
                applicable_after: document.getElementById('applicable_after').value,
                requires_approval: document.getElementById('requires_approval').checked,
                replace_weekends: document.getElementById('replace_weekends').checked,
                description: document.getElementById('description').value
            };

            return JSON.stringify(currentValues) !== JSON.stringify(originalValues);
        }

        // Cancel Button Functionality
        document.getElementById('cancelBtn').addEventListener('click', function () {
            if (hasChanges()) {
                Swal.fire({
                    title: 'هل أنت متأكد؟',
                    text: 'سيتم إلغاء جميع التغييرات التي قمت بها!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'نعم، إلغاء التغييرات',
                    cancelButtonText: 'لا، استمر في التعديل'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "{{ route('leave_types.index') }}";
                    }
                });
            } else {
                window.location.href = "{{ route('leave_types.index') }}";
            }
        });

        // Update Button Functionality
        document.getElementById('updateBtn').addEventListener('click', function () {
            if (!hasChanges()) {
                Swal.fire({
                    title: 'لا توجد تغييرات!',
                    text: 'لم تقم بإجراء أي تغييرات على البيانات',
                    icon: 'info',
                    confirmButtonText: 'موافق',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            if (!validateForm()) {
                return;
            }

            let confirmText = 'هل تريد حفظ التغييرات على نوع الإجازة؟';
            @if(isset($usageInfo) && $usageInfo['total_requests'] > 0)
                confirmText = 'هذا النوع من الإجازات قيد الاستخدام حالياً. هل تريد المتابعة؟';
            @endif

            Swal.fire({
                title: 'تأكيد التحديث',
                text: confirmText,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'نعم، احفظ التغييرات',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'جاري التحديث...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    form.submit();
                }
            });
        });

        // Form validation function
        function validateForm() {
            let isValid = true;
            const name = document.getElementById('name');
            const colorHidden = document.getElementById('colorHidden');
            const maxDaysPerYear = document.getElementById('max_days_per_year');
            const maxConsecutiveDays = document.getElementById('max_consecutive_days');
            const applicableAfter = document.getElementById('applicable_after');

            // Clear previous validation states
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

            // Validate name
            if (!name.value.trim()) {
                name.classList.add('is-invalid');
                isValid = false;
            }

            // Validate color
            if (!colorHidden.value) {
                document.getElementById('colorDisplay').classList.add('is-invalid');
                isValid = false;
            }

            // Validate max days per year
            if (!maxDaysPerYear.value || parseInt(maxDaysPerYear.value) <= 0) {
                maxDaysPerYear.classList.add('is-invalid');
                isValid = false;
            }

            // Validate max consecutive days (if provided)
            if (maxConsecutiveDays.value && parseInt(maxConsecutiveDays.value) < 0) {
                maxConsecutiveDays.classList.add('is-invalid');
                isValid = false;
            }

            // Validate applicable after (if provided)
            if (applicableAfter.value && parseInt(applicableAfter.value) < 0) {
                applicableAfter.classList.add('is-invalid');
                isValid = false;
            }

            if (!isValid) {
                Swal.fire({
                    title: 'خطأ في البيانات!',
                    text: 'يرجى ملء جميع الحقول المطلوبة بشكل صحيح',
                    icon: 'error',
                    confirmButtonText: 'موافق',
                    confirmButtonColor: '#d33'
                });
            }

            return isValid;
        }

        // Handle form submission response
        @if(session('success'))
            Swal.fire({
                title: 'تم التحديث بنجاح!',
                text: '{{ session('success') }}',
                icon: 'success',
                confirmButtonText: 'موافق',
                confirmButtonColor: '#28a745'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('leave_types.index') }}";
                }
            });
        @endif

        @if(session('error'))
            Swal.fire({
                title: 'حدث خطأ!',
                text: '{{ session('error') }}',
                icon: 'error',
                confirmButtonText: 'موافق',
                confirmButtonColor: '#d33'
            });
        @endif

        @if ($errors->any())
            let errorMessages = '';
            @foreach ($errors->all() as $error)
                errorMessages += '• {{ $error }}\n';
            @endforeach

            Swal.fire({
                title: 'أخطاء في البيانات!',
                text: errorMessages,
                icon: 'error',
                confirmButtonText: 'موافق',
                confirmButtonColor: '#d33'
            });
        @endif

        // Warn before leaving page with unsaved changes
        window.addEventListener('beforeunload', function (e) {
            if (hasChanges()) {
                e.preventDefault();
                e.returnValue = '';
            }
        });
    });
</script>
@endsection
