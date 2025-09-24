@extends('master')

@section('title')
أيام الحضور
@stop

@section('content')

<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">أيام الحضور</h2>
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

    <div class="card">
        <div class="card-content">
            <div class="card-body">
                <div class="card-title">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>بحث</div>
                        <div>
                            <a href="{{ route('attendanceDays.create') }}" class="btn btn-outline-success">
                                <i class="fa fa-plus me-2"></i>أضف الحضور اليومي
                            </a>
                               <a href="{{ route('barcode.scan') }}" class="btn btn-outline-success">
                                <i class="fa fa-camera me-2"></i>  كاميرا مسح الباركود
                            </a>
                            <a href="{{ route('attendanceDays.calculation') }}" class="btn btn-outline-primary">
                                <i class="fa fa-calendar-alt me-2"></i>أحسب الحضور
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <!-- إضافة div للتحكم في حالة التحميل -->
                <div id="loading" class="text-center" style="display: none;">
                    <i class="fa fa-spinner fa-spin fa-2x"></i>
                    <p>جاري البحث...</p>
                </div>

                <form class="form" id="filterForm">
                    @csrf
                    <div class="form-body row">
                        <div class="form-group col-md-4">
                            <label for="keywords">البحث بواسطة الموظف</label>
                            <input type="text" class="form-control filter-input" placeholder="ادخل الإسم او الكود" name="keywords" id="keywords">
                        </div>
                        <div class="form-group col-4">
                            <label for="from_date">من تاريخ</label>
                            <input type="date" class="form-control filter-input" name="from_date" id="from_date">
                        </div>
                        <div class="form-group col-4">
                            <label for="to_date">الي تاريخ</label>
                            <input type="date" class="form-control filter-input" name="to_date" id="to_date">
                        </div>
                    </div>

                    <!-- Hidden Div للبحث المتقدم -->
                    <div class="collapse" id="advancedSearchForm">
                        <div class="form-body row">
                            <div class="form-group col-4">
                                <label for="status">حالة الحضور</label>
                                <select class="form-control filter-input" name="status" id="status">
                                    <option value="">جميع الحالات</option>
                                    <option value="present">حاضر</option>
                                    <option value="absent">غياب</option>
                                    <option value="holiday">يوم اجازة</option>
                                </select>
                            </div>
                            <div class="col-md-4">
    <label for="department" class="form-label">أختر قسم</label>
    <select class="form-control select2 filter-input" id="department" name="department">
        <option value="">-- اختر القسم --</option>
        @foreach($departments as $department)
            <option value="{{ $department->id }}"
                {{ request('department') == $department->id ? 'selected' : '' }}>
                {{ $department->name }}
            </option>
        @endforeach
    </select>
</div>

<div class="col-md-4">
    <label for="branch" class="form-label">أختر فرع</label>
    <select class="form-control select2 filter-input" id="branch" name="branch">
        <option value="">-- اختر الفرع --</option>
        @foreach($branches as $branch)
            <option value="{{ $branch->id }}"
                {{ request('branch') == $branch->id ? 'selected' : '' }}>
                {{ $branch->name }}
            </option>
        @endforeach
    </select>
</div>

                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary mr-1 waves-effect waves-light">بحث</button>
                        <a class="btn btn-outline-secondary ml-2 mr-2" data-toggle="collapse" data-target="#advancedSearchForm">
                            <i class="bi bi-sliders"></i> بحث متقدم
                        </a>
                        <button type="button" id="clearFilters" class="btn btn-outline-danger waves-effect waves-light">الغاء الفلترة</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-content">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" dir="rtl">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">موظف</th>
                                <th scope="col">التاريخ</th>
                                <th scope="col">تسجيل دخول</th>
                                <th scope="col">تسجيل خروج</th>
                                <th scope="col">ساعات العمل الإجمالية</th>
                                <th scope="col">الحالة</th>
                                <th scope="col">ترتيب بواسطة</th>
                            </tr>
                        </thead>
                        <tbody id="attendance-table-body">
                            @include('hr::attendance.attendance_days.table_rows')
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {

    // دالة لتنفيذ الفلترة
    function performFilter() {
        $('#loading').show();

        const formData = new FormData($('#filterForm')[0]);

        $.ajax({
            url: '{{ route("attendanceDays.filter") }}',
            type: 'GET',
            data: Object.fromEntries(formData),
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $('#attendance-table-body').html(response.html);

                    // إعادة تفعيل الـ tooltips والـ dropdowns إذا لزم الأمر
                    $('[data-toggle="dropdown"]').dropdown();
                }
            },
            error: function(xhr, status, error) {
                console.error('خطأ في الفلترة:', error);
                alert('حدث خطأ أثناء البحث. يرجى المحاولة مرة أخرى.');
            },
            complete: function() {
                $('#loading').hide();
            }
        });
    }

    // تنفيذ الفلترة عند الكتابة في حقول النص (مع تأخير)
    let searchTimeout;
    $('.filter-input').on('keyup change', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            performFilter();
        }, 300); // تأخير 300ms لتجنب الكثير من الطلبات
    });

    // تنفيذ الفلترة عند إرسال النموذج
    $('#filterForm').on('submit', function(e) {
        e.preventDefault();
        performFilter();
    });

    // مسح جميع الفلاتر
    $('#clearFilters').on('click', function() {
        $('#filterForm')[0].reset();
        performFilter();
    });

    // تنفيذ الفلترة عند تغيير التواريخ والقوائم المنسدلة
    $('input[type="date"], select').on('change', function() {
        performFilter();
    });
});
</script>
@endsection
