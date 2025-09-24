@extends('master')

@section('title', 'إدارة أرصدة الإجازات')

@section('content')
    <!-- Header Section -->
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">إدارة أرصدة الإجازات</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">الرئيسية</a></li>
                            <li class="breadcrumb-item active">أرصدة الإجازات</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions Card -->
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div class="d-flex align-items-center">
                    <a href="{{ route('employee_leave_balances.create') }}" class="btn btn-primary mr-2">
                        <i class="fa fa-plus"></i> إضافة رصيد جديد
                    </a>
                    <button type="button" id="recalculateBtn" class="btn btn-outline-warning mr-2" title="إعادة حساب الأرصدة">
                        <i class="fa fa-calculator"></i> إعادة حساب الأرصدة
                    </button>
                    <button type="button" id="exportBtn" class="btn btn-outline-success" title="تصدير البيانات">
                        <i class="fa fa-file-excel"></i> تصدير
                    </button>
                </div>
                <div class="d-flex align-items-center">
                    <span class="badge badge-info mr-2">إجمالي الأرصدة: {{ $balances->total() }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('employee_leave_balances.index') }}" id="filtersForm">
                <div class="row">
                    <div class="col-md-3">
                        <label for="employee_id" class="form-label">الموظف</label>
                        <select class="form-control select2" id="employee_id" name="employee_id">
                            <option value="">جميع الموظفين</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->full_name }} - {{ $employee->employee_code ?? $employee->id }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="leave_type_id" class="form-label">نوع الإجازة</label>
                        <select class="form-control select2" id="leave_type_id" name="leave_type_id">
                            <option value="">جميع الأنواع</option>
                            @foreach($leaveTypes as $leaveType)
                                <option value="{{ $leaveType->id }}" {{ request('leave_type_id') == $leaveType->id ? 'selected' : '' }}>
                                    {{ $leaveType->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="year" class="form-label">السنة</label>
                        <select class="form-control" id="year" name="year">
                            <option value="">جميع السنوات</option>
                            @for($i = date('Y') + 1; $i >= date('Y') - 5; $i--)
                                <option value="{{ $i }}" {{ request('year', date('Y')) == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-outline-primary mr-2">
                            <i class="fa fa-search"></i> بحث
                        </button>
                        <a href="{{ route('employee_leave_balances.index') }}" class="btn btn-outline-secondary">
                            <i class="fa fa-refresh"></i> إعادة تعيين
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Balances Table -->
    <div class="card">
        <div class="card-body">
            @if($balances->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="balancesTable">
                        <thead class="thead-dark">
                            <tr>
                                <th>#</th>
                                <th>الموظف</th>
                                <th>نوع الإجازة</th>
                                <th>السنة</th>
                                <th>الرصيد المبدئي</th>
                                <th>المرحل</th>
                                <th>الإضافي</th>
                                <th>الإجمالي</th>
                                <th>المستخدم</th>
                                <th>المتبقي</th>
                                <th>النسبة المئوية</th>
                                <th>الحالة</th>
                                <th>آخر تحديث</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($balances as $balance)
                                <tr>
                                    <td>{{ $loop->iteration + ($balances->currentPage() - 1) * $balances->perPage() }}</td>
                                    <td>
                                        <div>
                                            <strong>{{ $balance->employee->full_name }}</strong><br>
                                            <small class="text-muted">{{ $balance->employee->employee_code ?? $balance->employee->id }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge" style="background-color: {{ $balance->leaveType->color ?? '#6c757d' }}; color: white;">
                                            {{ $balance->leaveType->name }}
                                        </span>
                                    </td>
                                    <td>{{ $balance->year }}</td>
                                    <td>{{ $balance->initial_balance }}</td>
                                    <td>{{ $balance->carried_forward }}</td>
                                    <td>{{ $balance->additional_balance }}</td>
                                    <td><strong>{{ $balance->getTotalAvailableBalance() }}</strong></td>
                                    <td>{{ $balance->used_balance }}</td>
                                    <td class="{{ $balance->getActualRemainingBalance() <= 5 ? 'text-danger' : 'text-success' }}">
                                        <strong>{{ $balance->getActualRemainingBalance() }}</strong>
                                    </td>
                                    <td>
                                        @php
                                            $percentage = $balance->getTotalAvailableBalance() > 0
                                                ? round(($balance->used_balance / $balance->getTotalAvailableBalance()) * 100, 1)
                                                : 0;
                                        @endphp
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar
                                                {{ $percentage >= 90 ? 'bg-danger' : ($percentage >= 75 ? 'bg-warning' : 'bg-success') }}"
                                                role="progressbar"
                                                style="width: {{ $percentage }}%"
                                                aria-valuenow="{{ $percentage }}"
                                                aria-valuemin="0"
                                                aria-valuemax="100">
                                                {{ $percentage }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($percentage >= 90)
                                            <span class="badge badge-danger">مكتمل تقريباً</span>
                                        @elseif($percentage >= 75)
                                            <span class="badge badge-warning">مرتفع</span>
                                        @elseif($percentage >= 50)
                                            <span class="badge badge-info">متوسط</span>
                                        @else
                                            <span class="badge badge-success">منخفض</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $balance->updated_at->format('Y-m-d') }}</small><br>
                                        <small class="text-muted">{{ $balance->updated_at->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('employee_leave_balances.edit', $balance->id) }}"
                                               class="btn btn-sm btn-outline-primary" title="تعديل">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            @if($balance->used_balance == 0)
                                                <button type="button" class="btn btn-sm btn-outline-danger delete-btn"
                                                        data-id="{{ $balance->id }}"
                                                        data-employee="{{ $balance->employee->full_name }}"
                                                        data-leave-type="{{ $balance->leaveType->name }}"
                                                        title="حذف">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-sm btn-secondary" disabled title="لا يمكن الحذف - الرصيد مُستخدم">
                                                    <i class="fa fa-lock"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $balances->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="fa fa-calendar-times fa-3x text-muted"></i>
                    </div>
                    <h5 class="text-muted">لا توجد أرصدة إجازات</h5>
                    <p class="text-muted">لم يتم العثور على أي أرصدة بالفلاتر المحددة</p>
                    <a href="{{ route('employee_leave_balances.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus"></i> إضافة رصيد جديد
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Initialize Select2
        $('.select2').select2({
            placeholder: 'اختر...',
            allowClear: true
        });

        // Auto-submit filters on change
        $('.form-control').on('change', function() {
            if (this.id !== 'employee_id' && this.id !== 'leave_type_id') {
                document.getElementById('filtersForm').submit();
            }
        });

        // Delete functionality
        document.addEventListener('click', function(e) {
            if (e.target.closest('.delete-btn')) {
                const btn = e.target.closest('.delete-btn');
                const id = btn.getAttribute('data-id');
                const employee = btn.getAttribute('data-employee');
                const leaveType = btn.getAttribute('data-leave-type');

                Swal.fire({
                    title: 'تأكيد الحذف',
                    text: `هل تريد حذف رصيد إجازة "${leaveType}" للموظف "${employee}"؟`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'نعم، احذف',
                    cancelButtonText: 'إلغاء'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`/admin/employee-leave-balances/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json',
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: 'تم الحذف!',
                                    text: data.message,
                                    icon: 'success',
                                    confirmButtonText: 'موافق'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: 'خطأ!',
                                    text: data.message,
                                    icon: 'error',
                                    confirmButtonText: 'موافق'
                                });
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                title: 'خطأ!',
                                text: 'حدث خطأ أثناء الحذف',
                                icon: 'error',
                                confirmButtonText: 'موافق'
                            });
                        });
                    }
                });
            }
        });

        // Recalculate balances
        document.getElementById('recalculateBtn').addEventListener('click', function() {
            Swal.fire({
                title: 'إعادة حساب الأرصدة',
                text: 'سيتم إعادة حساب جميع الأرصدة بناءً على الطلبات المعتمدة. هل تريد المتابعة؟',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'نعم، احسب',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'جاري إعادة الحساب...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    fetch('/recalculate', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            year: document.getElementById('year').value || new Date().getFullYear(),
                            employee_id: document.getElementById('employee_id').value
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'تم بنجاح!',
                                text: data.message,
                                icon: 'success',
                                confirmButtonText: 'موافق'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'خطأ!',
                                text: data.message,
                                icon: 'error',
                                confirmButtonText: 'موافق'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            title: 'خطأ!',
                            text: 'حدث خطأ أثناء إعادة الحساب',
                            icon: 'error',
                            confirmButtonText: 'موافق'
                        });
                    });
                }
            });
        });

        // Export functionality (placeholder)
        document.getElementById('exportBtn').addEventListener('click', function() {
            Swal.fire({
                title: 'قريباً',
                text: 'ميزة التصدير ستكون متاحة قريباً',
                icon: 'info',
                confirmButtonText: 'موافق'
            });
        });

        // Handle session messages
        @if(session('success'))
            Swal.fire({
                title: 'تم بنجاح!',
                text: '{{ session('success') }}',
                icon: 'success',
                confirmButtonText: 'موافق',
                confirmButtonColor: '#28a745'
            });
        @endif

        @if(session('error'))
            Swal.fire({
                title: 'حدث خطأ!',
                text: '{{ session('error') }}',
                icon: 'error',
                confirmButtonText: 'موافق',
                confirmButtonColor: '#d33'
            });
        @endif
    });
</script>
@endsection
