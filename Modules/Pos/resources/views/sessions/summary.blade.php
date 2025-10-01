{{-- resources/views/pos/sessions/summary.blade.php --}}
@extends('master')

@section('title')
ملخص الجلسة - {{ $session->session_number }}
@stop

@section('content')

<div class="content-header row">
    <div class="content-header-left col-md-8 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">
                    ملخص الجلسة - {{ $session->session_number }}
                    <span class="badge badge-secondary ms-2">مغلقة</span>
                </h2>
                <div class="breadcrumbs-top mt-2">
                    <div class="breadcrumb-wrapper">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('/') }}">الرئيسية</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('pos.sessions.index') }}">الجلسات</a></li>
                            <li class="breadcrumb-item active">ملخص الجلسة</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="content-header-right col-md-4 col-12">
        <div class="float-end">
         <a href="{{ route('pos.sessions.print', $session->id) }}" 
   target="_blank" 
   class="btn btn-outline-primary me-2">
    <i class="fa fa-print me-1"></i> طباعة
</a>

            <a href="{{ route('pos.sessions.index') }}" class="btn btn-primary">
                <i class="fa fa-plus me-1"></i>جلسة جديدة
            </a>
        </div>
    </div>
</div>

{{-- رسائل النجاح --}}
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fa fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- معلومات الجلسة الأساسية --}}
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-secondary text-white">
                <h4 class="card-title mb-0">
                    <i class="fa fa-info-circle me-2"></i>معلومات الجلسة
                </h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2">
                        <strong><i class="fa fa-hashtag me-2"></i>رقم الجلسة:</strong><br>
                        <span class="text-primary h5">{{ $session->session_number }}</span>
                    </div>
                    <div class="col-md-2">
                        <strong><i class="fa fa-user me-2"></i>الكاشير:</strong><br>
                        {{ $session->user->name }}
                    </div>
                    <div class="col-md-2">
                        <strong><i class="fa fa-clock me-2"></i>بداية الجلسة:</strong><br>
                        {{ $session->started_at->format('d/m/Y') }}<br>
                        <small>{{ $session->started_at->format('H:i:s') }}</small>
                    </div>
                    <div class="col-md-2">
                        <strong><i class="fa fa-stop-circle me-2"></i>نهاية الجلسة:</strong><br>
                        {{ $session->ended_at->format('d/m/Y') }}<br>
                        <small>{{ $session->ended_at->format('H:i:s') }}</small>
                    </div>
                    <div class="col-md-2">
                        <strong><i class="fa fa-stopwatch me-2"></i>مدة الجلسة:</strong><br>
                        <span class="text-info">{{ $session->started_at->diff($session->ended_at)->format('%H:%I:%S') }}</span>
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

{{-- ملخص المبيعات --}}
<div class="row mt-3">
    <div class="col-md-3">
        <div class="card text-white bg-success">
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
        <div class="card text-white bg-info">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="card-title mb-1">{{ number_format($session->total_transactions) }}</h3>
                        <p class="card-text mb-0">عدد المعاملات</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fa fa-shopping-cart fa-3x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-white bg-primary">
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
        <div class="card text-white bg-warning">
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

