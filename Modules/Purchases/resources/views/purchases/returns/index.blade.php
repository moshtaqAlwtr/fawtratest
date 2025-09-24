@extends('master')

@section('title')
    ادارة مرتجعات الشراء
@stop

@section('content')

    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">ادارة مرتجعات الشراء</h2>
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
                                    <span class="text-muted pagination-info">صفحة 1 من 1</span>
                                </li>
                            </ul>
                        </nav>

                        <!-- عداد النتائج -->
                        <span class="text-muted mx-2 results-info">0 نتيجة</span>

                        <a href="{{ route('ReturnsInvoice.create') }}" class="btn btn-success">
                            <i class="fa fa-plus me-1"></i>
                            اضف مرتجع شراء
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- نموذج البحث -->
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
                <form class="form" id="searchForm">
                    @csrf
                    <div class="row g-3">
                        <!-- الحقول الأساسية -->
                        <div class="col-md-4">
                            <select name="employee_search" class="form-control select2">
                                <option value="">البحث بواسطة إسم المورد أو الرقم التعريفي</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->trade_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <input type="text" name="number_invoice" class="form-control" placeholder="رقم المرتجع">
                        </div>


                        <div class="col-md-4">
                            <select name="payment_status" class="form-control select2">
                                <option value="">اختر حالة الدفع</option>
                                <option value="paid">تم التسوية</option>
                                <option value="partial">تسوية جزئية</option>
                                <option value="unpaid">غير مسوى</option>
                                <option value="refunded">مرتد</option>
                                <option value="processing">قيد المعالجة</option>
                            </select>
                        </div>

                        <!-- الحقول المتقدمة -->
                        <div class="col-md-4 advanced-field" style="display: none;">
                            <select name="created_by" class="form-control select2">
                                <option value="">اضيفت بواسطة</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4 advanced-field" style="display: none;">
                            <select name="tag" class="form-control select2">
                                <option value="">اختر الوسم</option>
                                @foreach ($tags ?? [] as $tag)
                                    <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- حقول البحث المتقدم -->
                    <div class="collapse" id="advancedSearchForm">
                        <div class="row g-3 mt-2">
                            <!-- طريقة الدفع -->

                            <!-- نوع المرتجع -->

                            <!-- حالة الاستلام -->
                            <div class="col-md-4">
                                <select name="receiving_status" class="form-control select2">
                                    <option value="">حالة الاستلام</option>
                                    <option value="not_received">لم يستلم</option>
                                    <option value="received">مستلم</option>
                                    <option value="partially_received">مستلم جزئياً</option>
                                    <option value="rejected">مرفوض</option>
                                </select>
                            </div>

                            <!-- الحساب -->
                            <div class="col-md-4">
                                <select name="account_id" class="form-control select2">
                                    <option value="">اختر الحساب</option>
                                    @foreach ($accounts ?? [] as $account)
                                        <option value="{{ $account->id }}">{{ $account->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- حالة الموافقة -->

                            <!-- البحث بالمبالغ -->

                            <!-- التواريخ -->
                            <div class="col-md-4">
                                <input type="date" name="return_date_from" class="form-control" placeholder="تاريخ المرتجع من">
                            </div>

                            <div class="col-md-4">
                                <input type="date" name="return_date_to" class="form-control" placeholder="تاريخ المرتجع إلى">
                            </div>

                        </div>
                    </div>

                    <div class="form-actions mt-2">
                        <button type="submit" class="btn btn-primary">بحث</button>
                        <button type="button" id="resetSearch" class="btn btn-outline-warning">إلغاء</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- جدول النتائج -->
        <div class="card">
            <div class="card-body">
                <div id="results-container">
                    <!-- سيتم تحميل الجدول هنا عبر AJAX -->
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
            background: rgba(255,255,255,0.9);
            border-radius: 0.375rem;
        }
        .spinner-border {
            width: 2rem;
            height: 2rem;
        }
        .avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 14px;
        }
        .badge {
            font-size: 0.75em;
        }
        .btn-group .dropdown-menu {
            min-width: 150px;
        }
        .dropdown-item {
            padding: 0.5rem 1rem;
        }
        .dropdown-item i {
            width: 16px;
        }
    </style>
