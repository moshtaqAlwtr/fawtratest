@extends('master')
@section('title')
تقرير مبيعات التصنيفات
@stop

@section('css')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<style>
:root {
    --primary-color: #2c3e50;
    --secondary-color: #34495e;
    --accent-color: #3498db;
    --success-color: #27ae60;
    --warning-color: #f39c12;
    --light-gray: #f8f9fa;
    --border-color: #e9ecef;
    --text-color: #2d3748;
    --text-light: #718096;
}

body {
    background-color: #f5f7f9;
    color: var(--text-color);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.report-form {
    background: white;
    padding: 25px;
    border-radius: 10px;
    margin-bottom: 25px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    border: 1px solid var(--border-color);
}

.report-form h5 {
    color: var(--primary-color);
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 1px solid var(--border-color);
    font-weight: 600;
}

.stats-card {
    background: white;
    color: var(--text-color);
    padding: 20px;
    border-radius: 8px;
    text-align: center;
    margin-bottom: 20px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.04);
    border-left: 4px solid var(--accent-color);
    transition: transform 0.2s, box-shadow 0.2s;
}

.stats-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 12px rgba(0,0,0,0.08);
}

.stats-card.success { border-left-color: var(--success-color); }
.stats-card.info { border-left-color: var(--accent-color); }
.stats-card.warning { border-left-color: var(--warning-color); }

.stats-number {
    font-size: 1.8rem;
    font-weight: bold;
    margin-bottom: 5px;
    color: var(--primary-color);
}

.stats-card .bi {
    font-size: 1.5rem;
    margin-bottom: 10px;
    color: var(--accent-color);
}

.stats-card.success .bi { color: var(--success-color); }
.stats-card.info .bi { color: var(--accent-color); }
.stats-card.warning .bi { color: var(--warning-color); }

.table-container {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    overflow: hidden;
    margin-bottom: 30px;
}

.modern-table {
    border-collapse: separate;
    border-spacing: 0;
    width: 100%;
}

.modern-table th {
    background: var(--primary-color);
    color: white;
    padding: 15px 12px;
    text-align: right;
    border: none;
    font-weight: 600;
    font-size: 0.9rem;
}

.modern-table td {
    padding: 14px 12px;
    border-bottom: 1px solid var(--border-color);
    vertical-align: middle;
}

.modern-table tbody tr {
    transition: background-color 0.15s;
}

.modern-table tbody tr:hover {
    background: #f8fafc;
}

.totals-row {
    background: #edf2f7 !important;
    color: var(--primary-color) !important;
    font-weight: bold;
}

.totals-row td {
    border-top: 2px solid var(--border-color);
    border-bottom: none;
    font-size: 1.05rem;
}

.badge-custom {
    padding: 8px 14px;
    font-size: 0.85rem;
    border-radius: 6px;
    background: #e9ecef;
    color: var(--text-light);
    font-weight: 500;
    margin-left: 8px;
}

.badge-custom .bi {
    margin-left: 5px;
}

.content-header {
    margin-bottom: 25px;
}

.content-header h2 {
    color: var(--primary-color);
    font-weight: 700;
}

.breadcrumb-item a {
    color: var(--accent-color);
}

.form-control {
    border: 1px solid #d2d6dc;
    border-radius: 6px;
    padding: 8px 12px;
    height: calc(1.5em + 0.75rem + 2px);
}

.form-control:focus {
    border-color: var(--accent-color);
    box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
}

label {
    font-weight: 500;
    margin-bottom: 6px;
    color: var(--secondary-color);
}

.btn {
    border-radius: 6px;
    padding: 8px 16px;
    font-weight: 500;
    transition: all 0.2s;
}

.btn-primary {
    background-color: var(--accent-color);
    border-color: var(--accent-color);
}

.btn-primary:hover {
    background-color: #2980b9;
    border-color: #2980b9;
    transform: translateY(-1px);
}

.alert {
    border-radius: 8px;
    border: none;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}

.alert-warning {
    background-color: #fff3cd;
    color: #856404;
}

