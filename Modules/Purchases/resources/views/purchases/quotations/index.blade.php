@extends('master')

@section('title')
    اضف طلب عرض سعر مشتريات
@stop

@section('content')
    <div class="card">
    </div>

    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">ادارة عروض الاسعار الشراء</h2>
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
        @include('layouts.alerts.error')
        @include('layouts.alerts.success')

        <!-- Header with pagination and add button -->
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <!-- Loading spinner -->
                        <div id="loading-spinner" class="spinner-border spinner-border-sm text-primary d-none" role="status">
                            <span class="visually-hidden"></span>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <!-- Pagination will be updated via Ajax -->
                        <div id="pagination-top">
                            @include('purchases::purchases.quotations.partials.pagination', ['purchaseQuotation' => $purchaseQuotation])
                        </div>

                        <a href="{{ route('Quotations.create') }}" class="btn btn-success">
                            <i class="fa fa-plus me-1"></i>
                            أضف طلب عرض اسعار شراء
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search Form -->
        <div class="card">
            <div class="card-body">
                <form class="form" id="search-form">
                    <div class="form-body row">
                        <div class="form-group col-md-3">
                            <label for="supplier_id">مورد</label>
                            <select name="supplier_id" class="form-control search-input select2" id="supplier_id">
                                <option value="">اختر المورد</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->trade_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="follow_status">حالة المتابعة</label>
                            <select name="follow_status" class="form-control search-input" id="follow_status">
                                <option value="">جميع حالات المتابعة</option>
                                <option value="1">متابع</option>
                                <option value="2">غير متابع</option>
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="code">الكود</label>
                            <input type="text" class="form-control search-input" name="code" id="code" placeholder="ادخل الكود">
                        </div>

                        <div class="form-group col-md-3">
                            <label for="status">الحالة</label>
                            <select class="form-control search-input" name="status" id="status">
                                <option value="">الحالة</option>
                                <option value="1">نشط</option>
                                <option value="2">متوقف</option>
                                <option value="3">غير نشط</option>
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="order_date_from">تاريخ الطلب (من)</label>
                            <input type="date" class="form-control search-input" name="order_date_from" id="order_date_from">
                        </div>

                        <div class="form-group col-md-3">
                            <label for="order_date_to">تاريخ الطلب (إلى)</label>
                            <input type="date" class="form-control search-input" name="order_date_to" id="order_date_to">
                        </div>

                        <div class="form-group col-md-3">
                            <label for="due_date_from">تاريخ الاستحقاق (من)</label>
                            <input type="date" class="form-control search-input" name="due_date_from" id="due_date_from">
                        </div>

                        <div class="form-group col-md-3">
                            <label for="due_date_to">تاريخ الاستحقاق (إلى)</label>
                            <input type="date" class="form-control search-input" name="due_date_to" id="due_date_to">
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary mr-1" id="search-btn">
                            <i class="fa fa-search me-1"></i>
                            بحث
                        </button>
                        <button type="button" class="btn btn-outline-danger" id="clear-filters">
                            <i class="fa fa-times me-1"></i>
                            إلغاء الفلترة
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Data Table Container -->
        <div class="card">
            <div class="card-body">
                <div id="table-container">
                    @include('purchases::purchases.quotations.partials.table', ['purchaseQuotation' => $purchaseQuotation])
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    let searchTimeout;

    // تهيئة Select2 للمورد
    if ($('#supplier_id').length) {
        $('#supplier_id').select2({
            placeholder: 'اختر المورد',
            allowClear: true,
            dir: 'rtl'
        });
    }

    // البحث التلقائي عند الكتابة أو التغيير
    $('.search-input').on('input change', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            performSearch();
        }, 500); // انتظار نصف ثانية بعد التوقف عن الكتابة
    });

    // البحث التلقائي لـ Select2
    $('#supplier_id').on('select2:select select2:clear', function() {
        performSearch();
    });

    // البحث عند الضغط على زر البحث
    $('#search-form').on('submit', function(e) {
        e.preventDefault();
        performSearch();
    });

    // مسح الفلاتر
    $('#clear-filters').on('click', function() {
        $('#search-form')[0].reset();
        $('#supplier_id').val(null).trigger('change'); // مسح Select2
        performSearch();
    });

    // التعامل مع روابط الـ pagination
    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        let url = $(this).attr('href');
        let page = new URL(url).searchParams.get('page');
        performSearch(page);
    });

    // دالة البحث الرئيسية
    function performSearch(page = 1) {
        showLoading();

        let formData = $('#search-form').serialize();
        formData += '&page=' + page;

        $.ajax({
            url: '{{ route("Quotations.index") }}',
            type: 'GET',
            data: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.success) {
                    $('#table-container').html(response.data);
                    $('#pagination-top').html(response.pagination);

                    // إضافة تأثيرات بصرية للتحديث
                    $('#table-container').hide().fadeIn(300);
                }
                hideLoading();
            },
            error: function(xhr, status, error) {
                console.error('خطأ في البحث:', error);
                hideLoading();

                // عرض رسالة خطأ
                if (typeof toastr !== 'undefined') {
                    toastr.error('حدث خطأ أثناء البحث، يرجى المحاولة مرة أخرى');
                } else {
                    alert('حدث خطأ أثناء البحث، يرجى المحاولة مرة أخرى');
                }
            }
        });
    }

    // دالة حذف عرض السعر
    $(document).on('click', '.delete-quotation', function(e) {
        e.preventDefault();

        let quotationId = $(this).data('quotation-id');
        let quotationCode = $(this).data('quotation-code');

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'حذف طلب عرض السعر',
                text: `هل أنت متأكد من حذف طلب عرض السعر رقم "${quotationCode}"؟`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'نعم، احذف',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteQuotation(quotationId);
                }
            });
        } else {
            // استخدام confirm عادي إذا لم يكن SweetAlert متاح
            if (confirm(`هل أنت متأكد من حذف طلب عرض السعر رقم "${quotationCode}"؟`)) {
                deleteQuotation(quotationId);
            }
        }
    });

    // دالة حذف عرض السعر
    function deleteQuotation(quotationId) {
        $.ajax({
            url: `/purchases/quotations/${quotationId}`,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.success) {
                    if (typeof toastr !== 'undefined') {
                        toastr.success('تم حذف طلب عرض السعر بنجاح');
                    }
                    performSearch(); // إعادة تحميل البيانات
                } else {
                    if (typeof toastr !== 'undefined') {
                        toastr.error('حدث خطأ أثناء الحذف');
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('خطأ في الحذف:', error);
                if (typeof toastr !== 'undefined') {
                    toastr.error('حدث خطأ أثناء الحذف');
                } else {
                    alert('حدث خطأ أثناء الحذف');
                }
            }
        });
    }

    // دالة إظهار اللودينغ
    function showLoading() {
        $('#loading-spinner').removeClass('d-none');
        $('#search-btn').prop('disabled', true);
        $('#table-container').css('opacity', '0.6');
    }

    // دالة إخفاء اللودينغ
    function hideLoading() {
        $('#loading-spinner').addClass('d-none');
        $('#search-btn').prop('disabled', false);
        $('#table-container').css('opacity', '1');
    }

    // تفعيل Select All checkbox
    $(document).on('change', '#selectAll', function() {
        $('.quotation-checkbox').prop('checked', this.checked);
    });

    // التحكم في Select All عند تغيير الـ checkboxes الفردية
    $(document).on('change', '.quotation-checkbox', function() {
        let totalCheckboxes = $('.quotation-checkbox').length;
        let checkedCheckboxes = $('.quotation-checkbox:checked').length;

        $('#selectAll').prop('indeterminate', checkedCheckboxes > 0 && checkedCheckboxes < totalCheckboxes);
        $('#selectAll').prop('checked', checkedCheckboxes === totalCheckboxes);
    });
});
</script>
@endsection
