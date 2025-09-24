@extends('master')

@section('title')
    أذونات إجازة
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">أذونات إجازة</h2>
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
        <!-- Alert Messages -->
        <div id="alertContainer">
            @include('layouts.alerts.success')
            @include('layouts.alerts.error')
        </div>

        <!-- Search Card -->
        <div class="card">
            <div class="card-content">
                <div class="card-body">
                    <div class="card-title">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div>بحث</div>
                            <div>
                                <a href="{{ route('leave_permissions.create') }}" class="btn btn-outline-primary">
                                    <i class="fa fa-plus me-2"></i>أضف إذن إجازة
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <form class="form" id="searchForm">
                            @csrf
                            <div class="form-body row">
                                <div class="form-group col-md-4">
                                    <label>البحث بواسطة الموظف</label>
                                    <input type="text" class="form-control" placeholder="ادخل الاسم أو الكود"
                                        name="keywords" id="keywords">
                                </div>
                                <div class="form-group col-4">
                                    <label>من تاريخ</label>
                                    <input type="date" class="form-control" name="from_date" id="from_date">
                                </div>
                                <div class="form-group col-4">
                                    <label>إلى تاريخ</label>
                                    <input type="date" class="form-control" name="to_date" id="to_date">
                                </div>
                            </div>

                            <!-- Advanced Search -->
                            <div class="collapse" id="advancedSearchForm">
                                <div class="form-body row">
                                    <div class="form-group col-4">
                                        <label>نوع الإجازة</label>
                                        <select class="form-control" name="status" id="status">
                                            <option value="">كل الأنواع</option>
                                            <option value="annual_leave">اجازة اعتيادية</option>
                                            <option value="emergency_leave">اجازة عرضية</option>
                                        </select>
                                    </div>

                                    <div class="form-group col-4">
                                        <label>حالة الموافقة</label>
                                        <select class="form-control" name="approval_status" id="approval_status">
                                            <option value="">كل الحالات</option>
                                            <option value="0">في الانتظار</option>
                                            <option value="1">موافق عليه</option>
                                            <option value="2">مرفوض</option>
                                        </select>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">اختر قسم</label>
                                        <select class="form-control select2" name="department" id="department">
                                            <option value="">-- اختر القسم --</option>
                                            @foreach ($departments as $department)
                                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">اختر فرع</label>
                                        <select class="form-control select2" name="branch" id="branch">
                                            <option value="">-- اختر الفرع --</option>
                                            @foreach ($branches as $branch)
                                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">اختر الوظيفة</label>
                                        <select class="form-control select2" name="job_title" id="job_title">
                                            <option value="">-- اختر الوظيفة --</option>
                                            @foreach ($job_titles as $job)
                                                <option value="{{ $job->id }}">{{ $job->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary mr-1 waves-effect waves-light">
                                    <i class="fas fa-search me-2"></i>بحث
                                </button>
                                <button type="button" class="btn btn-outline-secondary ml-2 mr-2" data-bs-toggle="collapse"
                                    data-bs-target="#advancedSearchForm">
                                    <i class="fas fa-sliders-h me-2"></i>بحث متقدم
                                </button>
                                <button type="button" class="btn btn-outline-danger waves-effect waves-light"
                                    id="resetFilters">
                                    <i class="fas fa-times me-2"></i>إلغاء الفلترة
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
                    <!-- Loading Spinner -->
                    <div id="loadingSpinner" class="text-center d-none">
                        <div class="spinner-border text-primary" role="status">
                        </div>
                    </div>

                    <!-- Results Table -->
                    <div class="table-responsive" id="resultsContainer">
                        <!-- سيتم تحميل النتائج هنا عبر Ajax -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="deleteModalLabel">حذف إذن الإجازة</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-0"><strong>هل أنت متأكد من أنك تريد الحذف؟</strong></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">تأكيد الحذف</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <script>
        $(document).ready(function() {
            let deleteId = null;

            // Load initial data
            loadLeavePermissions();

            // Search form submission
            $('#searchForm').on('submit', function(e) {
                e.preventDefault();
                loadLeavePermissions();
            });

            // Real-time search as user types
            $('#keywords').on('input', debounce(function() {
                loadLeavePermissions();
            }, 300));

            // Date and filter change handlers
            $('#from_date, #to_date, #status, #approval_status, #department, #branch, #job_title').on('change', function() {
                loadLeavePermissions();
            });

            // Reset filters
            $('#resetFilters').on('click', function() {
                $('#searchForm')[0].reset();
                loadLeavePermissions();
            });

            // Delete confirmation
            $(document).on('click', '.delete-btn', function() {
                deleteId = $(this).data('id');
                $('#deleteModal').modal('show');
            });

            $('#confirmDeleteBtn').on('click', function() {
                if (deleteId) {
                    deleteLeavePermission(deleteId);
                }
            });

            // Function to load leave permissions
            function loadLeavePermissions() {
                showLoading();

                const formData = $('#searchForm').serialize();

                $.ajax({
                    url: "{{ route('leave_permissions.ajax.index') }}",
                    method: 'GET',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        hideLoading();
                        $('#resultsContainer').html(response.html).addClass('fade-in');

                        if (response.message) {
                            showAlert('success', response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        hideLoading();
                        console.error('خطأ في تحميل البيانات:', error);
                        showAlert('danger', 'حدث خطأ في تحميل البيانات. يرجى المحاولة مرة أخرى.');
                    }
                });
            }

            // Function to delete leave permission
            function deleteLeavePermission(id) {
                showLoading();

                $.ajax({
                    url: "{{ route('leave_permissions.destroy', '') }}/" + id,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        hideLoading();
                        $('#deleteModal').modal('hide');

                        if (response.success) {
                            showAlert('success', response.message || 'تم الحذف بنجاح');
                            loadLeavePermissions(); // Reload data
                        } else {
                            showAlert('danger', response.message || 'حدث خطأ أثناء الحذف');
                        }
                    },
                    error: function(xhr, status, error) {
                        hideLoading();
                        $('#deleteModal').modal('hide');
                        console.error('خطأ في الحذف:', error);

                        let errorMessage = 'حدث خطأ أثناء الحذف. يرجى المحاولة مرة أخرى.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        showAlert('danger', errorMessage);
                    }
                });
            }

            // Utility functions
            function showLoading() {
                $('#loadingSpinner').removeClass('d-none');
                $('#resultsContainer').addClass('opacity-50');
            }

            function hideLoading() {
                $('#loadingSpinner').addClass('d-none');
                $('#resultsContainer').removeClass('opacity-50');
            }

            function showAlert(type, message) {
                const alertHtml = `
                    <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
                $('#alertContainer').html(alertHtml);

                // Auto-hide after 5 seconds
                setTimeout(function() {
                    $('.alert').fadeOut();
                }, 5000);
            }

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
        });
    </script>
@endsection
