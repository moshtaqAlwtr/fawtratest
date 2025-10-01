@extends('master')

@section('title')
الجلسات
@stop

@section('content')

    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">الجلسات</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('/') }}">الرئيسية</a></li>
                            <li class="breadcrumb-item active">عرض</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">

        @include('layouts.alerts.success')
        @include('layouts.alerts.error')

        <!-- بطاقة البحث -->
        <div class="card">
            <div class="card-content">
                <div class="card-body">
                    <div class="card-title">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div>بحث</div>
                            <div>
                                <a href="{{ route('pos.sessions.create') }}" class="btn btn-outline-success">
                                    <i class="fa fa-plus me-2"></i>بدء الجلسة
                                </a>
                            </div>
                        </div>
                    </div>
                    <form method="GET" action="{{ route('pos.sessions.index') }}" id="searchForm">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="device_id" class="form-label">جهاز</label>
                                <select id="device_id" name="device_id" class="form-control">
                                    <option value="">أي جهاز</option>
                                    @foreach($devices ?? [] as $device)
                                        <option value="{{ $device->id }}" {{ request('device_id') == $device->id ? 'selected' : '' }}>
                                            {{ $device->device_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="shift_id" class="form-label">وردة</label>
                                <select id="shift_id" name="shift_id" class="form-control">
                                    <option value="">أي وردة</option>
                                    @foreach($shifts ?? [] as $shift)
                                        <option value="{{ $shift->id }}" {{ request('shift_id') == $shift->id ? 'selected' : '' }}>
                                            {{ $shift->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="status" class="form-label">الحالة</label>
                                <select id="status" name="status" class="form-control">
                                    <option value="">أي حالة</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشطة</option>
                                    <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>مغلقة</option>
                                    <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>معلقة</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="search" class="form-label">رقم الجلسة</label>
                                <input type="text" id="search" name="search" class="form-control" 
                                       value="{{ request('search') }}" placeholder="البحث برقم الجلسة...">
                            </div>
                        </div>
                        <div class="d-flex justify-content-start">
                            <button type="submit" class="btn btn-primary me-2">بحث</button>
                            <button type="button" class="btn btn-secondary" onclick="clearFilters()">إلغاء الفلتر</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- بطاقة الجدول -->
        <div class="card mt-4">
            <div class="card-body">
                @if($sessions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered text-center">
                            <thead>
                                <tr>
                                    <th>رقم الجلسة</th>
                                    <th>الجلسة/موظف الخزنة</th>
                                    <th>المبيعات</th>
                                    <th>فتح/إغلاق</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sessions as $session)
                                    <tr>
                                        <td>{{ $session->session_number }}</td>
                                        <td>
                                            <div>
                                                <strong>{{ $session->shift->name ?? 'غير محدد' }}</strong><br>
                                                <small class="text-muted">{{ $session->user->name }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <strong>{{ number_format($session->total_sales, 2) }} ر.س</strong><br>
                                            <small class="text-muted">{{ $session->total_transactions }} معاملة</small>
                                        </td>
                                        <td>
                                            <div>
                                                <strong class="text-success">{{ $session->started_at->format('H:i d/m/Y') }}</strong><br>
                                                @if($session->ended_at)
                                                    <small class="text-danger">{{ $session->ended_at->format('H:i d/m/Y') }}</small>
                                                @else
                                                    <small class="text-muted">مفتوحة</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @switch($session->status)
                                                @case('active')
                                                    <span class="badge bg-danger">مفتوحة</span>
                                                    @break
                                                @case('closed')
                                                    <span class="badge bg-secondary">مغلقة</span>
                                                    @break
                                                @case('suspended')
                                                    <span class="badge bg-warning">معلقة</span>
                                                    @break
                                                @default
                                                    <span class="badge bg-light text-dark">{{ $session->status }}</span>
                                            @endswitch
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <div class="dropdown">
                                                    <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1 btn-sm" 
                                                            type="button" id="dropdownMenuButton{{ $session->id }}" 
                                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    </button>
                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $session->id }}">
                                                        @if($session->status == 'active')
                                                            <li>
                                                                <a class="dropdown-item" href="{{ route('pos.sessions.show', $session->id) }}">
                                                                    <i class="fa fa-eye me-2 text-primary"></i>عرض الجلسة
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item" href="{{ url('/pos') }}">
                                                                    <i class="fa fa-shopping-cart me-2 text-success"></i>نقطة البيع
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item text-warning" href="#" onclick="suspendSession({{ $session->id }})">
                                                                    <i class="fa fa-pause me-2"></i>تعليق الجلسة
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item text-danger" href="#" onclick="closeSessionConfirm({{ $session->id }})">
                                                                    <i class="fa fa-stop me-2"></i>إغلاق الجلسة
                                                                </a>
                                                            </li>
                                                        @elseif($session->status == 'closed')
                                                            <li>
                                                                <a class="dropdown-item" href="{{ route('pos.sessions.summary', $session->id) }}">
                                                                    <i class="fa fa-file-alt me-2 text-primary"></i>عرض الملخص
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item" href="{{ route('pos.sessions.print', $session->id) }}" target="_blank">
                                                                    <i class="fa fa-print me-2 text-info"></i>طباعة
                                                                </a>
                                                            </li>
                                                        @elseif($session->status == 'suspended')
                                                            <li>
                                                                <a class="dropdown-item text-success" href="#" onclick="resumeSession({{ $session->id }})">
                                                                    <i class="fa fa-play me-2"></i>استكمال الجلسة
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item text-danger" href="#" onclick="closeSessionConfirm({{ $session->id }})">
                                                                    <i class="fa fa-stop me-2"></i>إغلاق الجلسة
                                                                </a>
                                                            </li>
                                                        @endif
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <a class="dropdown-item" href="#" onclick="viewSessionDetails({{ $session->id }})">
                                                                <i class="fa fa-info-circle me-2 text-info"></i>التفاصيل
                                                            </a>
                                                        </li>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if($sessions->hasPages())
                        <div class="d-flex justify-content-center mt-3">
                            {{ $sessions->appends(request()->query())->links() }}
                        </div>
                    @endif
                @else
                    {{-- Empty State --}}
                    <div class="text-center py-5">
                        <i class="fa fa-inbox fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">لا توجد جلسات</h5>
                        <p class="text-muted">لم يتم العثور على أي جلسات تطابق معايير البحث</p>
                        <div class="mt-3">
                            <button type="button" class="btn btn-secondary me-2" onclick="clearFilters()">
                                <i class="fa fa-refresh me-2"></i>مسح الفلاتر
                            </button>
                            <a href="{{ route('pos.sessions.create') }}" class="btn btn-success">
                                <i class="fa fa-plus me-2"></i>إنشاء جلسة جديدة
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Statistics Cards --}}
        @if(isset($stats) && $sessions->count() > 0)
            <div class="row mt-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <h4>{{ $stats['total_sessions'] ?? 0 }}</h4>
                            <p class="mb-0">إجمالي الجلسات</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h4>{{ number_format($stats['total_sales'] ?? 0, 0) }}</h4>
                            <p class="mb-0">إجمالي المبيعات (ر.س)</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <h4>{{ $stats['active_sessions'] ?? 0 }}</h4>
                            <p class="mb-0">الجلسات النشطة</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body text-center">
                            <h4>{{ number_format($stats['avg_sales'] ?? 0, 0) }}</h4>
                            <p class="mb-0">متوسط المبيعات (ر.س)</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

{{-- Modal للتفاصيل --}}
<div class="modal fade" id="sessionDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تفاصيل الجلسة</h5>
                <button type="button" class="btn-close" data-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="sessionDetailsContent">
                <div class="text-center">
                    <i class="fa fa-spinner fa-spin fa-2x"></i>
                    <p>جاري التحميل...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function clearFilters() {
    window.location.href = '{{ route('pos.sessions.index') }}';
}

function suspendSession(sessionId) {
    if (confirm('هل أنت متأكد من تعليق هذه الجلسة؟')) {
        fetch(`/pos/sessions/${sessionId}/suspend`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'حدث خطأ أثناء تعليق الجلسة');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ أثناء تعليق الجلسة');
        });
    }
}

