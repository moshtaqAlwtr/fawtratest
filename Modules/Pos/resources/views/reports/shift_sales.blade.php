@extends('master')

@section('title')
تقرير مبيعات الورديات
@stop

@section('content')

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">تقرير مبيعات الورديات</h2>
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">الرئيسية</a></li>
                        <li class="breadcrumb-item active">تقارير نقطة البيع</li>
                        <li class="breadcrumb-item active">مبيعات الورديات</li>
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
                    <!--<a class="dropdown-item" href="#" onclick="exportReport()">-->
                    <!--    <i data-feather="download"></i><span class="align-middle ml-1">تصدير Excel</span>-->
                    <!--</a>-->
                    <a class="dropdown-item" href="#" onclick="printReport()">
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
        <h4 class="card-title">مرشحات التقرير</h4>
        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
        <div class="heading-elements">
            <ul class="list-inline mb-0">
                <li><a data-action="collapse"><i data-feather="chevron-down"></i></a></li>
            </ul>
        </div>
    </div>
    <div class="card-body">
        <form id="filterForm" method="GET" action="{{ route('pos.reports.shift') }}">
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
                                {{ $device->name }}
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
                        <option value="returns" {{ request('sort_by') == 'returns' ? 'selected' : '' }}>المرتجعات</option>
                    </select>
                </div>
            </div>
            
            <div class="row mt-4">
                <div class="col-md-12 text-left">
                    <button type="submit" class="btn btn-primary">
                        <i data-feather="search"></i> عرض التقرير
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="resetFilters()">
                        <i data-feather="refresh-cw"></i> إعادة تعيين
                    </button>
                    <!--<button type="button" class="btn btn-success" onclick="exportReport()">-->
                    <!--    <i data-feather="download"></i> تصدير Excel-->
                    <!--</button>-->
                </div>
            </div>
        </form>
    </div>
</div>