@endsection

@section('scripts')
    <!-- تأكد من وجود CSRF token -->


    <script>
    $(document).ready(function() {
    // متغيرات عامة
    let isLoading = false;
    let currentPage = 1;

    // تحميل البيانات الأولية عند تحميل الصفحة
    loadData();

    // البحث عند إرسال النموذج
    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        if (!isLoading) {
            loadData(1);
        }
    });

    // البحث الفوري عند تغيير قيم المدخلات (مع تأخير)
    let searchTimeout;
    $('#searchForm input, #searchForm select, #searchForm textarea').on('change input', function() {
        if (isLoading) return;

        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            loadData(1);
        }, 800); // تأخير أكبر لتجنب الطلبات المتكررة
    });

    // إعادة تعيين الفلاتر
    $('#resetSearch, .btn-outline-warning[type="button"]').on('click', function() {
        if (isLoading) return;

        $('#searchForm')[0].reset();
        $('.select2').val(null).trigger('change'); // إعادة تعيين select2
        loadData(1);
    });

    // التعامل مع الترقيم
    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        if (isLoading) return;

        let url = $(this).attr('href');
        if (url) {
            let page = new URL(url).searchParams.get('page') || 1;
            loadData(page);
        }
    });

    // دالة تحميل البيانات الرئيسية
    function loadData(page = 1) {
        if (isLoading) return;

        isLoading = true;
        currentPage = page;

        // إظهار مؤشر التحميل
        showLoading();

        // جمع بيانات النموذج
        let formData = $('#searchForm').serialize();
        if (page > 1) {
            formData += '&page=' + page;
        }

        $.ajax({
            url: window.location.pathname, // استخدام المسار الحالي
            method: 'GET',
            data: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            timeout: 30000, // مهلة زمنية 30 ثانية
            success: function(response) {
                console.log('Response received:', response);

                if (response.success) {
                    // تحديث محتوى الجدول
                    $('#results-container').html(response.data);

                    // تحديث معلومات الترقيم
                    updatePaginationInfo(response);

                    // إعادة تفعيل الأحداث
                    initializeEvents();

                    // إظهار رسالة نجاح إذا لم تكن هناك بيانات
                    if (response.total === 0) {
                        $('#results-container').html(`
                            <div class="alert alert-info text-center" role="alert">
                                <i class="fa fa-info-circle me-2"></i>
                                <p class="mb-0">لا يوجد مرتجعات مشتريات تطابق معايير البحث</p>
                            </div>
                        `);
                    }
                } else {
                    throw new Error(response.message || 'خطأ في الاستجابة');
                }
            },
            error: function(xhr, status, error) {
                console.error('خطأ AJAX:', {xhr, status, error});

                let errorMessage = 'حدث خطأ في تحميل البيانات';

                if (xhr.status === 500) {
                    errorMessage = 'خطأ في الخادم. يرجى المحاولة لاحقاً';
                } else if (xhr.status === 404) {
                    errorMessage = 'الصفحة المطلوبة غير موجودة';
                } else if (xhr.status === 403) {
                    errorMessage = 'غير مصرح لك بالوصول';
                } else if (status === 'timeout') {
                    errorMessage = 'انتهت مهلة الاتصال. يرجى المحاولة مرة أخرى';
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }

                $('#results-container').html(`
                    <div class="alert alert-danger text-center" role="alert">
                        <i class="fa fa-exclamation-triangle me-2"></i>
                        <p class="mb-2">${errorMessage}</p>
                        <button class="btn btn-outline-danger btn-sm" onclick="loadData(${currentPage})">
                            <i class="fa fa-refresh me-1"></i>
                            إعادة المحاولة
                        </button>
                    </div>
                `);
            },
            complete: function() {
                hideLoading();
                isLoading = false;
            }
        });
    }

    // إظهار مؤشر التحميل
    function showLoading() {
        $('#results-container').css('opacity', '0.6');
        if ($('#loading-indicator').length === 0) {
            $('#results-container').prepend(`
                <div id="loading-indicator" class="d-flex justify-content-center align-items-center p-4">
                    <div class="text-center">
                        <div class="spinner-border text-primary mb-2" role="status">
                            <span class="visually-hidden"></span>
                        </div>
                        <div class="text-muted"></div>
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
        // تحديث النص في الشريط العلوي
        if (response.total > 0) {
            $('.pagination-info').text(`صفحة ${response.current_page} من ${response.last_page}`);
            $('.results-info').text(`${response.from}-${response.to} من ${response.total} نتيجة`);
        } else {
            $('.pagination-info').text('صفحة 1 من 1');
            $('.results-info').text('0 نتيجة');
        }
    }

    // إعادة تفعيل الأحداث للعناصر الجديدة
    function initializeEvents() {
        // أحداث الحذف
        $('.delete-return').off('click').on('click', function(e) {
            e.preventDefault();
            if (isLoading) return;

            const returnId = $(this).data('id');
            const returnCode = $(this).closest('tr').find('td:nth-child(2)').text().trim();

            Swal.fire({
                title: 'تأكيد الحذف',
                text: `هل أنت متأكد من حذف المرتجع ${returnCode}؟`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'نعم، احذف',
                cancelButtonText: 'إلغاء',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteReturn(returnId);
                }
            });
        });

        // تحديد الكل
        $('#selectAll').off('change').on('change', function() {
            $('.order-checkbox').prop('checked', $(this).prop('checked'));
            updateBulkActions();
        });

        // تحديث حالة "تحديد الكل"
        $('.order-checkbox').off('change').on('change', function() {
            let totalCheckboxes = $('.order-checkbox').length;
            let checkedCheckboxes = $('.order-checkbox:checked').length;

            $('#selectAll').prop('checked', totalCheckboxes === checkedCheckboxes);
            $('#selectAll').prop('indeterminate', checkedCheckboxes > 0 && checkedCheckboxes < totalCheckboxes);

            updateBulkActions();
        });
    }

    // تحديث أزرار العمليات المجمعة
    function updateBulkActions() {
        let checkedCount = $('.order-checkbox:checked').length;
        // يمكن إضافة منطق هنا لإظهار/إخفاء أزرار العمليات المجمعة
    }

    // حذف المرتجع
    function deleteReturn(returnId) {
        if (isLoading) return;

        const row = $(`.delete-return[data-id="${returnId}"]`).closest('tr');
        row.css('opacity', '0.5');

        $.ajax({
            url: `/ReturnsInvoice/destroy/${returnId}`,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'تم الحذف',
                    text: 'تم حذف المرتجع بنجاح',
                    timer: 2000,
                    showConfirmButton: false
                });

                // إعادة تحميل البيانات
                loadData(currentPage);
            },
            error: function(xhr, status, error) {
                row.css('opacity', '1');

                let errorMessage = 'حدث خطأ أثناء حذف المرتجع';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: errorMessage
                });
            }
        });
    }

    // تفعيل الأحداث الأولية
    initializeEvents();

    // جعل دالة loadData متاحة عالمياً
    window.loadData = loadData;
});

// دوال التحكم في البحث المتقدم
function toggleSearchText(button) {
    const buttonText = button.querySelector('.button-text');
    const advancedForm = document.getElementById('advancedSearchForm');
    const advancedFields = document.querySelectorAll('.advanced-field');

    if (buttonText.textContent.trim() === 'متقدم') {
        buttonText.textContent = 'بحث بسيط';
        advancedFields.forEach(field => field.style.display = 'block');
        $(advancedForm).collapse('show');
    } else {
        buttonText.textContent = 'متقدم';
        advancedFields.forEach(field => field.style.display = 'none');
        $(advancedForm).collapse('hide');
    }
}

function toggleSearchFields(button) {
    const searchCard = button.closest('.card');
    const cardBody = searchCard.querySelector('.card-body');
    const buttonText = button.querySelector('.hide-button-text');
    const icon = button.querySelector('i');

    if (buttonText.textContent === 'اخفاء') {
        cardBody.style.display = 'none';
        buttonText.textContent = 'اظهار';
        icon.classList.remove('fa-times');
        icon.classList.add('fa-eye');
    } else {
        cardBody.style.display = 'block';
        buttonText.textContent = 'اخفاء';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-times');
    }
}
    </script>
@endsection
