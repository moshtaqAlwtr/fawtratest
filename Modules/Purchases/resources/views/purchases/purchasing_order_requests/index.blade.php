@extends('master')

@section('title')
    ادارة اوامر الشراء
@stop

@section('content')

    <div class="card">

    </div>
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">ادارة اوامر الشراء</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a>
                            </li>
                            <li class="breadcrumb-item active">عرض
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">

        @include('layouts.alerts.error')
        @include('layouts.alerts.success')

        <!-- شريط الأدوات العلوي -->
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <!-- يمكن إضافة أدوات إضافية هنا -->
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <!-- التصفح -->
                        <div id="pagination-container">
                            <!-- سيتم تحميل الترقيم هنا عبر AJAX -->
                        </div>

                        <!-- عداد النتائج -->
                        <span class="text-muted mx-2" id="results-counter">
                            <!-- سيتم تحديث العداد هنا -->
                        </span>

                        <a href="{{ route('OrdersRequests.create') }}" class="btn btn-success">
                            <i class="fa fa-plus me-1"></i>
                            اضف امر شراء
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- نموذج البحث -->
        <div class="card">
            <div class="card-body">
                <form class="form" id="search-form">
                    <div class="form-body row">
                        <div class="form-group col-md-3">
                            <label for="code">الكود</label>
                            <input type="text" class="form-control" name="code" id="code" placeholder="ادخل الكود">
                        </div>

                        <div class="form-group col-md-3">
                            <label for="start_date_from">تاريخ (من)</label>
                            <input type="date" class="form-control" name="start_date_from" id="start_date_from">
                        </div>

                        <div class="form-group col-md-3">
                            <label for="start_date_to">تاريخ (إلى)</label>
                            <input type="date" class="form-control" name="start_date_to" id="start_date_to">
                        </div>

                        <div class="form-group col-md-3">
                            <label for="currency">العملة</label>
                            <select class="form-control select2" name="currency" id="currency">
                                <option value="">العملة</option>
                                <option value="SAR">SAR ريال سعودي</option>
                                <option value="USD">USD دولار أمريكي</option>
                                <option value="EUR">EUR يورو</option>
                                <option value="GBP">GBP جنيه إسترليني</option>
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="supplier_id">المورد</label>
                            <select class="form-control select2" name="supplier_id" id="supplier_id">
                                <option value="">اختر المورد</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->trade_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="status">الحالة</label>
                            <select class="form-control" name="status" id="status">
                                <option value="">الحالة</option>
                                <option value="1">تحت المراجعة</option>
                                <option value="2">محولة الى فاتورة</option>
                                <option value="0">ملغي</option>
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="tag">الوسم</label>
                            <select class="form-control" name="tag" id="tag">
                                <option value="">اختر الوسم</option>
                                <!-- إضافة الخيارات حسب الحاجة -->
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="type">النوع</label>
                            <select class="form-control" name="type" id="type">
                                <option value="">اختر النوع</option>
                                <option value="1">نوع 1</option>
                                <option value="2">نوع 2</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary mr-1">
                            <i class="fa fa-search"></i> بحث
                        </button>
                        <button type="button" class="btn btn-outline-danger" id="reset-filters">
                            <i class="fa fa-times"></i> إلغاء الفلترة
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- جدول النتائج -->
        <div class="card">
            <div class="card-body">
                <!-- Loading Spinner -->
                <div id="loading-spinner" class="text-center" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">جارِ التحميل...</span>
                    </div>
                </div>

                <!-- محتوى الجدول -->
                <div id="table-container">
                    <!-- سيتم تحميل الجدول هنا عبر AJAX -->
                    <div class="alert alert-info text-center" role="alert">
                        <p class="mb-0">استخدم نموذج البحث أعلاه لعرض النتائج</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // تحميل البيانات الأولية عند تحميل الصفحة
    loadData();

    // البحث عند إرسال النموذج
    $('#search-form').on('submit', function(e) {
        e.preventDefault();
        loadData();
    });

    // البحث الفوري عند تغيير قيم المدخلات
    $('#search-form input, #search-form select').on('change', function() {
        loadData();
    });

    // إعادة تعيين الفلاتر
    $('#reset-filters').on('click', function() {
        $('#search-form')[0].reset();
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

    // دالة تحميل البيانات
    function loadData(page = 1) {
        // إظهار مؤشر التحميل
        $('#loading-spinner').show();
        $('#table-container').hide();

        // جمع بيانات النموذج
        let formData = $('#search-form').serialize();
        formData += '&page=' + page;

        $.ajax({
            url: '{{ route("OrdersRequests.index") }}',
            method: 'GET',
            data: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.success) {
                    // تحديث محتوى الجدول
                    $('#table-container').html(response.data);

                    // تحديث الترقيم
                    $('#pagination-container').html(response.pagination);

                    // تحديث عداد النتائج
                    if (response.total > 0) {
                        $('#results-counter').text(response.from + '-' + response.to + ' من ' + response.total);
                    } else {
                        $('#results-counter').text('0 نتيجة');
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('خطأ في تحميل البيانات:', error);
                $('#table-container').html(
                    '<div class="alert alert-danger text-center">' +
                    '<p class="mb-0">حدث خطأ في تحميل البيانات. يرجى المحاولة مرة أخرى.</p>' +
                    '</div>'
                );
            },
            complete: function() {
                // إخفاء مؤشر التحميل
                $('#loading-spinner').hide();
                $('#table-container').show();
            }
        });
    }

    // التحديد الجماعي للصفوف
    $(document).on('change', '#selectAll', function() {
        $('.order-checkbox').prop('checked', $(this).prop('checked'));
    });

    // تحديث حالة خانة "تحديد الكل" عند تغيير الصفوف الفردية
    $(document).on('change', '.order-checkbox', function() {
        let totalCheckboxes = $('.order-checkbox').length;
        let checkedCheckboxes = $('.order-checkbox:checked').length;

        $('#selectAll').prop('checked', totalCheckboxes === checkedCheckboxes);
        $('#selectAll').prop('indeterminate', checkedCheckboxes > 0 && checkedCheckboxes < totalCheckboxes);
    });
});
</script>
@endsection
