@extends('master')

@section('title')
سياسة الاجازة
@stop

@section('content')

<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">سياسة الاجازة</h2>
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
                            <a href="{{ route('leave_policy.create') }}" class="btn btn-outline-primary">
                                <i class="fa fa-plus me-2"></i>أضف نوع الاجازة
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <form class="form" id="searchForm">
                        <div class="form-body row">
                            <div class="form-group col-md-6">
                                <label for="">البحث بواسطة اسم السياسة</label>
                                <input type="text" class="form-control" placeholder="ادخل الإسم او المعرف" name="keywords" id="keywords">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="">الحالة</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="">الكل</option>
                                    <option value="0">نشط</option>
                                    <option value="1">غير نشط</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary mr-1 waves-effect waves-light">
                                <i class="fa fa-search me-2"></i>بحث
                            </button>
                            <button type="button" id="clearFilter" class="btn btn-outline-danger waves-effect waves-light">
                                <i class="fa fa-times me-2"></i>الغاء الفلترة
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-content">
                <div class="card-body">
                    <div class="table-responsive" id="tableContainer">
                        @include('hr::attendance.settings.leave_policies.table-content')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // البحث بـ AJAX
    $('#searchForm').on('submit', function(e) {
        e.preventDefault();

        const formData = {
            keywords: $('#keywords').val(),
            status: $('#status').val(),
            _token: '{{ csrf_token() }}'
        };

        $.ajax({
            url: '{{ route("leave_policy.search") }}',
            type: 'POST',
            data: formData,
            beforeSend: function() {
                $('#tableContainer').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i></div>');
            },
            success: function(response) {
                if (response.success) {
                    $('#tableContainer').html(response.html);
                } else {
                    toastr.error('حدث خطأ أثناء البحث');
                }
            },
            error: function() {
                toastr.error('حدث خطأ في الاتصال');
                location.reload();
            }
        });
    });

    // إلغاء الفلترة
    $('#clearFilter').on('click', function() {
        $('#keywords').val('');
        $('#status').val('');
        $('#searchForm').trigger('submit');
    });

    // تحميل إحصائيات الموظفين عند عرض التفاصيل
    $(document).on('click', '.view-employees', function(e) {
        e.preventDefault();

        const policyId = $(this).data('policy-id');
        const button = $(this);

        // تحميل عدد الموظفين
        $.ajax({
            url: '/leave-policies/' + policyId + '/employees-count',
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    button.find('.employees-count').text(response.count);
                }
            }
        });
    });

    // عرض قائمة الموظفين في Modal
    $(document).on('click', '.show-employees-modal', function() {
        const policyId = $(this).data('policy-id');
        const policyName = $(this).data('policy-name');

        $('#employeesModalLabel').text('موظفي سياسة: ' + policyName);
        $('#employeesTableBody').html('<tr><td colspan="4" class="text-center"><i class="fa fa-spinner fa-spin"></i> جاري التحميل...</td></tr>');
        $('#employeesModal').modal('show');

        // تحميل قائمة الموظفين
$.ajax({
    url: "{{ route('leave_policy.employeesList', ':policyId') }}/" + policyId, // استخدام named route
    type: 'GET',
    dataType: 'json', // تأكد من预期 توقع JSON :cite[5]
    success: function(response) {
        if (response.success && response.employees && response.employees.length > 0) {
            let tableContent = '';
            response.employees.forEach(function(employee) {
                tableContent += `
                    <tr>
                        <td>${employee.full_name}</td>
                        <td>${employee.department ? employee.department.name : 'غير محدد'}</td>
                        <td>${employee.job_title ? employee.job_title.name : 'غير محدد'}</td>
                        <td>
                            <span class="badge ${employee.assignment_type === 'مباشر' ? 'badge-primary' : 'badge-info'}">
                                ${employee.assignment_type}
                            </span>
                        </td>
                    </tr>
                `;
            });
            $('#employeesTableBody').html(tableContent);
        } else {
            $('#employeesTableBody').html('<tr><td colspan="4" class="text-center text-muted">لا توجد موظفين مخصصين لهذه السياسة</td></tr>');
        }
    },
    error: function(xhr, status, error) {
        console.error("Error details:", status, error, xhr.responseText); // تسجيل الخطأ للتصحيح
        $('#employeesTableBody').html('<tr><td colspan="4" class="text-center text-danger">حدث خطأ أثناء تحميل البيانات</td></tr>');
    }
});
    });
});
</script>

<!-- Modal لعرض الموظفين -->
<div class="modal fade" id="employeesModal" tabindex="-1" role="dialog" aria-labelledby="employeesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h4 class="modal-title text-white" id="employeesModalLabel">قائمة الموظفين</h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead class="table-light">
                            <tr>
                                <th>اسم الموظف</th>
                                <th>القسم</th>
                                <th>المنصب</th>
                                <th>نوع التخصيص</th>
                            </tr>
                        </thead>
                        <tbody id="employeesTableBody">
                            <!-- سيتم ملؤها بـ AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">إغلاق</button>
            </div>
        </div>
    </div>
</div>
@endsection