<!-- عرض التقرير -->
<div class="card mt-4">
    <div class="card-header">
        <div class="text-center w-100">
            <h4 class="mb-1">إجمالي مبيعات الورديات</h4>
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

        @if($shiftData->isEmpty())
            <div class="alert alert-info text-center">
                <i data-feather="info"></i>
                <h4>لا توجد بيانات</h4>
                <p>لا توجد ورديات تطابق المعايير المحددة في الفترة المختارة</p>
            </div>
        @else
            <!-- ملخص الإحصائيات -->
            <div class="row mb-3">
                <div class="col-xl-3 col-md-6 col-12">
                    <div class="card bg-gradient-primary text-white">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="media d-flex">
                                    <div class="media-body white text-left">
                                        <h3 class="white">{{ $totals['total_sessions'] }}</h3>
                                        <span>إجمالي الورديات</span>
                                    </div>
                                    <div class="media-right media-middle">
                                        <i data-feather="clock" class="white font-large-2 float-right"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 col-12">
                    <div class="card bg-gradient-success text-white">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="media d-flex">
                                    <div class="media-body white text-left">
                                        <h3 class="white">{{ number_format($totals['grand_total'], 2) }}</h3>
                                        <span>إجمالي المبيعات (ر.س)</span>
                                    </div>
                                    <div class="media-right media-middle">
                                        <i data-feather="trending-up" class="white font-large-2 float-right"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 col-12">
                    <div class="card bg-gradient-warning text-white">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="media d-flex">
                                    <div class="media-body white text-left">
                                        <h3 class="white">{{ number_format($totals['total_net'], 2) }}</h3>
                                        <span>الصافي (ر.س)</span>
                                    </div>
                                    <div class="media-right media-middle">
                                        <i data-feather="dollar-sign" class="white font-large-2 float-right"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 col-12">
                    <div class="card bg-gradient-info text-white">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="media d-flex">
                                    <div class="media-body white text-left">
                                        <h3 class="white">{{ $totals['total_sales_count'] }}</h3>
                                        <span>عدد المعاملات</span>
                                    </div>
                                    <div class="media-right media-middle">
                                        <i data-feather="shopping-cart" class="white font-large-2 float-right"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- جدول البيانات -->
            <div class="table-responsive">
                <table class="table table-bordered text-center" id="shiftTable">
                    <thead class="table-light">
                        <tr>
                            <th>رقم الجلسة</th>
                            <th>الوردية</th>
                            <th>وقت الفتح</th>
                            <th>وقت الإغلاق</th>
                            <th>فتحت الوردية بواسطة</th>
                         
                            <th>المبيعات</th>
                            <th>المردود</th>
                            <th>الصافي</th>
                            <th>المتوسط</th>
                            <th>الضرائب</th>
                            <th>الخصم</th>
                            <th>الإجمالي (SAR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($shiftData as $shift)
                        <tr>
                            <td>{{ $shift['code'] }}</td>
                            <td>{{ $shift['shift_name'] }}</td>
                            <td>{{ $shift['opening_time'] }}</td>
                            <td>{{ $shift['closing_time'] ?: 'مفتوحة' }}</td>
                            <td>{{ $shift['user_id'] }}</td>
        
                            <td>{{ $shift['sales_count'] }}</td>
                            <td>{{ $shift['returns_count'] }}</td>
                            <td>{{ $shift['net_total'] }}</td>
                            <td>{{ $shift['average'] }}</td>
                            <td>{{ $shift['total_tax'] }}</td>
                            <td>{{ $shift['total_discount'] }}</td>
                            <td>{{ $shift['grand_total'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                   <tfoot>
    <tr class="table-light fw-bold">
        <td colspan="5">المجموع</td>
        <td>{{ $totals['total_sales_count'] }}</td>
        <td>{{ $totals['total_returns_count'] }}</td>
        <td>{{ number_format($totals['total_net'], 2) }}</td>
        <td>{{ $totals['total_sales_count'] > 0 ? number_format($totals['total_net'] / $totals['total_sales_count'], 2) : '0.00' }}</td>
        <td>{{ number_format($totals['total_tax'], 2) }}</td>
        <td>{{ number_format($totals['total_discount'], 2) }}</td>
        <td>{{ number_format($totals['grand_total'], 2) }}</td>
    </tr>
</tfoot>

                </table>
            </div>
        @endif
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // تحديث التقرير تلقائياً كل 30 ثانية
    setInterval(function() {
        if ($('#auto_refresh').is(':checked')) {
            refreshReport();
        }
    }, 30000);
});

function resetFilters() {
    $('#filterForm')[0].reset();
    $('#date_from').val('{{ \Carbon\Carbon::now()->subMonth()->format('Y-m-d') }}');
    $('#date_to').val('{{ \Carbon\Carbon::now()->format('Y-m-d') }}');
}

function refreshReport() {
    $('#filterForm').submit();
}

function exportReport() {
    const form = $('#filterForm');
    const currentAction = form.attr('action');
    
    // تغيير الـ action مؤقتاً للتصدير
    form.attr('action', '{{ route("pos.reports.shift.export") }}');
    form.submit();
    
    // إعادة الـ action الأصلي
    form.attr('action', currentAction);
    
    // عرض رسالة نجاح
    toastr.success('تم بدء تصدير التقرير', 'نجح التصدير');
}

function printReport() {
    window.print();
}

// معالجة الأخطاء
$(document).ajaxError(function(event, xhr, settings, thrownError) {
    toastr.error('حدث خطأ أثناء تحميل البيانات', 'خطأ');
});

// رسائل التحميل
$(document).ajaxStart(function() {
    toastr.info('جاري تحميل البيانات...', 'تحميل');
});

$(document).ajaxStop(function() {
    toastr.clear();
});
</script>

<style>
@media print {
    .card-header .dropdown,
    .btn,
    .breadcrumb,
    .content-header-right {
        display: none !important;
    }
    
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    
    .table {
        font-size: 12px;
    }
    
    .table th,
    .table td {
        padding: 5px !important;
    }
}

.table-responsive {
    max-height: 600px;
    overflow-y: auto;
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

.loading {
    opacity: 0.6;
    pointer-events: none;
}

.no-data {
    text-align: center;
    padding: 50px;
    color: #6c757d;
}
</style>
@endsection