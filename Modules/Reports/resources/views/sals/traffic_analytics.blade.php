@extends('master')

@section('title')
    تحليل الزيارات
@stop

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<style>
    .period-selector {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
        padding: 10px;
        background: #f8f9fa;
        border-radius: 5px;
    }

    .period-selector .btn {
        min-width: 200px;
        text-align: center;
    }

    .table-container {
        overflow-x: auto;
        margin-top: 20px;
    }

    .avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .avatar-content {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 18px;
    }

    .week-header {
        font-size: 12px;
    }

    .week-number {
        font-weight: bold;
        margin-bottom: 2px;
    }

    .week-dates {
        font-size: 10px;
        color: #6c757d;
    }

    .activity-cell {
        transition: all 0.2s ease;
    }

    .activity-cell:hover {
        background-color: #f8f9fa !important;
        transform: scale(1.05);
    }

    .bg-visit-cell {
        background-color: #e7f3ff !important;
    }

    .bg-invoice-cell {
        background-color: #e8eaf6 !important;
    }

    .bg-note-cell {
        background-color: #fff9e6 !important;
    }

    .activity-icons i {
        font-size: 16px;
        cursor: pointer;
    }

    .client-row:hover {
        background-color: #f5f5f5;
    }
</style>
@endsection

@section('content')
@php
// دالة مساعدة لحساب الأنشطة
function getClientActivity($clientId, $weekNumber, $clientWeeklyStats, $employees) {
    $activities = [];
    $activityTypes = [];
    $hasActivity = false;
    $notesData = [];

    if (!isset($clientWeeklyStats[$clientId][$weekNumber])) {
        return ['activities' => [], 'types' => [], 'has' => false, 'color' => '', 'notes' => []];
    }

    $stats = $clientWeeklyStats[$clientId][$weekNumber];

    // فحص الفواتير
    if (isset($stats['invoice_count']) && $stats['invoice_count'] > 0) {
        $activities[] = [
            'icon' => 'fas fa-file-invoice',
            'title' => $stats['invoice_count'] . ' فاتورة',
            'color' => '#4e73df',
        ];
        $activityTypes[] = 'invoice';
        $hasActivity = true;
    }

    // فحص الملاحظات مع التفاصيل
    if (isset($stats['note_count']) && $stats['note_count'] > 0) {
        // إنشاء نص Tooltip يحتوي على وصف كل الملاحظات
        $notesTooltip = '';
        $notes = $stats['notes'] ?? [];
        foreach ($notes as $index => $note) {
            if ($index > 0) $notesTooltip .= ' | ';
            $notesTooltip .= ($note['description'] ?? 'لا يوجد وصف');
        }

        $activities[] = [
            'icon' => 'fas fa-sticky-note',
            'title' => $notesTooltip ?: ($stats['note_count'] . ' ملاحظة'),
            'color' => '#f6c23e',
            'type' => 'note',
            'notes' => $notes
        ];
        $activityTypes[] = 'note';
        $hasActivity = true;
        $notesData = $notes;
    }

    // فحص الزيارات
    if (isset($stats['visits']) && count($stats['visits']) > 0) {
        foreach ($stats['visits'] as $visit) {
            $color = '#e74a3b';
            $title = 'زيارة';

            if (isset($employees[$visit['employee_id']])) {
                $employee = $employees[$visit['employee_id']];
                $title = 'زيارة: ' . $employee->name;

                if ($employee->role === 'manager') {
                    $color = '#007bff';
                } elseif ($employee->role === 'employee' && $employee->employee) {
                    if ($employee->employee->Job_role_id == 1) {
                        $color = '#28a745';
                    } elseif ($employee->employee->Job_role_id == 2) {
                        $color = '#fd7e14';
                    }
                }
            }

            $activities[] = [
                'icon' => 'fas fa-shoe-prints',
                'title' => $title . ' (' . $visit['days'] . ' يوم)',
                'color' => $color,
            ];
        }
        $activityTypes[] = 'visit';
        $hasActivity = true;
    }

    // تحديد لون الخلية
    $cellColorClass = '';
    if (in_array('visit', $activityTypes)) {
        $cellColorClass = 'bg-visit-cell';
    } elseif (in_array('invoice', $activityTypes)) {
        $cellColorClass = 'bg-invoice-cell';
    } elseif (in_array('note', $activityTypes)) {
        $cellColorClass = 'bg-note-cell';
    }

    return [
        'activities' => $activities,
        'types' => $activityTypes,
        'has' => $hasActivity,
        'color' => $cellColorClass,
        'notes' => $notesData
    ];
}
@endphp

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">تحليل الزيارات</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-left">
                    <li class="breadcrumb-item"><a href="/">الرئيسية</a></li>
                    <li class="breadcrumb-item active">تحليل الزيارات</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Modal لعرض تفاصيل الملاحظات -->
