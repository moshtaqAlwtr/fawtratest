@extends('master')

@section('title')
    طلبات الأجازة
@stop

@section('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .nav-tabs .nav-link {
            border-radius: 0.375rem 0.375rem 0 0;
            margin-bottom: -1px;
            background: none;
            border: 1px solid transparent;
        }

        .nav-tabs .nav-link.active {
            color: #495057;
            background-color: #fff;
            border-color: #dee2e6 #dee2e6 #fff;
        }

        .badge {
            font-size: 0.75em;
        }

        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }

        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
            border-top: none;
        }

        .btn-group .dropdown-menu {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .balance-card {
            transition: transform 0.2s;
        }

        .balance-card:hover {
            transform: translateY(-2px);
        }
    </style>
@endsection

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">طلبات الأجازة</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a></li>
                            <li class="breadcrumb-item active">طلبات الأجازة</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- بطاقة التبويبات والأزرار -->
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
            <!-- التبويبات -->
            <ul class="nav nav-tabs card-header-tabs" id="statusTabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" id="all-tab" data-status="" type="button">
                        <i class="fas fa-list me-2"></i>الكل
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="pending-tab" data-status="pending" type="button">
                        <i class="fas fa-clock me-2"></i>تحت المراجعة
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="approved-tab" data-status="approved" type="button">
                        <i class="fas fa-check-circle me-2"></i>موافق عليه
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="rejected-tab" data-status="rejected" type="button">
                        <i class="fas fa-times-circle me-2"></i>مرفوض
                    </button>
                </li>
            </ul>

            <!-- الأزرار -->
            <div class="mt-2 mt-md-0">
                <a href="{{ route('attendance.leave_requests.create') }}" class="btn btn-success me-2">
                    <i class="fa fa-plus me-2"></i>أضف طلب أجازة
                </a>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#leaveBalanceModal">
                    <i class="fa fa-calendar-alt me-2"></i>رصيد الأجازات
                </button>
            </div>
        </div>
    </div>

    <!-- بطاقة البحث والتصفية -->
    <div class="card mt-3">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-search me-2"></i>بحث وتصفية
            </h5>
        </div>
        <div class="card-body">
            <form id="searchForm" class="form">
                <div class="row">
                    <div class="form-group col-md-3 mb-3">
                        <label for="employee_search" class="form-label">البحث بواسطة الموظف</label>
                        <input type="text" class="form-control" placeholder="ادخل الإسم أو الكود"
                            name="employee_search" id="employee_search">
                    </div>
                    <div class="form-group col-md-3 mb-3">
                        <label for="from_date" class="form-label">من تاريخ</label>
                        <input type="date" class="form-control" name="from_date" id="from_date">
                    </div>
                    <div class="form-group col-md-3 mb-3">
                        <label for="to_date" class="form-label">إلى تاريخ</label>
                        <input type="date" class="form-control" name="to_date" id="to_date">
                    </div>
                    <div class="form-group col-md-3 mb-3">
                        <label for="created_date" class="form-label">تاريخ الإنشاء</label>
                        <input type="date" class="form-control" name="created_date" id="created_date">
                    </div>
                </div>

                <!-- البحث المتقدم -->
                <div class="collapse" id="advancedSearchForm">
                    <div class="row">
                        <div class="form-group col-md-4 mb-3">
                            <label for="leave_type" class="form-label">نوع الإجازة</label>
                            <select class="form-control" name="leave_type" id="leave_type">
@foreach ($leaveTypes as $leaveType)
    <option value="{{ $leaveType->id }}">{{ $leaveType->name }}</option>
@endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="department" class="form-label">القسم</label>
                            <select class="form-control" name="department" id="department">
                                <option value="">كل الأقسام</option>
@foreach ($departments as $department)
    <option value="{{ $department->id }}">{{ $department->name }}</option>
@endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="branch" class="form-label">الفرع</label>
                            <select class="form-control" name="branch" id="branch">
                                <option value="">كل الفروع</option>
@foreach ($branches as $branch)
    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
@endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-actions d-flex flex-wrap gap-2">
                    <button type="button" id="searchBtn" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>بحث
                    </button>
                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="collapse"
                        data-bs-target="#advancedSearchForm" aria-expanded="false">
                        <i class="bi bi-sliders me-2"></i>بحث متقدم
                    </button>
                    <button type="button" id="clearFilters" class="btn btn-outline-danger">
                        <i class="fas fa-times me-2"></i>مسح الفلاتر
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- جدول النتائج -->
    <div class="card mt-3">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-table me-2"></i>نتائج طلبات الإجازة
                <span id="totalResults" class="badge bg-primary ms-2"></span>
            </h5>
        </div>
        <div class="card-body p-0 position-relative">
            <div id="loadingOverlay" class="loading-overlay" style="display: none;">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">جاري التحميل...</span>
                    </div>
                    <p class="mt-2">جاري تحميل البيانات...</p>
                </div>
            </div>


        </div>

    </div>

    <!-- مودال رصيد الإجازات -->
    <div class="modal fade" id="leaveBalanceModal" tabindex="-1" aria-labelledby="leaveBalanceModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="leaveBalanceModalLabel">
                        <i class="fas fa-calendar-check me-2"></i>رصيد الإجازات
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                </div>
                <div class="modal-body">
                    <!-- البحث عن الموظف -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <label for="employeeSelect" class="form-label">
                                <i class="fas fa-user me-2"></i>اختر الموظف
                            </label>
                            <select class="form-control form-control-lg" id="employeeSelect">
                                <option value="">-- اختر موظف --</option>
                            </select>
                        </div>
                    </div>

                    <!-- Loading Spinner -->
                    <div id="loadingSpinner" class="text-center" style="display: none;">
                        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                            <span class="visually-hidden">جاري التحميل...</span>
                        </div>
                        <p class="mt-3 text-muted">جاري تحميل بيانات الموظف...</p>
                    </div>

                    <!-- رصيد الإجازات -->
                    <div id="leaveBalanceContent" style="display: none;">
                        <!-- معلومات الموظف -->
                        <div class="text-center mb-4 p-3 bg-light rounded">
                            <h4 id="selectedEmployeeName" class="text-primary"></h4>
                            <p class="text-muted mb-0" id="selectedEmployeeCode"></p>
                        </div>

                        <!-- الأرصدة العامة -->
                        <div class="row text-center mb-4">
                            <div class="col-md-4 mb-3">
                                <div class="card bg-primary text-white balance-card">
                                    <div class="card-body">
                                        <i class="fas fa-calendar fa-2x mb-2"></i>
                                        <h2 id="totalBalance" class="mb-0">0</h2>
                                        <p class="mb-0">إجمالي الرصيد</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card bg-warning text-white balance-card">
                                    <div class="card-body">
                                        <i class="fas fa-calendar-minus fa-2x mb-2"></i>
                                        <h2 id="usedBalance" class="mb-0">0</h2>
                                        <p class="mb-0">المستخدم</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card bg-success text-white balance-card">
                                    <div class="card-body">
                                        <i class="fas fa-calendar-plus fa-2x mb-2"></i>
                                        <h2 id="remainingBalance" class="mb-0">0</h2>
                                        <p class="mb-0">المتبقي</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- تفاصيل أنواع الإجازات -->
                        <div class="mt-4">
                            <h5 class="mb-3">
                                <i class="fas fa-list-alt me-2"></i>تفاصيل الإجازات حسب النوع
                            </h5>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-primary">
                                        <tr>
                                            <th>نوع الإجازة</th>
                                            <th class="text-center">الرصيد الكلي</th>
                                            <th class="text-center">المستخدم</th>
                                            <th class="text-center">المتبقي</th>
                                            <th class="text-center">النسبة المستخدمة</th>
                                        </tr>
                                    </thead>
                                    <tbody id="leaveTypesBalance">
                                        <!-- سيتم ملؤها ديناميكياً -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- رسالة عدم وجود بيانات -->
                    <div id="noEmployeeSelected" class="text-center py-5">
                        <i class="fas fa-user-clock fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">اختر موظف لعرض رصيد الإجازات</h5>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>إغلاق
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- مودال تأكيد الحذف -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle me-2"></i>تأكيد الحذف
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>هل أنت متأكد من حذف طلب الإجازة هذا؟</p>
                    <p class="text-muted">لا يمكن التراجع عن هذا الإجراء.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                        <i class="fas fa-trash me-2"></i>حذف
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            let currentPage = 1;
            let currentFilters = {};
            let deleteRequestId = null;

            // إعداد AJAX headers
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // تحميل قائمة الموظفين عند فتح المودال
            $('#leaveBalanceModal').on('show.bs.modal', function() {
                console.log('فتح مودال رصيد الإجازات...');
                loadEmployees();
                resetBalanceModal();
            });

            // عند اختيار موظف في مودال رصيد الإجازات
            $('#employeeSelect').on('change', function() {
                const employeeId = $(this).val();
                console.log('تم اختيار الموظف:', employeeId);

                if (employeeId && employeeId !== '') {
                    loadEmployeeLeaveBalance(employeeId);
                } else {
                    resetBalanceModal();
                }
            });

            // التبويبات - فلترة حسب الحالة
            $('.nav-link').on('click', function(e) {
                e.preventDefault();
                $('.nav-link').removeClass('active');
                $(this).addClass('active');

                const status = $(this).data('status');
                currentFilters.status = status;
                currentPage = 1;
                searchLeaveRequests();
            });

            // البحث
            $('#searchBtn').on('click', function() {
                currentPage = 1;
                searchLeaveRequests();
            });

            // مسح الفلاتر
            $('#clearFilters').on('click', function() {
                $('#searchForm')[0].reset();
                currentFilters = {};
                currentPage = 1;
                $('.nav-link').removeClass('active');
                $('#all-tab').addClass('active');
                searchLeaveRequests();
            });

            // البحث التلقائي عند كتابة اسم الموظف
            $('#employee_search').on('keyup', debounce(function() {
                currentPage = 1;
                searchLeaveRequests();
            }, 500));

            // تحديث البحث عند تغيير التواريخ والفلاتر
            $('#from_date, #to_date, #created_date, #leave_type, #department, #branch').on('change', function() {
                currentPage = 1;
                searchLeaveRequests();
            });

            // وظيفة البحث الرئيسية
            function searchLeaveRequests() {
                // جمع البيانات من النموذج
                currentFilters = {
                    employee_search: $('#employee_search').val(),
                    from_date: $('#from_date').val(),
                    to_date: $('#to_date').val(),
                    created_date: $('#created_date').val(),
                    leave_type: $('#leave_type').val(),
                    department: $('#department').val(),
                    branch: $('#branch').val(),
                    status: $('.nav-link.active').data('status') || '',
                    page: currentPage
                };

                // إظهار مؤشر التحميل
                showTableLoading();

                $.ajax({
                    url: "{{ route('attendance.leave_requests.search') }}",
                    method: 'GET',
                    data: currentFilters,
                    success: function(response) {
                        $('#loadingOverlay').hide();

                        if (response.table) {
                            $('#tableContainer').html(response.table);
                        }

                        if (response.pagination) {
                            $('#paginationContainer').html(response.pagination);
                        }

                        // تحديث عدد النتائج
                        if (response.total !== undefined) {
                            $('#totalResults').text(response.total);
                        }

                        setupPaginationHandlers();
                        setupActionHandlers();
                    },
                    error: function(xhr, status, error) {
                        $('#loadingOverlay').hide();
                        console.error('خطأ في البحث:', xhr.responseText);
                        showAlert('حدث خطأ أثناء البحث: ' + error, 'danger');
                    }
                });
            }

            // تحميل قائمة الموظفين
            function loadEmployees() {
                $.ajax({
                    url: "{{ route('attendance.employees.list') }}",
                    method: 'GET',
                    beforeSend: function() {
                        console.log('إرسال طلب رصيد الإجازات...');
                    },
                    success: function(response) {
                        console.log('تم تحميل رصيد الإجازات:', response);
                        $('#loadingSpinner').hide();

                        // التأكد من وجود البيانات
                        if (response && response.employee) {
                            // تحديث معلومات الموظف
                            const employeeName = response.employee.name || 'غير محدد';
                            const employeeCode = response.employee.employee_code || 'غير محدد';

                            $('#selectedEmployeeName').text(employeeName);
                            $('#selectedEmployeeCode').text(`كود الموظف: #${employeeCode}`);

                            // تحديث الأرصدة العامة
                            $('#totalBalance').text(response.total_balance || 0);
                            $('#usedBalance').text(response.used_balance || 0);
                            $('#remainingBalance').text(response.remaining_balance || 0);

                            // تحديث تفاصيل أنواع الإجازات
                            const tbody = $('#leaveTypesBalance');
                            tbody.empty();

                            if (response.leave_types && response.leave_types.length > 0) {
                                response.leave_types.forEach(function(type) {
                                    const percentage = type.total_balance > 0 ?
                                        Math.round((type.used_balance / type.total_balance) * 100) : 0;

                                    let progressClass = 'bg-success';
                                    if (percentage > 70) progressClass = 'bg-warning';
                                    if (percentage > 90) progressClass = 'bg-danger';

                                    tbody.append(`
                                        <tr>
                                            <td>
                                                <strong>${type.name || 'غير محدد'}</strong>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-primary">${type.total_balance || 0}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-warning">${type.used_balance || 0}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-success">${type.remaining_balance || 0}</span>
                                            </td>
                                            <td class="text-center">
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar ${progressClass}"
                                                         style="width: ${percentage}%"
                                                         aria-valuenow="${percentage}"
                                                         aria-valuemin="0"
                                                         aria-valuemax="100">
                                                        ${percentage}%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    `);
                                });
                            } else {
                                tbody.append(`
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="fas fa-calendar-times fa-2x mb-2"></i>
                                            <br>لا توجد بيانات إجازات متاحة لهذا الموظف
                                        </td>
                                    </tr>
                                `);
                            }

                            $('#leaveBalanceContent').show();
                        } else {
                            console.error('بيانات غير صحيحة:', response);
                            showAlert('تم استلام بيانات غير صحيحة من الخادم', 'warning');
                            resetBalanceModal();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('خطأ في تحميل رصيد الإجازات:', xhr.responseText);
                        $('#loadingSpinner').hide();

                        let errorMessage = 'حدث خطأ في تحميل رصيد الإجازات';
                        if (xhr.status === 404) {
                            errorMessage = 'الموظف المحدد غير موجود';
                        } else if (xhr.status === 500) {
                            errorMessage = 'خطأ في الخادم، يرجى المحاولة مرة أخرى';
                        } else if (xhr.status === 403) {
                            errorMessage = 'ليس لديك صلاحية لعرض هذه البيانات';
                        }

                        showAlert(errorMessage, 'danger');
                        resetBalanceModal();
                    }
                });
            }

            // إعادة تعيين حالة المودال
            function resetBalanceModal() {
                $('#leaveBalanceContent').hide();
                $('#loadingSpinner').hide();
                $('#noEmployeeSelected').show();
            }

            // إعداد معالجات الصفحات
            function setupPaginationHandlers() {
                $(document).off('click', '.pagination a').on('click', '.pagination a', function(e) {
                    e.preventDefault();
                    const url = $(this).attr('href');
                    if (url) {
                        const urlParams = new URLSearchParams(url.split('?')[1]);
                        const page = urlParams.get('page');
                        if (page) {
                            currentPage = parseInt(page);
                            searchLeaveRequests();
                        }
                    }
                });
            }

            // إعداد معالجات الإجراءات
            function setupActionHandlers() {
                // حذف طلب الإجازة
                $(document).off('click', '.delete-request').on('click', '.delete-request', function() {
                    deleteRequestId = $(this).data('id');
                    $('#deleteConfirmModal').modal('show');
                });

                // الموافقة على طلب
                $(document).off('click', '.approve-request').on('click', '.approve-request', function() {
                    const requestId = $(this).data('id');
                    updateRequestStatus(requestId, 'approved');
                });

                // رفض طلب
                $(document).off('click', '.reject-request').on('click', '.reject-request', function() {
                    const requestId = $(this).data('id');
                    updateRequestStatus(requestId, 'rejected');
                });
            }

            // تأكيد الحذف
            $('#confirmDeleteBtn').on('click', function() {
                if (deleteRequestId) {
                    deleteRequest(deleteRequestId);
                    $('#deleteConfirmModal').modal('hide');
                }
            });

            // حذف طلب الإجازة
            function deleteRequest(requestId) {
                $.ajax({
                    url: `{{ route('attendance.leave_requests.destroy', '') }}/${requestId}`,
                    method: 'DELETE',
                    beforeSend: function() {
                        showTableLoading();
                    },
                    success: function(response) {
                        showAlert('تم حذف الطلب بنجاح', 'success');
                        searchLeaveRequests();
                        deleteRequestId = null;
                    },
                    error: function(xhr, status, error) {
                        console.error('خطأ في حذف الطلب:', xhr.responseText);
                        showAlert('حدث خطأ أثناء حذف الطلب: ' + error, 'danger');
                        $('#loadingOverlay').hide();
                    }
                });
            }

            // تحديث حالة الطلب
            function updateRequestStatus(requestId, status) {
                $.ajax({
                    url: `{{ route('attendance.leave_requests.update_status', '') }}/${requestId}`,
                    method: 'PATCH',
                    data: {
                        status: status
                    },
                    beforeSend: function() {
                        showTableLoading();
                    },
                    success: function(response) {
                        const statusText = status === 'approved' ? 'تمت الموافقة على' : 'تم رفض';
                        showAlert(`${statusText} الطلب بنجاح`, 'success');
                        searchLeaveRequests();
                    },
                    error: function(xhr, status, error) {
                        console.error('خطأ في تحديث حالة الطلب:', xhr.responseText);
                        showAlert('حدث خطأ أثناء تحديث الطلب: ' + error, 'danger');
                        $('#loadingOverlay').hide();
                    }
                });
            }

            // إظهار مؤشر التحميل في الجدول
            function showTableLoading() {
                $('#loadingOverlay').show();
            }

            // وظيفة تأخير التنفيذ
            function debounce(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            }

            // إظهار التنبيهات
            function showAlert(message, type = 'info') {
                // إزالة التنبيهات السابقة
                $('.alert').remove();

                const alertHtml = `
                    <div class="alert alert-${type} alert-dismissible fade show position-fixed"
                         style="top: 20px; right: 20px; z-index: 9999; max-width: 400px;" role="alert">
                        <strong>${getAlertIcon(type)}</strong> ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;

                $('body').append(alertHtml);

                // إخفاء التنبيه تلقائياً بعد 5 ثوان
                setTimeout(() => {
                    $('.alert').fadeOut(500, function() {
                        $(this).remove();
                    });
                }, 5000);
            }

            // الحصول على أيقونة التنبيه
            function getAlertIcon(type) {
                const icons = {
                    'success': '<i class="fas fa-check-circle me-2"></i>',
                    'danger': '<i class="fas fa-exclamation-circle me-2"></i>',
                    'warning': '<i class="fas fa-exclamation-triangle me-2"></i>',
                    'info': '<i class="fas fa-info-circle me-2"></i>'
                };
                return icons[type] || icons['info'];
            }

            // تحميل البيانات عند تحميل الصفحة
            searchLeaveRequests();
            setupActionHandlers();
        });
    </script>
@endsection