@extends('master')

@section('title', 'تحليل حركة المرور - محسن')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">📊 تحليل حركة المرور (محسن)</h1>
                </div>
                <div class="col-sm-6">
                    <div class="float-sm-right">
                        <button id="clearVisitsBtn" class="btn btn-danger btn-sm">
                            <i class="fas fa-trash"></i> مسح بيانات الزيارات القديمة
                        </button>
                        <button id="refreshDataBtn" class="btn btn-primary btn-sm">
                            <i class="fas fa-sync"></i> تحديث البيانات
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            @if(isset($error))
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> {{ $error }}
                </div>
            @endif

            <!-- معلومات الأداء -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>تحسينات الأداء:</strong>
                        • تم إزالة الاعتماد على جدول visits القديم
                        • استخدام Cache لتسريع التحميل
                        • عرض آخر 8 أسابيع فقط
                        • تحسين استعلامات قاعدة البيانات
                    </div>
                </div>
            </div>

            @if(!empty($branches))
                @foreach($branches as $branchData)
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-building"></i>
                                {{ $branchData['branch']->name }}
                                <span class="badge badge-light ml-2">
                                    {{ $branchData['status_counts']['total'] }} عميل
                                </span>
                            </h5>
                        </div>

                        <div class="card-body">
                            <!-- إحصائيات سريعة -->
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <div class="info-box bg-success">
                                        <span class="info-box-icon"><i class="fas fa-check"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">نشط</span>
                                            <span class="info-box-number">{{ $branchData['status_counts']['active'] }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-box bg-warning">
                                        <span class="info-box-icon"><i class="fas fa-pause"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">غير نشط</span>
                                            <span class="info-box-number">{{ $branchData['status_counts']['inactive'] }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-box bg-info">
                                        <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">قيد المراجعة</span>
                                            <span class="info-box-number">{{ $branchData['status_counts']['pending'] }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-box bg-secondary">
                                        <span class="info-box-icon"><i class="fas fa-users"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">الإجمالي</span>
                                            <span class="info-box-number">{{ $branchData['status_counts']['total'] }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- جدول العملاء المحسن -->
                            @if(!empty($branchData['clients']))
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>العميل</th>
                                                <th>الحالة</th>
                                                @foreach($weeks as $week)
                                                    <th class="text-center">{{ $week['label'] }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($branchData['clients'] as $client)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $client->trade_name }}</strong>
                                                    </td>
                                                    <td>
                                                        @if($client->status_client)
                                                            <span class="badge badge-{{ $client->status_id == 1 ? 'success' : ($client->status_id == 2 ? 'warning' : 'secondary') }}">
                                                                {{ $client->status_client->name }}
                                                            </span>
                                                        @else
                                                            <span class="badge badge-secondary">غير محدد</span>
                                                        @endif
                                                    </td>
                                                    @foreach($weeks as $week)
                                                        <td class="text-center">
                                                            @php
                                                                $stats = $clientWeeklyStats[$client->id][$week['week_number']] ?? [
                                                                    'visits' => 0,
                                                                    'payments' => 0,
                                                                    'receipts' => 0,
                                                                    'notes' => 0
                                                                ];
                                                                $hasActivity = $stats['visits'] > 0 || $stats['payments'] > 0 || $stats['receipts'] > 0 || $stats['notes'] > 0;
                                                            @endphp

                                                            @if($hasActivity)
                                                                <div class="activity-cell bg-light p-2 rounded">
                                                                    @if($stats['visits'] > 0)
                                                                        <div class="text-primary">
                                                                            <i class="fas fa-walking"></i> {{ $stats['visits'] }}
                                                                        </div>
                                                                    @endif
                                                                    @if($stats['payments'] > 0)
                                                                        <div class="text-success">
                                                                            <i class="fas fa-money-bill"></i> {{ number_format($stats['payments']) }}
                                                                        </div>
                                                                    @endif
                                                                    @if($stats['receipts'] > 0)
                                                                        <div class="text-info">
                                                                            <i class="fas fa-receipt"></i> {{ number_format($stats['receipts']) }}
                                                                        </div>
                                                                    @endif
                                                                    @if($stats['notes'] > 0)
                                                                        <div class="text-warning">
                                                                            <i class="fas fa-sticky-note"></i> {{ $stats['notes'] }}
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    لا توجد عملاء في هذا الفرع
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            @else
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    لا توجد بيانات للعرض
                </div>
            @endif
        </div>
    </section>
</div>

<style>
.activity-cell {
    min-height: 60px;
    font-size: 0.85em;
}
.activity-cell div {
    margin-bottom: 2px;
}
.info-box {
    margin-bottom: 10px;
}
.table th {
    font-size: 0.9em;
    padding: 8px;
}
.table td {
    padding: 8px;
    vertical-align: middle;
}
</style>

<script>
$(document).ready(function() {
    // مسح بيانات الزيارات
    $('#clearVisitsBtn').click(function() {
        if (confirm('هل أنت متأكد من حذف جميع بيانات الزيارات؟ هذا الإجراء لا يمكن التراجع عنه!')) {
            $.ajax({
                url: '{{ route("visits.clearData") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        alert('تم حذف ' + response.deleted_count + ' سجل بنجاح');
                        location.reload();
                    } else {
                        alert('حدث خطأ: ' + response.message);
                    }
                },
                error: function() {
                    alert('حدث خطأ في الاتصال');
                }
            });
        }
    });

    // تحديث البيانات
    $('#refreshDataBtn').click(function() {
        // مسح الـ cache وإعادة التحميل
        $.ajax({
            url: '{{ route("visits.clearCache") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function() {
                location.reload();
            }
        });
    });
});
</script>
@endsection