{{-- تسوية الصندوق --}}
<div class="row mt-3">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">
                    <i class="fa fa-balance-scale me-2"></i>تسوية الصندوق
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tbody>
                                <tr>
                                    <td><strong>الرصيد الافتتاحي:</strong></td>
                                    <td class="text-end">{{ number_format($session->opening_balance, 2) }} ر.س</td>
                                </tr>
                                <tr>
                                    <td><strong>النقدي المحصل:</strong></td>
                                    <td class="text-end text-success">+ {{ number_format($session->total_cash, 2) }} ر.س</td>
                                </tr>
                                <tr>
                                    <td><strong>المرتجعات النقدية:</strong></td>
                                    <td class="text-end text-danger">- {{ number_format($session->total_returns, 2) }} ر.س</td>
                                </tr>
                                <tr class="table-active">
                                    <td><strong>الرصيد المتوقع:</strong></td>
                                    <td class="text-end"><strong>{{ number_format($session->closing_balance, 2) }} ر.س</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tbody>
                                <tr>
                                    <td><strong>الرصيد الفعلي:</strong></td>
                                    <td class="text-end">{{ number_format($session->actual_closing_balance, 2) }} ر.س</td>
                                </tr>
                                <tr>
                                    <td><strong>الفرق:</strong></td>
                                    <td class="text-end">
                                        <span class="badge badge-{{ $session->difference >= 0 ? 'success' : 'danger' }} badge-lg">
                                            {{ $session->difference >= 0 ? '+' : '' }}{{ number_format($session->difference, 2) }} ر.س
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        @if($session->difference > 0)
                                            <div class="alert alert-success alert-sm mb-0">
                                                <i class="fa fa-arrow-up me-1"></i>
                                                زيادة في الصندوق
                                            </div>
                                        @elseif($session->difference < 0)
                                            <div class="alert alert-warning alert-sm mb-0">
                                                <i class="fa fa-arrow-down me-1"></i>
                                                نقص في الصندوق
                                            </div>
                                        @else
                                            <div class="alert alert-success alert-sm mb-0">
                                                <i class="fa fa-check me-1"></i>
                                                الصندوق متطابق تماماً
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">
                    <i class="fa fa-chart-pie me-2"></i>توزيع وسائل الدفع
                </h5>
            </div>
            <div class="card-body">
                @php
                    $totalPayments = $session->total_cash + $session->total_card;
                    $cashPercentage = $totalPayments > 0 ? ($session->total_cash / $totalPayments) * 100 : 0;
                    $cardPercentage = $totalPayments > 0 ? ($session->total_card / $totalPayments) * 100 : 0;
                @endphp
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span><i class="fa fa-money-bill text-success me-1"></i>نقدي</span>
                        <span>{{ number_format($cashPercentage, 1) }}%</span>
                    </div>
                    <div class="progress mb-2">
                        <div class="progress-bar bg-success" style="width: {{ $cashPercentage }}%"></div>
                    </div>
                    <small class="text-muted">{{ number_format($session->total_cash, 2) }} ر.س</small>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span><i class="fa fa-credit-card text-info me-1"></i>بطاقات</span>
                        <span>{{ number_format($cardPercentage, 1) }}%</span>
                    </div>
                    <div class="progress mb-2">
                        <div class="progress-bar bg-info" style="width: {{ $cardPercentage }}%"></div>
                    </div>
                    <small class="text-muted">{{ number_format($session->total_card, 2) }} ر.س</small>
                </div>

                <hr>
                <div class="text-center">
                    <strong>إجمالي المدفوعات</strong><br>
                    <span class="h5 text-primary">{{ number_format($totalPayments, 2) }} ر.س</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- الإحصائيات التفصيلية --}}
<div class="row mt-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">
                    <i class="fa fa-chart-line me-2"></i>إحصائيات المعاملات
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 border-end">
                        <h4 class="text-success mb-1">{{ $session->details->where('transaction_type', 'sale')->count() }}</h4>
                        <p class="text-muted mb-0 small">عدد المبيعات</p>
                    </div>
                    <div class="col-6">
                        <h4 class="text-danger mb-1">{{ $session->details->where('transaction_type', 'return')->count() }}</h4>
                        <p class="text-muted mb-0 small">عدد المرتجعات</p>
                    </div>
                </div>
                <hr>
                <div class="row text-center">
                    <div class="col-6 border-end">
                        <h5 class="text-info mb-1">{{ $session->total_transactions > 0 ? number_format($session->total_sales / $session->total_transactions, 2) : '0.00' }}</h5>
                        <p class="text-muted mb-0 small">متوسط قيمة المعاملة (ر.س)</p>
                    </div>
                    <div class="col-6">
                        <h5 class="text-warning mb-1">{{ $session->started_at->diffInHours($session->ended_at) > 0 ? number_format($session->total_transactions / $session->started_at->diffInHours($session->ended_at), 1) : '0' }}</h5>
                        <p class="text-muted mb-0 small">معاملات في الساعة</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">
                    <i class="fa fa-sticky-note me-2"></i>ملاحظات الإغلاق
                </h5>
            </div>
            <div class="card-body">
                @if($session->closing_notes)
                    <p class="mb-0">{{ $session->closing_notes }}</p>
                @else
                    <p class="text-muted mb-0 text-center">
                        <i class="fa fa-info-circle me-1"></i>
                        لا توجد ملاحظات
                    </p>
                @endif
                
                <hr>
                <div class="row">
                    <div class="col-6">
                        <small class="text-muted">
                            <strong>تاريخ الإغلاق:</strong><br>
                            {{ $session->ended_at->format('d/m/Y H:i:s') }}
                        </small>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">
                            <strong>مدة الجلسة:</strong><br>
                            {{ $session->started_at->diff($session->ended_at)->format('%H ساعة %I دقيقة') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- تفاصيل المعاملات --}}
