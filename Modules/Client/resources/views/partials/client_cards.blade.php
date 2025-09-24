<!-- تحديث ملف: resources/views/client/partials/client_cards.blade.php -->
@if (isset($clients) && $clients->count() > 0)
    <div class="row">
        @foreach ($clients as $client)
            @php
                $clientData = $clientsData[$client->id] ?? null;
                $due = $clientDueBalances[$client->id] ?? 0;
                $totalSales = $clientTotalSales[$client->id] ?? 0;
                $currentMonth = now()->format('m');
                $monthlyGroup = $clientData['monthly_groups'][$currentMonth]['group'] ?? ($clientData['group'] ?? 'D');
                $monthlyGroupClass =
                    $clientData['monthly_groups'][$currentMonth]['group_class'] ??
                    ($clientData['group_class'] ?? 'secondary');
            @endphp

            <div class="col-md-6 my-3 client-card" data-client-id="{{ $client->id }}">
                <div class="card shadow-sm border border-1 rounded-3 h-100">
                    <!-- مؤشر التحميل -->
                    <div class="card-loading" style="display: none;">
                        <div class="text-center py-4">
                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                <span class="visually-hidden">جاري التحميل...</span>
                            </div>
                        </div>
                    </div>

                    <!-- محتوى الكارد الأساسي -->
                    <div class="card-content">
                        <!-- Card Header -->
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <!-- حالة العميل -->
                            @php
                                $lastNote = $client
                                    ->appointmentNotes()
                                    ->where('employee_id', auth()->id())
                                    ->where('process', 'إبلاغ المشرف')
                                    ->whereNotNull('employee_view_status')
                                    ->latest()
                                    ->first();
                                $statusToShow = $client->status_client;
                                if (
                                    auth()->user()->role === 'employee' &&
                                    $lastNote &&
                                    $lastNote->employee_id == auth()->id()
                                ) {
                                    $statusToShow = $statuses->find($lastNote->employee_view_status);
                                }
                            @endphp

                            <div>
                                @if ($statusToShow)
                                    <span class="badge rounded-pill"
                                        style="background-color: {{ $statusToShow->color }}; font-size: 11px;">
                                        <i class="fas fa-circle me-1"></i>
                                        {{ $statusToShow->name }}
                                    </span>
                                @else
                                    <span class="badge rounded-pill bg-secondary" style="font-size: 11px;">
                                        <i class="fas fa-question-circle me-1"></i>
                                        غير محدد
                                    </span>
                                @endif
                            </div>

                            <!-- أزرار الإجراءات -->
                            <div class="d-flex align-items-center gap-1">
                                <!-- زر إخفاء من الخريطة -->
                                <button class="btn btn-sm btn-warning hide-from-map-btn"
                                    data-client-id="{{ $client->id }}"
                                    data-client-name="{{ $client->trade_name }}"
                                    title="إخفاء من الخريطة لمدة 24 ساعة"
                                    style="font-size: 10px; padding: 4px 8px;">
                                    <i class="fas fa-eye-slash me-1"></i>
                                    إخفاء
                                </button>

                                <!-- Dropdown الحالي -->
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary" type="button"
                                        id="clientActionsDropdown{{ $client->id }}" data-bs-toggle="dropdown"
                                        aria-expanded="false" style="font-size: 11px;">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="clientActionsDropdown{{ $client->id }}">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('clients.show', $client->id) }}">
                                                <i class="far fa-eye me-1"></i> عرض
                                            </a>
                                        </li>
                                        @if (auth()->user()->hasPermissionTo('Edit_Client'))
                                            <li>
                                                <a class="dropdown-item" href="{{ route('clients.edit', $client->id) }}">
                                                    <i class="fas fa-edit me-1"></i> تعديل
                                                </a>
                                            </li>
                                        @endif
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li>
                                            <a class="dropdown-item text-warning hide-from-map-link" href="#"
                                                data-client-id="{{ $client->id }}"
                                                data-client-name="{{ $client->trade_name }}">
                                                <i class="fas fa-eye-slash me-1"></i> إخفاء من الخريطة (24 ساعة)
                                            </a>
                                        </li>
                                        @if (auth()->user()->hasPermissionTo('Delete_Client'))
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <li>
                                                <a class="dropdown-item text-danger"
                                                    href="{{ route('clients.destroy', $client->id) }}">
                                                    <i class="fas fa-trash-alt me-1"></i> حذف
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Client Info -->
                        <div class="row row-cols-2 g-2 mb-2">
                            <!-- Column 1 -->
                            <div class="col">
                                <h6 class="client-name text-primary mb-2" style="font-size: 15px;">
                                    <i class="fas fa-store me-1"></i>
                                    {{ $client->trade_name }}
                                </h6>

                                <div class="mb-1">
                                    <small><i class="fas fa-phone text-secondary me-1"></i>
                                        {{ $client->phone ?? '-' }}</small>
                                </div>
                                <div class="mb-1">
                                    <small><i class="fas fa-user text-secondary me-1"></i>
                                        {{ $client->frist_name ?? '-' }}</small>
                                </div>
                                <div class="mb-1">
                                    <small>
                                        <i class="fas fa-map-marker-alt text-secondary me-1"></i>
                                        <a href="https://www.google.com/maps/search/?api=1&query={{ $client->locations->latitude??"" }},{{ $client->locations->longitude??"" }}">
                                            عرض الموقع
                                        </a>
                                    </small>
                                    @php
                                        $distanceInfo = $clientDistances[$client->id] ?? null;
                                        $distanceColor = 'text-secondary';
                                        $distanceText = '--';
                                        $distanceIcon = 'fas fa-route';

                                        if ($distanceInfo && isset($distanceInfo['distance'])) {
                                            if ($distanceInfo['distance'] === null) {
                                                $distanceText = $distanceInfo['message'] ?? 'غير متاح';
                                            } else {
                                                $distanceText = number_format($distanceInfo['distance'], 2) . ' كم';

                                                if ($distanceInfo['within_range']) {
                                                    $distanceColor = 'text-success';
                                                    $distanceIcon = 'fas fa-check-circle';
                                                } else {
                                                    $distanceColor = 'text-danger';
                                                    $distanceIcon = 'fas fa-route';
                                                }
                                            }
                                        }
                                    @endphp

                                    <div class="mb-1">
                                        <small>
                                            <i class="{{ $distanceIcon }} {{ $distanceColor }} me-1"></i>
                                            <span class="{{ $distanceColor }}">
                                                {{ $distanceText }}
                                            </span>
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <div class="col">
                                <div class="mb-1">
                                    <h7 class="client-name mb-2">
                                        {{ $client->code }}
                                    </h7>
                                </div>

                                <div class="mb-1">
                                    <small><i class="fas fa-tags text-secondary me-1"></i>
                                        {{ optional($client->categoriesClient)->name }}
                                    </small>
                                </div>
                                <div class="mb-1">
                                    <small><i class="fas fa-code-branch text-secondary me-1"></i>
                                        {{ $client->Neighborhood->Region->name ?? '-' }}</small>
                                </div>
                                <div class="mb-1">
                                    <small><i class="fas fa-code-branch text-secondary me-1"></i>
                                        {{ $client->branch->name ?? '-' }}</small>
                                </div>
                            </div>
                        </div>

                        <!-- Dates and Status -->
                        <div class="d-flex justify-content-between text-center border rounded p-2 mb-2">
                            <div>
                                <i class="fas fa-calendar-plus text-secondary"></i>
                                <div><small>الإضافة</small></div>
                                <small>{{ $client->created_at->format('Y-m-d') }}</small>
                            </div>
                            <div>
                                <i class="fas fa-file-invoice-dollar text-secondary"></i>
                                <div><small>آخر فاتورة</small></div>
                                <small>{{ $client->invoices->last()->invoice_date ?? '-' }}</small>
                            </div>
                            <div>
                                <i class="fas fa-check" style="color: {{ $statusToShow->color ?? '#6c757d' }}"></i>
                                <div><small>الحالة</small></div>
                                @if ($statusToShow)
                                    <strong style="color: {{ $statusToShow->color }};">
                                        {{ $statusToShow->name }}
                                    </strong>
                                @else
                                    <strong class="text-secondary">غير محدد</strong>
                                @endif
                            </div>
                        </div>

                        <!-- Stats Section -->
                        <div class="d-flex justify-content-around text-center border rounded p-2 mb-3">
                            <div class="px-1">
                                <i class="fas fa-cash-register text-primary"></i>
                                <div class="small text-muted">المبيعات</div>
                                <strong class="text-primary">{{ number_format($totalSales ?? 0) }}</strong>
                            </div>
                            <div class="px-1">
                                <i class="fas fa-money-bill-wave text-success"></i>
                                <div class="small text-muted">التحصيلات</div>
                                <strong class="text-success">{{ number_format($clientsData[$client->id]['total_collected'] ?? 0) }}</strong>
                            </div>
                            <div class="px-1">
                                <i class="fas fa-clock text-warning"></i>
                                <div class="small text-muted">الآجلة</div>
                                <strong class="text-warning">{{ number_format($clientDueBalances[$client->id] ?? 0) }}</strong>
                            </div>
                        </div>

                        <!-- Monthly Classification -->
                        <div class="mb-3">
                            <h6 class="text-muted mb-2">التصنيف الشهري لعام {{ $currentYear }}</h6>
                            <div class="d-flex flex-wrap justify-content-start">
                                @foreach ($months as $monthName => $monthNumber)
                                    @continue($monthNumber > now()->month)

                                    @php
                                        $monthData = $clientsData[$client->id]['monthly'][$monthName] ?? null;
                                        $group = $monthData['group'] ?? 'd';
                                        $groupClass = $monthData['group_class'] ?? 'secondary';
                                        $collected = $monthData['collected'] ?? 0;
                                        $percentage = $monthData['percentage'] ?? 0;
                                        $paymentsTotal = $monthData['payments_total'] ?? 0;
                                        $receiptsTotal = $monthData['receipts_total'] ?? 0;
                                        $target = $monthData['target'] ?? 100000;
                                    @endphp

                                    <div class="text-center position-relative" style="margin: 5px;"
                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="التحصيلات: {{ number_format($collected) }} | المدفوعات: {{ number_format($paymentsTotal) }} | سندات القبض: {{ number_format($receiptsTotal) }} | النسبة: {{ $percentage }}%">

                                        <!-- Classification Circle -->
                                        <div class="rounded-circle border-2 border-{{ $groupClass }}
                                             text-{{ $groupClass }} fw-bold
                                             d-flex align-items-center justify-content-center"
                                            style="width:40px; height:40px; cursor: pointer;">
                                            {{ $group }}
                                        </div>

                                        <!-- Month Name -->
                                        <small class="d-block text-muted mt-1">{{ $monthName }}</small>

                                        <!-- Amount Collected (if any) -->
                                        @if ($collected > 0)
                                            <small class="d-block text-success" style="font-size: 0.7rem;">
                                                {{ number_format($collected, 0) }}
                                            </small>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="alert alert-info text-center py-4" role="alert">
        <i class="fas fa-info-circle fa-2x mb-3"></i>
        <h5 class="mb-0">لا توجد عملاء مسجلين حالياً</h5>
    </div>
@endif