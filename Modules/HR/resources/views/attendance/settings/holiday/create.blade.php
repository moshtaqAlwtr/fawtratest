@extends('master')

@section('title', 'أضافة قوائم العطلات')

@section('content')
    <!-- Header Section -->
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">أضافة قوائم العطلات</h2>
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
            <h4 class="mb-4 p-1" style="background: #f8f8f8">معلومات قائمة العطلات</h4>
            <form id="addHolidayForm" method="POST" action="{{ route('holiday_lists.store') }}">
                @csrf
                <div class="row">
                    <div class="col-md-12">
                        <label for="name" class="form-label">الاسم <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                        <div class="invalid-feedback">يرجى إدخال اسم قائمة العطلات</div>
                    </div>
                </div>
                <br><br>
                <h4 class="mb-4 p-1" style="background: #f8f8f8">ايام العطلات</h4>
                <div class="col-md-6">
                    <p>عدد العطلات المختارة: <strong id="holidayCount">1</strong></p>
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
                        <tr>
                            <td style="width: 10%">1</td>
                            <td><input type="date" class="form-control" name="holiday_date[]" value="{{ old('holiday_date') }}" required></td>
                            <td><input type="text" class="form-control" name="named[]" value="{{ old('named') }}" required></td>
                            <td style="width: 10%">
                                <button type="button" class="btn btn-outline-danger btn-sm remove-row">إزالة <i class="fa fa-minus-circle"></i></button>
                            </td>
                        </tr>
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
        const form = document.getElementById('addHolidayForm');
        let rowCount = holidayTable.rows.length;

        // Function to update holiday count
        function updateHolidayCount() {
            holidayCount.textContent = holidayTable.rows.length;
            // Update serial numbers
            const rows = holidayTable.querySelectorAll('tr');
            rows.forEach((row, index) => {
                row.children[0].textContent = index + 1;
            });
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
                row.remove();
                updateHolidayCount();
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
                    window.location.href = "{{ route('holiday_lists.index') }}";
                }
            });
        });

        // Save Button Functionality
        document.getElementById('saveBtn').addEventListener('click', function () {
            // Validate form
            if (!validateForm()) {
                return;
            }

            Swal.fire({
                title: 'تأكيد الحفظ',
                text: 'هل تريد حفظ قائمة العطلات؟',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'نعم، احفظ',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'جاري الحفظ...',
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
                title: 'تم بنجاح!',
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
    });
</script>
@endsection