<div class="row mt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">
                    <i class="fa fa-list me-2"></i>سجل المعاملات
                </h4>
            </div>
            <div class="card-body">
                @if($session->details && $session->details->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead class="table-dark">
                                <tr>
                                    <th width="8%">الوقت</th>
                                    <th width="12%">النوع</th>
                                    <th width="15%">المرجع</th>
                                    <th width="10%">المبلغ</th>
                                    <th width="10%">النقدي</th>
                                    <th width="10%">البطاقة</th>
                                    <th width="35%">الوصف</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($session->details->sortBy('transaction_time') as $detail)
                                    <tr>
                                        <td><small>{{ $detail->transaction_time->format('H:i:s') }}</small></td>
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
                                                @case('closing_balance')
                                                    <span class="badge badge-secondary">
                                                        <i class="fa fa-stop me-1"></i>ختامي
                                                    </span>
                                                    @break
                                                @case('cash_adjustment')
                                                    <span class="badge badge-warning">
                                                        <i class="fa fa-edit me-1"></i>تعديل
                                                    </span>
                                                    @break
                                                @default
                                                    <span class="badge badge-light">{{ $detail->transaction_type }}</span>
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
                                            <span class="fw-bold text-{{ in_array($detail->transaction_type, ['return', 'cash_adjustment']) && $detail->cash_amount < 0 ? 'danger' : 'success' }}">
                                                {{ number_format($detail->amount, 2) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($detail->cash_amount != 0)
                                                <span class="text-{{ $detail->cash_amount > 0 ? 'success' : 'danger' }}">
                                                    {{ number_format($detail->cash_amount, 2) }}
                                                </span>
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
                                        <td><small>{{ $detail->description ?? 'لا يوجد وصف' }}</small></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fa fa-inbox fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">لا توجد معاملات</h5>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- إجراءات نهائية --}}
<div class="row mt-3 mb-4">
    <div class="col-12 text-center">
        <a href="{{ route('pos.sessions.index') }}" class="btn btn-success btn-lg me-2">
            <i class="fa fa-plus-circle me-2"></i>بدء جلسة جديدة
        </a>
         <a href="{{ route('pos.sessions.print', $session->id) }}" 
   target="_blank" 
   class="btn btn-outline-primary me-2">
    <i class="fa fa-print me-1"></i> طباعة
</a>
        
        <a href="{{ url('/') }}" class="btn btn-outline-secondary btn-lg">
            <i class="fa fa-home me-2"></i>العودة للرئيسية
        </a>
    </div>
</div>

<style>
/* تحسينات التصميم */
.opacity-75 {
    opacity: 0.75;
}

.badge-lg {
    font-size: 0.9rem;
    padding: 0.5rem 0.75rem;
}

.alert-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.progress {
    height: 6px;
}

/* طباعة */
@media print {
    .btn, .content-header-right {
        display: none !important;
    }
    
    .alert {
        -webkit-print-color-adjust: exact;
        color-adjust: exact;
    }
    
    .card {
        border: 1px solid #000 !important;
        box-shadow: none !important;
        break-inside: avoid;
    }
    
    .badge {
        border: 1px solid #000 !important;
        -webkit-print-color-adjust: exact;
        color-adjust: exact;
    }
    
    body {
        font-size: 12px;
    }
    
    .h1, .h2, .h3, .h4, .h5, .h6 {
        margin-bottom: 0.5rem;
    }
}

/* تجاوبية */
@media (max-width: 768px) {
    .card-body .row > div {
        margin-bottom: 1rem;
    }
    
    .table-responsive {
        font-size: 0.8rem;
    }
    
    .btn-lg {
        width: 100%;
        margin-bottom: 0.5rem;
    }
}
</style>

@endsection