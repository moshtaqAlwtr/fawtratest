@extends('master')

@section('title')
محطة العمل
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/logs.css') }}">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.min.css">
@endsection

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">محطة العمل</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('manufacturing.workstations.index') }}">محطات العمل</a></li>
                            <li class="breadcrumb-item active">{{ $workstation->name }} | <small class="text-muted">#{{ $workstation->code }}</small></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">
        @include('layouts.alerts.error')
        @include('layouts.alerts.success')

        <div class="card">
            <div class="card-title">
                <div class="d-flex justify-content-between align-items-center flex-wrap p-1">
                    <div>
                        <a href="{{ route('manufacturing.workstations.edit', $workstation->id) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fa fa-edit"></i> تعديل
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-danger" id="deleteBtn" data-id="{{ $workstation->id }}" data-name="{{ $workstation->name }}">
                            <i class="fa fa-trash me-2"></i> حذف
                        </button>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" aria-controls="home" role="tab" aria-selected="true">معلومات</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="activate-tab" data-toggle="tab" href="#activate" aria-controls="activate" role="tab" aria-selected="false">سجل النشاطات</a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane active" id="home" aria-labelledby="home-tab" role="tabpanel">
                        <!-- معلومات محطة العمل -->
                        <div class="card">
                            <div class="card-header p-1" style="background: #f8f8f8">
                                <strong>معلومات محطة العمل</strong>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <table class="table">
                                        <tbody>
                                            <tr>
                                                <td style="width: 50%">
                                                    <p><small>الاسم</small></p>
                                                    <strong>{{ $workstation->name }}</strong>
                                                </td>
                                                <td>
                                                    <p><small>إجمالي التكلفة</small></p>
                                                    <h4><strong>{{ number_format($workstation->total_cost, 2) }} ر.س</strong></h4>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width: 20%">
                                                    <p><small>الوحدة</small></p>
                                                    <strong>{{ $workstation->unit ?? '-' }}</strong>
                                                </td>
                                                <td>
                                                    <p><small>الوصف</small></p>
                                                    <strong>{{ $workstation->description ?? '-' }}</strong>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- التكلفة -->
                        <div class="card">
                            <div class="card-header p-1" style="background: #f8f8f8">
                                <strong>التكلفة</strong>
                            </div>

                            <!-- المصروفات -->
                            @if(isset($workstation->stationsCosts) && $workstation->stationsCosts->count() > 0)
                                <div class="card-body">
                                    <p><strong>المصروفات:</strong></p>
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th style="width: 50%">التكلفة</th>
                                                    <th>الحساب</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($workstation->stationsCosts as $stationsCost)
                                                    <tr>
                                                        <td>{{ number_format($stationsCost->cost_expenses, 2) }} <small class="text-muted">- {{ $workstation->unit }}</small></td>
                                                        <td>{{ $stationsCost->accountExpenses->name ?? '-' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif

                            <!-- الأجور -->
                            @if($workstation->cost_wages && $workstation->account_wages)
                                <div class="card-body">
                                    <p><strong>الأجور:</strong></p>
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th style="width: 50%">التكلفة</th>
                                                    <th>الحساب</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>{{ number_format($workstation->cost_wages, 2) }} <small class="text-muted">- {{ $workstation->unit }}</small></td>
                                                    <td>{{ $workstation->accountWages->name ?? '-' }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif

                            <!-- الأصول -->
                            @if($workstation->cost_origin && $workstation->account_origin)
                                <div class="card-body">
                                    <p><strong>أصل:</strong></p>
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th style="width: 50%">التكلفة</th>
                                                    <th>الحساب</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>{{ number_format($workstation->cost_origin, 2) }} <small class="text-muted">- {{ $workstation->unit }}</small></td>
                                                    <td>{{ $workstation->accountOrigin->name ?? '-' }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- تاب سجل النشاطات -->
                    <div class="tab-pane" id="activate" aria-labelledby="activate-tab" role="tabpanel">
                        <div class="row mt-4">
                            <div class="col-12">
                                <!-- Loading للوغز -->
                                <div id="logsLoading" class="text-center p-4" style="display: none;">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="sr-only">جاري تحميل السجلات...</span>
                                    </div>
                                    <p class="mt-2">جاري تحميل سجل النشاطات...</p>
                                </div>

                                <!-- محتوى السجلات -->
                                <div id="logsContent">
                                    @if (isset($logs) && count($logs) > 0)
                                        @php
                                            $previousDate = null;
                                        @endphp

                                        @foreach ($logs as $date => $dayLogs)
                                            @php
                                                $currentDate = \Carbon\Carbon::parse($date);
                                                $diffInDays = $previousDate ? $previousDate->diffInDays($currentDate) : 0;
                                            @endphp

                                            @if ($diffInDays > 7)
                                                <div class="timeline-date">
                                                    <h4>{{ $currentDate->format('Y-m-d') }}</h4>
                                                </div>
                                            @endif

                                            <div class="timeline-day">{{ $currentDate->translatedFormat('l') }}</div>

                                            <ul class="timeline">
                                                @foreach ($dayLogs as $log)
                                                    @if ($log)
                                                        <li class="timeline-item">
                                                            <div class="timeline-content">
                                                                <div class="time">
                                                                    <i class="far fa-clock"></i>
                                                                    {{ $log->created_at->format('H:i:s') }}
                                                                </div>
                                                                <div>
                                                                    <strong>{{ $log->user->name ?? 'مستخدم غير معروف' }}</strong>
                                                                    {!! Str::markdown($log->description ?? 'لا يوجد وصف') !!}
                                                                    <div class="text-muted">
                                                                        {{ $log->user->branch->name ?? 'فرع غير معروف' }}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    @endif
                                                @endforeach
                                            </ul>

                                            @php
                                                $previousDate = $currentDate;
                                            @endphp
                                        @endforeach
                                    @else
                                        <div class="alert alert-info text-center" role="alert">
                                            <i class="fas fa-info-circle me-2"></i>
                                            <p class="mb-0">لا توجد سجلات نشاط حتى الآن!</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.all.min.js"></script>

    <script>
    $(document).ready(function() {

        // إعداد SweetAlert مع اللغة العربية
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        // وظيفة الحذف
        $('#deleteBtn').on('click', function() {
            const id = $(this).data('id');
            const name = $(this).data('name');

            Swal.fire({
                title: 'تأكيد الحذف',
                html: `
                    <div class="text-center">
                        <i class="fas fa-exclamation-triangle text-warning" style="font-size: 48px;"></i>
                        <p class="mt-3">هل أنت متأكد من حذف محطة العمل؟</p>
                        <strong class="text-danger">${name}</strong>
                        <p class="text-muted mt-2">لا يمكن التراجع عن هذا الإجراء!</p>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-trash"></i> نعم، احذف',
                cancelButtonText: '<i class="fas fa-times"></i> إلغاء',
                reverseButtons: true,
                customClass: {
                    popup: 'swal2-rtl',
                    title: 'swal2-title-rtl',
                    content: 'swal2-content-rtl'
                },
                buttonsStyling: true,
                allowOutsideClick: false,
                allowEscapeKey: false,
                focusConfirm: false,
                focusCancel: true
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteWorkstation(id, name);
                }
            });
        });

        // وظيفة حذف محطة العمل
        function deleteWorkstation(id, name) {
            // عرض مؤشر التحميل
            Swal.fire({
                title: 'جاري الحذف...',
                html: `
                    <div class="text-center">
                        <div class="spinner-border text-danger" role="status">
                            <span class="sr-only">جاري الحذف...</span>
                        </div>
                        <p class="mt-3">يرجى الانتظار أثناء حذف محطة العمل</p>
                    </div>
                `,
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                customClass: {
                    popup: 'swal2-rtl'
                }
            });

            // إرسال طلب الحذف
            $.ajax({
                url: `{{ route('manufacturing.workstations.delete', '') }}/${id}`,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'تم الحذف بنجاح!',
                            html: `
                                <div class="text-center">
                                    <p>تم حذف محطة العمل <strong>${name}</strong> بنجاح</p>
                                    <p class="text-muted">سيتم توجيهك إلى قائمة محطات العمل...</p>
                                </div>
                            `,
                            timer: 2000,
                            showConfirmButton: false,
                            customClass: {
                                popup: 'swal2-rtl'
                            }
                        }).then(() => {
                            // إعادة توجيه إلى صفحة القائمة
                            window.location.href = '{{ route("manufacturing.workstations.index") }}';
                        });
                    } else {
                        showError(response.message || 'حدث خطأ أثناء الحذف');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'حدث خطأ غير متوقع أثناء الحذف';

                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.status === 404) {
                        errorMessage = 'محطة العمل غير موجودة';
                    } else if (xhr.status === 403) {
                        errorMessage = 'ليس لديك صلاحية لحذف هذه محطة العمل';
                    } else if (xhr.status === 500) {
                        errorMessage = 'حدث خطأ في الخادم، يرجى المحاولة لاحقاً';
                    }

                    showError(errorMessage);
                }
            });
        }

        // وظيفة عرض رسائل الخطأ
        function showError(message) {
            Swal.fire({
                icon: 'error',
                title: 'فشل في الحذف!',
                text: message,
                confirmButtonText: 'موافق',
                confirmButtonColor: '#dc3545',
                customClass: {
                    popup: 'swal2-rtl'
                }
            });
        }

        // تحميل سجل النشاطات بـ Ajax (اختياري)
        $('#activate-tab').on('click', function() {
            if (!$(this).hasClass('loaded')) {
                loadActivityLogs();
                $(this).addClass('loaded');
            }
        });

        function loadActivityLogs() {
            $('#logsLoading').show();
            $('#logsContent').hide();

            // يمكنك إضافة Ajax call هنا لتحميل السجلات ديناميكياً
            // في الوقت الحالي سنظهر المحتوى الموجود
            setTimeout(() => {
                $('#logsLoading').hide();
                $('#logsContent').show();
            }, 1000);
        }

        // إضافة أنيميشن لطيف للكروت
        $('.card').hover(
            function() {
                $(this).addClass('shadow-sm');
            },
            function() {
                $(this).removeClass('shadow-sm');
            }
        );
    });
    </script>

    <style>
    /* تحسينات CSS إضافية */
    .swal2-rtl {
        text-align: right !important;
        direction: rtl;
    }

    .swal2-title-rtl {
        text-align: center !important;
    }

    .swal2-content-rtl {
        text-align: right !important;
    }

    .card {
        transition: all 0.3s ease;
        border: 1px solid #e3e6f0;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1) !important;
    }

    .timeline-item {
        transition: all 0.3s ease;
    }

    .timeline-item:hover {
        background-color: #f8f9fa;
        border-radius: 5px;
        padding: 10px;
    }

    .btn {
        transition: all 0.3s ease;
    }

    .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .spinner-border {
        width: 2rem;
        height: 2rem;
    }

    .table-responsive {
        border-radius: 8px;
        overflow: hidden;
    }

    .table th {
        background-color: #f8f9fa;
        border-top: none;
        font-weight: 600;
    }
    </style>
@endsection
