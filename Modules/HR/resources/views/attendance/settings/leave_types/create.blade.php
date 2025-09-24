@extends('master')

@section('title', 'إضافة نوع الإجازة')

@section('content')
    <!-- Header Section -->
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">إضافة نوع الإجازة</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">الرئيسية</a></li>
                            <li class="breadcrumb-item active">إضافة</li>
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
                    <button type="button" id="saveBtn" class="btn btn-outline-primary">
                        <i class="fa fa-save"></i> حفظ
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Section -->
    <div class="card">
        <div class="card-body">
            <h4 class="mb-4 p-1" style="background: #f8f8f8">معلومات نوع الإجازة</h4>
            <form id="addLeaveTypeForm" method="POST" action="{{ route('leave_types.store') }}">
                @csrf
                <div class="row">
                    <!-- اسم نوع الإجازة -->
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">الاسم <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                        <div class="invalid-feedback">يرجى إدخال اسم نوع الإجازة</div>
                    </div>

                    <!-- اللون -->
                    <div class="col-md-6 mb-3">
                        <label for="colorDisplay" class="form-label">اللون <span class="text-danger">*</span></label>
                        <div class="row">
                            <div class="col-8">
                                <input type="text" disabled id="colorDisplay" class="form-control" value="#9a4d40">
                                <input type="hidden" name="color" id="colorHidden" class="form-control" value="#9a4d40">
                                <div class="invalid-feedback">يرجى اختيار لون للإجازة</div>
                            </div>
                            <div class="col-4">
                                <input type="text" id="colorPicker" class="form-control" value="#9a4d40">
                            </div>
                        </div>
                    </div>

                    <!-- الحد الأقصى للأيام خلال العام -->
                    <div class="col-md-6 mb-3">
                        <label for="max_days_per_year" class="form-label">الحد الأقصى للأيام خلال العام <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="max_days_per_year" name="max_days_per_year" value="{{ old('max_days_per_year') }}" min="1" required>
                        <div class="invalid-feedback">يرجى إدخال عدد الأيام (أكبر من صفر)</div>
                    </div>

                    <!-- الحد الأقصى للأيام المتوالية -->
                    <div class="col-md-6 mb-3">
                        <label for="max_consecutive_days" class="form-label">الحد الأقصى للأيام المتوالية</label>
                        <input type="number" class="form-control" id="max_consecutive_days" name="max_consecutive_days" value="{{ old('max_consecutive_days') }}" min="0">
                        <small class="text-muted">اتركه فارغاً إذا لم يكن هناك حد أقصى</small>
                    </div>

                    <!-- قابلة للتطبيق بعد -->
                    <div class="col-md-6 mb-3">
                        <label for="applicable_after" class="form-label">قابلة للتطبيق بعد (بالأيام)</label>
                        <input type="number" class="form-control" id="applicable_after" name="applicable_after" value="{{ old('applicable_after') }}" min="0">
                        <small class="text-muted">عدد الأيام المطلوبة قبل التمكن من طلب هذا النوع من الإجازة</small>
                    </div>

                    <!-- خانات الاختيار -->
                    <div class="col-md-6 mb-3">
                        <div class="row">
                            <div class="col-12 mb-2">
                                <div class="vs-checkbox-con vs-checkbox-primary">
                                    <input type="checkbox" id="requires_approval" name="requires_approval" value="1" {{ old('requires_approval') ? 'checked' : '' }}>
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
                                    <input type="checkbox" id="replace_weekends" name="replace_weekends" value="1" {{ old('replace_weekends') ? 'checked' : '' }}>
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
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="أدخل وصف لنوع الإجازة...">{{ old('description') }}</textarea>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('addLeaveTypeForm');

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

        // Cancel Button Functionality
        document.getElementById('cancelBtn').addEventListener('click', function () {
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: 'سيتم إلغاء جميع التغييرات التي قمت بها!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'نعم، إلغاء',
                cancelButtonText: 'لا، استمر'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('leave_types.index') }}";
                }
            });
        });

        // Save Button Functionality
        document.getElementById('saveBtn').addEventListener('click', function () {
            if (!validateForm()) {
                return;
            }

            Swal.fire({
                title: 'تأكيد الحفظ',
                text: 'هل تريد حفظ نوع الإجازة؟',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'نعم، احفظ',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'جاري الحفظ...',
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
                title: 'تم بنجاح!',
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
    });
</script>
@endsection