function resumeSession(sessionId) {
    if (confirm('هل أنت متأكد من استكمال هذه الجلسة؟')) {
        fetch(`/pos/sessions/${sessionId}/resume`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    location.reload();
                }
            } else {
                alert(data.message || 'حدث خطأ أثناء استكمال الجلسة');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ أثناء استكمال الجلسة');
        });
    }
}

function closeSessionConfirm(sessionId) {
    if (confirm('هل أنت متأكد من إغلاق هذه الجلسة؟\nسيتم توجيهك لصفحة إغلاق الجلسة.')) {
        window.location.href = `/pos/sessions/${sessionId}`;
    }
}

function viewSessionDetails(sessionId) {
    $('#sessionDetailsModal').modal('show');
    
    fetch(`/POS/session/${sessionId}/details`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const session = data.session;
                const content = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6>معلومات أساسية</h6>
                            <ul class="list-unstyled">
                                <li><strong>رقم الجلسة:</strong> ${session.session_number}</li>
                                <li><strong>الكاشير:</strong> ${session.user_name}</li>
                                <li><strong>الجهاز:</strong> ${session.device_name}</li>
                                <li><strong>الوردية:</strong> ${session.shift_name}</li>
                                <li><strong>الحالة:</strong> ${session.status_text}</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>الأوقات</h6>
                            <ul class="list-unstyled">
                                <li><strong>بداية الجلسة:</strong> ${session.started_at}</li>
                                <li><strong>نهاية الجلسة:</strong> ${session.ended_at || 'لا زالت نشطة'}</li>
                                <li><strong>المدة:</strong> ${session.duration}</li>
                                <li><strong>آخر معاملة:</strong> ${session.last_transaction || 'لا توجد معاملات'}</li>
                            </ul>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <h6>الإحصائيات المالية</h6>
                            <ul class="list-unstyled">
                                <li><strong>الرصيد الافتتاحي:</strong> ${session.opening_balance} ر.س</li>
                                <li><strong>إجمالي المبيعات:</strong> ${session.total_sales} ر.س</li>
                                <li><strong>النقدي:</strong> ${session.total_cash} ر.س</li>
                                <li><strong>البطاقات:</strong> ${session.total_card} ر.س</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>إحصائيات أخرى</h6>
                            <ul class="list-unstyled">
                                <li><strong>عدد المعاملات:</strong> ${session.total_transactions}</li>
                                <li><strong>عدد تفاصيل المعاملات:</strong> ${session.transactions_count}</li>
                                ${session.closing_balance ? `<li><strong>الرصيد المتوقع:</strong> ${session.closing_balance} ر.س</li>` : ''}
                                ${session.actual_closing_balance ? `<li><strong>الرصيد الفعلي:</strong> ${session.actual_closing_balance} ر.س</li>` : ''}
                                ${session.difference ? `<li><strong>الفرق:</strong> ${session.difference} ر.س</li>` : ''}
                            </ul>
                        </div>
                    </div>
                    ${session.closing_notes ? `
                    <hr>
                    <h6>ملاحظات الإغلاق</h6>
                    <p>${session.closing_notes}</p>
                    ` : ''}
                `;
                document.getElementById('sessionDetailsContent').innerHTML = content;
            } else {
                document.getElementById('sessionDetailsContent').innerHTML = 
                    '<div class="alert alert-danger">حدث خطأ أثناء جلب التفاصيل</div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('sessionDetailsContent').innerHTML = 
                '<div class="alert alert-danger">حدث خطأ أثناء جلب التفاصيل</div>';
        });
}

// تطبيق الفلاتر تلقائياً عند التغيير
document.addEventListener('DOMContentLoaded', function() {
    const filters = ['device_id', 'shift_id', 'status'];
    filters.forEach(filterId => {
        document.getElementById(filterId).addEventListener('change', function() {
            document.getElementById('searchForm').submit();
        });
    });
});
</script>

@endsection