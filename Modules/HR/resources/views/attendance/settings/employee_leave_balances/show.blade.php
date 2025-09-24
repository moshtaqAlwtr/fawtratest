@extends('master')

@section('title')
    عرض رصيد الإجازة
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/logs.css') }}">
@endsection

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">عرض رصيد الإجازة</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">الرئيسية</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('employee_leave_balances.index') }}">أرصدة الإجازات</a></li>
                            <li class="breadcrumb-item active">رصيد #{{ $balance->id }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.alerts.error')
    @include('layouts.alerts.success')

    <!-- Status and Actions Card -->
    <div class="card p-3 d-flex flex-row align-items-center justify-content-between" style="width: 100%;">
        <span class="fw-bold">#رصيد {{ $balance->id }}</span>
        <div class="d-flex gap-2">
            @php
                $percentage = $balance->getTotalAvailableBalance() > 0
                    ? round(($balance->used_balance / $balance->getTotalAvailableBalance()) * 100, 1)
                    : 0;
            @endphp

            @if($percentage >= 90)
                <button class="btn btn-danger">مكتمل تقريباً</button>
            @elseif($percentage >= 75)
                <button class="btn btn-warning">مرتفع الاستخدام</button>
            @elseif($percentage >= 50)
                <button class="btn btn-info">متوسط الاستخدام</button>
            @else
                <button class="btn btn-success">استخدام منخفض</button>
            @endif

            <a href="{{ route('employee_leave_balances.edit', $balance->id) }}" class="btn btn-primary d-flex align-items-center gap-1">
                <i class="fas fa-edit"></i> تعديل الرصيد
            </a>
            <a href="{{ route('employee_leave_balances.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-1">
                <i class="fas fa-arrow-right"></i> العودة
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-title p-2">
            <!-- Quick Actions -->
            <button type="button" id="recalculateBtn" class="btn btn-outline-warning btn-sm waves-effect waves-light"
                    data-employee-id="{{ $balance->employee_id }}" data-year="{{ $balance->year }}">
                <i class="fa fa-calculator"></i> إعادة حساب الرصيد
            </button>

            @if($balance->used_balance == 0)
                <button type="button" class="btn btn-outline-danger btn-sm waves-effect waves-light" data-toggle="modal"
                        data-target="#modal_DELETE{{ $balance->id }}">
                    <i class="fa fa-trash"></i> حذف الرصيد
                </button>
            @endif

            <a href="#" class="btn btn-outline-info btn-sm waves-effect waves-light" data-toggle="modal"
               data-target="#modal_ADD_BALANCE{{ $balance->id }}">
                <i class="fas fa-plus-circle"></i> إضافة رصيد إضافي
            </a>
        </div>

        <div class="card-body">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="details-tab" data-toggle="tab" href="#details" role="tab"
                        aria-controls="details" aria-selected="true">تفاصيل الرصيد</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="activity-log-tab" data-toggle="tab" href="#activity-log" role="tab"
                        aria-controls="activity-log" aria-selected="false">سجل النشاطات</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="requests-log-tab" data-toggle="tab" href="#requests-log" role="tab"
                        aria-controls="requests-log" aria-selected="false">سجل الطلبات</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="statistics-tab" data-toggle="tab" href="#statistics" role="tab"
                        aria-controls="statistics" aria-selected="false">الإحصائيات</a>
                </li>
            </ul>

            <div class="tab-content">
                <!-- تفاصيل الرصيد -->
                <div class="tab-pane fade show active" id="details" role="tabpanel" aria-labelledby="details-tab">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <table class="table">
                                    <thead style="background: #f8f8f8">
                                        <tr>
                                            <th style="width: 70%">معلومات رصيد الإجازة</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <p><small>الرصيد المبدئي</small>:</p>
                                                <strong>{{ $balance->initial_balance }} يوم</strong>
                                            </td>
                                            <td>
                                                <p><small>المرحل من العام السابق</small>:</p>
                                                <strong>{{ $balance->carried_forward }} يوم</strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p><small>الرصيد الإضافي</small>:</p>
                                                <strong>{{ $balance->additional_balance }} يوم</strong>
                                            </td>
                                            <td>
                                                <p><small>إجمالي الرصيد المتاح</small>:</p>
                                                <strong class="text-info">{{ $balance->getTotalAvailableBalance() }} يوم</strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p><small>المستخدم</small>:</p>
                                                <strong class="text-danger">{{ $balance->used_balance }} يوم</strong>
                                            </td>
                                            <td>
                                                <p><small>المتبقي</small>:</p>
                                                <strong class="{{ $balance->getActualRemainingBalance() <= 5 ? 'text-danger' : 'text-success' }}">
                                                    {{ $balance->getActualRemainingBalance() }} يوم
                                                </strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p><small>نسبة الاستخدام</small>:</p>
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
                                                <p><small>السنة</small>:</p>
                                                <strong>{{ $balance->year }}</strong>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                                <table class="table">
                                    <thead style="background: #f8f8f8">
                                        <tr>
                                            <th style="width: 70%">تفاصيل إضافية</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <p><small>الموظف</small>:</p>
                                                <strong>
                                                    <a href="#" class="text-primary text-decoration-none">
                                                        <i class="fas fa-user"></i>
                                                        {{ $balance->employee->full_name }}
                                                        <span class="badge bg-secondary">#{{ $balance->employee->id }}</span>
                                                    </a>
                                                </strong>
                                                <br>
                                                <small class="text-muted">
                                                    <i class="fas fa-building"></i> {{ $balance->employee->department->name ?? 'غير محدد' }} |
                                                    <i class="fas fa-map-marker-alt"></i> {{ $balance->employee->branch->name ?? 'غير محدد' }}
                                                </small>
                                            </td>
                                            <td>
                                                <p><small>المنصب</small>:</p>
                                                <strong>{{ $balance->employee->position->name ?? 'غير محدد' }}</strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p><small>نوع الإجازة</small>:</p>
                                                <strong>
                                                    <span class="badge" style="background-color: {{ $balance->leaveType->color ?? '#6c757d' }}; color: white;">
                                                        {{ $balance->leaveType->name }}
                                                    </span>
                                                </strong>
                                            </td>
                                            <td>
                                                <p><small>وصف النوع</small>:</p>
                                                <strong>{{ $balance->leaveType->description ?? 'لا يوجد وصف' }}</strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p><small>تاريخ الإنشاء</small>:</p>
                                                <strong>{{ $balance->created_at->format('Y-m-d H:i') }}</strong>
                                            </td>
                                            <td>
                                                <p><small>آخر تحديث</small>:</p>
                                                <strong>{{ $balance->updated_at->format('Y-m-d H:i') }}</strong>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- سجل النشاطات -->
