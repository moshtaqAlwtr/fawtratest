@extends('master')

@section('title')
    عرض طلب الإجازة
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">عرض طلب الإجازة</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسية</a></li>
                            <li class="breadcrumb-item active">طلب إجازة #{{ $leaveRequest->id }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.alerts.error')
    @include('layouts.alerts.success')
    <div class="card p-3 d-flex flex-row align-items-center justify-content-between" style="width: 100%;">
        <span class="fw-bold">#طلب 1</span>
        <div class="d-flex gap-2">
            <button class="btn btn-primary">تحت المراجعة</button>
            <button class="btn btn-success d-flex align-items-center gap-1">
                <i class="fas fa-check-circle"></i> موافقة
            </button>
            <button class="btn btn-danger d-flex align-items-center gap-1">
                <i class="fas fa-times-circle"></i> رفض
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-title p-2">
            @if ($leaveRequest->status == 'pending')
                <a href="#" class="btn btn-outline-danger btn-sm waves-effect waves-light" data-toggle="modal"
                    data-target="#modal_DELETE{{ $leaveRequest->id }}">حذف <i class="fa fa-trash"></i></a>
                <a href="{{ route('attendance.leave_requests.edit', $leaveRequest->id) }}"
                    class="btn btn-outline-primary btn-sm waves-effect waves-light">تعديل <i class="fa fa-edit"></i></a>
            @endif

            <!-- زر إضافة ملاحظة أو مرفق -->
            <a href="" class="btn btn-outline-info btn-sm waves-effect waves-light" data-toggle="modal"
                data-target="#modal_ADD_NOTE{{ $leaveRequest->id }}">
                <i class="fas fa-plus-circle"></i> إضافة ملاحظة/مرفق
            </a>
        </div>

        <div class="card-body">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="details-tab" data-toggle="tab" href="#details" role="tab"
                        aria-controls="details" aria-selected="true">تفاصيل الطلب</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="activity-log-tab" data-toggle="tab" href="#activity-log" role="tab"
                        aria-controls="activity-log" aria-selected="false">سجل النشاطات</a>
                </li>
            </ul>

            <div class="tab-content">
                <!-- تفاصيل الطلب -->
                <div class="tab-pane fade show active" id="details" role="tabpanel" aria-labelledby="details-tab">
                    <div class="tab-content">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <table class="table">
                                        <thead style="background: #f8f8f8">
                                            <tr>
                                                <th style="width: 70%">معلومات طلب الإجازة</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <p><small>تاريخ البدء</small>: </p>
                                                    <strong>{{ \Carbon\Carbon::parse($leaveRequest->start_date)->format('Y-m-d') }}</strong>
                                                </td>
                                                <td>
                                                    <p><small>تاريخ الانتهاء</small>: </p>
                                                    <strong>{{ \Carbon\Carbon::parse($leaveRequest->end_date)->format('Y-m-d') }}</strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <p><small>عدد الأيام</small>: </p>
                                                    <strong>{{ $leaveRequest->days }} يوم</strong>
                                                </td>
                                                <td>
                                                    <p><small>نوع الطلب</small>: </p>
                                                    <strong>
                                                        @if ($leaveRequest->request_type == 'leave')
                                                            إجازة عادية
                                                        @elseif($leaveRequest->request_type == 'emergency')
                                                            إجازة طارئة
                                                        @else
                                                            إجازة مرضية
                                                        @endif
                                                    </strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <p><small>نوع الإجازة</small>: </p>
                                                    <strong>
                                                        @if ($leaveRequest->leave_type == 'annual')
                                                            إجازة اعتيادية
                                                        @elseif($leaveRequest->leave_type == 'casual')
                                                            إجازة عرضية
                                                        @elseif($leaveRequest->leave_type == 'sick')
                                                            إجازة مرضية
                                                        @else
                                                            إجازة بدون راتب
                                                        @endif
                                                    </strong>
                                                </td>
                                                <td>
                                                    <p><small>الحالة</small>: </p>
                                                    <strong>
                                                        @if ($leaveRequest->status == 'pending')
                                                            <span class="badge bg-warning">تحت المراجعة</span>
                                                        @elseif($leaveRequest->status == 'approved')
                                                            <span class="badge bg-success">موافق عليه</span>
                                                        @else
                                                            <span class="badge bg-danger">مرفوض</span>
                                                        @endif
                                                    </strong>
                                                </td>
                                            </tr>
                                            @if ($leaveRequest->status != 'pending')
                                                <tr>
                                                    <td colspan="2">
                                                        <p><small>تمت الموافقة/الرفض بواسطة</small>: </p>
                                                        <strong>{{ $leaveRequest->approver->name ?? 'غير معروف' }}</strong>
                                                    </td>
                                                </tr>
                                            @endif
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
                                                        <a href="{{ route('employee.show', $leaveRequest->employee->id) }}"
                                                            target="_blank">
                                                            {{ $leaveRequest->employee->full_name }}
                                                            #{{ $leaveRequest->employee->id }}
                                                        </a>
                                                    </strong>
                                                </td>
                                                <td>
                                                    <p><small>القسم</small>:</p>
                                                    <strong>{{ $leaveRequest->employee->department->name ?? 'غير محدد' }}</strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <p><small>تاريخ الإنشاء</small>:</p>
                                                    <strong>{{ $leaveRequest->created_at->format('Y-m-d H:i') }}</strong>
                                                </td>
                                                <td>
                                                    <p><small>آخر تحديث</small>:</p>
                                                    <strong>{{ $leaveRequest->updated_at->format('Y-m-d H:i') }}</strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    <p><small>الوصف</small>:</p>
                                                    <strong>{{ $leaveRequest->description ?? 'لا يوجد وصف' }}</strong>
                                                </td>
                                            </tr>
                                            @if ($leaveRequest->attachments)
                                                <tr>
                                                    <td colspan="2">
                                                        <p><small>المرفقات</small>:</p>
                                                        <a href="{{ asset('assets/uploads/' . $leaveRequest->attachments) }}"
                                                            target="_blank" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-download"></i> تحميل المرفق
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- سجل النشاطات -->
                <div class="tab-pane fade" id="activity-log" role="tabpanel" aria-labelledby="activity-log-tab">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center gap-3 w-100">
                            <!-- يمكنك إضافة أدوات التصفية هنا -->
                        </div>
                    </div>

                    <div class="position-relative">
                        <div class="car" style="background: #f8f9fa">
                            <div class="activity-list p-4">
                                <div class="card-content">
                                    <div class="card border-0">
                                        <ul class="activity-timeline timeline-left list-unstyled">
                                            <!-- يمكنك إضافة سجل النشاطات هنا -->
                                            <li class="d-flex position-relative mb-4">
                                                <div class="timeline-icon position-absolute" style="left: -43px; top: 0;">
                                                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center"
                                                        style="width: 35px; height: 35px;">
                                                        <i class="fas fa-plus text-white"></i>
                                                    </div>
                                                </div>
                                                <div class="timeline-info position-relative"
                                                    style="padding-left: 20px; border-left: 2px solid #e9ecef;">
                                                    <div class="d-flex align-items-center mb-1">
                                                        <span
                                                            class="badge bg-purple me-2">{{ $leaveRequest->employee->full_name }}</span>
                                                        <span class="text-dark">قام بإنشاء طلب إجازة
                                                            #{{ $leaveRequest->id }}</span>
                                                    </div>
                                                    <div class="d-flex align-items-center mt-2">
                                                        <small class="text-muted me-3">
                                                            <i class="far fa-clock me-1"></i>
                                                            {{ $leaveRequest->created_at->format('H:i:s') }}
                                                        </small>
                                                        <small class="text-muted">
                                                            <i class="far fa-calendar me-1"></i>
                                                            {{ $leaveRequest->created_at->format('Y-m-d') }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </li>
                                            @if ($leaveRequest->status != 'pending')
                                                <li class="d-flex position-relative mb-4">
                                                    <div class="timeline-icon position-absolute"
                                                        style="left: -43px; top: 0;">
                                                        <div class="bg-{{ $leaveRequest->status == 'approved' ? 'success' : 'danger' }} rounded-circle d-flex align-items-center justify-content-center"
                                                            style="width: 35px; height: 35px;">
                                                            <i
                                                                class="fas fa-{{ $leaveRequest->status == 'approved' ? 'check' : 'times' }} text-white"></i>
                                                        </div>
                                                    </div>
                                                    <div class="timeline-info position-relative"
                                                        style="padding-left: 20px; border-left: 2px solid #e9ecef;">
                                                        <div class="d-flex align-items-center mb-1">
                                                            <span
                                                                class="badge bg-purple me-2">{{ $leaveRequest->approver->name ?? 'مدير النظام' }}</span>
                                                            <span class="text-dark">قام
                                                                {{ $leaveRequest->status == 'approved' ? 'بقبول' : 'برفض' }}
                                                                طلب الإجازة</span>
                                                        </div>
                                                        <div class="d-flex align-items-center mt-2">
                                                            <small class="text-muted me-3">
                                                                <i class="far fa-clock me-1"></i>
                                                                {{ $leaveRequest->updated_at->format('H:i:s') }}
                                                            </small>
                                                            <small class="text-muted">
                                                                <i class="far fa-calendar me-1"></i>
                                                                {{ $leaveRequest->updated_at->format('Y-m-d') }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                </li>
                                            @endif
                                        </ul>
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
    <div class="modal fade text-left" id="modal_DELETE{{ $leaveRequest->id }}" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #EA5455 !important;">
                    <h4 class="modal-title" id="myModalLabel1" style="color: #FFFFFF">حذف طلب الإجازة</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="color: #DC3545">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <strong>
                        هل أنت متأكد من أنك تريد حذف طلب الإجازة هذا؟
                    </strong>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light waves-effect waves-light"
                        data-dismiss="modal">إلغاء</button>
                    <form action="{{ route('attendance.leave_requests.destroy', $leaveRequest->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger waves-effect waves-light">تأكيد</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--end delete-->

@endsection
