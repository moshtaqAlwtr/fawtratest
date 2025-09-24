@extends('master')

@section('title', 'تعديل قوائم العطلات')

@section('content')
    <!-- Header Section -->
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">تعديل قوائم العطلات</h2>
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
            <h4 class="mb-4 p-1" style="background: #f8f8f8">معلومات قائمة العطلات</h4>
            <form id="editHolidayForm" method="POST" action="{{ route('holiday_lists.update', $holiday_list->id) }}">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-12">
                        <label for="name" class="form-label">الاسم <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $holiday_list->name) }}" required>
                        <div class="invalid-feedback">يرجى إدخال اسم قائمة العطلات</div>
                    </div>
                </div>
                <br><br>
                <h4 class="mb-4 p-1" style="background: #f8f8f8">ايام العطلات</h4>
                <div class="col-md-6">
                    <p>عدد العطلات المختارة: <strong id="holidayCount">{{ $holiday_list->holidays->count() }}</strong></p>
                </div>
                <table class="table table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>الرقم المتسلسل</th>
                            <th>التاريخ</th>
                            <th>مسمى</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="holidayTable">
                        @foreach ($holiday_list->holidays as $index => $holiday)
                            <tr>
                                <td style="width: 10%">{{ $index + 1 }}</td>
                                <td><input type="date" class="form-control" name="holiday_date[]" value="{{ $holiday->holiday_date }}" required></td>
                                <td><input type="text" class="form-control" name="named[]" value="{{ $holiday->named }}" required></td>
                                <td style="width: 10%">
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-row">إزالة <i class="fa fa-minus-circle"></i></button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="row">
                    <div class="col-md-6">
                        <button type="button" id="addRow" class="btn btn-outline-success btn-sm">اضافة <i class="fa fa-plus-circle"></i></button>
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
        const holidayTable = document.getElementById('holidayTable');
        const holidayCount = document.getElementById('holidayCount');
        const form = document.getElementById('editHolidayForm');
        let rowCount = holidayTable.rows.length;

        // Store original form data for comparison
        const originalData = new FormData(form);

        // Function to update holiday count
        function updateHolidayCount() {
            holidayCount.textContent = holidayTable.rows.length;
            // Update serial numbers
            const rows = holidayTable.querySelectorAll('tr');
            rows.forEach((row, index) => {
                row.children[0].textContent = index + 1;
            });
        }

        // Function to check if form has changes
        function hasChanges() {
            const currentData = new FormData(form);
            const originalEntries = [...originalData.entries()];
            const currentEntries = [...currentData.entries()];

            if (originalEntries.length !== currentEntries.length) {
                return true;
            }

            for (let i = 0; i < originalEntries.length; i++) {
                if (originalEntries[i][0] !== currentEntries[i][0] ||
                    originalEntries[i][1] !== currentEntries[i][1]) {
                    return true;
                }
            }
            return false;
        }

        // Add Row Functionality
        document.getElementById('addRow').addEventListener('click', function () {
            rowCount++;
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td style="width: 10%">${rowCount}</td>
                <td><input type="date" class="form-control" name="holiday_date[]" required></td>
                <td><input type="text" class="form-control" name="named[]" required></td>
                <td style="width: 10%">
                    <button type="button" class="btn btn-outline-danger btn-sm remove-row">إزالة <i class="fa fa-minus-circle"></i></button>
                </td>
            `;
            holidayTable.appendChild(newRow);
            updateHolidayCount();
        });

        // Remove Row Functionality
        holidayTable.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-row') || e.target.closest('.remove-row')) {
                if (holidayTable.rows.length === 1) {
                    Swal.fire({
                        title: 'تنبيه!',
                        text: 'لا يمكن حذف آخر عطلة. يجب أن تحتوي القائمة على عطلة واحدة على الأقل.',
                        icon: 'warning',
                        confirmButtonText: 'موافق',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }

                const row = e.target.closest('tr');
                Swal.fire({
                    title: 'تأكيد الحذف',
                    text: 'هل تريد حذف هذه العطلة؟',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'نعم، احذف',
                    cancelButtonText: 'إلغاء'
                }).then((result) => {
                    if (result.isConfirmed) {
                        row.remove();
                        updateHolidayCount();
                    }
                });
            }
        });

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
                        window.location.href = "{{ route('holiday_lists.index') }}";
                    }
                });
            } else {
                window.location.href = "{{ route('holiday_lists.index') }}";
            }
        });

        // Update Button Functionality
        document.getElementById('updateBtn').addEventListener('click', function () {
            // Validate form
            if (!validateForm()) {
                return;
            }

            if (!hasChanges()) {
                Swal.fire({
                    title: 'لا توجد تغييرات!',
                    text: 'لم يتم إجراء أي تعديلات على البيانات',
                    icon: 'info',
                    confirmButtonText: 'موافق',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            Swal.fire({
                title: 'تأكيد التحديث',
                text: 'هل تريد حفظ التغييرات؟',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'نعم، احفظ التغييرات',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'جاري التحديث...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Submit form
                    form.submit();
                }
            });
        });

        // Form validation function
        function validateForm() {
            let isValid = true;
            const name = document.getElementById('name');
            const holidayDates = document.querySelectorAll('input[name="holiday_date[]"]');
            const holidayNames = document.querySelectorAll('input[name="named[]"]');

            // Validate name
            if (!name.value.trim()) {
                name.classList.add('is-invalid');
                isValid = false;
            } else {
                name.classList.remove('is-invalid');
            }

            // Validate holiday dates and names
            holidayDates.forEach((input, index) => {
                if (!input.value) {
                    input.classList.add('is-invalid');
                    isValid = false;
                } else {
                    input.classList.remove('is-invalid');
                }
            });

            holidayNames.forEach((input, index) => {
                if (!input.value.trim()) {
                    input.classList.add('is-invalid');
                    isValid = false;
                } else {
                    input.classList.remove('is-invalid');
                }
            });

            // Check for duplicate dates
            const dates = [...holidayDates].map(input => input.value).filter(date => date);
            const uniqueDates = [...new Set(dates)];
            if (dates.length !== uniqueDates.length) {
                isValid = false;
                Swal.fire({
                    title: 'تواريخ مكررة!',
                    text: 'يوجد تواريخ مكررة في قائمة العطلات',
                    icon: 'error',
                    confirmButtonText: 'موافق',
                    confirmButtonColor: '#d33'
                });
                return false;
            }

            if (!isValid) {
                Swal.fire({
                    title: 'خطأ في البيانات!',
                    text: 'يرجى ملء جميع الحقول المطلوبة',
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
                    window.location.href = "{{ route('holiday_lists.index') }}";
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

        // Warn user before leaving page if there are unsaved changes
        window.addEventListener('beforeunload', function (e) {
            if (hasChanges()) {
                e.preventDefault();
                e.returnValue = 'لديك تغييرات غير محفوظة. هل تريد المغادرة؟';
            }
        });
    });
</script>
@endsection