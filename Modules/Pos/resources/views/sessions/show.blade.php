@extends('master')

@section('title')
الجلسة النشطة - {{ $session->session_number }}
@stop

@section('content')

<div class="content-header row">
    <div class="content-header-left col-md-6 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">
                    الجلسة النشطة - {{ $session->session_number }}
                    <span class="badge badge-success ms-2">نشطة</span>
                </h2>
            </div>
        </div>
    </div>
    
    {{-- أزرار التحكم في الأعلى --}}
    <div class="content-header-right col-md-6 col-12">
        <div class="float-end d-flex flex-wrap">
            <a href="{{ url('/pos') }}" class="btn btn-success me-2">
                <i class="fa fa-shopping-cart me-1"></i>نقطة البيع
            </a>
            
            <button type="button" class="btn btn-info me-2" onclick="showSessionStats()">
                <i class="fa fa-chart-bar me-1"></i>الإحصائيات
            </button>
            
            <button onclick="window.print()" class="btn btn-outline-secondary me-2">
                <i class="fa fa-print me-1"></i>طباعة
            </button>
            
            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#closeSessionModal">
                <i class="fa fa-stop-circle me-1"></i>إغلاق الجلسة
            </button>
        </div>
    </div>
</div>

{{-- عرض رسائل النجاح --}}
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fa fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- عرض رسائل الخطأ --}}
@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fa fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- معلومات الجلسة الأساسية -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fa fa-info-circle me-2"></i>تفاصيل الجلسة
                    </h4>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2">
                        <strong><i class="fa fa-hashtag me-2"></i>رقم الجلسة:</strong><br>
                        <span class="text-primary h5">{{ $session->session_number }}</span>
                    </div>
                    <div class="col-md-2">
                        <strong><i class="fa fa-user me-2"></i>الموظف:</strong><br>
                        {{ $session->user->name }}
                    </div>
                    <div class="col-md-2">
                        <strong><i class="fa fa-clock me-2"></i>وقت البدء:</strong><br>
                        {{ $session->started_at->format('Y-m-d') }}<br>
                        <small>{{ $session->started_at->format('H:i:s') }}</small>
                    </div>
                    <div class="col-md-2">
                        <strong><i class="fa fa-stopwatch me-2"></i>المدة:</strong><br>
                        <span class="text-info">{{ $session->duration }}</span>
                    </div>
                    <div class="col-md-2">
                        <strong><i class="fa fa-calendar me-2"></i>الوردية:</strong><br>
                        {{ $session->shift->name }}
                    </div>
                    <div class="col-md-2">
                        <strong><i class="fa fa-desktop me-2"></i>الجهاز:</strong><br>
                        {{ $session->device->device_name }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- إحصائيات الجلسة -->
<div class="row mt-3">
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="card-title mb-1">{{ number_format($session->total_transactions) }}</h3>
                        <p class="card-text mb-0">إجمالي المعاملات</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fa fa-shopping-cart fa-3x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="card-title mb-1">{{ number_format($session->total_sales, 2) }}</h3>
                        <p class="card-text mb-0">إجمالي المبيعات (ر.س)</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fa fa-money-bill fa-3x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="card-title mb-1">{{ number_format($session->total_cash, 2) }}</h3>
                        <p class="card-text mb-0">النقدي (ر.س)</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fa fa-coins fa-3x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="card-title mb-1">{{ number_format($session->total_card, 2) }}</h3>
                        <p class="card-text mb-0">الشبكة (ر.س)</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fa fa-credit-card fa-3x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ملخص وسائل الدفع -->
<div class="row mt-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">
                    <i class="fa fa-chart-pie me-2"></i>ملخص وسائل الدفع
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-4">
                        <div class="border-end">
                            <h4 class="text-success mb-1">{{ number_format($session->opening_balance, 2) }}</h4>
                            <p class="text-muted mb-0 small">الرصيد الافتتاحي</p>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="border-end">
                            <h4 class="text-primary mb-1">{{ number_format($session->total_cash, 2) }}</h4>
                            <p class="text-muted mb-0 small">النقدي المحصل</p>
                        </div>
                    </div>
                    <div class="col-4">
                        <h4 class="text-info mb-1">{{ number_format($session->opening_balance + $session->total_cash, 2) }}</h4>
                        <p class="text-muted mb-0 small">إجمالي النقدي</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">
                    <i class="fa fa-clock me-2"></i>معلومات الوقت
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <p class="mb-2"><strong>بداية الجلسة:</strong></p>
                        <p class="text-muted">{{ $session->started_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="col-6">
                        <p class="mb-2"><strong>الوقت الحالي:</strong></p>
                        <p class="text-muted" id="current-time">{{ now()->format('d/m/Y H:i:s') }}</p>
                    </div>
                </div>
                <div class="progress mt-2">
                    <div class="progress-bar bg-info" style="width: {{ min(($session->started_at->diffInHours(now()) / 8) * 100, 100) }}%"></div>
                </div>
                <small class="text-muted">مدة الوردية التقريبية: 8 ساعات</small>
            </div>
        </div>
    </div>
</div>

<!-- تفاصيل المعاملات -->
<div class="row mt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fa fa-list me-2"></i>تفاصيل المعاملات
                    </h4>
                    <button class="btn btn-sm btn-outline-primary" onclick="refreshTransactions()">
                        <i class="fa fa-refresh me-1"></i>تحديث
                    </button>
                </div>
            </div>
            <div class="card-body">
                @if($session->details && $session->details->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th width="10%">الوقت</th>
                                    <th width="15%">النوع</th>
                                    <th width="15%">المرجع</th>
                                    <th width="12%">المبلغ</th>
                                    <th width="12%">النقدي</th>
                                    <th width="12%">البطاقة</th>
                                    <th width="24%">الوصف</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($session->details->sortByDesc('transaction_time') as $detail)
                                    <tr>
                                        <td>
                                            <small>{{ $detail->transaction_time->format('H:i:s') }}</small>
                                        </td>
                                        <td>
                                            @switch($detail->transaction_type)
                                                @case('sale')
                                                    <span class="badge badge-success">
                                                        <i class="fa fa-shopping-cart me-1"></i>بيع
                                                    </span>
                                                    @break
                                                @case('return')
                                                    <span class="badge badge-danger">
                                                        <i class="fa fa-undo me-1"></i>إرجاع
                                                    </span>
                                                    @break
                                                @case('opening_balance')
                                                    <span class="badge badge-primary">
                                                        <i class="fa fa-play me-1"></i>افتتاحي
                                                    </span>
                                                    @break
                                                @case('cash_adjustment')
                                                    <span class="badge badge-warning">
                                                        <i class="fa fa-edit me-1"></i>تعديل
                                                    </span>
                                                    @break
                                                @default
                                                    <span class="badge badge-secondary">{{ $detail->transaction_type }}</span>
                                            @endswitch
                                        </td>
                                        <td>
                                            @if($detail->reference_number)
                                                <code>{{ $detail->reference_number }}</code>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="fw-bold text-{{ $detail->transaction_type == 'return' ? 'danger' : 'success' }}">
                                                {{ number_format($detail->amount, 2) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($detail->cash_amount > 0)
                                                <span class="text-primary">{{ number_format($detail->cash_amount, 2) }}</span>
                                            @else
                                                <span class="text-muted">0.00</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($detail->card_amount > 0)
                                                <span class="text-info">{{ number_format($detail->card_amount, 2) }}</span>
                                            @else
                                                <span class="text-muted">0.00</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small>{{ $detail->description ?? 'لا يوجد وصف' }}</small>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- ملخص سريع -->
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <div class="row text-center">
                                    <div class="col-md-3">
                                        <strong>آخر معاملة:</strong><br>
                                        <small>{{ $session->details->sortByDesc('transaction_time')->first()->transaction_time->diffForHumans() }}</small>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>عدد المبيعات:</strong><br>
                                        {{ $session->details->where('transaction_type', 'sale')->count() }}
                                    </div>
                                    <div class="col-md-3">
                                        <strong>عدد المرتجعات:</strong><br>
                                        {{ $session->details->where('transaction_type', 'return')->count() }}
                                    </div>
                                    <div class="col-md-3">
                                        <strong>متوسط قيمة المعاملة:</strong><br>
                                        {{ $session->total_transactions > 0 ? number_format($session->total_sales / $session->total_transactions, 2) : '0.00' }} ر.س
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fa fa-inbox fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">لا توجد معاملات حتى الآن</h5>
                        <p class="text-muted">ستظهر جميع المعاملات هنا عند بدء البيع من نقطة البيع</p>
                        <a href="{{ url('/pos') }}" class="btn btn-primary">
                            <i class="fa fa-shopping-cart me-2"></i>انتقال لنقطة البيع
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- مودل إغلاق الجلسة --}}
<div class="modal fade" id="closeSessionModal" tabindex="-1" aria-labelledby="closeSessionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="closeSessionForm" method="POST" action="{{ route('pos.sessions.close', $session->id) }}">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="closeSessionModalLabel">
                        <i class="fa fa-stop-circle me-2"></i>إغلاق الجلسة
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                    {{-- ملخص الجلسة --}}
                    <div class="alert alert-info">
                        <h6 class="alert-heading">
                            <i class="fa fa-info-circle me-2"></i>ملخص الجلسة: {{ $session->session_number }}
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="list-unstyled mb-0">
                                    <li><strong>الموظف:</strong> {{ $session->user->name }}</li>
                                    <li><strong>بداية الجلسة:</strong> {{ $session->started_at->format('d/m/Y H:i') }}</li>
                                    <li><strong>مدة الجلسة:</strong> {{ $session->duration }}</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="list-unstyled mb-0">
                                    <li><strong>إجمالي المبيعات:</strong> {{ number_format($session->total_sales, 2) }} ر.س</li>
                                    <li><strong>عدد المعاملات:</strong> {{ $session->total_transactions }}</li>
                                    <li><strong>الجهاز:</strong> {{ $session->device->device_name }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{-- حساب الأرصدة --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h6 class="mb-0">الحسابات المتوقعة</h6>
                                </div>
                                <div class="card-body">
                                    @php
                                        $expectedCash = $session->opening_balance + $session->total_cash - $session->total_returns;
                                    @endphp
                                    <div class="row">
                                        <div class="col-12 mb-2">
                                            <div class="d-flex justify-content-between">
                                                <span>الرصيد الافتتاحي:</span>
                                                <span class="fw-bold">{{ number_format($session->opening_balance, 2) }} ر.س</span>
                                            </div>
                                        </div>
                                        <div class="col-12 mb-2">
                                            <div class="d-flex justify-content-between">
                                                <span>النقدي المحصل:</span>
                                                <span class="fw-bold text-success">+ {{ number_format($session->total_cash, 2) }} ر.س</span>
                                            </div>
                                        </div>
                                        <div class="col-12 mb-2">
                                            <div class="d-flex justify-content-between">
                                                <span>المرتجعات النقدية:</span>
                                                <span class="fw-bold text-danger">- {{ number_format($session->total_returns, 2) }} ر.س</span>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="col-12">
                                            <div class="d-flex justify-content-between">
                                                <span class="h6">الرصيد المتوقع:</span>
                                                <span class="h5 text-primary fw-bold">{{ number_format($expectedCash, 2) }} ر.س</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">عد الصندوق الفعلي</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="actual_closing_balance" class="form-label">
                                            <i class="fa fa-money-bill me-1"></i>الرصيد الفعلي في الصندوق
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <input type="number" 
                                                   class="form-control" 
                                                   id="actual_closing_balance" 
                                                   name="actual_closing_balance" 
                                                   value="{{ number_format($expectedCash, 2, '.', '') }}" 
                                                   step="0.01" 
                                                   min="0" 
                                                   required>
                                            <span class="input-group-text">ر.س</span>
                                        </div>
                                        <small class="form-text text-muted">
                                            قم بعد النقود الموجودة في الصندوق فعلياً
                                        </small>
                                    </div>

                                    {{-- عرض الفرق --}}
                                    <div id="difference-display" class="alert" style="display: none;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>الفرق:</span>
                                            <span id="difference-amount" class="fw-bold"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ملاحظات الإغلاق --}}
                    <div class="mt-3">
                        <label for="closing_notes" class="form-label">
                            <i class="fa fa-sticky-note me-1"></i>ملاحظات الإغلاق (اختياري)
                        </label>
                        <textarea class="form-control" 
                                  id="closing_notes" 
                                  name="closing_notes" 
                                  rows="3" 
                                  placeholder="أضف أي ملاحظات حول الجلسة..."></textarea>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fa fa-times me-1"></i>إلغاء
                    </button>
                    <button type="submit" class="btn btn-danger" id="confirmCloseBtn">
                        <i class="fa fa-stop-circle me-1"></i>تأكيد الإغلاق
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- مودل الإحصائيات --}}
<div class="modal fade" id="statsModal" tabindex="-1" aria-labelledby="statsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="statsModalLabel">
                    <i class="fa fa-chart-bar me-2"></i>إحصائيات تفصيلية - {{ $session->session_number }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>الإحصائيات المالية</h6>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between">
                                <span>إجمالي المبيعات:</span>
                                <strong>{{ number_format($session->total_sales, 2) }} ر.س</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>النقدي:</span>
                                <strong>{{ number_format($session->total_cash, 2) }} ر.س</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>البطاقات:</span>
                                <strong>{{ number_format($session->total_card, 2) }} ر.س</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>المرتجعات:</span>
                                <strong class="text-danger">{{ number_format($session->total_returns, 2) }} ر.س</strong>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>إحصائيات المعاملات</h6>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between">
                                <span>إجمالي المعاملات:</span>
                                <strong>{{ $session->total_transactions }}</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>متوسط قيمة المعاملة:</span>
                                <strong>{{ $session->total_transactions > 0 ? number_format($session->total_sales / $session->total_transactions, 2) : '0.00' }} ر.س</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>نسبة النقدي:</span>
                                <strong>{{ $session->total_sales > 0 ? number_format(($session->total_cash / $session->total_sales) * 100, 1) : '0' }}%</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>نسبة البطاقات:</span>
                                <strong>{{ $session->total_sales > 0 ? number_format(($session->total_card / $session->total_sales) * 100, 1) : '0' }}%</strong>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// تحديث الوقت الحالي كل ثانية
function updateCurrentTime() {
    const now = new Date();
    const currentTimeElement = document.getElementById('current-time');
    if (currentTimeElement) {
        const options = {
            year: 'numeric',
            month: '2-digit', 
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false
        };
        currentTimeElement.textContent = now.toLocaleDateString('ar-SA', options);
    }
}

// تحديث المعاملات
function refreshTransactions() {
    location.reload();
}

// عرض إحصائيات تفصيلية
function showSessionStats() {
    const statsModal = new bootstrap.Modal(document.getElementById('statsModal'));
    statsModal.show();
}

// حساب الفرق عند تغيير الرصيد الفعلي
document.addEventListener('DOMContentLoaded', function() {
    const actualBalanceInput = document.getElementById('actual_closing_balance');
    const differenceDisplay = document.getElementById('difference-display');
    const differenceAmount = document.getElementById('difference-amount');
    const expectedCash = {{ $session->opening_balance + $session->total_cash - $session->total_returns }};

    if (actualBalanceInput) {
        actualBalanceInput.addEventListener('input', function() {
            const actualBalance = parseFloat(this.value) || 0;
            const difference = actualBalance - expectedCash;

            if (Math.abs(difference) > 0.01) {
                differenceDisplay.style.display = 'block';
                differenceAmount.textContent = (difference >= 0 ? '+' : '') + difference.toFixed(2) + ' ر.س';
                
                // تغيير لون التنبيه حسب نوع الفرق
                differenceDisplay.className = 'alert ' + (difference >= 0 ? 'alert-success' : 'alert-warning');
            } else {
                differenceDisplay.style.display = 'none';
            }
        });
    }

    // تأكيد إغلاق الجلسة
    const closeForm = document.getElementById('closeSessionForm');
    if (closeForm) {
        closeForm.addEventListener('submit', function(e) {
            const actualBalance = parseFloat(document.getElementById('actual_closing_balance').value) || 0;
            const difference = actualBalance - expectedCash;

            if (Math.abs(difference) > 50) { // إذا كان الفرق أكبر من 50 ريال
                e.preventDefault();
                
                const confirmMessage = difference > 0 
                    ? `هناك زيادة في الصندوق بمقدار ${difference.toFixed(2)} ر.س\nهل أنت متأكد من الرصيد المدخل؟`
                    : `هناك نقص في الصندوق بمقدار ${Math.abs(difference).toFixed(2)} ر.س\nهل أنت متأكد من الرصيد المدخل؟`;

                if (confirm(confirmMessage)) {
                    this.submit();
                }
            }
        });
    }

    // تحديث الوقت كل ثانية
    setInterval(updateCurrentTime, 1000);
    
    // إخفاء التنبيهات تلقائياً
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert-success, .alert-danger');
        alerts.forEach(alert => {
            if (alert.classList.contains('fade')) {
                alert.classList.remove('show');
            }
        });
    }, 5000);
});

// تحديث الصفحة كل 5 دقائق للحصول على أحدث المعاملات
setInterval(function() {
    // تحديث صامت للإحصائيات
    fetch(window.location.href + '?ajax=1')
        .then(response => response.text())
        .then(html => {
            // يمكن تحديث أجزاء محددة من الصفحة هنا
        })
        .catch(error => {
            console.log('خطأ في التحديث التلقائي:', error);
        });
}, 300000); // 5 دقائق
</script>

<style>
/* تحسينات التصميم */
.opacity-75 {
    opacity: 0.75;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.card-header {
    border-bottom: 1px solid rgba(0, 0, 0, 0.125);
}

.table-hover tbody tr:hover {
    background-color: rgba(0, 0, 0, 0.075);
}

.badge {
    font-size: 0.75em;
}

/* تحسينات المودل */
.modal-content {
    border: none;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.modal-header {
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

.modal-footer {
    border-top: 1px solid rgba(0, 0, 0, 0.125);
}

/* أزرار التحكم */
.content-header-right .btn {
    margin-bottom: 0.25rem;
}

/* طباعة */
@media print {
    .btn, .card-header .btn, .content-header-right {
        display: none !important;
    }
    
    .alert {
        display: none !important;
    }
    
    .card {
        border: 1px solid #000 !important;
        box-shadow: none !important;
    }
    
    .modal {
        display: none !important;
    }
}

/* تجاوبية */
@media (max-width: 768px) {
    .content-header-right {
        margin-top: 1rem;
    }
    
    .content-header-right .btn {
        width: 100%;
        margin-bottom: 0.5rem;
    }
    
    .card-body .row > div {
        margin-bottom: 1rem;
    }
    
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .modal-dialog {
        margin: 0.5rem;
    }
}

/* تحسين حقل الرصيد */
#actual_closing_balance {
    font-size: 1.1rem;
    font-weight: bold;
    text-align: center;
}

#actual_closing_balance:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

/* تنسيق عرض الفرق */
#difference-display {
    border-left: 4px solid;
    margin-top: 10px;
}

.alert-success#difference-display {
    border-left-color: #28a745;
}

.alert-warning#difference-display {
    border-left-color: #ffc107;
}
</style>

@endsection