<div class="tab-pane fade" id="activity-log" role="tabpanel" aria-labelledby="activity-log-tab">
    <div class="row mt-4">
        <div class="col-12">
            @if ($logs && count($logs) > 0)
                @php
                    $previousDate = null;
                @endphp

                @foreach ($logs as $date => $dayLogs)
                    @php
                        $currentDate = \Carbon\Carbon::parse($date);
                        $diffInDays = $previousDate ? $previousDate->diffInDays($currentDate) : 0;
                    @endphp

                    {{-- عرض التاريخ إذا كان الفرق أكبر من 7 أيام --}}
                    @if ($diffInDays > 7)
                        <div class="timeline-date">
                            <h4>{{ $currentDate->format('Y-m-d') }}</h4>
                        </div>
                    @endif

                    {{-- عرض اليوم --}}
                    <div class="timeline-day">{{ $currentDate->translatedFormat('l') }}</div>

                    <ul class="timeline">
                        @foreach ($dayLogs as $log)
                            <li class="timeline-item">
                                <div class="timeline-content">
                                    <div class="time mb-1">
                                        <i class="far fa-clock"></i>
                                        {{ $log->created_at->format('H:i:s') }}
                                        <span class="ms-2 text-muted">
                                            <i class="far fa-calendar"></i>
                                            {{ $log->created_at->locale('ar')->translatedFormat('l, d F Y') }}
                                        </span>
                                    </div>

                                    <div>
                                        <strong>{{ $log->user->name ?? 'النظام' }}</strong>
                                        <span class="ms-2">
                                            {{ $log->description ?? 'نشاط غير معرف' }}
                                        </span>
                                    </div>

                                    {{-- تفاصيل إضافية إن وجدت --}}
                                    @if(isset($log->details))
                                        <div class="mt-1 text-muted small">
                                            {{ $log->details }}
                                        </div>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>

                    @php
                        $previousDate = $currentDate;
                    @endphp
                @endforeach
            @else
                <div class="alert alert-danger text-center" role="alert">
                    <p class="mb-0">لا توجد سجلات نشاط على الرصيد حتى الآن!</p>
                </div>

                {{-- عرض إنشاء الرصيد كأول نشاط إذا مافيه Logs --}}
                <ul class="timeline mt-3">
                    <li class="timeline-item">
                        <div class="timeline-content">
                            <div class="time mb-1">
                                <i class="far fa-clock"></i>
                                {{ $balance->created_at->format('H:i:s') }}
                                <span class="ms-2 text-muted">
                                    <i class="far fa-calendar"></i>
                                    {{ $balance->created_at->locale('ar')->translatedFormat('l, d F Y') }}
                                </span>
                            </div>
                            <div>
                                <strong>النظام</strong>
                                <span class="ms-2">تم إنشاء رصيد {{ $balance->leaveType->name }}</span>
                            </div>
                            <div class="mt-1 text-muted small">
                                للموظف {{ $balance->employee->full_name }} - الإجمالي {{ $balance->getTotalAvailableBalance() }} يوم لعام {{ $balance->year }}
                            </div>
                        </div>
                    </li>
                </ul>
            @endif
        </div>
    </div>
