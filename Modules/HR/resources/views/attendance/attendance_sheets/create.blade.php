@extends('master')

@section('title', 'دفاتر الحضور')

@section('content')
    <!-- Header Section -->
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">دفاتر الحضور</h2>
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

    <div class="content-body">

        <!-- Form Actions -->
        <div class="card mb-3">
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

        @if ($errors->any())
            <div class="alert alert-danger d-none" role="alert" id="errorAlert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form Section -->
        <div class="card">
            <div class="card-body">
                <form action="{{ route('attendance_sheets.store') }}" method="POST" id="addAttendanceForm">
                    @csrf
                    <p style="background-color: #f8f8f8; padding: 10px"><strong>معلومات الحضور</strong></p>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="attendance-date" class="form-label">من <span style="color: red">*</span></label>
                            <input type="date" name="from_date" class="form-control" value="{{ old('from_date') }}">
                            @error('from_date')
                            <small class="text-danger" id="basic-default-name-error" class="error">
                                {{ $message }}
                            </small>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="attendance-date" class="form-label">الي <span style="color: red">*</span></label>
                            <input type="date" name="to_date" class="form-control" value="{{ old('to_date') }}">
                            @error('to_date')
                            <small class="text-danger" id="basic-default-name-error" class="error">
                                {{ $message }}
                            </small>
                            @enderror
                        </div>
                    </div>

                    <hr>

                    <!-- Attendance Status -->
                    <div class="mb-3">
                        <p class="mb-2"><strong>إختيار المعايير</strong></p>
                        <div class="row g-3">
                            <!-- Present -->
                            <div class="col-12">
                                <ul class="list-unstyled mb-0">
                                    <li class="d-inline-block mr-5">
                                        <fieldset>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" class="custom-control-input" name="use_rules" id="customRadio1" {{ old('use_rules') == 'rules' ? 'checked' : '' }} value="rules">
                                                <label class="custom-control-label" for="customRadio1">إختيار القواعد <span style="color: red">*</span></label>
                                            </div>
                                        </fieldset>
                                    </li>
                                    <li class="d-inline-block mr-5">
                                        <fieldset>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" class="custom-control-input" name="use_rules" id="customRadio2" {{ old('use_rules') == 'employees' ? 'checked' : '' }} value="employees">
                                                <label class="custom-control-label" for="customRadio2">إختيار الموظفين <span style="color: red">*</span></label>
                                            </div>
                                        </fieldset>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div id="employee" style="display: {{ old('use_rules') == 'employees' ? 'block' : 'none' }}">
                        <p style="background-color: #f8f8f8; padding: 10px"><strong>اختيار الموظفين</strong></p>

                        <div class="row g-3 mb-3">

                            <div class="col-md-6">
                                <label for="" selected>اختر الموظفين</label>
                                <select id="employee-select-employees" name="employee_id[]" class="form-control select2" multiple="multiple">
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ in_array($employee->id, old('employee_id', [])) ? 'selected' : '' }}>
                                            {{ $employee->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('employee_id')
                                <small class="text-danger" id="basic-default-name-error" class="error">
                                    {{ $message }}
                                </small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div id="rules"style="display: {{ old('use_rules') == 'rules' ? 'block' : 'none' }}">
                        <p style="background-color: #f8f8f8; padding: 10px"><strong>اختيار القواعد</strong></p>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for=""></label>
                                <select id="branch" name="branch_id" class="form-control select2">
                                    <option value="" disabled selected>اختر فرع</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for=""></label>
                                <select id="department" name="department_id" class="form-control select2">
                                    <option value="" disabled selected>اختر قسم</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for=""></label>
                                <select id="job_title" name="job_title_id" class="form-control select2">
                                    <option value="" disabled selected>اختر المسمى الوظيفي</option>
                                    @foreach ($job_titles as $job_title)
                                        <option value="{{ $job_title->id }}" {{ old('job_title_id') == $job_title->id ? 'selected' : '' }}>{{ $job_title->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for=""></label>
                                <select name="shifts_id" id="shifts_id" class="form-control select2">
                                    <option value="" disabled selected>اختر الوردية</option>
                                    @foreach ($shifts as $shift)
                                        <option value="{{ $shift->id }}" {{ old('shifts_id') == $shift->id ? 'selected' : '' }}>{{ $shift->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="">اختر الموظفين المستبعدين</label>
                                <select id="employee-select-rules" name="employee_id[]" class="form-control select2" multiple="multiple">
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ in_array($employee->id, old('employee_id', [])) ? 'selected' : '' }}>
                                            {{ $employee->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('employee_id')
                                <small class="text-danger" id="basic-default-name-error" class="error">
                                    {{ $message }}
                                </small>
                                @enderror
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>

    </div>

@endsection

@section('scripts')
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const radios = document.querySelectorAll('input[name="use_rules"]');
    const employeeSection = document.getElementById('employee');
    const rulesSection = document.getElementById('rules');
    const employeeInputs = employeeSection.querySelectorAll('input, select, textarea');
    const rulesInputs = rulesSection.querySelectorAll('input, select, textarea');
    const form = document.getElementById('addAttendanceForm');
    const saveBtn = document.getElementById('saveBtn');
    const cancelBtn = document.getElementById('cancelBtn');

    // SweetAlert2 Configuration in Arabic
    const swalConfig = {
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        reverseButtons: true,
        customClass: {
            confirmButton: 'btn btn-success mx-1',
            cancelButton: 'btn btn-danger mx-1'
        },
        buttonsStyling: false
    };

    function toggleInputs(section, inputs, isEnabled) {
        inputs.forEach(input => {
            if (isEnabled) {
                input.removeAttribute('disabled');
            } else {
                input.setAttribute('disabled', 'disabled');
            }
        });
    }

    // Validation function
    function validateForm() {
        const fromDate = document.querySelector('input[name="from_date"]').value;
        const toDate = document.querySelector('input[name="to_date"]').value;
        const useRules = document.querySelector('input[name="use_rules"]:checked')?.value;

        if (!fromDate) {
            Swal.fire({
                title: 'خطأ في البيانات',
                text: 'يرجى تحديد تاريخ البداية',
                icon: 'error',
                confirmButtonText: 'حسناً',
                ...swalConfig
            });
            return false;
        }

        if (!toDate) {
            Swal.fire({
                title: 'خطأ في البيانات',
                text: 'يرجى تحديد تاريخ النهاية',
                icon: 'error',
                confirmButtonText: 'حسناً',
                ...swalConfig
            });
            return false;
        }

        if (new Date(fromDate) > new Date(toDate)) {
            Swal.fire({
                title: 'خطأ في التواريخ',
                text: 'تاريخ البداية يجب أن يكون أقل من أو يساوي تاريخ النهاية',
                icon: 'error',
                confirmButtonText: 'حسناً',
                ...swalConfig
            });
            return false;
        }

        if (!useRules) {
            Swal.fire({
                title: 'خطأ في البيانات',
                text: 'يرجى اختيار طريقة إضافة الموظفين (قواعد أو موظفين محددين)',
                icon: 'error',
                confirmButtonText: 'حسناً',
                ...swalConfig
            });
            return false;
        }

        if (useRules === 'employees') {
            const selectedEmployees = document.querySelector('#employee-select-employees').value;
            if (!selectedEmployees || selectedEmployees.length === 0) {
                Swal.fire({
                    title: 'خطأ في البيانات',
                    text: 'يرجى اختيار موظف واحد على الأقل',
                    icon: 'error',
                    confirmButtonText: 'حسناً',
                    ...swalConfig
                });
                return false;
            }
        }

        return true;
    }

    radios.forEach(radio => {
        radio.addEventListener('change', function () {
            if (this.id === 'customRadio1') {
                employeeSection.style.display = 'none';
                rulesSection.style.display = 'block';

                toggleInputs(employeeSection, employeeInputs, false);
                toggleInputs(rulesSection, rulesInputs, true);
            } else if (this.id === 'customRadio2') {
                employeeSection.style.display = 'block';
                rulesSection.style.display = 'none';

                toggleInputs(employeeSection, employeeInputs, true);
                toggleInputs(rulesSection, rulesInputs, false);
            }

            $('#employee-select-employees').select2({
                allowClear: true,
                placeholder: 'اختر الموظفين'
            });

            $('#employee-select-rules').select2({
                allowClear: true,
                placeholder: 'اختر الموظفين المستبعدين'
            });
        });
    });

    // Handle Save Button Click with SweetAlert2 Confirmation and Validation
    saveBtn.addEventListener('click', function() {
        // Validate form first
        if (!validateForm()) {
            return;
        }

        const useRules = document.querySelector('input[name="use_rules"]:checked').value;
        let confirmationText = 'هل أنت متأكد من حفظ بيانات دفتر الحضور؟';

        if (useRules === 'employees') {
            const selectedEmployees = $('#employee-select-employees').select2('data');
            const employeeCount = selectedEmployees.length;
            confirmationText = `سيتم إنشاء ${employeeCount} دفتر حضور منفصل للموظفين المحددين. هل أنت متأكد؟`;
        }

        Swal.fire({
            title: 'تأكيد الحفظ',
            text: confirmationText,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'نعم، احفظ',
            cancelButtonText: 'إلغاء',
            ...swalConfig
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'جاري الحفظ...',
                    text: 'يرجى الانتظار',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Submit the form
                form.submit();
            }
        });
    });

    // Handle Cancel Button Click with SweetAlert2 Confirmation
    cancelBtn.addEventListener('click', function() {
        Swal.fire({
            title: 'تأكيد الإلغاء',
            text: 'هل أنت متأكد من إلغاء العملية؟ سيتم فقدان جميع البيانات المدخلة.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'نعم، ألغي',
            cancelButtonText: 'لا، تراجع',
            ...swalConfig
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "{{ route('attendance_sheets.index') }}";
            }
        });
    });

    // عند تحميل الصفحة، تحقق من الحالة الحالية
    if (document.getElementById('customRadio1').checked) {
        toggleInputs(employeeSection, employeeInputs, false);
        toggleInputs(rulesSection, rulesInputs, true);
    } else if (document.getElementById('customRadio2').checked) {
        toggleInputs(employeeSection, employeeInputs, true);
        toggleInputs(rulesSection, rulesInputs, false);
    }

    // Handle Server Messages with Enhanced SweetAlert2
    @if ($errors->any())
        let errorMessages = '';
        @foreach ($errors->all() as $error)
            errorMessages += '• {{ $error }}\n';
        @endforeach

        Swal.fire({
            title: 'خطأ في البيانات',
            html: errorMessages.replace(/\n/g, '<br>'),
            icon: 'error',
            confirmButtonText: 'حسناً',
            width: '500px',
            ...swalConfig
        });
    @endif

    @if (session('success'))
        Swal.fire({
            title: 'تم بنجاح!',
            text: '{{ session('success') }}',
            icon: 'success',
            confirmButtonText: 'ممتاز',
            timer: 5000,
            timerProgressBar: true,
            ...swalConfig
        });
    @endif

    @if (session('info'))
        Swal.fire({
            title: 'معلومة',
            text: '{{ session('info') }}',
            icon: 'info',
            confirmButtonText: 'حسناً',
            ...swalConfig
        });
    @endif

    @if (session('warning'))
        Swal.fire({
            title: 'تنبيه',
            html: '{{ session('warning') }}'.replace(/\n/g, '<br>'),
            icon: 'warning',
            confirmButtonText: 'حسناً',
            width: '600px',
            ...swalConfig
        });
    @endif

    @if (session('error'))
        Swal.fire({
            title: 'خطأ',
            text: '{{ session('error') }}',
            icon: 'error',
            confirmButtonText: 'حسناً',
            ...swalConfig
        });
    @endif
});
</script>
@endsection
