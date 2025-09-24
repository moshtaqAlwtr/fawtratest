@extends('master')

@section('title')
    الموردين
@stop

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-2"></i>
                <strong>{{ session('success') }}</strong>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0"> ادارة الموردين</h2>
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

    <div class="content-body">
        <!-- Header Card with Pagination -->
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2"></div>
                    <div id="pagination-top" class="d-flex align-items-center gap-3">
                        @include('purchases::purchases.supplier_management.partials.pagination', [
                            'suppliers' => $suppliers,
                        ])
                    </div>
                </div>
            </div>
        </div>

        <!-- Search Card -->
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
                </div>
            </div>
            <div class="card-body">
                <form class="form" id="searchForm" method="GET">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <select name="employee_search" class="form-control select2 search-input">
                                <option value="">البحث بواسطة إسم المورد أو الرقم التعريفي</option>
                                @if(isset($allSuppliers))
                                    @foreach ($allSuppliers as $supplier)
                                        <option value="{{ $supplier->id }}"
                                            {{ request('employee_search') == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->trade_name }}-{{ $supplier->id }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="supplier_number" class="form-control search-input"
                                placeholder="رقم المورد" value="{{ request('supplier_number') }}">
                        </div>
                        <div class="col-md-4">
                            <input type="email" name="email" class="form-control search-input"
                                placeholder="البريد الإلكتروني" value="{{ request('email') }}">
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="mobile" class="form-control search-input" placeholder="رقم الجوال"
                                value="{{ request('mobile') }}">
                        </div>
                        <div class="col-md-4 advanced-field" style="display: none;">
                            <input type="text" name="phone" class="form-control search-input" placeholder="الهاتف"
                                value="{{ request('phone') }}">
                        </div>
                        <div class="col-md-4 advanced-field" style="display: none;">
                            <input type="text" name="address" class="form-control search-input" placeholder="العنوان"
                                value="{{ request('address') }}">
                        </div>
                    </div>

                    <div class="collapse" id="advancedSearchForm">
                        <div class="row g-3 mt-2">
                            <div class="col-md-4">
                                <input type="text" name="postal_code" class="form-control search-input"
                                    placeholder="الرمز البريدي" value="{{ request('postal_code') }}">
                            </div>
                            <div class="col-md-4">
                                <select name="currency" class="form-control search-input">
                                    <option value="">اختر العملة</option>
                                    <option value="SAR" {{ request('currency') == 'SAR' ? 'selected' : '' }}>SAR
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select name="status" class="form-control search-input">
                                    <option value="">اختر الحالة</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط
                                    </option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>موقوف
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="tax_number" class="form-control search-input"
                                    placeholder="الرقم الضريبي" value="{{ request('tax_number') }}">
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="commercial_registration" class="form-control search-input"
                                    placeholder="السجل التجاري" value="{{ request('commercial_registration') }}">
                            </div>
                            <div class="col-md-4">
                                <select name="created_by" class="form-control search-input">
                                    <option value="">أضيفت بواسطة</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}"
                                            {{ request('created_by') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions mt-2">
                        <button type="button" class="btn btn-primary" id="searchBtn">بحث</button>
                        <button type="button" class="btn btn-outline-warning" id="clearBtn">إلغاء</>
                    </div>
                </form>
            </div>
        </div>

        <!-- Results Card -->
        <div class="card">
            <div class="card-body">
                <!-- Loading Spinner -->
                <div id="loading" class="text-center" style="display: none;">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden"></span>
                    </div>
                </div>

                <!-- Table Container -->
                <div id="suppliers-table">
                    @include('purchases::purchases.supplier_management.partials.suppliers_table', [
                        'suppliers' => $suppliers,
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

        .spinner-border {
            width: 3rem;
            height: 3rem;
        }

        #loading {
            padding: 2rem;
        }

        /* تحسينات SweetAlert2 */
        .swal2-popup {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
            direction: rtl !important;
            text-align: right !important;
        }

        .swal2-title {
            font-size: 1.5rem !important;
            font-weight: 600 !important;
        }

        .swal2-html-container {
            font-size: 1rem !important;
            line-height: 1.5 !important;
        }

        .btn:focus {
            box-shadow: none !important;
        }

        /* تحسين أزرار الحالة */
        .btn[title]:hover {
            transform: scale(1.05);
            transition: all 0.2s ease;
        }

        /* تحسين مظهر الأزرار */
        .btn-sm {
            font-size: 0.875rem;
            padding: 0.375rem 0.75rem;
        }
    </style>
@endsection

@section('scripts')
    <script>
        let searchTimeout;

        $(document).ready(function() {
            // البحث التلقائي عند الكتابة
            $('.search-input').on('input change', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    performSearch();
                }, 500); // انتظار 500ms بعد توقف الكتابة
            });

            // زر البحث
            $('#searchBtn').on('click', function() {
                performSearch();
            });

            // زر الإلغاء
            $('#clearBtn').on('click', function() {
                clearSearch();
            });
            
            // زر إعادة تعيين البحث من الجدول
            $(document).on('click', '#clearSearchBtn', function() {
                clearSearch();
            });
            
            function clearSearch() {
                // تفريغ النموذج
                $('#searchForm')[0].reset();
                $('.select2').val('').trigger('change');

                // حذف باراميترات الرابط (تهيئة الرابط)
                const url = window.location.origin + window.location.pathname;
                window.history.replaceState({}, document.title, url);

                // إعادة البحث
                performSearch();
            }


            // التنقل بين الصفحات
            $(document).on('click', '.pagination a', function(e) {
                e.preventDefault();
                let url = $(this).attr('href');
                loadPage(url);
            });
        });

        function performSearch() {
            let formData = $('#searchForm').serialize();
            let url = "{{ route('SupplierManagement.index') }}?" + formData;
            loadPage(url);
        }

        function loadPage(url) {
            $('#loading').show();
            $('#suppliers-table').hide();

            $.ajax({
                url: url,
                type: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    $('#suppliers-table').html(response.html).show();
                    $('#pagination-top').html(response.pagination);
                    $('#loading').hide();

                    // تحديث URL في المتصفح بدون إعادة تحميل
                    window.history.pushState({}, '', url);
                },
                error: function() {
                    $('#loading').hide();
                    $('#suppliers-table').show();
                    alert('حدث خطأ أثناء البحث. يرجى المحاولة مرة أخرى.');
                }
            });
        }

        function toggleSearchText(button) {
            const buttonText = button.querySelector('.button-text');
            const advancedFields = document.querySelectorAll('.advanced-field');

            if (buttonText.textContent.trim() === 'متقدم') {
                buttonText.textContent = 'بحث بسيط';
                advancedFields.forEach(field => field.style.display = 'block');
            } else {
                buttonText.textContent = 'متقدم';
                advancedFields.forEach(field => field.style.display = 'none');
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

        // دالة تغيير حالة المورد
        function changeStatus(supplierId, newStatus, supplierName) {
            const statusText = newStatus == 1 ? 'تفعيل' : 'إيقاف';
            const statusColor = newStatus == 1 ? '#28a745' : '#dc3545';
            const icon = newStatus == 1 ? 'success' : 'warning';

            Swal.fire({
                title: `${statusText} المورد`,
                html: `هل أنت متأكد من <strong>${statusText}</strong> المورد <br><strong>"${supplierName}"</strong>؟`,
                icon: icon,
                showCancelButton: true,
                confirmButtonColor: statusColor,
                cancelButtonColor: '#6c757d',
                confirmButtonText: `نعم، ${statusText}`,
                cancelButtonText: 'إلغاء',
                reverseButtons: true,
                customClass: {
                    confirmButton: 'btn btn-primary me-2',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    // عرض مؤشر التحميل
                    Swal.fire({
                        title: 'جاري التحديث...',
                        text: 'يرجى الانتظار',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // إرسال طلب AJAX
                    $.ajax({
                        url: `/SupplierManagement/${supplierId}/update-status`,
                        type: 'POST',
                        data: {
                            status: newStatus,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    title: 'تم بنجاح!',
                                    text: response.message,
                                    icon: 'success',
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    // تحديث الجدول بدون إعادة تحميل
                                    performSearch();
                                });
                            } else {
                                Swal.fire({
                                    title: 'خطأ!',
                                    text: response.message || 'حدث خطأ أثناء تحديث الحالة',
                                    icon: 'error',
                                    confirmButtonText: 'موافق'
                                });
                            }
                        },
                        error: function(xhr) {
                            let errorMessage = 'حدث خطأ غير متوقع';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }

                            Swal.fire({
                                title: 'خطأ!',
                                text: errorMessage,
                                icon: 'error',
                                confirmButtonText: 'موافق'
                            });
                        }
                    });
                }
            });
        }
    </script>
@endsection
