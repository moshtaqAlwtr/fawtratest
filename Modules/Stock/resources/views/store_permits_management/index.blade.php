@extends('master')

@section('title')
    الأذون المخزنية
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">الأذون المخزنية</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a></li>
                            <li class="breadcrumb-item active">عرض</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.alerts.error')
    @include('layouts.alerts.success')

    <div class="content-body">
        <!-- شريط الأدوات العلوي -->
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <!-- معلومات الترقيم -->
                        <nav aria-label="Page navigation">
                            <ul class="pagination pagination-sm mb-0">
                                <li class="page-item mx-2">
                                    <span class="text-muted pagination-info">
                                        صفحة {{ $wareHousePermits->currentPage() }} من {{ $wareHousePermits->lastPage() }}
                                    </span>
                                </li>
                            </ul>
                        </nav>

                        <!-- عداد النتائج -->
                        <span class="text-muted mx-2 results-info">
                            {{ $wareHousePermits->total() }} نتيجة
                        </span>

                        <!-- إحصائيات سريعة -->
                        <div class="d-flex gap-2 mx-3">
                            <span class="badge bg-warning">قيد الانتظار: {{ $stats['pending'] ?? 0 }}</span>
                            <span class="badge bg-success">موافق: {{ $stats['approved'] ?? 0 }}</span>
                            <span class="badge bg-danger">مرفوض: {{ $stats['rejected'] ?? 0 }}</span>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <div class="btn-group dropdown">
                            <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-plus me-1"></i>
                                إضافة إذن مخزني
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="{{ route('store_permits_management.create') }}">
                                    <i class="fa fa-plus me-2"></i>إضافة يدوي
                                </a>
                                <a class="dropdown-item" href="{{ route('store_permits_management.manual_disbursement') }}">
                                    <i class="fa fa-minus me-2"></i>صرف يدوي
                                </a>
                                <a class="dropdown-item" href="{{ route('store_permits_management.manual_conversion') }}">
                                    <i class="fa fa-exchange me-2"></i>تحويل يدوي
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- نموذج البحث والفلترة -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center p-2">
                <div class="d-flex gap-2">
                    <span class="hide-button-text">بحث وتصفية</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-outline-secondary btn-sm" onclick="toggleSearchFields(this)">
                        <i class="fa fa-times"></i>
                        <span class="hide-button-text">اخفاء</span>
                    </button>
                    <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="collapse"
                        data-bs-target="#advancedSearchForm" onclick="toggleSearchText(this)">
                        <i class="fa fa-filter"></i>
                        <span class="button-text">متقدم</span>
                    </button>
                    <button type="button" id="resetSearch" class="btn btn-outline-warning btn-sm">
                        <i class="fa fa-refresh"></i>
                        إعادة تعيين
                    </button>
                </div>
            </div>

            <div class="card-body">
                <form class="form" id="searchForm" method="GET" action="{{ route('store_permits_management.index') }}">
                    @csrf
                    <div class="row g-3">
                        <!-- الحقول الأساسية -->
                        <div class="col-md-4">
                            <select name="branch" class="form-control select2">
                                <option value="">جميع الفروع</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}"
                                        {{ request('branch') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <input type="text" name="keywords" class="form-control"
                                placeholder="البحث بالرقم أو التفاصيل" value="{{ request('keywords') }}">
                        </div>

                        <div class="col-md-4">
                         <select name="permission_source" class="form-control select2">
    <option value="">مصدر الإذن</option>
    @foreach ($permissionSources as $permissionSource)
        <option value="{{ $permissionSource->id }}"
            {{ request('permission_source') == $permissionSource->id ? 'selected' : '' }}>
            {{ $permissionSource->name }}
        </option>
    @endforeach
</select>
                        </div>

                        <div class="col-md-4">
                            <input type="text" name="id" class="form-control" placeholder="الرقم المعرف"
                                value="{{ request('id') }}">
                        </div>

                        <div class="col-md-4">
                            <select name="store_house" class="form-control select2">
                                <option value="">جميع المستودعات</option>
                                @foreach ($storeHouses as $storeHouse)
                                    <option value="{{ $storeHouse->id }}"
                                        {{ request('store_house') == $storeHouse->id ? 'selected' : '' }}>
                                        {{ $storeHouse->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <select name="status" class="form-control select2">
                                <option value="">جميع الحالات</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار
                                </option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>موافق عليه
                                </option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض
                                </option>
                                <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>قيد
                                    المعالجة</option>
                            </select>
                        </div>
                    </div>

                    <!-- حقول البحث المتقدم -->
                    <div class="collapse" id="advancedSearchForm">
                        <div class="row g-3 mt-2">
                            <div class="col-md-4">
                                <select name="permission_type" class="form-control select2">
                                    <option value="">نوع الإذن</option>
                                    <option value="1" {{ request('permission_type') == '1' ? 'selected' : '' }}>إذن
                                        إضافة</option>
                                    <option value="2" {{ request('permission_type') == '2' ? 'selected' : '' }}>إذن
                                        صرف</option>
                                    <option value="3" {{ request('permission_type') == '3' ? 'selected' : '' }}>تحويل
                                        يدوي</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <select name="client" class="form-control select2">
                                    <option value="">اختر العميل</option>
                                    @foreach ($clients as $client)
                                        <option value="{{ $client->id }}"
                                            {{ request('client') == $client->id ? 'selected' : '' }}>
                                            {{ $client->trade_name }}{{ $client->code ?? '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <select name="supplier" class="form-control select2">
                                    <option value="">اختر المورد</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}"
                                            {{ request('supplier') == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->trade_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <select name="created_by" class="form-control select2">
                                    <option value="">أضيفت بواسطة</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}"
                                            {{ request('created_by') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <select name="product" class="form-control select2">
                                    <option value="">اختر المنتج</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}"
                                            {{ request('product') == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <input type="date" name="from_date" class="form-control"
                                    value="{{ request('from_date') }}" placeholder="من تاريخ">
                            </div>

                            <div class="col-md-4">
                                <input type="date" name="to_date" class="form-control"
                                    value="{{ request('to_date') }}" placeholder="إلى تاريخ">
                            </div>
                        </div>
                    </div>

                    <div class="form-actions mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-search me-1"></i>بحث
                        </button>
                        <button type="button" id="resetSearchBtn" class="btn btn-outline-warning">
                            <i class="fa fa-refresh me-1"></i>إلغاء
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- جدول النتائج -->
        <div class="card">
            <div class="card-body">
                <div id="results-container">
                    @include('stock::store_permits_management.partials.table', [
                        'wareHousePermits' => $wareHousePermits,
                    ])
                </div>
            </div>
        </div>
    </div>

@endsection

@section('css')
    <style>
        .form-control {
            margin-bottom: 10px;
        }

        #loading-indicator {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 0.375rem;
        }

        .spinner-border {
            width: 2rem;
            height: 2rem;
        }

        .badge {
            font-size: 0.75em;
        }

        .avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 0.75rem;
        }

        .permission-card {
            border: 1px solid #e3e6f0;
            border-radius: 0.35rem;
            transition: all 0.15s ease-in-out;
        }

        .permission-card:hover {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border-color: #bbc5d1;
        }

        .permission-status {
            font-size: 0.875rem;
            font-weight: 500;
        }

        .text-truncate-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
@endsection

@section('scripts')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // تحميل البيانات الأولية عند تحميل الصفحة
            loadData();

            // البحث عند إرسال النموذج
            $('#searchForm').on('submit', function(e) {
                e.preventDefault();
                loadData();
            });

            // البحث الفوري عند تغيير قيم المدخلات
            $('#searchForm input, #searchForm select').on('change input', function() {
                clearTimeout(window.searchTimeout);
                window.searchTimeout = setTimeout(function() {
                    loadData();
                }, 500);
            });

            // إعادة تعيين الفلاتر
            $('#resetSearchBtn, #resetSearch').on('click', function() {
                $('#searchForm')[0].reset();
                $('.select2').val('').trigger('change');
                loadData();
            });

            // التعامل مع الترقيم
            $(document).on('click', '.pagination a', function(e) {
                e.preventDefault();
                let url = $(this).attr('href');
                if (url) {
                    let page = new URL(url).searchParams.get('page');
                    loadData(page);
                }
            });

            // دالة تحميل البيانات الرئيسية
            function loadData(page = 1) {
                showLoading();

                let formData = $('#searchForm').serialize();
                if (page > 1) {
                    formData += '&page=' + page;
                }

                $.ajax({
                    url: '{{ route('store_permits_management.index') }}',
                    method: 'GET',
                    data: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#results-container').html(response.data);
                            updatePaginationInfo(response);
                            initializeEvents();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('خطأ في تحميل البيانات:', error);
                        $('#results-container').html(
                            '<div class="alert alert-danger text-center">' +
                            '<p class="mb-0">حدث خطأ في تحميل البيانات. يرجى المحاولة مرة أخرى.</p>' +
                            '</div>'
                        );
                    },
                    complete: function() {
                        hideLoading();
                    }
                });
            }

            // إظهار مؤشر التحميل
            function showLoading() {
                $('#results-container').css('opacity', '0.6');
                if ($('#loading-indicator').length === 0) {
                    $('#results-container').prepend(`
                        <div id="loading-indicator" class="text-center p-3">
                            <div class="spinner-border text-primary" role="status">

                            </div>

                        </div>
                    `);
                }
            }

            // إخفاء مؤشر التحميل
            function hideLoading() {
                $('#loading-indicator').remove();
                $('#results-container').css('opacity', '1');
            }

            // تحديث معلومات الترقيم والعداد
            function updatePaginationInfo(response) {
                $('.pagination-info').text(`صفحة ${response.current_page} من ${response.last_page}`);

                if (response.total > 0) {
                    $('.results-info').text(`${response.from}-${response.to} من ${response.total}`);
                } else {
                    $('.results-info').text('لا توجد نتائج');
                }
            }

            // إعادة تفعيل الأحداث للعناصر الجديدة
            function initializeEvents() {
                // تأكيد الموافقة على الإذن
                $('.approve-btn').off('click').on('click', function(e) {
                    e.preventDefault();

                    const $btn = $(this);
                    const permitId = $btn.data('id');
                    const permitNumber = $btn.data('number');

                    if ($btn.hasClass('processing')) {
                        return false;
                    }

                    Swal.fire({
                        title: 'تأكيد الموافقة',
                        html: `
                            <div class="text-center">
                                <i class="fa fa-check-circle text-success" style="font-size: 64px; margin-bottom: 15px;"></i>
                                <p>هل أنت متأكد من الموافقة على الإذن المخزني رقم <strong>#${permitNumber}</strong>؟</p>
                                <div class="alert alert-info mt-3">
                                    <i class="fa fa-info-circle"></i>
                                    سيتم تنفيذ العملية في المستودع المحدد وتحديث حالة الإذن إلى "موافق عليه"
                                </div>
                            </div>
                        `,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#28a745',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: '<i class="fa fa-check"></i> نعم، موافق',
                        cancelButtonText: '<i class="fa fa-times"></i> إلغاء',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            approvePermit(permitId);
                        }
                    });
                });

                // تأكيد حذف الإذن
                $('.delete-btn').off('click').on('click', function(e) {
                    e.preventDefault();

                    const permitId = $(this).data('id');
                    const permitNumber = $(this).data('number');

                    Swal.fire({
                        title: 'تأكيد الحذف',
                        html: `
                            <div class="text-center">
                                <i class="fa fa-trash text-danger" style="font-size: 64px; margin-bottom: 15px;"></i>
                                <p>هل أنت متأكد من حذف الإذن المخزني رقم <strong>#${permitNumber}</strong>؟</p>
                                <div class="alert alert-danger mt-3">
                                    <i class="fa fa-exclamation-triangle"></i>
                                    <strong>تحذير:</strong> هذا الإجراء لا يمكن التراجع عنه!
                                </div>
                            </div>
                        `,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: '<i class="fa fa-trash"></i> نعم، احذف',
                        cancelButtonText: '<i class="fa fa-times"></i> إلغاء',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            deletePermit(permitId);
                        }
                    });
                });
            }

            // دالة الموافقة على الإذن
            function approvePermit(permitId) {
                $.ajax({
                    url: `{{ route('store_permits_management.approve', '') }}/${permitId}`,
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'تم بنجاح!',
                                text: response.message ||
                                    'تمت الموافقة على الإذن المخزني بنجاح',
                                icon: 'success',
                                timer: 3000,
                                timerProgressBar: true
                            }).then(() => {
                                loadData();
                            });
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'حدث خطأ أثناء معالجة الطلب';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            title: 'خطأ!',
                            text: errorMessage,
                            icon: 'error'
                        });
                    }
                });
            }

            // دالة حذف الإذن
            function deletePermit(permitId) {
                $.ajax({
                    url: `{{ route('store_permits_management.delete', '') }}/${permitId}`,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'تم الحذف',
                            text: 'تم حذف الإذن المخزني بنجاح',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        loadData();
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ',
                            text: 'حدث خطأ أثناء حذف الإذن المخزني'
                        });
                    }
                });
            }

            // تفعيل الأحداث الأولية
            initializeEvents();

            // عرض رسائل النجاح أو الخطأ من الخادم
            @if (session('success'))
                Swal.fire({
                    title: 'تم بنجاح!',
                    text: '{{ session('success') }}',
                    icon: 'success',
                    timer: 3000
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    title: 'خطأ!',
                    text: '{{ session('error') }}',
                    icon: 'error'
                });
            @endif
        });

        // دوال التحكم في البحث المتقدم
        function toggleSearchText(button) {
            const buttonText = button.querySelector('.button-text');

            if (buttonText.textContent.trim() === 'متقدم') {
                buttonText.textContent = 'بحث بسيط';
            } else {
                buttonText.textContent = 'متقدم';
            }
        }

        function toggleSearchFields(button) {
            const searchForm = document.getElementById('searchForm');
            const buttonText = button.querySelector('.hide-button-text');
            const icon = button.querySelector('i');

            if (buttonText.textContent === 'اخفاء') {
                searchForm.style.display = 'none';
                buttonText.textContent = 'اظهار';
                icon.classList.remove('fa-times');
                icon.classList.add('fa-eye');
            } else {
                searchForm.style.display = 'block';
                buttonText.textContent = 'اخفاء';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-times');
            }
        }
    </script>
@endsection
