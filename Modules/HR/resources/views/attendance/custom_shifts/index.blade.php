@extends('master')

@section('title')
ورديات متخصصه
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">ورديات متخصصه</h2>
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
        @include('layouts.alerts.success')
        @include('layouts.alerts.error')

        <div class="card">
            <div class="card-content">
                <div class="card-body">
                    <div class="card-title">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div>بحث </div>
                            <div>
                                <a href="{{ route('custom_shifts.create') }}" class="btn btn-outline-primary">
                                    <i class="fa fa-plus me-2"></i>أضف ورديات مخصصة
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <form id="filterForm" class="form" method="GET" action="#">
                        <div class="form-body row">
                            <div class="form-group col-md-4">
                                <label for="keywords">البحث بواسطة الموظف</label>
                                <input type="text" class="form-control" placeholder="ادخل الإسم او الكود" name="keywords" id="keywords">
                            </div>
                            <div class="form-group col-4">
                                <label for="from_date">من تاريخ</label>
                                <input type="date" class="form-control" name="from_date" id="from_date">
                            </div>
                            <div class="form-group col-4">
                                <label for="to_date">الي تاريخ</label>
                                <input type="date" class="form-control" name="to_date" id="to_date">
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary mr-1 waves-effect waves-light">بحث</button>
                            <button type="button" id="resetFilter" class="btn btn-outline-danger waves-effect waves-light">الغاء الفلترة</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-content">
                <div class="card-body">
                    <div class="table-responsive">
                        <div id="noDataMessage" class="alert alert-danger text-xl-center d-none" role="alert">
                            <p class="mb-0">
                                لا توجد دفاتر حضور مضافة حتى الان !!
                            </p>
                        </div>

                        <table id="customShiftsTable" class="table table-striped d-none" dir="rtl">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">الاسم</th>
                                    <th scope="col">وردية</th>
                                    <th scope="col">تاريخ البدء</th>
                                    <th scope="col">تاريخ الانتهاء</th>
                                    <th scope="col">اجراء</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                @include('hr::attendance.custom_shifts.partials.table_rows', ['custom_shifts' => $custom_shifts])
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
        // إظهار الجدول أو رسالة عدم وجود بيانات عند التحميل
        @if(isset($custom_shifts) && $custom_shifts->count() > 0)
            $('#customShiftsTable').removeClass('d-none');
        @else
            $('#noDataMessage').removeClass('d-none');
        @endif

        // فلترة البيانات عند تغيير أي حقل
        $('#keywords, #from_date, #to_date').on('input change', function() {
            filterData();
        });

        // إعادة تعيين الفلترة
        $('#resetFilter').on('click', function() {
            $('#keywords').val('');
            $('#from_date').val('');
            $('#to_date').val('');
            filterData();
        });

        // منع إعادة تحميل الصفحة عند الضغط على Enter في النموذج
        $('#filterForm').on('submit', function(e) {
            e.preventDefault();
            filterData();
        });

        // وظيفة الفلترة
        function filterData() {
            const keywords = $('#keywords').val();
            const from_date = $('#from_date').val();
            const to_date = $('#to_date').val();

            $.ajax({
                url: "{{ route('custom_shifts.filter') }}",
                method: 'GET',
                data: {
                    keywords: keywords,
                    from_date: from_date,
                    to_date: to_date
                },
                success: function(response) {
                    if (response.html) {
                        $('#tableBody').html(response.html);

                        if (response.count > 0) {
                            $('#customShiftsTable').removeClass('d-none');
                            $('#noDataMessage').addClass('d-none');
                        } else {
                            $('#customShiftsTable').addClass('d-none');
                            $('#noDataMessage').removeClass('d-none');
                        }
                    }
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        }
    });
</script>
@endsection
