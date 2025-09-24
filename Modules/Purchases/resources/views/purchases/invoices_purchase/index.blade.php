@extends('master')

@section('title')
    ادارة فواتير الشراء
@stop

@section('content')

    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">ادارة فواتير الشراء</h2>
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

                        <a href="{{ route('invoicePurchases.create') }}" class="btn btn-success">
                            <i class="fa fa-plus me-1"></i>
                            اضف فاتورة شراء
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
                            <select name="employee_search" class="form-control  select2">
                                <option value="">البحث بواسطة إسم المورد أو الرقم التعريفي</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->trade_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <input type="text" name="number_invoice" class="form-control" placeholder="رقم الفاتورة">
                        </div>


                        <div class="col-md-4">
                            <select name="payment_status" class="form-control select2">
                                <option value="">اختر حالة الدفع</option>
                                <option value="paid">مدفوع</option>
                                <option value="partial">مدفوع جزئيا</option>
                                <option value="unpaid">غير مدفوع</option>
                                {{-- <option value="returned">مرتجع</option>
                                <option value="overpaid">مدفوعة بالزيادة</option>
                                <option value="draft">مسودة</option> --}}
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
                            <option value="">اضيفت بواسطة</option>
                            <select name="tag" class="form-control select2">
                                <option value="">اختر الوسم</option>
                                @foreach ($tags ?? [] as $tag)
                                    <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- حقول البحث المتقدم -->
                    <!-- إضافة هذه الحقول داخل قسم البحث المتقدم -->
                    <div class="collapse" id="advancedSearchForm">
                        <div class="row g-3 mt-2">
                            <!-- الحقول الموجودة مسبقاً -->
                            <div class="col-md-4">
                                <input type="text" name="contract" class="form-control" placeholder="حقل مخصص">
                            </div>

                            <div class="col-md-4">
                                <input type="text" name="description" class="form-control" placeholder="تحتوي على البند">
                            </div>

                            <div class="col-md-4">
                                <select name="source" class="form-control select2">
                                    <option value="">إختر المصدر</option>
                                    <option value="invoice">فاتورة</option>
                                    <option value="return">المرتجع</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <select name="receiving_status" class="form-control select2">
                                    <option value="">حالة التسليم</option>
                                    <option value="received">مستلم</option>

                                    <option value="partial_received">مستلم جزئيا</option>
                                    <option value="not_received">لم يستلم</option>
                                </select>
                            </div>

                            <!-- الحقول الجديدة المضافة -->



                            <!-- الحساب -->
                            <div class="col-md-4">
                                <select name="account_id" class="form-control select2">
                                    <option value="">اختر الحساب</option>
                                    @foreach ($accounts ?? [] as $account)
                                        <option value="{{ $account->id }}">{{ $account->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- نوع الخصم -->

                            <!-- حالة الدفع (منطقية) -->

                            <!-- نوع الضريبة -->
                            <div class="col-md-4">
                                <select name="tax_type" class="form-control select2">
                                    @foreach ($taxes ?? [] as $tax)
                                        <option value="{{ $tax->id }}">{{ $tax->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- البحث بالمبالغ - يمكن إضافة المزيد حسب الحاجة -->
                            <!-- التواريخ الموجودة مسبقاً -->
                            <div class="col-md-4">
                                <input type="date" name="start_date_from" class="form-control"
                                    placeholder="التاريخ من">
                            </div>

                            <div class="col-md-4">
                                <input type="date" name="start_date_to" class="form-control"
                                    placeholder="التاريخ الى">
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
            background: rgba(255, 255, 255, 0.9);
            border-radius: 0.375rem;
        }

        .spinner-border {
            width: 2rem;
            height: 2rem;
        }
    </style>
@endsection

@section('scripts')
    <!-- تأكد من وجود CSRF token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

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
                // تأخير البحث قليلاً عند الكتابة
                clearTimeout(window.searchTimeout);
                window.searchTimeout = setTimeout(function() {
                    loadData();
                }, 500);
            });

            // البحث السريع
            $('#quickSearch').on('input', function() {
                let searchTerm = $(this).val();
                $('#searchForm input[name="number_invoice"]').val(searchTerm);

                clearTimeout(window.quickSearchTimeout);
                window.quickSearchTimeout = setTimeout(function() {
                    loadData();
                }, 300);
            });

            // إعادة تعيين الفلاتر
            $('#resetSearch').on('click', function() {
                $('#searchForm')[0].reset();
                $('#quickSearch').val('');
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
                // إظهار مؤشر التحميل
                showLoading();

                // جمع بيانات النموذج
                let formData = $('#searchForm').serialize();
                if (page > 1) {
                    formData += '&page=' + page;
                }

                $.ajax({
                    url: '{{ route('invoicePurchases.index') }}',
                    method: 'GET',
                    data: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        if (response.success) {
                            // تحديث محتوى الجدول
                            $('#results-container').html(response.data);

                            // تحديث معلومات الترقيم
                            updatePaginationInfo(response);

                            // إعادة تفعيل الأحداث
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
                        <span class="visually-hidden"></span>
                    </div>
                    <div class="mt-2 text-muted"></div>
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
                $('.pagination-info').text(`صفحة ${response.current_page} من ${response.last_page}`);

                if (response.total > 0) {
                    $('.results-info').text(`${response.from}-${response.to} من ${response.total}`);
                } else {
                    $('.results-info').text('لا توجد نتائج');
                }
            }

            // إعادة تفعيل الأحداث للعناصر الجديدة
            function initializeEvents() {
                // أحداث الحذف
                $('.delete-invoice').off('click').on('click', function(e) {
                    e.preventDefault();
                    const invoiceId = $(this).data('id');

                    Swal.fire({
                        title: 'تأكيد الحذف',
                        text: 'هل أنت متأكد من حذف هذه الفاتورة؟',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'نعم، احذف',
                        cancelButtonText: 'إلغاء'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            deleteInvoice(invoiceId);
                        }
                    });
                });

                // تحديد الكل
                $('#selectAll').off('change').on('change', function() {
                    $('.order-checkbox').prop('checked', $(this).prop('checked'));
                });

                // تحديث حالة "تحديد الكل"
                $('.order-checkbox').off('change').on('change', function() {
                    let totalCheckboxes = $('.order-checkbox').length;
                    let checkedCheckboxes = $('.order-checkbox:checked').length;

                    $('#selectAll').prop('checked', totalCheckboxes === checkedCheckboxes);
                    $('#selectAll').prop('indeterminate', checkedCheckboxes > 0 && checkedCheckboxes <
                        totalCheckboxes);
                });
            }

            // حذف الفاتورة
            function deleteInvoice(invoiceId) {
                const row = $(`.delete-invoice[data-id="${invoiceId}"]`).closest('tr');
                row.css('opacity', '0.5');

                $.ajax({
                    url: `/invoicePurchases/${invoiceId}`,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'تم الحذف',
                            text: 'تم حذف الفاتورة بنجاح',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        // إعادة تحميل البيانات
                        loadData();
                    },
                    error: function(xhr, status, error) {
                        row.css('opacity', '1');
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ',
                            text: 'حدث خطأ أثناء حذف الفاتورة'
                        });
                    }
                });
            }

            // تفعيل الأحداث الأولية
            initializeEvents();
        });

        // دوال التحكم في البحث المتقدم (نفس الكود السابق)
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
    </script>
@endsection
