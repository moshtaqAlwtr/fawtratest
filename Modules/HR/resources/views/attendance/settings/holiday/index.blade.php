@extends('master')

@section('title')
قوائم العطلات
@stop

@section('content')

<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">قوائم العطلات</h2>
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

    @include('layouts.alerts.success')
    @include('layouts.alerts.error')

    <!-- Search Card -->
    <div class="card">
        <div class="card-content">
            <div class="card-body">
                <div class="card-title">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>بحث</div>
                        <div>
                            <a href="{{ route('holiday_lists.create') }}" class="btn btn-outline-primary">
                                <i class="fa fa-plus me-2"></i>أضف قائمة العطلات
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <form class="form" id="searchForm">
                        @csrf
                        <div class="form-body row">
                            <div class="form-group col-md-12">
                                <label for="keywords">البحث بواسطة اسم القائمة</label>
                                <input type="text" id="keywords" class="form-control"
                                       placeholder="ادخل الإسم او المعرف" name="keywords"
                                       value="{{ request('keywords') }}">
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary mr-1 waves-effect waves-light">
                                <span class="search-text">بحث</span>
                                <span class="search-loading d-none">
                                    <i class="fa fa-spinner fa-spin"></i> جاري البحث...
                                </span>
                            </button>
                            <button type="button" id="clearFilter" class="btn btn-outline-danger waves-effect waves-light">
                                الغاء الفلترة
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
                    @include('hr::attendance.settings.holiday.table-content', compact('holiday_lists'))
                </div>
            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // البحث بـ AJAX
    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        performSearch();
    });

    // البحث التلقائي أثناء الكتابة (اختياري)
    $('#keywords').on('input', function() {
        clearTimeout(window.searchTimeout);
        window.searchTimeout = setTimeout(function() {
            performSearch();
        }, 500); // انتظار نصف ثانية بعد التوقف عن الكتابة
    });

    // مسح الفلتر
    $('#clearFilter').on('click', function() {
        $('#keywords').val('');
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
            _token: $('input[name="_token"]').val()
        };

        $.ajax({
            url: '{{ route("holiday_lists.search") }}',
            method: 'GET',
            data: formData,
            success: function(response) {
                if (response.success) {
                    $('#tableContainer').html(response.html);

                    // تحديث URL بدون إعادة تحميل الصفحة
                    const newUrl = new URL(window.location);
                    if (formData.keywords) {
                        newUrl.searchParams.set('keywords', formData.keywords);
                    } else {
                        newUrl.searchParams.delete('keywords');
                    }
                    window.history.pushState({}, '', newUrl);
                } else {
                    showAlert('حدث خطأ أثناء البحث', 'danger');
                }
            },
            error: function(xhr, status, error) {
                showAlert('حدث خطأ في الاتصال', 'danger');
                console.error('Search error:', error);
            },
            complete: function() {
                // إخفاء مؤشر التحميل
                searchBtn.removeClass('d-none');
                loadingBtn.addClass('d-none');
            }
        });
    }

    // دالة لإظهار التنبيهات
    function showAlert(message, type) {
        const alert = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        `;
        $('.content-body').prepend(alert);

        // إخفاء التنبيه تلقائياً بعد 3 ثوانٍ
        setTimeout(function() {
            $('.alert').fadeOut();
        }, 3000);
    }

    // معالج حذف العناصر بـ AJAX (اختياري)
    $(document).on('click', '.delete-item', function(e) {
        e.preventDefault();
        const deleteUrl = $(this).data('url');
        const itemName = $(this).data('name');

        if (confirm(`هل أنت متأكد من حذف ${itemName}؟`)) {
            $.ajax({
                url: deleteUrl,
                method: 'DELETE',
                data: {
                    _token: $('input[name="_token"]').val()
                },
                success: function(response) {
                    if (response.success) {
                        showAlert('تم الحذف بنجاح', 'success');
                        performSearch(); // إعادة تحميل النتائج
                    } else {
                        showAlert('فشل في الحذف', 'danger');
                    }
                },
                error: function() {
                    showAlert('حدث خطأ أثناء الحذف', 'danger');
                }
            });
        }
    });
});
</script>
@endpush