</div>

                <!-- سجل الطلبات -->
                <div class="tab-pane fade" id="requests-log" role="tabpanel" aria-labelledby="requests-log-tab">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5>طلبات الإجازة المعتمدة لعام {{ $balance->year }}</h5>
                    </div>

                    @if($leaveRequests->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead style="background: #f8f8f8">
                                    <tr>
                                        <th>#</th>
                                        <th>تاريخ البداية</th>
                                        <th>تاريخ النهاية</th>
                                        <th>عدد الأيام</th>
                                        <th>السبب</th>
                                        <th>تاريخ الطلب</th>
                                        <th>الحالة</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($leaveRequests as $request)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ \Carbon\Carbon::parse($request->start_date)->format('Y-m-d') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($request->end_date)->format('Y-m-d') }}</td>
                                            <td>
                                                <span class="badge badge-info">{{ $request->total_days }} أيام</span>
                                            </td>
                                            <td>
                                                <span class="text-truncate" style="max-width: 200px; display: inline-block;"
                                                      title="{{ $request->reason }}">
                                                    {{ Str::limit($request->reason, 50) }}
                                                </span>
                                            </td>
                                            <td>{{ $request->created_at->format('Y-m-d') }}</td>
                                            <td>
                                                <span class="badge bg-success">معتمد</span>
                                            </td>
                                            <td>
                                                <a href="{{ route('attendance.leave_requests.show', $request->id) }}"
                                                   class="btn btn-sm btn-outline-primary" title="عرض التفاصيل">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="fa fa-calendar-times fa-3x text-muted"></i>
                            </div>
                            <h5 class="text-muted">لا توجد طلبات إجازات معتمدة</h5>
                            <p class="text-muted">لم يتم تقديم أي طلبات إجازة معتمدة لهذا النوع في عام {{ $balance->year }}</p>
                        </div>
                    @endif
                </div>

                <!-- الإحصائيات -->
                <div class="tab-pane fade" id="statistics" role="tabpanel" aria-labelledby="statistics-tab">
                    <div class="position-relative">
                        <div class="card" style="background: #f8f9fa">
                            <div class="card-body p-4">
                                <h5 class="mb-4">إحصائيات الموظف لعام {{ $balance->year }}</h5>



                                <!-- الرسم البياني للاستخدام -->
                                <div class="card border-0 mt-4">
                                    <div class="card-body">
                                        <h6 class="card-title">توزيع الرصيد</h6>
                                        <div class="row text-center">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <h3 class="text-success">{{ $balance->getActualRemainingBalance() }}</h3>
                                                    <p class="text-muted">المتبقي</p>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <h3 class="text-danger">{{ $balance->used_balance }}</h3>
                                                    <p class="text-muted">المستخدم</p>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <h3 class="text-info">{{ $balance->getTotalAvailableBalance() }}</h3>
                                                    <p class="text-muted">الإجمالي</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal delete -->
    @if($balance->used_balance == 0)
        <div class="modal fade text-left" id="modal_DELETE{{ $balance->id }}" tabindex="-1" role="dialog"
            aria-labelledby="myModalLabel1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="background-color: #EA5455 !important;">
                        <h4 class="modal-title" id="myModalLabel1" style="color: #FFFFFF">حذف رصيد الإجازة</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" style="color: #DC3545">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <strong>
                            هل أنت متأكد من أنك تريد حذف رصيد إجازة "{{ $balance->leaveType->name }}"
                            للموظف "{{ $balance->employee->full_name }}"؟
                        </strong>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light waves-effect waves-light"
                            data-dismiss="modal">إلغاء</button>
                        <button type="button" class="btn btn-danger waves-effect waves-light delete-confirm"
                                data-id="{{ $balance->id }}">تأكيد</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Add Additional Balance -->
    <div class="modal fade text-left" id="modal_ADD_BALANCE{{ $balance->id }}" tabindex="-1" role="dialog"
        aria-labelledby="addBalanceLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h4 class="modal-title text-white" id="addBalanceLabel">إضافة رصيد إضافي</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="color: #FFFFFF">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="addBalanceForm">
                        <div class="form-group">
                            <label for="additional_days">عدد الأيام الإضافية</label>
                            <input type="number" class="form-control" id="additional_days" min="1" required>
                        </div>
                        <div class="form-group">
                            <label for="reason">السبب</label>
                            <textarea class="form-control" id="reason" rows="3" placeholder="أدخل سبب إضافة الرصيد الإضافي"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light waves-effect waves-light" data-dismiss="modal">إلغاء</button>
                    <button type="button" class="btn btn-info waves-effect waves-light" id="confirmAddBalance"
                            data-id="{{ $balance->id }}">إضافة الرصيد</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Delete functionality
        document.addEventListener('click', function(e) {
            if (e.target.closest('.delete-confirm')) {
                const btn = e.target.closest('.delete-confirm');
                const id = btn.getAttribute('data-id');

                fetch(`{{ route('employee_leave_balances.index') }}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    $(`#modal_DELETE${id}`).modal('hide');

                    if (data.success) {
                        Swal.fire({
                            title: 'تم الحذف!',
                            text: data.message,
                            icon: 'success',
                            confirmButtonText: 'موافق'
                        }).then(() => {
                            window.location.href = '{{ route("employee_leave_balances.index") }}';
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
                    $(`#modal_DELETE${id}`).modal('hide');
                    Swal.fire({
                        title: 'خطأ!',
                        text: 'حدث خطأ أثناء الحذف',
                        icon: 'error',
                        confirmButtonText: 'موافق'
                    });
                });
            }
        });

        // Recalculate balance
        document.getElementById('recalculateBtn').addEventListener('click', function() {
            const employeeId = this.getAttribute('data-employee-id');
            const year = this.getAttribute('data-year');

            Swal.fire({
                title: 'إعادة حساب الرصيد',
                text: 'سيتم إعادة حساب الرصيد بناءً على الطلبات المعتمدة. هل تريد المتابعة؟',
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

                    fetch('{{ route("employee_leave_balances.recalculate") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            year: year,
                            employee_id: employeeId
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

        // Add additional balance
        document.getElementById('confirmAddBalance').addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const additionalDays = document.getElementById('additional_days').value;
            const reason = document.getElementById('reason').value;

            if (!additionalDays || additionalDays <= 0) {
                Swal.fire({
                    title: 'خطأ!',
                    text: 'يجب إدخال عدد أيام صحيح',
                    icon: 'error',
                    confirmButtonText: 'موافق'
                });
                return;
            }

            // هنا يمكنك إضافة الطلب لإضافة الرصيد الإضافي
            $('#modal_ADD_BALANCE' + id).modal('hide');

            Swal.fire({
                title: 'قريباً',
                text: 'ميزة إضافة الرصيد الإضافي ستكون متاحة قريباً',
                icon: 'info',
                confirmButtonText: 'موافق'
            });
        });
    });
</script>
@endsection
