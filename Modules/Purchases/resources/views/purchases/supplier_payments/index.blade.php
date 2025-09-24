@extends('master')

@section('title', 'مدفوعات الموردين')

@section('css')
    <style>
        .payment-status-badge {
            font-size: 0.85rem;
            padding: 0.35rem 0.65rem;
        }

        .table-hover-custom tr:hover {
            background-color: #f8f9fa;
        }

        .avatar-content {
            font-size: 1.1rem;
        }

        .select2-container--default .select2-selection--single {
            height: calc(1.5em + 0.75rem + 2px);
        }

        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
@endsection

@section('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">إدارة مدفوعات الموردين</h2>
                    <div class="breadcrumb-wrapper">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسية</a></li>
                            <li class="breadcrumb-item active">مدفوعات الموردين</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">
        <!-- شريط الأدوات العلوي -->
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">

                        <!-- زر المواعيد -->
                        <a href="{{ route('appointments.index') }}"
                            class="btn btn-outline-primary btn-sm d-flex align-items-center rounded-pill px-3">
                            <i class="fas fa-calendar-alt me-1"></i>المواعيد
                        </a>

                        <!-- زر استيراد -->
                        <button class="btn btn-outline-primary btn-sm d-flex align-items-center rounded-pill px-3">
                            <i class="fas fa-cloud-upload-alt me-1"></i>استيراد
                        </button>
                    </div>

                    <!-- معلومات النتائج -->
                    <div class="d-flex align-items-center gap-2" id="top-pagination-info" style="display: none;">
                        <span class="text-muted mx-2 results-info">0 نتيجة</span>
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
        <div class="row g-3" id="basicSearchFields">
            <!-- 1. رقم الفاتورة -->
            <div class="col-md-4 mb-3">
                <input type="text" id="invoice_number" class="form-control" placeholder="رقم الفاتورة"
                    name="invoice_number">
            </div>

            <!-- 2. رقم عملية الدفع -->
            <div class="col-md-4 mb-3">
                <input type="text" id="payment_number" class="form-control" placeholder="رقم عملية الدفع"
                    name="payment_number">
            </div>

            <!-- 3. المورد -->
            <div class="col-md-4 mb-3">
                <select name="supplier" class="form-control select2" id="supplier">
                    <option value="">اختر المورد</option>
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->trade_name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 4. أضيفت بواسطة -->
            <div class="col-md-4 mb-3">
                <select name="added_by" class="form-control select2" id="added_by">
                    <option value="">أضيفت بواسطة</option>
                    @foreach ($employees as $employee)
                        <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- البحث المتقدم -->
        <div class="collapse" id="advancedSearchForm">
            <div class="row g-3 mt-2">
                <!-- 4. حالة الدفع -->
                <div class="col-md-4 mb-3">
                    <select name="payment_status" class="form-control select2" id="payment_status">
                        <option value="">حالة الدفع</option>
                        <option value="1">مكتمل</option>
                        <option value="2">غير مكتمل</option>
                        <option value="3">مسودة</option>
                        <option value="4">تحت المراجعة</option>
                        <option value="5">فاشلة</option>
                    </select>
                </div>

                <!-- 5. التخصيص -->
                <div class="col-md-4 mb-3">
                    <select name="customization" class="form-control select2" id="customization">
                        <option value="">تخصيص</option>
                        <option value="1">شهريًا</option>
                        <option value="0">أسبوعيًا</option>
                        <option value="2">يوميًا</option>
                    </select>
                </div>

                <!-- 6. من (التاريخ) -->
                <div class="col-md-4 mb-3">
                    <input type="date" id="from_date" class="form-control" placeholder="من"
                        name="from_date">
                </div>

                <!-- 7. إلى (التاريخ) -->
                <div class="col-md-4 mb-3">
                    <input type="date" id="to_date" class="form-control" placeholder="إلى"
                        name="to_date">
                </div>

                <!-- 8. رقم التعريفي -->
                <div class="col-md-4 mb-3">
                    <input type="text" id="identifier" class="form-control" placeholder="رقم التعريفي"
                        name="identifier">
                </div>

                <!-- 9. رقم معرف التحويل -->
                <div class="col-md-4 mb-3">
                    <input type="text" id="transfer_id" class="form-control"
                        placeholder="رقم معرف التحويل" name="transfer_id">
                </div>

                <!-- 10. الإجمالي أكبر من -->
                <div class="col-md-4 mb-3">
                    <input type="text" id="total_greater_than" class="form-control"
                        placeholder="الاجمالي اكبر من" name="total_greater_than">
                </div>

                <!-- 11. الإجمالي أصغر من -->
                <div class="col-md-4 mb-3">
                    <input type="text" id="total_less_than" class="form-control"
                        placeholder="الاجمالي اصغر من" name="total_less_than">
                </div>
            </div>
        </div>

        <!-- الأزرار -->
        <div class="form-actions mt-2">
            <button type="submit" class="btn btn-primary">بحث</button>
            <button type="button" id="resetSearch" class="btn btn-outline-warning">إلغاء</button>
        </div>
    </form>
</div>
        </div>
        <!-- بطاقة النتائج -->
        <div class="card">
            <div class="card-body position-relative">
                <!-- مؤشر التحميل -->
                <div id="loadingIndicator" class="loading-overlay" style="display: none;">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">جاري التحميل...</span>
                        </div>
                        <p class="mt-2 text-muted">جاري تحميل البيانات...</p>
                    </div>
                </div>

                <!-- نتائج البحث -->
                <div id="resultsContainer">
                    @include('purchases::purchases.supplier_payments.partials.table', [
                        'payments' => $payments,
                    ])
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            let currentPage = 1;
            let isLoading = false;
            let searchXHR = null;

            // تهيئة Select2
            $('.select2').select2({
                width: '100%',
                placeholder: 'اختر...',
                allowClear: true,
                language: {
                    noResults: function() {
                        return "لا توجد نتائج";
                    }
                }
            });

            // تبديل عرض/إخفاء البحث
            $('#toggleSearchBtn').click(function() {
                $('#searchSection').toggle();
                if ($('#searchSection').is(':visible')) {
                    $(this).html('<i class="fas fa-search-minus mr-1"></i> إخفاء');
                } else {
                    $(this).html('<i class="fas fa-search-plus mr-1"></i> عرض البحث');
                }
            });

            // تبديل البحث المتقدم
            $('#toggleAdvancedBtn').click(function() {
                if ($('#advancedSearchCollapse').hasClass('show')) {
                    $(this).html('<i class="fas fa-filter mr-1"></i> بحث متقدم');
                } else {
                    $(this).html('<i class="fas fa-times mr-1"></i> إغلاق المتقدم');
                }
            });

            // إرسال نموذج البحث
            $('#searchForm').submit(function(e) {
                e.preventDefault();
                if (!isLoading) {
                    currentPage = 1;
                    loadData();
                }
            });

            // البحث الفوري مع تأخير
            $('#searchForm input, #searchForm select').on('change input', function() {
                if (searchXHR) {
                    searchXHR.abort();
                }

                clearTimeout(window.searchTimeout);
                window.searchTimeout = setTimeout(function() {
                    if (!isLoading) {
                        currentPage = 1;
                        loadData();
                    }
                }, 500);
            });

            // إعادة تعيين البحث
            $('#resetSearch').click(function() {
                $('#searchForm')[0].reset();
                $('.select2').val(null).trigger('change');
                currentPage = 1;
                loadData();
            });

            // الترقيم
            $(document).on('click', '.page-link:not(.disabled):not(.active)', function(e) {
                e.preventDefault();
                if (!isLoading) {
                    currentPage = $(this).data('page');
                    loadData();
                    $('html, body').animate({
                        scrollTop: $("#resultsContainer").offset().top - 20
                    }, 300);
                }
            });

            // دالة تحميل البيانات
            function loadData() {
                if (isLoading) return;

                isLoading = true;
                showLoading();

                // جمع بيانات النموذج
                let formData = $('#searchForm').serializeArray()
                    .filter(item => item.name !== '_token')
                    .reduce((obj, item) => {
                        obj[item.name] = item.value;
                        return obj;
                    }, {});

                formData.page = currentPage;

                // إلغاء أي طلب سابق
                if (searchXHR) {
                    searchXHR.abort();
                }

                searchXHR = $.ajax({
                    url: "{{ route('PaymentSupplier.indexPurchase') }}",
                    method: 'GET',
                    data: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#resultsContainer').html(response.html);
                            updatePaginationInfo(response);
                        } else {
                            showError('حدث خطأ أثناء جلب البيانات');
                        }
                    },
                    error: function(xhr) {
                        if (xhr.statusText !== 'abort') {
                            let errorMsg = 'حدث خطأ في الاتصال بالخادم';
                            if (xhr.status === 422) {
                                errorMsg = 'بيانات البحث غير صالحة';
                            } else if (xhr.status === 404) {
                                errorMsg = 'الصفحة المطلوبة غير موجودة';
                            } else if (xhr.status === 500) {
                                errorMsg = 'خطأ في الخادم الداخلي';
                            }

                            $('#resultsContainer').html(`
                        <div class="alert alert-danger text-center py-4">
                            <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                            <h5>${errorMsg}</h5>
                            <button class="btn btn-sm btn-outline-danger mt-2" onclick="loadData()">
                                <i class="fas fa-sync-alt mr-1"></i> إعادة المحاولة
                            </button>
                        </div>
                    `);
                        }
                    },
                    complete: function() {
                        isLoading = false;
                        hideLoading();
                        searchXHR = null;
                    }
                });
            }

            // إظهار مؤشر التحميل
            function showLoading() {
                $('#loadingIndicator').show();
            }

            // إخفاء مؤشر التحميل
            function hideLoading() {
                $('#loadingIndicator').hide();
            }

            // تحديث معلومات الترقيم
            function updatePaginationInfo(response) {
                $('.results-info').text(`عرض ${response.from} إلى ${response.to} من ${response.total} نتائج`);
            }

            // دالة تحميل البيانات مع رقم الصفحة (للاستخدام من pagination.blade.php)
            window.loadDataWithPage = function(page) {
                if (!isLoading) {
                    currentPage = page;
                    loadData();
                    $('html, body').animate({
                        scrollTop: $("#resultsContainer").offset().top - 20
                    }, 300);
                }
            };

            // تأكيد الحذف
            $(document).on('click', '.delete-payment', function(e) {
                e.preventDefault();
                let url = $(this).attr('href');

                Swal.fire({
                    title: 'تأكيد الحذف',
                    text: 'هل أنت متأكد من حذف هذه الدفعة؟ لا يمكن التراجع عن هذا الإجراء.',
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
                            url: url,
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        title: 'تم الحذف!',
                                        text: 'تم حذف الدفعة بنجاح.',
                                        icon: 'success',
                                        timer: 1500,
                                        showConfirmButton: false
                                    });
                                    loadData();
                                } else {
                                    Swal.fire('خطأ!', response.message ||
                                        'فشل في حذف الدفعة', 'error');
                                }
                            },
                            error: function() {
                                Swal.fire('خطأ!', 'حدث خطأ أثناء محاولة الحذف',
                                'error');
                            }
                        });
                    }
                });
            });

            // تحديد/إلغاء تحديد الكل
            $(document).on('change', '#selectAll', function() {
                $('.payment-checkbox').prop('checked', $(this).prop('checked'));
                updateSelectedCount();
            });

            // تحديث عداد المحدد
            $(document).on('change', '.payment-checkbox', function() {
                updateSelectedCount();
            });

            // تحديث عدد العناصر المحددة
            function updateSelectedCount() {
                let selectedCount = $('.payment-checkbox:checked').length;
                if (selectedCount > 0) {
                    $('#exportBtn').removeClass('btn-outline-secondary').addClass('btn-outline-primary');
                } else {
                    $('#exportBtn').removeClass('btn-outline-primary').addClass('btn-outline-secondary');
                }
            }

            // تصدير البيانات المحددة
            $('#exportBtn').click(function() {
                let selectedIds = $('.payment-checkbox:checked').map(function() {
                    return $(this).val();
                }).get();

                if (selectedIds.length === 0) {
                    Swal.fire({
                        title: 'لم يتم التحديد',
                        text: 'يرجى تحديد مدفوعات لتصديرها',
                        icon: 'warning',
                        confirmButtonText: 'حسناً'
                    });
                    return;
                }

                // هنا يمكنك إضافة منطق التصدير
                Swal.fire({
                    title: 'تصدير المحدد',
                    text: `ستقوم بتصدير ${selectedIds.length} مدفوعات`,
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'تصدير',
                    cancelButtonText: 'إلغاء'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // إجراء التصدير
                        console.log('تصدير المدفوعات:', selectedIds);
                    }
                });
            });
        });
    </script>
@endsection