<div class="modal fade" id="notesModal" tabindex="-1" role="dialog" aria-labelledby="notesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="notesModalLabel">تفاصيل الملاحظات</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="notesModalBody">
                <!-- سيتم ملء المحتوى هنا عبر JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">إغلاق</button>
            </div>
        </div>
    </div>
</div>

<div class="card-body">
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-chart-line mr-1"></i> تحليل حركة العملاء
            </h3>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-3 col-sm-6">
                    <form method="GET" action="{{ route('traffic.analysis') }}">
                        <div class="form-group">
                            <label for="year">اختر السنة:</label>
                            <select name="year" id="year" class="form-control" onchange="this.form.submit()">
                                @for($y = now()->year - 2; $y <= now()->year + 1; $y++)
                                    <option value="{{ $y }}" {{ $currentYear == $y ? 'selected' : '' }}>
                                        {{ $y }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </form>
                </div>
            </div>

            <div class="accordion" id="branches-accordion">
                @foreach ($branches as $branch)
                    <div class="card mb-3">
                        <div class="card-header" id="heading-branch-{{ $branch->id }}">
                            <h5 class="mb-0">
                                <button class="btn btn-link font-weight-bold text-right"
                                    data-toggle="collapse"
                                    data-target="#collapse-branch-{{ $branch->id }}"
                                    aria-expanded="false"
                                    aria-controls="collapse-branch-{{ $branch->id }}">
                                    <i class="fas fa-code-branch ml-2"></i> الفرع: {{ $branch->name }}
                                </button>
                            </h5>
                        </div>

                        <div id="collapse-branch-{{ $branch->id }}" class="collapse"
                            aria-labelledby="heading-branch-{{ $branch->id }}"
                            data-parent="#branches-accordion">
                            <div class="card-body">
                                <div class="accordion custom-accordion" id="groups-accordion-{{ $branch->id }}">
                                    @foreach ($branch->regionGroups as $group)
                                        @php
                                            $groupClients = $group->neighborhoods
                                                ->map(fn($neigh) => $neigh->client)
                                                ->filter()
                                                ->unique('id');

                                            $statusCounts = $groupClients->groupBy(fn($client) =>
                                                optional($client->status_client)->name ?? 'غير محدد'
                                            )->map->count();
                                        @endphp

                                        <div class="card card-outline card-info mb-2 group-section" id="group-{{ $group->id }}">
                                            <div class="card-header" id="heading-{{ $group->id }}">
                                                <h5 class="mb-0 d-flex justify-content-between align-items-center">
                                                    <button class="btn btn-link text-dark font-weight-bold w-100 text-right collapsed"
                                                        type="button"
                                                        data-toggle="collapse"
                                                        data-target="#collapse-{{ $group->id }}"
                                                        aria-expanded="false"
                                                        aria-controls="collapse-{{ $group->id }}">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <i class="fas fa-map-marker-alt mr-2"></i>
                                                                {{ $group->name }}
                                                                <span class="badge badge-primary badge-pill ml-2">
                                                                    {{ $groupClients->count() }}
                                                                </span>
                                                            </div>
                                                            <div class="status-badges">
                                                                @foreach ($statusCounts as $status => $count)
                                                                    @php
                                                                        $color = $groupClients->first(fn($client) =>
                                                                            (optional($client->status_client)->name ?? 'غير محدد') === $status
                                                                        )?->status_client?->color ?? '#6c757d';
                                                                    @endphp
                                                                    <span class="badge badge-pill ml-1"
                                                                        style="background-color: {{ $color }}; color: white;">
                                                                        {{ $status }}: {{ $count }}
                                                                    </span>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </button>
                                                </h5>
                                            </div>

                                            <div id="collapse-{{ $group->id }}" class="collapse"
                                                aria-labelledby="heading-{{ $group->id }}"
                                                data-parent="#groups-accordion-{{ $branch->id }}">
                                                <div class="card-body p-0">
                                                    @if ($groupClients->count() > 0)
                                                        <div class="table-responsive" style="overflow-x: auto; max-width: 100%;">
                                                            <table class="table table-hover table-bordered text-center mb-0 client-table"
                                                                style="white-space: nowrap;">
                                                                <thead class="thead-light">
                                                                    <tr>
                                                                        <th class="align-middle" style="min-width: 220px;">العميل</th>
                                                                        @foreach ($weeks as $week)
                                                                            <th class="week-header align-middle"
                                                                                style="min-width: 80px;">
                                                                                <div class="week-number">الأسبوع {{ $week['week_number'] }}</div>
                                                                                <div class="week-dates">
                                                                                    {{ \Carbon\Carbon::parse($week['start'])->format('d/m') }} -
                                                                                    {{ \Carbon\Carbon::parse($week['end'])->format('d/m') }}
                                                                                </div>
                                                                            </th>
                                                                        @endforeach
                                                                        <th class="align-middle">إجمالي النشاط</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach ($groupClients as $client)
                                                                        <tr class="client-row"
                                                                            data-client="{{ $client->trade_name }}"
                                                                            data-status="{{ optional($client->status_client)->name ?? 'غير محدد' }}"
                                                                            onclick="window.location.href='{{ route('clients.show', $client->id) }}'"
                                                                            style="cursor: pointer;">

                                                                            <td class="text-start align-middle">
                                                                                <div class="d-flex align-items-center">
                                                                                    <div class="avatar mr-2">
                                                                                        <span class="avatar-content"
                                                                                            style="background-color: {{ optional($client->status_client)->color ?? '#6c757d' }};">
                                                                                            {{ substr($client->trade_name, 0, 1) }}
                                                                                        </span>
                                                                                    </div>
                                                                                    <div>
                                                                                        <div class="font-weight-bold">
                                                                                            {{ $client->trade_name }}-{{ $client->code }}
                                                                                        </div>
                                                                                        <div class="client-status-badge">
                                                                                            @if ($client->status_client)
                                                                                                <span style="background-color: {{ $client->status_client->color }};
                                                                                                    color: #fff; padding: 2px 8px; font-size: 12px;
                                                                                                    border-radius: 4px; display: inline-block;">
                                                                                                    {{ $client->status_client->name }}
                                                                                                </span>
                                                                                            @else
                                                                                                <span style="background-color: #6c757d;
                                                                                                    color: #fff; padding: 2px 8px; font-size: 12px;
                                                                                                    border-radius: 4px; display: inline-block;">
                                                                                                    غير محدد
                                                                                                </span>
                                                                                            @endif
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </td>

                                                                            @php $totalActivities = 0; @endphp
                                                                            @foreach ($weeks as $week)
                                                                                @php
                                                                                    $result = getClientActivity($client->id, $week['week_number'], $clientWeeklyStats, $employees);
                                                                                    if ($result['has']) {
                                                                                        $totalActivities++;
                                                                                    }
                                                                                @endphp

                                                                                <td class="align-middle activity-cell {{ $result['color'] }} @if($result['has']) has-activity @endif"
                                                                                    data-has-activity="{{ $result['has'] ? '1' : '0' }}"
                                                                                    data-activity-types="{{ implode(',', $result['types']) }}"
                                                                                    onclick="event.stopPropagation()">
                                                                                    @if ($result['has'])
                                                                                        <div class="activity-icons d-flex justify-content-center">
                                                                                            @foreach ($result['activities'] as $activity)
                                                                                                @if(isset($activity['type']) && $activity['type'] === 'note' && !empty($activity['notes']))
                                                                                                    <a href="#" class="show-notes"
                                                                                                        data-notes="{{ htmlspecialchars(json_encode($activity['notes']), ENT_QUOTES, 'UTF-8') }}"
                                                                                                        data-client="{{ $client->trade_name }}"
                                                                                                        onclick="event.stopPropagation()">
                                                                                                        <i class="{{ $activity['icon'] }} mx-1"
                                                                                                            title="{{ $activity['title'] }}"
                                                                                                            data-toggle="tooltip"
                                                                                                            style="color: {{ $activity['color'] }}"></i>
                                                                                                    </a>
                                                                                                @else
                                                                                                    <i class="{{ $activity['icon'] }} mx-1"
                                                                                                        title="{{ $activity['title'] }}"
                                                                                                        data-toggle="tooltip"
                                                                                                        style="color: {{ $activity['color'] }}"></i>
                                                                                                @endif
                                                                                            @endforeach
                                                                                        </div>
                                                                                    @else
                                                                                        <span class="text-muted">—</span>
                                                                                    @endif
                                                                                </td>
                                                                            @endforeach

                                                                            <td class="align-middle">
                                                                                <span class="badge badge-pill @if($totalActivities > 0) badge-success @else badge-secondary @endif">
                                                                                    {{ $totalActivities }} / {{ count($weeks) }}
                                                                                </span>
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                                <tfoot>
                                                                    <tr>
                                                                        <td class="fw-bold text-center align-middle">إجمالي التحصيل</td>
                                                                        @foreach ($weeks as $week)
                                                                            @php
                                                                                $weekNumber = $week['week_number'];
                                                                                $totalCollection = 0;
                                                                                // نجمع تحصيل كل العملاء لهذا الأسبوع
                                                                                foreach ($groupClients as $client) {
                                                                                    $totalCollection += $clientWeeklyStats[$client->id][$weekNumber]['collection'] ?? 0;
                                                                                }
                                                                            @endphp
                                                                            <td class="fw-bold text-center align-middle" style="background: #f1f1f1;">
                                                                                {{ $totalCollection > 0 ? number_format($totalCollection, 2) : '—' }}
                                                                            </td>
                                                                        @endforeach
                                                                        <td class="fw-bold text-center align-middle">—</td>
                                                                    </tr>
                                                                </tfoot>
                                                            </table>
                                                        </div>
                                                    @else
                                                        <div class="alert alert-info m-3">لا يوجد عملاء في هذه المجموعة</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="card-footer">
            <div class="d-flex justify-content-between">
                <div class="small text-muted">
                    تاريخ التحديث: {{ now()->format('Y/m/d H:i') }}
                </div>
                <div>
                    <span class="badge badge-primary">السنة: {{ $currentYear }}</span>
                    <span class="badge badge-success ml-2">عملاء: {{ $totalClients ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    $(document).ready(function() {
        // تفعيل التلميحات
        $('[data-toggle="tooltip"]').tooltip({
            boundary: 'window',
            placement: 'top'
        });

        // عرض الملاحظات عند النقر على أيقونة الملاحظة
        $(document).on('click', '.show-notes', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const notesData = $(this).data('notes');
            const clientName = $(this).data('client');

            if (notesData && notesData.length > 0) {
                let modalContent = '<div class="list-group">';

                notesData.forEach(function(note, index) {
                    modalContent += `
                        <div class="list-group-item mb-2">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">ملاحظة ${index + 1}</h6>
                                <small class="text-muted">${note.created_at}</small>
                            </div>
                            <p class="mb-1"><strong>الوصف:</strong> ${note.description || 'لا يوجد'}</p>
                            <div class="row">
                                <div class="col-md-6">
                                    <small><strong>الحالة:</strong> ${note.status || 'غير محدد'}</small>
                                </div>
                                <div class="col-md-6">
                                    <small><strong>العملية:</strong> ${note.process || 'غير محدد'}</small>
                                </div>
                            </div>
                            <div class="row mt-1">
                                <div class="col-md-6">
                                    <small><strong>التاريخ:</strong> ${note.date || 'غير محدد'}</small>
                                </div>
                                <div class="col-md-6">
                                    <small><strong>الوقت:</strong> ${note.time || 'غير محدد'}</small>
                                </div>
                            </div>
                        </div>
                    `;
                });

                modalContent += '</div>';

                $('#notesModalLabel').text(`ملاحظات العميل: ${clientName}`);
                $('#notesModalBody').html(modalContent);
                $('#notesModal').modal('show');
            } else {
                toastr.info('لا توجد ملاحظات لعرضها');
            }
        });

        // بحث بسيط
        $('#client-search').on('input', function() {
            var searchTerm = $(this).val().toLowerCase();

            $('.client-row').each(function() {
                var clientName = $(this).data('client').toLowerCase();
                $(this).toggle(clientName.includes(searchTerm));
            });
        });
    });
</script>
@endsection