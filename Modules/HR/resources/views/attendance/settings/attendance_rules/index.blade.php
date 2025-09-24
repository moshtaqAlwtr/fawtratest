@extends('master')

@section('title', 'قواعد الحضور')

@section('content')

    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">قواعد الحضور</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسية</a></li>
                            <li class="breadcrumb-item active">قواعد الحضور</li>
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
                                <a href="{{ route('attendance-rules.create') }}" class="btn btn-outline-primary">
                                    <i class="fa fa-plus me-2"></i>إضافة قاعدة جديدة
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <form class="form" id="searchForm">
                            @csrf
                            <div class="form-body row">
                                <div class="form-group col-md-4">
                                    <label for="keywords">البحث</label>
                                    <input type="text" id="keywords" class="form-control"
                                        placeholder="البحث بالاسم أو الوصف..." name="keywords"
                                        value="{{ request('keywords') }}">
                                </div>

                                <div class="form-group col-md-3">
                                    <label for="status">الحالة</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="">جميع الحالات</option>
                                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط
                                        </option>
                                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>غير
                                            نشط</option>
                                    </select>
                                </div>

                                <div class="form-group col-md-3">
                                    <label for="shift">الوردية</label>
                                    <select class="form-control" id="shift" name="shift">
                                        <option value="">جميع الورديات</option>
                                        @foreach ($shifts as $shift)
                                            <option value="{{ $shift->id }}"
                                                {{ request('shift') == $shift->id ? 'selected' : '' }}>
                                                {{ $shift->name }}
                                            </option>
                                        @endforeach
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
                                <button type="button" id="clearFilter" class="btn btn-outline-danger">
                                    <i class="fa fa-refresh"></i> إعادة تعيين
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Results Card -->
        <div class="card">
            <div class="card-content">
                <div class="card-body">
                    <div class="table-responsive" id="tableContainer">
                        @include(
                            'hr::attendance.settings.attendance_rules.table-content',
                            compact('attendanceRules'))
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
            $('#keywords').on('input', function() {
                clearTimeout(window.searchTimeout);
                window.searchTimeout = setTimeout(function() {
                    performSearch();
                }, 500);
            });

            // البحث عند تغيير الفلاتر
            $('#status, #shift').on('change', function() {
                performSearch();
            });

            // مسح الفلتر
            $('#clearFilter').on('click', function() {
                $('#keywords').val('');
                $('#status').val('');
                $('#shift').val('');
                performSearch();
            });

            function performSearch() {
                const searchBtn = $('.search-text');
                const loadingBtn = $('.search-loading');

                // إظهار مؤشر التحميل
                searchBtn.addClass('d-none');
                loadingBtn.removeClass('d-none');

                const formData = {
                    keywords: $('#keywords').val(),
                    status: $('#status').val(),
                    shift: $('#shift').val(),
                    _token: $('input[name="_token"]').val()
                };

                $.ajax({
                    url: '{{ route('attendance-rules.search') }}',
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

            // Display success/error messages from session
            @if (session('success'))
                showAlert('{{ session('success') }}', 'success');
            @endif

            @if (session('error'))
                showAlert('{{ session('error') }}', 'error');
            @endif
        });

        // تفعيل/إلغاء تفعيل القاعدة
    function toggleStatus(ruleId, currentStatus) {
    const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
    const statusText = newStatus === 'active' ? 'تفعيل' : 'إلغاء تفعيل';

    Swal.fire({
        title: 'هل أنت متأكد؟',
        text: `سيتم ${statusText} قاعدة الحضور`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: `نعم، ${statusText}`,
        cancelButtonText: 'إلغاء',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "{{ route('attendance-rules.toggle-status', ':id') }}".replace(':id', ruleId),
                method: 'PATCH',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    console.log('Response received:', response); // للتصحيح

                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'تم بنجاح!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });

                        // تحديث الجدول فوراً بدلاً من انتظار
                        performSearch();
                    } else {
                        Swal.fire('خطأ!', response.message || 'حدث خطأ غير معروف', 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', xhr.responseText); // للتصحيح

                    let errorMessage = 'حدث خطأ أثناء تغيير الحالة';

                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }

                    Swal.fire('خطأ!', errorMessage, 'error');
                }
            });
        }
    });
}
      function deleteRule(ruleId, ruleName) {
    Swal.fire({
        title: 'هل أنت متأكد؟',
        text: `سيتم حذف قاعدة الحضور "${ruleName}" نهائياً`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'نعم، احذف',
        cancelButtonText: 'إلغاء',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "{{ route('attendance-rules.destroy', ':id') }}".replace(':id', ruleId),
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    console.log('Delete Response:', response); // للتصحيح

                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'تم الحذف!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });

                        // تحديث الجدول فوراً
                        performSearch();
                    } else {
                        Swal.fire('خطأ!', response.message || 'فشل في حذف القاعدة', 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Delete Error:', xhr.responseText); // للتصحيح

                    let errorMessage = 'حدث خطأ أثناء الحذف';

                    // تحقق من وجود رسالة خطأ مفصلة
                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.responseJSON.error) {
                            errorMessage = xhr.responseJSON.error;
                        }
                    } else if (xhr.status === 404) {
                        errorMessage = 'القاعدة غير موجودة';
                    } else if (xhr.status === 403) {
                        errorMessage = 'ليس لديك صلاحية لحذف هذه القاعدة';
                    }

                    Swal.fire('خطأ!', errorMessage, 'error');
                }
            });
        }
    });
}
    </script>
@endsection
