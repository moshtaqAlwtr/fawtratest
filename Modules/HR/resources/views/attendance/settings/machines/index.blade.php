@extends('master')

@section('title', 'الماكينات')

@section('content')
    <!-- Header Section -->
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">الماكينات</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">الرئيسية</a></li>
                            <li class="breadcrumb-item active">الماكينات</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">

        @include('layouts.alerts.success')
        @include('layouts.alerts.error')

        <!-- Search Card -->
        <div class="card">
            <div class="card-content">
                <div class="card-body">
                    <div class="card-title">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div>
                                <h4 class="mb-0">بحث وتصفية</h4>
                            </div>
                            <div>
                                <a href="{{ route('machines.create') }}" class="btn btn-outline-success">
                                    <i class="fa fa-plus me-2"></i>إضافة ماكينة جديدة
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <form class="form" id="searchForm">
                            @csrf
                            <div class="form-body row">
                                <div class="form-group col-md-4">
                                    <label for="name">البحث</label>
                                    <input type="text" id="name" class="form-control"
                                        placeholder="البحث باسم المكينة أو الرقم التسلسلي..." name="name"
                                        value="{{ request('name') }}">
                                </div>

                                <div class="form-group col-md-3">
                                    <label for="status">الحالة</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="">جميع الحالات</option>
                                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>نشط</option>
                                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>غير نشط</option>
                                    </select>
                                </div>

                                <div class="form-group col-md-3">
                                    <label for="machine_type">النوع</label>
                                    <select class="form-control" id="machine_type" name="machine_type">
                                        <option value="">كل الأنواع</option>
                                        <option value="zkteco" {{ request('machine_type') == 'zkteco' ? 'selected' : '' }}>ZkTeco</option>
                                        <option value="hikvision" {{ request('machine_type') == 'hikvision' ? 'selected' : '' }}>Hikvision</option>
                                        <option value="suprema" {{ request('machine_type') == 'suprema' ? 'selected' : '' }}>Suprema</option>
                                        <option value="other" {{ request('machine_type') == 'other' ? 'selected' : '' }}>أخرى</option>
                                    </select>
                                </div>

                                <div class="form-group col-md-2 d-flex align-items-end">
                                    <div class="w-100">
                                        <button type="submit" class="btn btn-primary btn-block">
                                            <span class="search-text">
                                                <i class="fa fa-search"></i> بحث
                                            </span>
                                            <span class="search-loading d-none">
                                                <i class="fa fa-spinner fa-spin"></i> جاري البحث...
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions">
                                <button type="button" id="clearFilter" class="btn btn-outline-warning">
                                    <i class="fa fa-times"></i> إعادة تعيين
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Results Card -->
        <div class="card mt-4">
            <div class="card-content">
                <div class="card-body">
                    <div class="table-responsive" id="tableContainer">
                        @include('hr::attendance.settings.machines.table-content', compact('machines'))
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // البحث بـ AJAX
            $('#searchForm').on('submit', function(e) {
                e.preventDefault();
                performSearch();
            });

            // البحث التلقائي أثناء الكتابة
            $('#name').on('input', function() {
                clearTimeout(window.searchTimeout);
                window.searchTimeout = setTimeout(function() {
                    performSearch();
                }, 500);
            });

            // البحث عند تغيير الفلاتر
            $('#status, #machine_type').on('change', function() {
                performSearch();
            });

            // مسح الفلتر
            $('#clearFilter').on('click', function() {
                $('#name').val('');
                $('#status').val('');
                $('#machine_type').val('');
                performSearch();
            });

            function performSearch() {
                const searchBtn = $('.search-text');
                const loadingBtn = $('.search-loading');

                // إظهار مؤشر التحميل
                searchBtn.addClass('d-none');
                loadingBtn.removeClass('d-none');

                const formData = {
                    name: $('#name').val(),
                    status: $('#status').val(),
                    machine_type: $('#machine_type').val(),
                    _token: $('input[name="_token"]').val()
                };

                $.ajax({
                    url: '{{ route('machines.search') }}',
                    method: 'GET',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            $('#tableContainer').html(response.html);

                            // إظهار عدد النتائج
                            const countText = response.count > 0 ?
                                `تم العثور على ${response.count} نتيجة` :
                                'لم يتم العثور على نتائج';

                            showAlert(countText, 'info', 2000);

                            // تحديث URL بدون إعادة تحميل الصفحة
                            updateUrl(formData);
                        } else {
                            showAlert('حدث خطأ أثناء البحث', 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        showAlert('حدث خطأ في الاتصال', 'error');
                        console.error('Search error:', error);
                    },
                    complete: function() {
                        // إخفاء مؤشر التحميل
                        searchBtn.removeClass('d-none');
                        loadingBtn.addClass('d-none');
                    }
                });
            }

            // تحديث URL
            function updateUrl(formData) {
                const newUrl = new URL(window.location);
                Object.keys(formData).forEach(key => {
                    if (formData[key] && key !== '_token') {
                        newUrl.searchParams.set(key, formData[key]);
                    } else if (key !== '_token') {
                        newUrl.searchParams.delete(key);
                    }
                });
                window.history.pushState({}, '', newUrl);
            }

            // إظهار التنبيهات
            function showAlert(message, type = 'success', duration = 3000) {
                let icon = type === 'error' ? 'error' : (type === 'info' ? 'info' : 'success');

                Swal.fire({
                    icon: icon,
                    title: message,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: duration,
                    timerProgressBar: true
                });
            }

            // جعل performSearch متاحة عالمياً
            window.performSearch = performSearch;

            // Display success/error messages from session
            @if (session('success'))
                showAlert('{{ session('success') }}', 'success');
            @endif

            @if (session('error'))
                showAlert('{{ session('error') }}', 'error');
            @endif

            @if (session('warning'))
                showAlert('{{ session('warning') }}', 'warning');
            @endif
        });

        // تفعيل/إلغاء تفعيل الماكينة
        function toggleStatus(machineId) {
            const toggle = document.querySelector(`[data-machine-id="${machineId}"]`);
            const isChecked = toggle.checked;
            const previousState = !isChecked;

            // تعطيل التبديل أثناء المعالجة
            toggle.disabled = true;

            fetch("{{ route('machines.toggle-status', ':id') }}".replace(':id', machineId), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                toggle.disabled = false;

                if (data.success) {
                    Swal.fire({
                        title: 'تم بنجاح!',
                        text: data.message,
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });

                    // تحديث الجدول فوراً
                    window.performSearch();
                } else {
                    // إرجاع الحالة السابقة
                    toggle.checked = previousState;
                    Swal.fire({
                        title: 'خطأ!',
                        text: data.message || 'حدث خطأ أثناء تغيير الحالة',
                        icon: 'error',
                        confirmButtonText: 'موافق'
                    });
                }
            })
            .catch(error => {
                console.error('Error details:', error);
                toggle.disabled = false;
                toggle.checked = previousState;

                Swal.fire({
                    title: 'خطأ!',
                    text: 'حدث خطأ في الاتصال بالخادم. يرجى المحاولة مرة أخرى.',
                    icon: 'error',
                    confirmButtonText: 'موافق'
                });
            });
        }

        // حذف الماكينة
        function deleteMachine(machineId, machineName) {
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: `سيتم حذف الماكينة "${machineName}" نهائياً ولا يمكن التراجع عن هذا الإجراء`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'نعم، احذف',
                cancelButtonText: 'إلغاء',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'جاري الحذف...',
                        text: 'يرجى الانتظار',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    fetch("{{ route('machines.destroy', ':id') }}".replace(':id', machineId), {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'تم الحذف!',
                                text: data.message,
                                icon: 'success',
                                confirmButtonText: 'موافق'
                            });

                            // تحديث الجدول فوراً
                            window.performSearch();
                        } else {
                            Swal.fire({
                                title: 'خطأ!',
                                text: data.message || 'حدث خطأ أثناء الحذف',
                                icon: 'error',
                                confirmButtonText: 'موافق'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error details:', error);
                        Swal.fire({
                            title: 'خطأ!',
                            text: 'حدث خطأ في الاتصال بالخادم. يرجى المحاولة مرة أخرى.',
                            icon: 'error',
                            confirmButtonText: 'موافق'
                        });
                    });
                }
            });
        }
    </script>
@endsection