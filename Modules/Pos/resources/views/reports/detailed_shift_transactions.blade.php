@extends('master')

@section('title')
حركة الورديات تفصيلي
@stop

@section('content')

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">حركة الورديات تفصيلي</h2>
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">الرئيسية</a></li>
                        <li class="breadcrumb-item active">تقارير نقطة البيع</li>
                        <li class="breadcrumb-item active">حركة الورديات تفصيلي</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
        <div class="form-group breadcrumb-right">
            <div class="dropdown">
                <button class="btn-icon btn btn-primary btn-round btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i data-feather="grid"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="#" onclick="exportDetailedReport()">
                        <i data-feather="download"></i><span class="align-middle ml-1">تصدير Excel</span>
                    </a>
                    <a class="dropdown-item" href="#" onclick="printDetailedReport()">
                        <i data-feather="printer"></i><span class="align-middle ml-1">طباعة</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- فورم الفلترة -->
<div class="card">
    <div class="card-header">
        <h4 class="card-title">مرشحات التقرير التفصيلي</h4>
        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
        <div class="heading-elements">
            <ul class="list-inline mb-0">
                <li><a data-action="collapse"><i data-feather="chevron-down"></i></a></li>
            </ul>
        </div>
    </div>
    <div class="card-body">
        <form id="detailedFilterForm" method="GET" action="{{ route('pos_reports.Detailed') }}">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="session_number" class="form-label">رقم الجلسة</label>
                    <select class="form-control" id="session_number" name="session_number">
                        <option value="">الكل</option>
                        @foreach($sessionNumbers as $sessionNumber)
                            <option value="{{ $sessionNumber }}" {{ request('session_number') == $sessionNumber ? 'selected' : '' }}>
                                {{ $sessionNumber }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="category" class="form-label">التصنيف</label>
                    <select class="form-control" id="category" name="category">
                        <option value="">الكل</option>
                        <option value="sales" {{ request('category') == 'sales' ? 'selected' : '' }}>مبيعات</option>
                        <option value="returns" {{ request('category') == 'returns' ? 'selected' : '' }}>مرتجعات</option>
                        <option value="mixed" {{ request('category') == 'mixed' ? 'selected' : '' }}>مختلط</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="pos_shift" class="form-label">حالة الوردية</label>
                    <select class="form-control" id="pos_shift" name="pos_shift">
                        <option value="">الكل</option>
                        <option value="active" {{ request('pos_shift') == 'active' ? 'selected' : '' }}>نشطة</option>
                        <option value="closed" {{ request('pos_shift') == 'closed' ? 'selected' : '' }}>مغلقة</option>
                        <option value="suspended" {{ request('pos_shift') == 'suspended' ? 'selected' : '' }}>معلقة</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="pos_shift_device" class="form-label">جهاز نقطة البيع</label>
                    <select class="form-control" id="pos_shift_device" name="pos_shift_device">
                        <option value="">الكل</option>
                        @foreach($devices as $device)
                            <option value="{{ $device->id }}" {{ request('pos_shift_device') == $device->id ? 'selected' : '' }}>
                                {{ $device->device_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="order_source" class="form-label">مصدر الطلب</label>
                    <select class="form-control" id="order_source" name="order_source">
                        <option value="">الكل</option>
                        <option value="pos" {{ request('order_source') == 'pos' ? 'selected' : '' }}>نقطة البيع</option>
                        <option value="online" {{ request('order_source') == 'online' ? 'selected' : '' }}>أونلاين</option>
                        <option value="mobile" {{ request('order_source') == 'mobile' ? 'selected' : '' }}>تطبيق الجوال</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="store" class="form-label">المخزن</label>
                    <select class="form-control" id="store" name="store">
                        <option value="">الكل</option>
                        @foreach($stores as $store)
                            <option value="{{ $store->id }}" {{ request('store') == $store->id ? 'selected' : '' }}>
                                {{ $store->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="date_from" class="form-label">التاريخ من</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" 
                           value="{{ request('date_from', $filters['date_from']) }}">
                </div>
                
                <div class="col-md-3">
                    <label for="date_to" class="form-label">التاريخ إلى</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" 
                           value="{{ request('date_to', $filters['date_to']) }}">
                </div>
                
                <div class="col-md-3">
                    <label for="currency" class="form-label">العملة</label>
                    <select class="form-control" id="currency" name="currency">
                        <option value="SAR" {{ request('currency') == 'SAR' ? 'selected' : '' }}>الجميع إلى (SAR)</option>
                        <option value="USD" {{ request('currency') == 'USD' ? 'selected' : '' }}>دولار أمريكي (USD)</option>
                        <option value="EUR" {{ request('currency') == 'EUR' ? 'selected' : '' }}>يورو (EUR)</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="group_by" class="form-label">تجميع حسب</label>
                    <select class="form-control" id="group_by" name="group_by">
                        <option value="">الكل</option>
                        <option value="date" {{ request('group_by') == 'date' ? 'selected' : '' }}>التاريخ</option>
                        <option value="device" {{ request('group_by') == 'device' ? 'selected' : '' }}>الجهاز</option>
                        <option value="user" {{ request('group_by') == 'user' ? 'selected' : '' }}>المستخدم</option>
                        <option value="store" {{ request('group_by') == 'store' ? 'selected' : '' }}>المخزن</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="sort_by" class="form-label">ترتيب حسب</label>
                    <select class="form-control" id="sort_by" name="sort_by">
                        <option value="session_number" {{ request('sort_by') == 'session_number' ? 'selected' : '' }}>رقم الجلسة</option>
                        <option value="date" {{ request('sort_by') == 'date' ? 'selected' : '' }}>التاريخ</option>
                        <option value="sales" {{ request('sort_by') == 'sales' ? 'selected' : '' }}>المبيعات</option>
                        <option value="net_sales" {{ request('sort_by') == 'net_sales' ? 'selected' : '' }}>الصافي</option>
                    </select>
                </div>
            </div>
            
            <div class="row mt-4">
                <div class="col-md-12 text-left">
                    <button type="submit" class="btn btn-primary">
                        <i data-feather="search"></i> عرض التقرير
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="resetDetailedFilters()">
                        <i data-feather="refresh-cw"></i> إعادة تعيين
                    </button>
                    <!--<button type="button" class="btn btn-success" onclick="exportDetailedReport()">-->
                    <!--    <i data-feather="download"></i> تصدير Excel-->
                    <!--</button>-->
                </div>
            </div>
        </form>
    </div>
</div>

<!-- عرض التقرير التفصيلي -->
<div class="card mt-4">
    <div class="card-header">
        <div class="text-center w-100">
            <h4 class="mb-1">إجمالي مبيعات الورديات تفصيلي</h4>
            <p class="mb-1">الوقت: {{ now()->format('H:i d/m/Y') }}</p>
            <h5 class="mb-1">مؤسسة أعمال خاصة للتجارة</h5>
            <p class="mb-0">الرياض، الرياض</p>
        </div>
    </div>
    
    <div class="card-body">
        @if(session('error'))
            <div class="alert alert-danger">
                <i data-feather="alert-circle"></i> {{ session('error') }}
            </div>
        @endif

        @if($detailedData->isEmpty())
            <div class="alert alert-info text-center">
                <i data-feather="info"></i>
                <h4>لا توجد بيانات</h4>
                <p>لا توجد ورديات تطابق المعايير المحددة في الفترة المختارة</p>
            </div>
        @else
            <!-- ملخص الإحصائيات التفصيلية -->
            <div class="row mb-3">
                <div class="col-xl-2 col-md-6 col-12">
                    <div class="card bg-gradient-primary text-white">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="media d-flex">
                                    <div class="media-body white text-left">
                                        <h4 class="white">{{ number_format($totals['total_sales'], 2) }}</h4>
                                        <span>إجمالي المبيعات</span>
                                    </div>
                                    <div class="media-right media-middle">
                                        <i data-feather="trending-up" class="white font-large-2 float-right"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-2 col-md-6 col-12">
                    <div class="card bg-gradient-danger text-white">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="media d-flex">
                                    <div class="media-body white text-left">
                                        <h4 class="white">{{ number_format($totals['total_returns'], 2) }}</h4>
                                        <span>إجمالي المرتجعات</span>
                                    </div>
                                    <div class="media-right media-middle">
                                        <i data-feather="trending-down" class="white font-large-2 float-right"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-2 col-md-6 col-12">
                    <div class="card bg-gradient-success text-white">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="media d-flex">
                                    <div class="media-body white text-left">
                                        <h4 class="white">{{ number_format($totals['net_sales'], 2) }}</h4>
                                        <span>الصافي</span>
                                    </div>
                                    <div class="media-right media-middle">
                                        <i data-feather="dollar-sign" class="white font-large-2 float-right"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-2 col-md-6 col-12">
                    <div class="card bg-gradient-warning text-white">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="media d-flex">
                                    <div class="media-body white text-left">
                                        <h4 class="white">{{ number_format($totals['net_cash'], 2) }}</h4>
                                        <span>صافي نقدي</span>
                                    </div>
                                    <div class="media-right media-middle">
                                        <i data-feather="credit-card" class="white font-large-2 float-right"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-2 col-md-6 col-12">
                    <div class="card bg-gradient-info text-white">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="media d-flex">
                                    <div class="media-body white text-left">
                                        <h4 class="white">{{ number_format($totals['difference'], 2) }}</h4>
                                        <span>الفرق</span>
                                    </div>
                                    <div class="media-right media-middle">
                                        <i data-feather="alert-triangle" class="white font-large-2 float-right"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-2 col-md-6 col-12">
                    <div class="card bg-gradient-dark text-white">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="media d-flex">
                                    <div class="media-body white text-left">
                                        <h4 class="white">{{ $totals['total_sessions'] }}</h4>
                                        <span>عدد الورديات</span>
                                    </div>
                                    <div class="media-right media-middle">
                                        <i data-feather="clock" class="white font-large-2 float-right"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- جدول البيانات التفصيلية -->
            <div class="table-responsive">
                <table class="table table-bordered text-center table-sm" id="detailedShiftTable">
                    <thead class="table-light">
                        <tr>
                            <th>الكود</th>
                            <th>وردية</th>
                            <th>وقت الفتح</th>
                            <th>وقت الإغلاق</th>
                            <th>موظف الخزينة</th>
                            <th>مؤكدة بواسطة</th>
                            <th>فرع</th>
                            <th>المبيعات</th>
                            <th>المردود</th>
                            <th>الصافي</th>
                            <th>صافي نقدي</th>
                            <th>صافي غير نقدي</th>
                            <th>إجمالي استلام نقدي</th>
                            <th>الآجل</th>
                            <th>إجمالي استلام نقدي</th>
                            <th>إجمالي صرف نقدي</th>
                            <th>إجمالي نظري</th>
                            <th>إجمالي المستلم نقدي</th>
                            <th>إجمالي المستلم غير نقدي</th>
                            <th>إجمالي المستلم</th>
                            <th>الفرق</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($detailedData as $shift)
                        <tr>
                            <td>{{ $shift['code'] }}</td>
                            <td>{{ $shift['shift_name'] }}</td>
                            <td>{{ $shift['opening_time'] }}</td>
                            <td>{{ $shift['closing_time'] ?: 'مفتوحة' }}</td>
                            <td>{{ $shift['treasury_employee'] }}</td>
                            <td>{{ $shift['confirmed_by'] }}</td>
                            <td>{{ $shift['branch'] }}</td>
                            <td>{{ $shift['total_sales'] }}</td>
                            <td>{{ $shift['total_returns'] }}</td>
                            <td>{{ $shift['net_sales'] }}</td>
                            <td>{{ $shift['net_cash'] }}</td>
                            <td>{{ $shift['net_non_cash'] }}</td>
                            <td>{{ $shift['total_cash_received'] }}</td>
                            <td>{{ $shift['credit_amount'] }}</td>
                            <td>{{ $shift['total_cash_collection'] }}</td>
                            <td>{{ $shift['total_cash_paid'] }}</td>
                            <td>{{ $shift['theoretical_total'] }}</td>
                            <td>{{ $shift['actual_cash_received'] }}</td>
                            <td>{{ $shift['actual_non_cash_received'] }}</td>
                            <td>{{ $shift['total_actual_received'] }}</td>
                            <td class="{{ $shift['raw_difference'] < 0 ? 'text-danger' : ($shift['raw_difference'] > 0 ? 'text-success' : '') }}">
                                {{ $shift['difference'] }}
                            </td>
                        </tr>
                        @endforeach
                        
                        <!-- صف فارغ كما في التصميم الأصلي -->
                        <!--<tr>-->
                        <!--    <td></td>-->
                        <!--    <td></td>-->
                        <!--    <td></td>-->
                        <!--    <td></td>-->
                        <!--    <td></td>-->
                        <!--    <td></td>-->
                        <!--    <td></td>-->
                        <!--    <td>{{ number_format($totals['total_sales'], 2) }}</td>-->
                        <!--    <td>{{ number_format($totals['total_returns'], 2) }}</td>-->
                        <!--    <td>{{ number_format($totals['net_sales'], 2) }}</td>-->
                        <!--    <td>{{ number_format($totals['net_cash'], 2) }}</td>-->
                        <!--    <td>{{ number_format($totals['net_non_cash'], 2) }}</td>-->
                        <!--    <td>{{ number_format($totals['total_cash_received'], 2) }}</td>-->
                        <!--    <td>{{ number_format($totals['credit_amount'], 2) }}</td>-->
                        <!--    <td>{{ number_format($totals['total_cash_received'], 2) }}</td>-->
                        <!--    <td>{{ number_format($totals['total_cash_paid'], 2) }}</td>-->
                        <!--    <td>{{ number_format($totals['theoretical_total'], 2) }}</td>-->
                        <!--    <td>{{ number_format($totals['actual_cash_received'], 2) }}</td>-->
                        <!--    <td>{{ number_format($totals['actual_non_cash_received'], 2) }}</td>-->
                        <!--    <td>{{ number_format($totals['total_actual_received'], 2) }}</td>-->
                        <!--    <td class="{{ $totals['difference'] < 0 ? 'text-danger' : ($totals['difference'] > 0 ? 'text-success' : '') }}">-->
                        <!--        {{ number_format($totals['difference'], 2) }}-->
                        <!--    </td>-->
                        <!--</tr>-->
                    </tbody>
                    <tfoot>
                        <tr class="table-light fw-bold">
                            <td colspan="7">المجموع</td>
                            <td>{{ number_format($totals['total_sales'], 2) }}</td>
                            <td>{{ number_format($totals['total_returns'], 2) }}</td>
                            <td>{{ number_format($totals['net_sales'], 2) }}</td>
                            <td>{{ number_format($totals['net_cash'], 2) }}</td>
                            <td>{{ number_format($totals['net_non_cash'], 2) }}</td>
                            <td>{{ number_format($totals['total_cash_received'], 2) }}</td>
                            <td>{{ number_format($totals['credit_amount'], 2) }}</td>
                            <td>{{ number_format($totals['total_cash_received'], 2) }}</td>
                            <td>{{ number_format($totals['total_cash_paid'], 2) }}</td>
                            <td>{{ number_format($totals['theoretical_total'], 2) }}</td>
                            <td>{{ number_format($totals['actual_cash_received'], 2) }}</td>
                            <td>{{ number_format($totals['actual_non_cash_received'], 2) }}</td>
                            <td>{{ number_format($totals['total_actual_received'], 2) }}</td>
                            <td class="{{ $totals['difference'] < 0 ? 'text-danger' : ($totals['difference'] > 0 ? 'text-success' : '') }}">
                                {{ number_format($totals['difference'], 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- الإجماليات السفلية -->
            <!--<tfoot>-->
            <!--    <tr>-->
            <!--        <th colspan="7" class="text-end">الإجماليات</th>-->
            <!--        <td>{{ number_format($totals['total_sales'], 2) }}</td>-->
            <!--        <td>{{ number_format($totals['total_returns'], 2) }}</td>-->
            <!--        <td>{{ number_format($totals['net_sales'], 2) }}</td>-->
            <!--        <td>{{ number_format($totals['net_cash'], 2) }}</td>-->
            <!--        <td>{{ number_format($totals['net_non_cash'], 2) }}</td>-->
            <!--        <td>{{ number_format($totals['total_cash_received'], 2) }}</td>-->
            <!--        <td>{{ number_format($totals['credit_amount'], 2) }}</td>-->
            <!--        <td>{{ number_format($totals['total_cash_received'], 2) }}</td>-->
            <!--        <td>{{ number_format($totals['total_cash_paid'], 2) }}</td>-->
            <!--        <td>{{ number_format($totals['theoretical_total'], 2) }}</td>-->
            <!--        <td>{{ number_format($totals['actual_cash_received'], 2) }}</td>-->
            <!--        <td>{{ number_format($totals['actual_non_cash_received'], 2) }}</td>-->
            <!--        <td>{{ number_format($totals['total_actual_received'], 2) }}</td>-->
            <!--        <td class="{{ $totals['difference'] < 0 ? 'text-danger' : ($totals['difference'] > 0 ? 'text-success' : '') }}">-->
            <!--            {{ number_format($totals['difference'], 2) }}-->
            <!--        </td>-->
            <!--    </tr>-->
            <!--</tfoot>-->
        @endif
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // تحديث التقرير تلقائياً كل 60 ثانية للتقرير التفصيلي
    setInterval(function() {
        if ($('#auto_refresh_detailed').is(':checked')) {
            refreshDetailedReport();
        }
    }, 60000);
    
    // تهيئة DataTable للجدول التفصيلي
    $('#detailedShiftTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Arabic.json"
        },
        "pageLength": 25,
        "scrollX": true,
        "order": [[ 0, "desc" ]],
        "columnDefs": [
            { "className": "text-center", "targets": "_all" }
        ]
    });
});

function resetDetailedFilters() {
    $('#detailedFilterForm')[0].reset();
    $('#date_from').val('{{ \Carbon\Carbon::now()->subMonth()->format('Y-m-d') }}');
    $('#date_to').val('{{ \Carbon\Carbon::now()->format('Y-m-d') }}');
}

function refreshDetailedReport() {
    $('#detailedFilterForm').submit();
}

function exportDetailedReport() {
    const form = $('#detailedFilterForm');
    const currentAction = form.attr('action');
    
    // تغيير الـ action مؤقتاً للتصدير
    form.attr('action', '{{ route("pos.reports.detailed.export") }}');
    form.submit();
    
    // إعادة الـ action الأصلي
    form.attr('action', currentAction);
    
    // عرض رسالة نجاح
    toastr.success('تم بدء تصدير التقرير التفصيلي', 'نجح التصدير');
}

function printDetailedReport() {
    window.print();
}

// معالجة الأخطاء
$(document).ajaxError(function(event, xhr, settings, thrownError) {
    toastr.error('حدث خطأ أثناء تحميل البيانات التفصيلية', 'خطأ');
});

// رسائل التحميل
$(document).ajaxStart(function() {
    toastr.info('جاري تحميل البيانات التفصيلية...', 'تحميل');
});

$(document).ajaxStop(function() {
    toastr.clear();
});

// إضافة تأثير hover للصفوف
$('#detailedShiftTable tbody tr').hover(
    function() {
        $(this).addClass('table-hover-effect');
    },
    function() {
        $(this).removeClass('table-hover-effect');
    }
);
</script>

<style>
@media print {
    .card-header .dropdown,
    .btn,
    .breadcrumb,
    .content-header-right,
    .dataTables_wrapper .dataTables_filter,
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_paginate {
        display: none !important;
    }
    
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    
    .table {
        font-size: 10px;
    }
    
    .table th,
    .table td {
        padding: 3px !important;
        font-size: 9px;
    }
    
    .table-responsive {
        overflow: visible !important;
    }
}

.table-responsive {
    max-height: 700px;
    overflow-y: auto;
}

.table-sm th,
.table-sm td {
    padding: 0.3rem;
    font-size: 0.85rem;
}

.card-gradient-primary {
    background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);
}

.card-gradient-success {
    background: linear-gradient(45deg, #11998e 0%, #38ef7d 100%);
}

.card-gradient-warning {
    background: linear-gradient(45deg, #f093fb 0%, #f5576c 100%);
}

.card-gradient-info {
    background: linear-gradient(45deg, #4facfe 0%, #00f2fe 100%);
}

.card-gradient-danger {
    background: linear-gradient(45deg, #ff6b6b 0%, #ee5a52 100%);
}

.card-gradient-dark {
    background: linear-gradient(45deg, #2c3e50 0%, #34495e 100%);
}

.loading {
    opacity: 0.6;
    pointer-events: none;
}

.no-data {
    text-align: center;
    padding: 50px;
    color: #6c757d;
}

.table-hover-effect {
    background-color: #f8f9fa !important;
    transform: scale(1.01);
    transition: all 0.2s ease-in-out;
}

.text-danger {
    color: #dc3545 !important;
    font-weight: bold;
}

.text-success {
    color: #28a745 !important;
    font-weight: bold;
}

/* تحسين عرض الجدول على الشاشات الصغيرة */
@media (max-width: 768px) {
    .table-responsive {
        font-size: 12px;
    }
    
    .table th,
    .table td {
        padding: 0.25rem !important;
        white-space: nowrap;
    }
}

/* تمييز الصفوف الهامة */
.table tbody tr:nth-child(even) {
    background-color: #f8f9fa;
}

.table tfoot tr {
    background-color: #e9ecef !important;
    font-weight: bold;
}

/* تحسين التمرير الأفقي */
.table-responsive::-webkit-scrollbar {
    height: 8px;
}

.table-responsive::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.table-responsive::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

.table-responsive::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>