.alert-danger {
    background-color: #f8d7da;
    color: #721c24;
}

.filter-badge {
    background: #e3f2fd;
    color: var(--accent-color);
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 0.8rem;
    margin-right: 5px;
}

@media (max-width: 768px) {
    .stats-card {
        margin-bottom: 15px;
    }
    
    .badge-custom {
        margin-bottom: 8px;
        display: inline-block;
    }
}
</style>
@endsection

@section('content')
<div class="content-header row">
    <div class="content-header-left col-12 mb-2">
        <h2 class="content-header-title float-left mb-0">تقرير مبيعات التصنيفات</h2>
        <div class="breadcrumb-wrapper col-12">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">الرئيسية</a></li>
                <li class="breadcrumb-item active">تقرير مبيعات التصنيفات</li>
            </ol>
        </div>
    </div>
</div>

<!-- فلاتر التقرير -->
<div class="report-form">
    <h5><i class="bi bi-funnel"></i> فلاتر التقرير</h5>
    <form method="GET" action="{{ route('pos_reports.Category') }}">
        <div class="row">
            <div class="col-md-3 mb-3">
                <label>رقم الجلسة</label>
                <select class="form-control" name="session_id">
                    <option value="">الكل</option>
                    @foreach($sessions ?? [] as $session)
                        <option value="{{ $session->id }}" {{ request('session_id') == $session->id ? 'selected' : '' }}>
                            {{ $session->session_number }} - {{ $session->started_at->format('Y-m-d') }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-3 mb-3">
                <label>التصنيف</label>
                <select class="form-control" name="category_id">
                    <option value="">الكل</option>
                    @foreach($categories ?? [] as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-3 mb-3">
                <label>المخزن</label>
                <select class="form-control" name="store_house_id">
                    <option value="">الكل</option>
                    @foreach($storeHouses ?? [] as $store)
                        <option value="{{ $store->id }}" {{ request('store_house_id') == $store->id ? 'selected' : '' }}>
                            {{ $store->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-3 mb-3">
                <label>تجميع حسب</label>
                <select class="form-control" name="group_by">
                    <option value="category" {{ request('group_by', 'category') == 'category' ? 'selected' : '' }}>التصنيف</option>
                    <option value="date" {{ request('group_by') == 'date' ? 'selected' : '' }}>التاريخ</option>
                    <option value="session" {{ request('group_by') == 'session' ? 'selected' : '' }}>الجلسة</option>
                </select>
            </div>
            
            <div class="col-md-3 mb-3">
                <label>من تاريخ</label>
                <input type="date" class="form-control" name="date_from" 
                       value="{{ request('date_from', \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d')) }}">
            </div>
            
            <div class="col-md-3 mb-3">
                <label>إلى تاريخ</label>
                <input type="date" class="form-control" name="date_to" 
                       value="{{ request('date_to', \Carbon\Carbon::now()->format('Y-m-d')) }}">
            </div>
            
            <div class="col-md-3 mb-3">
                <label>ترتيب حسب</label>
                <select class="form-control" name="sort_by">
                    <option value="total_amount" {{ request('sort_by', 'total_amount') == 'total_amount' ? 'selected' : '' }}>المجموع الإجمالي</option>
                    <option value="total_quantity" {{ request('sort_by') == 'total_quantity' ? 'selected' : '' }}>الكمية الإجمالية</option>
                    <option value="category_name" {{ request('sort_by') == 'category_name' ? 'selected' : '' }}>اسم التصنيف</option>
                    <option value="invoices_count" {{ request('sort_by') == 'invoices_count' ? 'selected' : '' }}>عدد الفواتير</option>
                </select>
            </div>
            
            <div class="col-md-3 mb-3">
                <label>اتجاه الترتيب</label>
                <select class="form-control" name="sort_direction">
                    <option value="desc" {{ request('sort_direction', 'desc') == 'desc' ? 'selected' : '' }}>تنازلي</option>
                    <option value="asc" {{ request('sort_direction') == 'asc' ? 'selected' : '' }}>تصاعدي</option>
                </select>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary mr-2">
                    <i class="bi bi-search"></i> عرض التقرير
                </button>
                <a href="{{ route('pos_reports.Category') }}" class="btn btn-outline-secondary mr-2">
                    <i class="bi bi-arrow-clockwise"></i> إعادة تعيين
                </a>
               
            </div>
        </div>
    </form>
</div>

<!-- عرض النتائج مباشرة من الخادم -->
@php
// جلب البيانات مباشرة من الخادم مع تطبيق جميع الفلاتر
$reportData = null;
try {
    $query = DB::table('invoices')
        ->join('invoice_items', 'invoices.id', '=', 'invoice_items.invoice_id')
        ->join('products', 'invoice_items.product_id', '=', 'products.id')
        ->join('categories', 'products.category_id', '=', 'categories.id')
        ->leftJoin('pos_sessions', 'invoices.session_id', '=', 'pos_sessions.id')
        ->where('invoices.type', 'pos');

    // تطبيق الفلاتر
    if (request('session_id')) {
        $query->where('invoices.session_id', request('session_id'));
    }
    if (request('category_id')) {
        $query->where('categories.id', request('category_id'));
    }
    if (request('store_house_id')) {
        $query->where('invoice_items.store_house_id', request('store_house_id'));
    }
    if (request('date_from')) {
        $query->whereDate('invoices.created_at', '>=', request('date_from'));
    }
    if (request('date_to')) {
        $query->whereDate('invoices.created_at', '<=', request('date_to'));
    }

    // تحديد نوع التجميع
    $groupBy = request('group_by', 'category');
    
    switch ($groupBy) {
        case 'category':
            $results = $query->select([
                'categories.id as category_id',
                'categories.name as category_name',
                DB::raw('COUNT(DISTINCT invoices.id) as invoices_count'),
                DB::raw('SUM(CAST(invoice_items.quantity as DECIMAL(10,2))) as total_quantity'),
                DB::raw('SUM(CAST(invoice_items.total as DECIMAL(10,2))) as total_amount'),
                DB::raw('AVG(CAST(invoice_items.unit_price as DECIMAL(10,2))) as avg_unit_price'),
                DB::raw('MIN(invoices.created_at) as first_sale_date'),
                DB::raw('MAX(invoices.created_at) as last_sale_date')
            ])
            ->groupBy('categories.id', 'categories.name')
            ->get();
            break;

        case 'date':
            $results = $query->select([
                DB::raw('DATE(invoices.created_at) as sale_date'),
                'categories.id as category_id',
                'categories.name as category_name',
                DB::raw('COUNT(DISTINCT invoices.id) as invoices_count'),
                DB::raw('SUM(CAST(invoice_items.quantity as DECIMAL(10,2))) as total_quantity'),
                DB::raw('SUM(CAST(invoice_items.total as DECIMAL(10,2))) as total_amount')
            ])
            ->groupBy(DB::raw('DATE(invoices.created_at)'), 'categories.id', 'categories.name')
            ->orderBy('sale_date', 'desc')
            ->get();
            break;

        case 'session':
            $results = $query->select([
                'pos_sessions.id as session_id',
                'pos_sessions.session_number',
                'pos_sessions.started_at',
                'categories.id as category_id',
                'categories.name as category_name',
                DB::raw('COUNT(DISTINCT invoices.id) as invoices_count'),
                DB::raw('SUM(CAST(invoice_items.quantity as DECIMAL(10,2))) as total_quantity'),
                DB::raw('SUM(CAST(invoice_items.total as DECIMAL(10,2))) as total_amount')
            ])
            ->whereNotNull('pos_sessions.id')
            ->groupBy('pos_sessions.id', 'pos_sessions.session_number', 'pos_sessions.started_at', 'categories.id', 'categories.name')
            ->orderBy('pos_sessions.started_at', 'desc')
            ->get();
            break;

        default:
            $results = $query->select([
                'categories.name as category_name',
                DB::raw('COUNT(DISTINCT invoices.id) as invoices_count'),
                DB::raw('SUM(CAST(invoice_items.quantity as DECIMAL(10,2))) as total_quantity'),
                DB::raw('SUM(CAST(invoice_items.total as DECIMAL(10,2))) as total_amount')
            ])
            ->groupBy('categories.id', 'categories.name')
            ->get();
    }

    // تطبيق الترتيب
    $sortBy = request('sort_by', 'total_amount');
    $sortDirection = request('sort_direction', 'desc');
    
    if ($sortDirection === 'desc') {
        $results = $results->sortByDesc($sortBy);
    } else {
        $results = $results->sortBy($sortBy);
    }

    $reportData = [
        'data' => $results->values()->toArray(),
        'totals' => [
            'total_invoices' => $results->sum('invoices_count'),
            'total_quantity' => $results->sum('total_quantity'),
            'total_amount' => $results->sum('total_amount'),
            'categories_count' => $results->pluck('category_name')->unique()->count()
        ],
        'group_by' => $groupBy,
        'filters' => request()->all()
    ];
} catch (\Exception $e) {
    $reportData = ['error' => $e->getMessage()];
}
@endphp

@if(isset($reportData['error']))
    <div class="alert alert-danger">
        <h5><i class="bi bi-exclamation-triangle"></i> خطأ في التقرير</h5>
        <p>{{ $reportData['error'] }}</p>
    </div>
@elseif(!empty($reportData['data']))
    <!-- شارات المعلومات السريعة -->
    <div class="row mb-4">
        <div class="col-md-12 d-flex flex-wrap">
            <span class="badge-custom">
                <i class="bi bi-funnel"></i> 
                {{ $reportData['totals']['categories_count'] }} تصنيف
            </span>
            <span class="badge-custom">
                <i class="bi bi-receipt"></i>
                {{ number_format($reportData['totals']['total_invoices']) }} فاتورة
            </span>
            <span class="badge-custom">
                <i class="bi bi-currency-dollar"></i>
                {{ number_format($reportData['totals']['total_amount'], 2) }} ر.س
            </span>
            <span class="badge-custom">
                <i class="bi bi-collection"></i>
                تجميع: {{ $reportData['group_by'] == 'category' ? 'التصنيف' : ($reportData['group_by'] == 'date' ? 'التاريخ' : 'الجلسة') }}
            </span>
        </div>
    </div>

    <!-- الإحصائيات -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stats-card info">
                <i class="bi bi-currency-dollar"></i>
                <div class="stats-number">{{ number_format($reportData['totals']['total_amount'], 2) }}</div>
                <div>إجمالي المبيعات (ر.س)</div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stats-card success">
                <i class="bi bi-box"></i>
                <div class="stats-number">{{ number_format($reportData['totals']['total_quantity']) }}</div>
                <div>إجمالي الكمية</div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stats-card">
                <i class="bi bi-receipt"></i>
                <div class="stats-number">{{ number_format($reportData['totals']['total_invoices']) }}</div>
                <div>عدد الفواتير</div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stats-card warning">
                <i class="bi bi-tags"></i>
                <div class="stats-number">{{ number_format($reportData['totals']['categories_count']) }}</div>
                <div>عدد التصنيفات</div>
            </div>
        </div>
    </div>

    <!-- الجدول -->
    <div class="table-container">
        <table class="table table-striped modern-table mb-0">
            <thead>
                <tr>
                    @if($reportData['group_by'] == 'category')
                        <th><i class="bi bi-tag"></i> التصنيف</th>
                        <th><i class="bi bi-receipt"></i> عدد الفواتير</th>
                        <th><i class="bi bi-box"></i> إجمالي الكمية</th>
                        <th><i class="bi bi-currency-dollar"></i> إجمالي المبلغ (ر.س)</th>
                        <th><i class="bi bi-calculator"></i> متوسط سعر الوحدة</th>
                        <th><i class="bi bi-calendar-check"></i> أول عملية بيع</th>
                        <th><i class="bi bi-calendar-x"></i> آخر عملية بيع</th>
                    @elseif($reportData['group_by'] == 'date')
                        <th><i class="bi bi-calendar"></i> التاريخ</th>
                        <th><i class="bi bi-tag"></i> التصنيف</th>
                        <th><i class="bi bi-receipt"></i> عدد الفواتير</th>
                        <th><i class="bi bi-box"></i> إجمالي الكمية</th>
                        <th><i class="bi bi-currency-dollar"></i> إجمالي المبلغ (ر.س)</th>
                    @elseif($reportData['group_by'] == 'session')
                        <th><i class="bi bi-hash"></i> رقم الجلسة</th>
                        <th><i class="bi bi-calendar"></i> تاريخ الجلسة</th>
                        <th><i class="bi bi-tag"></i> التصنيف</th>
                        <th><i class="bi bi-receipt"></i> عدد الفواتير</th>
                        <th><i class="bi bi-box"></i> إجمالي الكمية</th>
                        <th><i class="bi bi-currency-dollar"></i> إجمالي المبلغ (ر.س)</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($reportData['data'] as $row)
                <tr>
                    @if($reportData['group_by'] == 'category')
                        <td><strong>{{ $row->category_name }}</strong></td>
                        <td>{{ number_format($row->invoices_count) }}</td>
                        <td>{{ number_format($row->total_quantity) }}</td>
                        <td>{{ number_format($row->total_amount, 2) }}</td>
                        <td>{{ number_format($row->avg_unit_price ?? 0, 2) }}</td>
                        <td>{{ $row->first_sale_date ? \Carbon\Carbon::parse($row->first_sale_date)->format('Y-m-d') : '-' }}</td>
                        <td>{{ $row->last_sale_date ? \Carbon\Carbon::parse($row->last_sale_date)->format('Y-m-d') : '-' }}</td>
                    @elseif($reportData['group_by'] == 'date')
                        <td>{{ \Carbon\Carbon::parse($row->sale_date)->format('Y-m-d') }}</td>
                        <td>{{ $row->category_name }}</td>
                        <td>{{ number_format($row->invoices_count) }}</td>
                        <td>{{ number_format($row->total_quantity) }}</td>
                        <td>{{ number_format($row->total_amount, 2) }}</td>
                    @elseif($reportData['group_by'] == 'session')
                        <td>{{ $row->session_number ?? 'غير محدد' }}</td>
                        <td>{{ $row->started_at ? \Carbon\Carbon::parse($row->started_at)->format('Y-m-d') : '-' }}</td>
                        <td>{{ $row->category_name }}</td>
                        <td>{{ number_format($row->invoices_count) }}</td>
                        <td>{{ number_format($row->total_quantity) }}</td>
                        <td>{{ number_format($row->total_amount, 2) }}</td>
                    @endif
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="totals-row">
                    @if($reportData['group_by'] == 'category')
                        <td><strong>الإجمالي</strong></td>
                        <td><strong>{{ number_format($reportData['totals']['total_invoices']) }}</strong></td>
                        <td><strong>{{ number_format($reportData['totals']['total_quantity']) }}</strong></td>
                        <td><strong>{{ number_format($reportData['totals']['total_amount'], 2) }}</strong></td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                    @else
                        <td colspan="{{ $reportData['group_by'] == 'session' ? 3 : 2 }}"><strong>الإجمالي</strong></td>
                        <td><strong>{{ number_format($reportData['totals']['total_invoices']) }}</strong></td>
                        <td><strong>{{ number_format($reportData['totals']['total_quantity']) }}</strong></td>
                        <td><strong>{{ number_format($reportData['totals']['total_amount'], 2) }}</strong></td>
                    @endif
                </tr>
            </tfoot>
        </table>
    </div>

@else
    <div class="alert alert-warning">
        <h5><i class="bi bi-info-circle"></i> لا توجد بيانات</h5>
        <p>لا توجد مبيعات تطابق الفلاتر المحددة.</p>
        
        @php
        $totalPosInvoices = DB::table('invoices')->where('type', 'pos')->count();
        $appliedFilters = collect(request()->except(['_token']))->filter()->count();
        @endphp
        
        <hr>
        <div class="row">
            <div class="col-md-6">
                <h6>معلومات تشخيصية:</h6>
                <ul class="list-unstyled">
                    <li><i class="bi bi-receipt"></i> إجمالي فواتير POS: <strong>{{ $totalPosInvoices }}</strong></li>
                    <li><i class="bi bi-tags"></i> عدد التصنيفات: <strong>{{ DB::table('categories')->count() }}</strong></li>
                    <li><i class="bi bi-box"></i> عدد المنتجات: <strong>{{ DB::table('products')->count() }}</strong></li>
                </ul>
            </div>
            <div class="col-md-6">
                <h6>الفلاتر المطبقة:</h6>
                <ul class="list-unstyled">
                    <li><i class="bi bi-funnel"></i> عدد الفلاتر: <strong>{{ $appliedFilters }}</strong></li>
                    @if(request('session_id'))
                        <li><i class="bi bi-hash"></i> الجلسة: <strong>{{ request('session_id') }}</strong></li>
                    @endif
                    @if(request('category_id'))
                        <li><i class="bi bi-tag"></i> التصنيف: <strong>{{ request('category_id') }}</strong></li>
                    @endif
                    @if(request('date_from') || request('date_to'))
                        <li><i class="bi bi-calendar-range"></i> الفترة: <strong>{{ request('date_from') }} إلى {{ request('date_to') }}</strong></li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
@endif

@endsection

@section('js')
<script>
// دالة تصدير محسنة
function exportToCSV() {
    const data = @json($reportData['data'] ?? []);
    const totals = @json($reportData['totals'] ?? []);
    const groupBy = @json($reportData['group_by'] ?? 'category');
    
    if (!data || data.length === 0) {
        alert('لا توجد بيانات للتصدير');
        return;
    }
    
    let csv = '';
    
    // إضافة الهيدر حسب نوع التجميع
    if (groupBy === 'category') {
        csv = 'التصنيف,عدد الفواتير,إجمالي الكمية,إجمالي المبلغ,متوسط سعر الوحدة,أول عملية بيع,آخر عملية بيع\n';
        data.forEach(function(row) {
            csv += `"${row.category_name}","${row.invoices_count}","${row.total_quantity}","${row.total_amount}","${row.avg_unit_price || 0}","${row.first_sale_date || ''}","${row.last_sale_date || ''}"\n`;
        });
    } else if (groupBy === 'date') {
        csv = 'التاريخ,التصنيف,عدد الفواتير,إجمالي الكمية,إجمالي المبلغ\n';
        data.forEach(function(row) {
            csv += `"${row.sale_date}","${row.category_name}","${row.invoices_count}","${row.total_quantity}","${row.total_amount}"\n`;
        });
    } else if (groupBy === 'session') {
        csv = 'رقم الجلسة,تاريخ الجلسة,التصنيف,عدد الفواتير,إجمالي الكمية,إجمالي المبلغ\n';
        data.forEach(function(row) {
            csv += `"${row.session_number || ''}","${row.started_at || ''}","${row.category_name}","${row.invoices_count}","${row.total_quantity}","${row.total_amount}"\n`;
        });
    }
    
    // إضافة صف الإجماليات
    if (groupBy === 'category') {
        csv += `"الإجمالي","${totals.total_invoices}","${totals.total_quantity}","${totals.total_amount}","-","-","-"`;
    } else {
        csv += `"الإجمالي","","","${totals.total_invoices}","${totals.total_quantity}","${totals.total_amount}"`;
    }
    
    // تحميل الملف
    const blob = new Blob(['\ufeff' + csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', `category_sales_report_${groupBy}_${new Date().toISOString().slice(0,10)}.csv`);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

console.log('تقرير مبيعات التصنيفات محمل بنجاح');
console.log('نوع التجميع:', @json($reportData['group_by'] ?? null));
console.log('عدد السجلات:', @json(count($reportData['data'] ?? [])));
</script>
@endsection