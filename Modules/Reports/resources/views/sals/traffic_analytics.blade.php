@extends('master')

@section('title')
    تحليل الزيارات
@stop

@section('content')
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
</style>

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
    <div class="modal fade" id="notesModal" tabindex="-1" role="dialog" aria-labelledby="notesModalLabel"
        aria-hidden="true">
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
                </div>
            </div>

            <form method="GET" action="{{ route('traffic.analysis') }}">
                <select name="year" onchange="this.form.submit()">
                    @for($y = now()->year - 2; $y <= now()->year + 1; $y++)
                        <option value="{{ $y }}" {{ $currentYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </form>

            <div id="weeks-container" data-current-weeks="{{ json_encode($weeks) }}"></div>

            <div class="accordion" id="branches-accordion">
                @foreach ($branches as $branch)
                    <div class="card mb-3">
                        <div class="card-header" id="heading-branch-{{ $branch->id }}">
                            <h5 class="mb-0">
                                <button class="btn btn-link font-weight-bold text-right" data-toggle="collapse"
                                    data-target="#collapse-branch-{{ $branch->id }}" aria-expanded="false"
                                    aria-controls="collapse-branch-{{ $branch->id }}">
                                    <i class="fas fa-code-branch ml-2"></i> الفرع: {{ $branch->name }}
                                </button>
                            </h5>
                        </div>

                        <div id="collapse-branch-{{ $branch->id }}" class="collapse"
                            aria-labelledby="heading-branch-{{ $branch->id }}" data-parent="#branches-accordion">
                            <div class="card-body">
                                <div class="accordion custom-accordion" id="groups-accordion-{{ $branch->id }}">
                                    @foreach ($branch->regionGroups as $group)
                                        @php
                                            $clients = $group->neighborhoods
                                                ->flatMap(fn($neigh) => $neigh->client ? [$neigh->client] : [])
                                                ->filter()
                                                ->unique('id');

                                            $statusCounts = $clients->groupBy(fn($client) =>
                                                optional($client->status_client)->name ?? 'غير محدد'
                                            )->map->count();
                                        @endphp

                                        <div class="card card-outline card-info mb-2 group-section" id="group-{{ $group->id }}">
                                            <div class="card-header" id="heading-{{ $group->id }}">
                                                <h5 class="mb-0 d-flex justify-content-between align-items-center">
                                                    <button class="btn btn-link text-dark font-weight-bold w-100 text-right collapsed"
                                                        type="button" data-toggle="collapse"
                                                        data-target="#collapse-{{ $group->id }}" aria-expanded="false"
                                                        aria-controls="collapse-{{ $group->id }}">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <i class="fas fa-map-marker-alt mr-2"></i>
                                                                {{ $group->name }}
                                                                <span class="badge badge-primary badge-pill ml-2">
                                                                    {{ $clients->count() }}
                                                                </span>
                                                            </div>
                                                            <div class="status-badges">
                                                                @foreach ($statusCounts as $status => $count)
                                                                    @php
                                                                        $color = $clients->first(fn($client) =>
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
                                                    @if ($clients->count() > 0)
                                                        <div class="table-responsive" style="overflow-x: auto; max-width: 100%;">
                                                            <table
                                                                class="table table-hover table-bordered text-center mb-0 client-table"
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
    @foreach ($clients as $client)
        <tr class="client-row" data-client="{{ $client->trade_name }}"
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
                                <span
                                    style="background-color: {{ $client->status_client->color }};
                                    color: #fff; padding: 2px 8px; font-size: 12px;
                                    border-radius: 4px; display: inline-block;">
                                    {{ $client->status_client->name }}
                                </span>
                            @else
                                <span
                                    style="background-color: #6c757d;
                                    color: #fff; padding: 2px 8px; font-size: 12px;
                                    border-radius: 4px; display: inline-block;">
                                    غير محدد
                                </span>
                            @endif
                        </div>
                        <div class="small text-muted">
                            {{ optional($client->neighborhood)->name }}
                        </div>
                    </div>
                </div>
            </td>

            @php $totalActivities = 0; @endphp
            @foreach ($weeks as $week)
                @php
                    $activities = [];
                    $hasActivity = false;
                    $activityTypes = [];
                    $notesData = [];

                    // فحص الفواتير
                    $weekInvoices = $client->invoices->whereBetween('created_at', [$week['start'], $week['end']]);
                    if ($weekInvoices->count()) {
                        foreach ($weekInvoices as $invoice) {
                            $activities[] = [
                                'icon' => 'fas fa-file-invoice',
                                'title' => 'فاتورة #' . $invoice->id . ' بتاريخ ' . $invoice->created_at->format('Y-m-d'),
                                'color' => '#4e73df',
                            ];
                        }
                        $activityTypes[] = 'invoice';
                        $hasActivity = true;
                    }

                    // فحص المدفوعات
                    $weekPayments = $client->payments->whereBetween('created_at', [$week['start'], $week['end']]);
                    if ($weekPayments->count()) {
                        foreach ($weekPayments as $payment) {
                            $activities[] = [
                                'icon' => 'fas fa-money-bill-wave',
                                'title' => 'دفعة بـ ' . number_format($payment->amount, 2) . ' بتاريخ ' . $payment->created_at->format('Y-m-d') . 'لفاتورة رقم ' . $payment->invoice_id,
                                'color' => '#1cc88a',
                            ];
                        }
                        $activityTypes[] = 'payment';
                        $hasActivity = true;
                    }

                    // فحص الملاحظات
                    $weekNotes = $client->appointmentNotes->whereBetween(
                        'created_at',
                        [$week['start'], $week['end']],
                    );

                    if ($weekNotes->count()) {
                        foreach ($weekNotes as $note) {
                            $noteDateTime = $note->created_at->format('Y-m-d H:i');
                            $activities[] = [
                                'icon' => 'fas fa-sticky-note',
                                'title' => 'ملاحظة: ' . $note->content . ' بتاريخ ' . $noteDateTime,
                                'color' => '#f6c23e',
                                'note_details' => $note,
                            ];
                        }

                        $activityTypes[] = 'note';
                        $hasActivity = true;

                        // تمرير الملاحظات بالتفصيل للـ Tooltip أو Modal مثلاً
                        $notesData = $weekNotes->map(function ($note) {
                            return [
                                'status' => $note->status,
                                'process' => $note->process,
                                'time' => $note->time,
                                'date' => $note->date,
                                'description' => $note->description,
                                'created_at' => $note->created_at->format('Y-m-d H:i'),
                            ];
                        })->toArray();
                    }

                    $weekVisits = $client->visits->whereBetween('created_at', [$week['start'], $week['end']]);

                    if ($weekVisits->count()) {
                        // تجميع الزيارات حسب السنة-الشهر-اليوم-الساعة، ثم أخذ الأخيرة
                        $filteredVisits = $weekVisits
                            ->sortBy('created_at') // تأكد أن الترتيب حسب الزمن
                            ->groupBy(function ($visit) {
                                return $visit->created_at->format('Y-m-d H'); // تجميع حسب التاريخ + الساعة
                            })
                            ->map(function ($group) {
                                return $group->last(); // نأخذ آخر زيارة لكل مجموعة
                            });

                        foreach ($filteredVisits as $visit) {
                            $color = '#e74a3b'; // الافتراضي
                            $visitorName = optional($visit->employee)->name ?? 'غير معروف';
                            $visitTime = $visit->created_at->format('Y-m-d H:i');

                            if ($visit->employee && $visit->employee->role === 'manager') {
                                $color = '#007bff'; // مدير
                            } elseif ($visit->employee && $visit->employee->role === 'employee') {
                                $employeeModel = $visit->employee->employee;
                                if ($employeeModel) {
                                    if ($employeeModel->Job_role_id == 1) {
                                        $color = '#28a745'; // مندوب
                                    } elseif ($employeeModel->Job_role_id == 2) {
                                        $color = '#fd7e14'; // مشرف
                                    }
                                }
                            }

                            $activities[] = [
                                'icon' => 'fas fa-shoe-prints',
                                'title' => 'زيارة بواسطة: ' . $visitorName . ' في ' . $visitTime,
                                'color' => $color,
                            ];
                            $activityTypes[] = 'visit';
                            $hasActivity = true;
                        }
                    }

                    // فحص سندات القبض
                    $weekReceipts = $client->accounts
                        ->flatMap(function ($account) use ($week) {
                            return $account->receipts->whereBetween('created_at', [$week['start'], $week['end']]);
                        });

                    if ($weekReceipts->count()) {
                        foreach ($weekReceipts as $receipt) {
                            $activities[] = [
                                'icon' => 'fas fa-hand-holding-usd',
                                'title' => 'سند بـ ' . number_format($receipt->amount, 2) . ' بتاريخ ' . $receipt->created_at->format('Y-m-d'),
                                'color' => '#36b9cc',
                            ];
                        }
                        $activityTypes[] = 'receipt';
                        $hasActivity = true;
                    }

                    // تحديد لون الخلية بناء على نوع النشاط
                    $cellColorClass = '';
                    if (in_array('visit', $activityTypes)) {
                        $cellColorClass = 'bg-visit-cell';
                    } elseif (in_array('invoice', $activityTypes)) {
                        $cellColorClass = 'bg-invoice-cell';
                    } elseif (in_array('payment', $activityTypes)) {
                        $cellColorClass = 'bg-payment-cell';
                    } elseif (in_array('receipt', $activityTypes)) {
                        $cellColorClass = 'bg-receipt-cell';
                    } elseif (in_array('note', $activityTypes)) {
                        $cellColorClass = 'bg-note-cell';
                    }

                    if ($hasActivity) {
                        $totalActivities++;
                    }
                @endphp
                <td class="align-middle activity-cell {{ $cellColorClass }} @if ($hasActivity) has-activity @endif"
                    data-has-activity="{{ $hasActivity ? '1' : '0' }}"
                    data-activity-types="{{ implode(',', $activityTypes) }}"
                    data-notes="{{ htmlspecialchars(json_encode($notesData), ENT_QUOTES, 'UTF-8') }}">
                    @if ($hasActivity)
                        <div class="activity-icons d-flex justify-content-center">
                            @foreach ($activities as $activity)
                                @if ($activity['title'] === 'ملاحظة' && isset($activity['notes']))
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
                <span class="badge badge-pill @if ($totalActivities > 0) badge-success @else badge-secondary @endif">
                    {{ $totalActivities }} / {{ count($weeks) }}
                </span>
            </td>
        </tr>
    @endforeach





</tbody>
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
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
            <div class="card-footer">
                <div class="d-flex justify-content-between">
                    <div class="small text-muted">
                        تاريخ التحديث: {{ now()->format('Y/m/d H:i') }}
                    </div>
                    <div>
                        <span class="badge badge-primary">مجموعات: </span><Fieldset:d></Fieldset:d>
                        <span class="badge badge-success ml-2">عملاء: {{$totalClients ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>
    @endsection
    @section('css')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
        <link rel="stylesheet" href="{{ asset('assets/css/noteReport.css') }}">

    @endsection

    @section('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
        <script src="{{ asset('assets/js/noteReport.js') }}"></script>

    @endsection
<script>
function scrollTable(direction) {
    const tableWrapper = document.querySelector('.table-responsive');
    const scrollAmount = 300;
    if (direction === 'right') {
        tableWrapper.scrollBy({ left: scrollAmount, behavior: 'smooth' });
    } else {
        tableWrapper.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
    }
}
</script>
<script>
    // بحث بسيط
$('#client-search').on('input', function() {
    var searchTerm = $(this).val().toLowerCase();

    $('.client-row').each(function() {
        var clientName = $(this).data('client').toLowerCase();
        $(this).toggle(clientName.includes(searchTerm));
    });
});
</script>
