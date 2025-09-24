@extends('master')

@section('title', 'محددات الحضور')

@section('styles')
<style>
    .content-header {
        background: #f8f9fa;
        padding: 1rem;
        margin-bottom: 1.5rem;
        border-radius: 0.5rem;
    }

    .breadcrumb {
        background: transparent;
        margin: 0;
    }

    .card {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        margin-bottom: 1.5rem;
    }

    .card-title {
        background: #f8f9fa;
        padding: 1rem;
        margin: 0;
        border-bottom: 1px solid #dee2e6;
        font-weight: 600;
    }

    .table th {
        background: #f8f9fa;
        font-weight: 600;
        border-top: none;
    }

    .bullet {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }

    .bullet-success {
        background-color: #28a745;
    }

    .bullet-danger {
        background-color: #dc3545;
    }

    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.8);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        display: none;
    }

    .fade-in {
        animation: fadeIn 0.3s ease-in;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .btn-group .dropdown-menu {
        min-width: 150px;
    }

    .dropdown-item i {
        width: 16px;
        margin-left: 8px;
    }

    .table-loading {
        text-align: center;
        padding: 3rem;
        color: #6c757d;
    }

    .no-data {
        text-align: center;
        padding: 3rem;
        color: #6c757d;
    }
</style>
@endsection

@section('content')
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">جارٍ التحميل...</span>
        </div>
    </div>

    <!-- Header Section -->
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">محددات الحضور</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="" class="text-decoration-none">الرئيسية</a></li>
                            <li class="breadcrumb-item active">عرض</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    <div id="alertContainer"></div>

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
                            <a href="{{ route('attendance_determinants.create') }}" class="btn btn-outline-primary">
                                <i class="fa fa-plus me-2"></i>أضف محدد حضور
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <form class="form" id="searchForm">
                    @csrf
                    <div class="form-body row">
                        <div class="form-group col-md-6">
                            <label for="keywords" class="form-label">اسم محدد الحضور</label>
                            <input type="text" class="form-control" id="keywords" name="keywords"
                                   placeholder="بحث بواسطة اسم محدد الحضور">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="status" class="form-label">الحالة</label>
                            <select name="status" id="status" class="form-control">
                                <option value="">الكل</option>
                                <option value="0">نشط</option>
                                <option value="1">غير نشط</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-actions mt-3">
                        <button type="submit" class="btn btn-primary mr-1">
                            <i class="fa fa-search me-1"></i>بحث
                        </button>
                        <button type="button" id="resetFilter" class="btn btn-outline-danger">
                            <i class="fa fa-times me-1"></i>إلغاء الفلترة
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card">
        <div class="card-content">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">الاسم</th>
                                <th scope="col">التحقق من الموقع</th>
                                <th scope="col">التحقق من IP</th>
                                <th scope="col">التقاط الصورة</th>
                                <th scope="col">الحالة</th>
                                <th scope="col">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attendance_determinants as $determinant)
                            <tr>
                                <td>{{ $determinant->name }}</td>
                                <td>
                                    @if($determinant->enable_location_verification)
                                        <span class="badge bg-success">مفعل</span>
                                    @else
                                        <span class="badge bg-secondary">معطل</span>
                                    @endif
                                </td>
                                <td>
                                    @if($determinant->enable_ip_verification)
                                        <span class="badge bg-success">مفعل</span>
                                    @else
                                        <span class="badge bg-secondary">معطل</span>
                                    @endif
                                </td>
                                <td>
                                    @if($determinant->capture_employee_image)
                                        <span class="badge bg-success">مفعل</span>
                                    @else
                                        <span class="badge bg-secondary">معطل</span>
                                    @endif
                                </td>
                                <td>
                                    @if($determinant->status == 0)
                                        <span class="mr-1 bullet bullet-success bullet-sm"></span>
                                        <span class="mail-date">نشط</span>
                                    @else
                                        <span class="mr-1 bullet bullet-danger bullet-sm"></span>
                                        <span class="mail-date">غير نشط</span>
                                    @endif
                                </td>
                                <td style="width: 10%">
                                    <div class="btn-group">
                                        <div class="dropdown">
                                            <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1 btn-sm"
                                                    type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="{{ route('attendance_determinants.show', $determinant->id) }}">
                                                    <i class="fa fa-eye me-2 text-primary"></i>عرض
                                                </a>
                                                <a class="dropdown-item" href="{{ route('attendance_determinants.edit', $determinant->id) }}">
                                                    <i class="fa fa-edit me-2 text-success"></i>تعديل
                                                </a>
                                                <button class="dropdown-item text-danger delete-btn"
                                                        data-id="{{ $determinant->id }}" data-name="{{ $determinant->name }}">
                                                    <i class="fa fa-trash me-2"></i>حذف
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">
                                    <div class="alert alert-info" role="alert">
                                        <i class="fa fa-info-circle fa-2x mb-3"></i>
                                        <p class="mb-0">لا توجد محددات حضور مضافة حتى الآن!</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-3">
                    {{ $attendance_determinants->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade text-left" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #EA5455 !important;">
                    <h4 class="modal-title text-white">حذف محدد الحضور</h4>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <strong>هل أنت متأكد من أنك تريد الحذف؟</strong>
                    <p class="mt-2 text-muted">سيتم حذف جميع البيانات المرتبطة بهذا المحدد.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">إلغاء</button>
                    <form id="deleteForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">تأكيد الحذف</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    // Delete button click handler
    $('.delete-btn').on('click', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');

        $('#deleteForm').attr('action', `/attendance_determinants/${id}`);
        $('.modal-title').html(`حذف ${name}`);
        $('#deleteModal').modal('show');
    });

    // Delete form submission
    $('#deleteForm').on('submit', function(e) {
        e.preventDefault();

        Swal.fire({
            title: 'جارٍ الحذف...',
            text: 'يرجى الانتظار',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        this.submit();
    });

    // Search functionality
    $('#searchForm').on('submit', function(e) {
        e.preventDefault();

        const keywords = $('#keywords').val();
        const status = $('#status').val();

        let url = new URL(window.location.href);
        url.searchParams.set('keywords', keywords);
        url.searchParams.set('status', status);

        window.location.href = url.toString();
    });

    // Reset filter
    $('#resetFilter').on('click', function() {
        window.location.href = '{{ route("attendance_determinants.index") }}';
    });
});
</script>
@endsection
