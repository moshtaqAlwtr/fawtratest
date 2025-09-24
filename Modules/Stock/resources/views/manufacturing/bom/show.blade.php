@extends('master')

@section('title')
مواد الإنتاج
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
                    <h2 class="content-header-title float-left mb-0">مواد الإنتاج</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route( 'BOM.index') }}">مواد الإنتاج</a></li>
                            <li class="breadcrumb-item active">{{ $productionMaterial->name }} | <small class="text-muted">#{{ $productionMaterial->code }}</small></li>
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
                        <a href="{{ route('Bom.edit', $productionMaterial->id) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fa fa-edit"></i> تعديل
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-danger" id="deleteBtn" data-id="{{ $productionMaterial->id }}" data-name="{{ $productionMaterial->name }}">
                            <i class="fa fa-trash me-2"></i> حذف
                        </button>
                        @if($productionMaterial->status == 1)
                            <span class="badge badge-success">نشط</span>
                        @else
                            <span class="badge badge-secondary">غير نشط</span>
                        @endif
                        @if($productionMaterial->default == 1)
                            <span class="badge badge-primary">افتراضي</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card-body">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" aria-controls="home" role="tab" aria-selected="true">معلومات</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="materials-tab" data-toggle="tab" href="#materials" aria-controls="materials" role="tab" aria-selected="false">مكونات الإنتاج</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="activate-tab" data-toggle="tab" href="#activate" aria-controls="activate" role="tab" aria-selected="false">سجل النشاطات</a>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- تاب المعلومات الأساسية -->
                    <div class="tab-pane active" id="home" aria-labelledby="home-tab" role="tabpanel">
                        <!-- معلومات مواد الإنتاج -->
                        <div class="card">
                            <div class="card-header p-1" style="background: #f8f8f8">
                                <strong>معلومات مواد الإنتاج</strong>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <table class="table">
                                        <tbody>
                                            <tr>
                                                <td style="width: 50%">
                                                    <p><small>الاسم</small></p>
                                                    <strong>{{ $productionMaterial->name }}</strong>
                                                </td>
                                                <td>
                                                    <p><small>إجمالي التكلفة الأخيرة</small></p>
                                                    <h4><strong>{{ number_format($productionMaterial->last_total_cost, 2) }} ر.س</strong></h4>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <p><small>المنتج</small></p>
                                                    <strong>{{ $productionMaterial->product->name ?? '-' }}</strong>
                                                </td>
                                                <td>
                                                    <p><small>الكمية</small></p>
                                                    <strong>{{ number_format($productionMaterial->quantity, 2) }}</strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <p><small>الحساب</small></p>
                                                    <strong>{{ $productionMaterial->account->name ?? '-' }}</strong>
                                                </td>
                                                <td>
                                                    <p><small>مسار الإنتاج</small></p>
                                                    <strong>{{ $productionMaterial->productionPath->name ?? '-' }}</strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <p><small>تم الإنشاء بواسطة</small></p>
                                                    <strong>{{ $productionMaterial->createdByUser->name ?? '-' }}</strong>
                                                </td>
                                                <td>
                                                    <p><small>آخر تحديث بواسطة</small></p>
                                                    <strong>{{ $productionMaterial->updatedByUser->name ?? '-' }}</strong>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- تاب مكونات الإنتاج -->
                    <div class="tab-pane" id="materials" aria-labelledby="materials-tab" role="tabpanel">
                        @if(isset($productionMaterialItems) && $productionMaterialItems->count() > 0)
                            <!-- المواد الخام -->
                            @php
                                $rawMaterials = $productionMaterialItems->where('raw_product_id', '!=', null);
                                $expenses = $productionMaterialItems->where('expenses_account_id', '!=', null);
                                $workstations = $productionMaterialItems->where('workstation_id', '!=', null);
                                $endLifeProducts = $productionMaterialItems->where('end_life_product_id', '!=', null);
                            @endphp

                            @if($rawMaterials->count() > 0)
                                <div class="card mt-3">
                                    <div class="card-header p-1" style="background: #f8f8f8">
                                        <strong>المواد الخام</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>المنتج</th>
                                                        <th>الكمية</th>
                                                        <th>سعر الوحدة</th>
                                                        <th>المرحلة</th>
                                                        <th>الإجمالي</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($rawMaterials as $item)
                                                        <tr>
                                                            <td>{{ $item->rawProduct->name ?? '-' }}</td>
                                                            <td>{{ number_format($item->raw_quantity, 2) }}</td>
                                                            <td>{{ number_format($item->raw_unit_price, 2) }} ر.س</td>
                                                            <td>{{ $item->rawProductionStage->name ?? '-' }}</td>
                                                            <td><strong>{{ number_format($item->raw_total, 2) }} ر.س</strong></td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot>
                                                    <tr class="table-info">
                                                        <th colspan="4">إجمالي المواد الخام</th>
                                                        <th>{{ number_format($rawMaterials->sum('raw_total'), 2) }} ر.س</th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- المصروفات -->
                            @if($expenses->count() > 0)
                                <div class="card mt-3">
                                    <div class="card-header p-1" style="background: #f8f8f8">
                                        <strong>المصروفات</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>الحساب</th>
                                                        <th>نوع التكلفة</th>
                                                        <th>السعر</th>
                                                        <th>المرحلة</th>
                                                        <th>الوصف</th>
                                                        <th>الإجمالي</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($expenses as $item)
                                                        <tr>
                                                            <td>{{ $item->expensesAccount->name ?? '-' }}</td>
                                                            <td>{{ $item->expenses_cost_type ?? '-' }}</td>
                                                            <td>{{ number_format($item->expenses_price, 2) }} ر.س</td>
                                                            <td>{{ $item->expensesProductionStage->name ?? '-' }}</td>
                                                            <td>{{ $item->expenses_description ?? '-' }}</td>
                                                            <td><strong>{{ number_format($item->expenses_total, 2) }} ر.س</strong></td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot>
                                                    <tr class="table-warning">
                                                        <th colspan="5">إجمالي المصروفات</th>
                                                        <th>{{ number_format($expenses->sum('expenses_total'), 2) }} ر.س</th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- محطات العمل -->
                            @if($workstations->count() > 0)
                                <div class="card mt-3">
                                    <div class="card-header p-1" style="background: #f8f8f8">
                                        <strong>محطات العمل والتشغيل</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>محطة العمل</th>
                                                        <th>وقت التشغيل</th>
                                                        <th>نوع التكلفة</th>
                                                        <th>المرحلة</th>
                                                        <th>الوصف</th>
                                                        <th>الإجمالي</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($workstations as $item)
                                                        <tr>
                                                            <td>{{ $item->workStation->name ?? '-' }}</td>
                                                            <td>{{ $item->operating_time ?? '-' }}</td>
                                                            <td>{{ $item->manu_cost_type ?? '-' }}</td>
                                                            <td>{{ $item->workshopProductionStage->name ?? '-' }}</td>
                                                            <td>{{ $item->manu_description ?? '-' }}</td>
                                                            <td><strong>{{ number_format($item->manu_total, 2) }} ر.س</strong></td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot>
                                                    <tr class="table-success">
                                                        <th colspan="5">إجمالي محطات العمل</th>
                                                        <th>{{ number_format($workstations->sum('manu_total'), 2) }} ر.س</th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- المنتجات النهائية -->
                            @if($endLifeProducts->count() > 0)
                                <div class="card mt-3">
                                    <div class="card-header p-1" style="background: #f8f8f8">
                                        <strong>المنتجات النهائية</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>المنتج</th>
                                                        <th>الكمية</th>
                                                        <th>سعر الوحدة</th>
                                                        <th>المرحلة</th>
                                                        <th>الإجمالي</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($endLifeProducts as $item)
                                                        <tr>
                                                            <td>{{ $item->endLifeProduct->name ?? '-' }}</td>
                                                            <td>{{ number_format($item->end_life_quantity, 2) }}</td>
                                                            <td>{{ number_format($item->end_life_unit_price, 2) }} ر.س</td>
                                                            <td>{{ $item->endLifeProductionStage->name ?? '-' }}</td>
                                                            <td><strong>{{ number_format($item->end_life_total, 2) }} ر.س</strong></td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot>
                                                    <tr class="table-primary">
                                                        <th colspan="4">إجمالي المنتجات النهائية</th>
                                                        <th>{{ number_format($endLifeProducts->sum('end_life_total'), 2) }} ر.س</th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- ملخص التكاليف -->
                            <div class="card mt-3">
                                <div class="card-header p-1" style="background: #343a40; color: white;">
                                    <strong>ملخص التكاليف الإجمالية</strong>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <tbody>
                                                <tr>
                                                    <td><strong>إجمالي المواد الخام</strong></td>
                                                    <td class="text-right"><strong>{{ number_format($rawMaterials->sum('raw_total'), 2) }} ر.س</strong></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>إجمالي المصروفات</strong></td>
                                                    <td class="text-right"><strong>{{ number_format($expenses->sum('expenses_total'), 2) }} ر.س</strong></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>إجمالي محطات العمل</strong></td>
                                                    <td class="text-right"><strong>{{ number_format($workstations->sum('manu_total'), 2) }} ر.س</strong></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>إجمالي محطات العمل</strong></td>
                                                    <td class="text-right"><strong>{{ number_format($workstations->sum('manu_total'), 2) }} ر.س</strong></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>إجمالي  المهورات</strong></td>
                                                    <td class="text-right"><strong>{{ number_format($workstations->sum('end_life_total'), 2) }} ر.س</strong></td>
                                                </tr>
                                                <tr class="table-dark">
                                                    <td><strong>إجمالي التكلفة النهائية</strong></td>
                                                    <td class="text-right">
                                                        <h4><strong>{{ number_format($rawMaterials->sum('raw_total') + $expenses->sum('expenses_total') + $workstations->sum('manu_total') - $workstations->sum('end_life_total') ,2) }} ر.س</strong></h4>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info text-center" role="alert">
                                <i class="fas fa-info-circle me-2"></i>
                                <p class="mb-0">لا توجد مكونات إنتاج مضافة حتى الآن!</p>
                            </div>
                        @endif
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
                        <p class="mt-3">هل أنت متأكد من حذف مواد الإنتاج؟</p>
                        <strong class="text-danger">${name}</strong>
                        <p class="text-muted mt-2">سيتم حذف جميع المكونات المرتبطة أيضاً!</p>
                        <p class="text-muted">لا يمكن التراجع عن هذا الإجراء!</p>
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
                    deleteProductionMaterial(id, name);
                }
            });
        });

        // وظيفة حذف مواد الإنتاج
        function deleteProductionMaterial(id, name) {
            // عرض مؤشر التحميل
            Swal.fire({
                title: 'جاري الحذف...',
                html: `
                    <div class="text-center">
                        <div class="spinner-border text-danger" role="status">
                            <span class="sr-only">جاري الحذف...</span>
                        </div>
                        <p class="mt-3">يرجى الانتظار أثناء حذف مواد الإنتاج</p>
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
                url: `{{ route('Bom.destroy', '') }}/${id}`,
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
                                    <p>تم حذف مواد الإنتاج <strong>${name}</strong> بنجاح</p>
                                    <p class="text-muted">سيتم توجيهك إلى قائمة مواد الإنتاج...</p>
                                </div>
                            `,
                            timer: 2000,
                            showConfirmButton: false,
                            customClass: {
                                popup: 'swal2-rtl'
                            }
                        }).then(() => {
                            // إعادة توجيه إلى صفحة القائمة
                            window.location.href = '{{ route("BOM.index") }}';
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
                        errorMessage = 'مواد الإنتاج غير موجودة';
                    } else if (xhr.status === 403) {
                        errorMessage = 'ليس لديك صلاحية لحذف مواد الإنتاج';
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

        // تفعيل الـ tooltips
        $('[data-toggle="tooltip"]').tooltip();
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
        margin-bottom: 20px;
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

    .badge {
        font-size: 0.85em;
        padding: 0.4em 0.6em;
    }

    .nav-tabs .nav-link {
        border: none;
        border-bottom: 2px solid transparent;
        transition: all 0.3s ease;
    }

    .nav-tabs .nav-link:hover {
        border-color: #007bff;
        background-color: #f8f9fa;
    }

    .nav-tabs .nav-link.active {
        border-bottom-color: #007bff;
        background-color: transparent;
    }

    .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(0,123,255,.05);
    }

    .table-hover tbody tr:hover {
        background-color: rgba(0,123,255,.1);
    }

    .timeline-date h4 {
        color: #495057;
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
        padding-bottom: 8px;
    }

    .timeline-day {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 8px 15px;
        border-radius: 15px;
        display: inline-block;
        margin: 10px 0;
        font-weight: 600;
        font-size: 0.9em;
    }

    .timeline-content .time {
        color: #6c757d;
        font-size: 0.85em;
        margin-bottom: 5px;
    }

    .timeline-content .time i {
        margin-right: 5px;
    }

    /* تحسين مظهر الجداول */
    .table th {
        position: sticky;
        top: 0;
        z-index: 10;
    }

    /* ألوان مخصصة للأقسام المختلفة */
    .table-info th {
        background-color: #d1ecf1 !important;
        color: #0c5460;
    }

    .table-warning th {
        background-color: #fff3cd !important;
        color: #856404;
    }

    .table-success th {
        background-color: #d4edda !important;
        color: #155724;
    }

    .table-primary th {
        background-color: #cce5ff !important;
        color: #004085;
    }

    .table-dark th {
        background-color: #343a40 !important;
        color: white;
    }

    /* تحسين responsive للجداول */
    @media (max-width: 768px) {
        .table-responsive table,
        .table-responsive thead,
        .table-responsive tbody,
        .table-responsive th,
        .table-responsive td,
        .table-responsive tr {
            display: block;
        }

        .table-responsive thead tr {
            position: absolute;
            top: -9999px;
            left: -9999px;
        }

        .table-responsive tr {
            border: 1px solid #ccc;
            margin-bottom: 10px;
        }

        .table-responsive td {
            border: none;
            position: relative;
            padding-left: 50% !important;
        }

        .table-responsive td:before {
            content: attr(data-label);
            position: absolute;
            left: 6px;
            width: 45%;
            padding-right: 10px;
            white-space: nowrap;
            font-weight: bold;
        }
    }

    /* تحسين مظهر البطاقات */
    .card-header {
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
    }

    .breadcrumb-item + .breadcrumb-item::before {
        content: "←";
    }

    /* تحسين مظهر الأزرار */
    .btn-outline-primary:hover {
        background-color: #007bff;
        border-color: #007bff;
        transform: translateY(-2px);
    }

    .btn-outline-danger:hover {
        background-color: #dc3545;
        border-color: #dc3545;
        transform: translateY(-2px);
    }
    </style>
@endsection
