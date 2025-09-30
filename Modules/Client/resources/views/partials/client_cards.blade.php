<!-- تحديث ملف: resources/views/client/partials/client_cards.blade.php -->
@if (isset($clients) && $clients->count() > 0)
    <div class="row g-4">
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

            <div class="col-lg-6 col-xl-4 client-card" data-client-id="{{ $client->id }}">
                <div class="client-card-elegant">
                    <!-- مؤشر التحميل -->
                    <div class="loading-overlay d-none">
                        <div class="spinner-border text-primary" role="status"></div>
                    </div>

                    <!-- Header مع اسم العميل والإجراءات -->
                    <div class="card-header-elegant">
                        <div class="client-title-section">
                            <h3 class="client-title">{{ $client->trade_name }}</h3>
                            <span class="client-code-badge">{{ $client->code }}</span>
                        </div>

                        <div class="actions-section">
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

                            @if ($statusToShow)
                                <div class="status-indicator"
                                    style="background: linear-gradient(135deg, {{ $statusToShow->color }}15, {{ $statusToShow->color }}25);
                                            border-left: 3px solid {{ $statusToShow->color }};">
                                    <span style="color: {{ $statusToShow->color }};">{{ $statusToShow->name }}</span>
                                </div>
                            @else
                                <div class="status-indicator status-unknown">
                                    <span>غير محدد</span>
                                </div>
                            @endif

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

                    <!-- معلومات الاتصال -->
                    <div class="contact-section">
                        <div class="contact-grid">
                            <div class="contact-item">
                                <i class="fas fa-user"></i>
                                <span>{{ $client->frist_name ?: 'غير محدد' }}</span>
                            </div>
                            <div class="contact-item">
                                <i class="fas fa-phone"></i>
                                <span>{{ $client->phone ?: 'غير محدد' }}</span>
                            </div>
                            <div class="contact-item">
                                <i class="fas fa-tags"></i>
                                <span>{{ optional($client->categoriesClient)->name ?: 'غير مصنف' }}</span>
                            </div>
                            <div class="contact-item">
                                <i class="fas fa-building"></i>
                                <span>{{ $client->branch->name ?: 'غير محدد' }}</span>
                            </div>
                        </div>

                        <!-- الموقع والمسافة -->
                        <div class="location-section">
                            <div class="location-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <a href="https://www.google.com/maps/search/?api=1&query={{ $client->locations->latitude ?? '' }},{{ $client->locations->longitude ?? '' }}"
                                    target="_blank">{{ $client->Neighborhood->Region->name ?? 'عرض الموقع' }}</a>
                            </div>

                            @php
                                $distanceInfo = $clientDistances[$client->id] ?? null;
                                $distanceClass = 'distance-default';
                                $distanceText = 'غير متاح';

                                if ($distanceInfo && isset($distanceInfo['distance'])) {
                                    if ($distanceInfo['distance'] !== null) {
                                        $distanceText = number_format($distanceInfo['distance'], 1) . ' كم';
                                        $distanceClass = $distanceInfo['within_range']
                                            ? 'distance-close'
                                            : 'distance-far';
                                    } else {
                                        $distanceText = $distanceInfo['message'] ?? 'غير متاح';
                                    }
                                }
                            @endphp

                            <div class="distance-item {{ $distanceClass }}">
                                <i class="fas fa-route"></i>
                                <span>{{ $distanceText }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- التواريخ المهمة -->
                    <div class="dates-section">
                        <div class="date-item">
                            <div class="date-label">تاريخ التسجيل</div>
                            <div class="date-value">{{ $client->created_at->format('Y/m/d') }}</div>
                        </div>
                        <div class="date-separator"></div>
                        <div class="date-item">
                            <div class="date-label">آخر فاتورة</div>
                            <div class="date-value">{{ $client->invoices->last()->invoice_date ?? 'لا توجد' }}</div>
                        </div>
                    </div>

                    <!-- الإحصائيات المالية -->
                    <div class="stats-section">
                        <div class="stat-card stat-sales">
                            <div class="stat-number">{{ number_format($totalSales ?? 0) }}</div>
                            <div class="stat-label">إجمالي المبيعات</div>
                        </div>
                        <div class="stat-card stat-collected">
                            <div class="stat-number">
                                {{ number_format($clientsData[$client->id]['total_collected'] ?? 0) }}</div>
                            <div class="stat-label">التحصيلات</div>
                        </div>
                        <div class="stat-card stat-due">
                            <div class="stat-number">{{ number_format($clientDueBalances[$client->id] ?? 0) }}</div>
                            <div class="stat-label">المبالغ الآجلة</div>
                        </div>
                    </div>

                    <!-- التصنيف الشهري -->
                    <div class="classification-section">
                        <div class="classification-header">
                            <h4>التصنيف الشهري {{ $currentYear }}</h4>
                        </div>
                        <div class="months-container">
                            @foreach ($months as $monthName => $monthNumber)
                                @continue($monthNumber > now()->month)

                                @php
                                    $monthData = $clientsData[$client->id]['monthly'][$monthName] ?? null;
                                    $group = strtoupper($monthData['group'] ?? 'D');
                                    $groupClass = $monthData['group_class'] ?? 'secondary';
                                    $collected = $monthData['collected'] ?? 0;
                                    $percentage = $monthData['percentage'] ?? 0;
                                    $paymentsTotal = $monthData['payments_total'] ?? 0;
                                    $receiptsTotal = $monthData['receipts_total'] ?? 0;
                                @endphp

                                <div class="month-item" data-bs-toggle="tooltip"
                                    title="تفاصيل {{ $monthName }}: تحصيلات {{ number_format($collected) }} - مدفوعات {{ number_format($paymentsTotal) }} - نسبة {{ $percentage }}%">
                                    <div class="month-badge month-{{ $groupClass }}">{{ $group }}</div>
                                    <div class="month-name">{{ $monthName }}</div>
                                    @if ($collected > 0)
                                        <div class="month-amount">{{ number_format($collected / 1000, 0) }}k</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- زر الإخفاء السريع -->
                    <div class="quick-actions">
                        <button class="hide-btn hide-from-map-btn" data-client-id="{{ $client->id }}"
                            data-client-name="{{ $client->trade_name }}">
                            <i class="fas fa-eye-slash"></i>
                            إخفاء لـ 24 ساعة
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <style>
        .client-card-elegant {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            overflow: hidden;
            position: relative;
        }

        .client-card-elegant:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }

        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.95);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
            backdrop-filter: blur(2px);
        }

        /* Header Section */
        .card-header-elegant {
            padding: 24px;
            background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
            border-bottom: 1px solid #f0f0f0;
        }

        .client-title-section {
            margin-bottom: 16px;
        }

        .client-title {
            font-size: 18px;
            font-weight: 700;
            color: #1a1a1a;
            margin: 0 0 8px 0;
            line-height: 1.3;
        }

        .client-code-badge {
            background: #e8f4f8;
            color: #2c5aa0;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .actions-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .status-indicator {
            padding: 8px 16px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
        }

        .status-unknown {
            background: #f5f5f5;
            border-left: 3px solid #9e9e9e;
            color: #757575;
        }

        /* Menu Toggle */
        .menu-toggle {
            background: none;
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 3px;
            transition: all 0.2s ease;
        }

        .menu-toggle:hover {
            background: #f8f9fa;
        }

        .menu-toggle span {
            width: 4px;
            height: 4px;
            background: #6c757d;
            border-radius: 50%;
            transition: all 0.2s ease;
        }

        .menu-toggle:hover span {
            background: #495057;
        }

        /* Dropdown Menu */
        .dropdown-menu-elegant {
            border: none;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            border-radius: 12px;
            padding: 0;
            min-width: 280px;
            overflow: hidden;
        }

        .menu-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 16px 20px;
            margin: 0;
        }

        .menu-header h6 {
            margin: 0;
            font-size: 14px;
            font-weight: 600;
        }

        .menu-items {
            padding: 8px 0;
        }

        .menu-item {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            text-decoration: none;
            color: #495057;
            transition: all 0.2s ease;
            border: none;
            background: none;
            width: 100%;
        }

        .menu-item:hover {
            background: #f8f9fa;
            color: #212529;
        }

        .menu-item.danger:hover {
            background: #fff5f5;
            color: #dc3545;
        }

        .menu-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left: 12px;
            font-size: 14px;
        }

        .menu-icon.view {
            background: #e3f2fd;
            color: #1976d2;
        }

        .menu-icon.edit {
            background: #f3e5f5;
            color: #7b1fa2;
        }

        .menu-icon.hide {
            background: #fff3e0;
            color: #f57c00;
        }

        .menu-icon.delete {
            background: #ffebee;
            color: #d32f2f;
        }

        .menu-text {
            flex: 1;
        }

        .menu-title {
            display: block;
            font-weight: 600;
            font-size: 13px;
            line-height: 1.2;
        }

        .menu-subtitle {
            display: block;
            font-size: 11px;
            color: #6c757d;
            margin-top: 2px;
        }

        .menu-divider {
            height: 1px;
            background: #e9ecef;
            margin: 8px 0;
        }

        /* Contact Section */
        .contact-section {
            padding: 20px 24px;
        }

        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 16px;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            color: #495057;
        }

        .contact-item i {
            width: 16px;
            color: #6c757d;
            font-size: 12px;
        }

        .location-section {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 16px;
            padding-top: 16px;
            border-top: 1px solid #f0f0f0;
        }

        .location-item a {
            color: #007bff;
            text-decoration: none;
            font-weight: 500;
        }

        .distance-item {
            font-size: 12px;
            font-weight: 600;
            padding: 4px 8px;
            border-radius: 6px;
        }

        .distance-close {
            background: #d4edda;
            color: #155724;
        }

        .distance-far {
            background: #f8d7da;
            color: #721c24;
        }

        .distance-default {
            background: #e2e3e5;
            color: #383d41;
        }

        /* Dates Section */
        .dates-section {
            padding: 16px 24px;
            background: #f8f9fa;
            display: flex;
            align-items: center;
        }

        .date-item {
            flex: 1;
            text-align: center;
        }

        .date-label {
            font-size: 11px;
            color: #6c757d;
            margin-bottom: 4px;
            font-weight: 500;
        }

        .date-value {
            font-size: 13px;
            color: #212529;
            font-weight: 700;
        }

        .date-separator {
            width: 1px;
            height: 30px;
            background: #dee2e6;
            margin: 0 16px;
        }

        /* Stats Section */
        .stats-section {
            padding: 20px 24px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }

        .stat-card {
            text-align: center;
            padding: 16px 8px;
            border-radius: 10px;
            transition: transform 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
        }

        .stat-sales {
            background: linear-gradient(135deg, #667eea20, #764ba220);
        }

        .stat-collected {
            background: linear-gradient(135deg, #11998e20, #38ef7d20);
        }

        .stat-due {
            background: linear-gradient(135deg, #fc466b20, #3f5efb20);
        }

        .stat-number {
            font-size: 16px;
            font-weight: 800;
            color: #212529;
            margin-bottom: 4px;
        }

        .stat-label {
            font-size: 11px;
            color: #6c757d;
            font-weight: 600;
        }

        /* Classification Section */
        .classification-section {
            padding: 20px 24px;
            background: #fafafa;
        }

        .classification-header {
            text-align: center;
            margin-bottom: 16px;
        }

        .classification-header h4 {
            font-size: 13px;
            color: #495057;
            font-weight: 600;
            margin: 0;
        }

        .months-container {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            justify-content: center;
        }

        .month-item {
            text-align: center;
            cursor: pointer;
        }

        .month-badge {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: 800;
            margin: 0 auto 4px;
            border: 2px solid transparent;
        }

        .month-primary {
            background: #e3f2fd;
            color: #1976d2;
            border-color: #1976d2;
        }

        .month-success {
            background: #e8f5e8;
            color: #2e7d32;
            border-color: #2e7d32;
        }

        .month-warning {
            background: #fff3e0;
            color: #f57c00;
            border-color: #f57c00;
        }

        .month-danger {
            background: #ffebee;
            color: #d32f2f;
            border-color: #d32f2f;
        }

        .month-secondary {
            background: #f5f5f5;
            color: #616161;
            border-color: #9e9e9e;
        }

        .month-name {
            font-size: 9px;
            color: #6c757d;
            font-weight: 600;
        }

        .month-amount {
            font-size: 8px;
            color: #28a745;
            font-weight: 700;
            margin-top: 1px;
        }

        /* Quick Actions */
        .quick-actions {
            padding: 16px 24px;
            border-top: 1px solid #f0f0f0;
        }

        .hide-btn {
            width: 100%;
            background: linear-gradient(135deg, #ffeaa7, #fab1a0);
            color: #2d3436;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .hide-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .contact-grid {
                grid-template-columns: 1fr;
                gap: 8px;
            }

            .stats-section {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .stat-card {
                display: flex;
                align-items: center;
                text-align: right;
                gap: 12px;
            }

            .location-section {
                grid-template-columns: 1fr;
                gap: 8px;
            }
        }
    </style>
@else
    <div class="empty-state-elegant">
        <div class="empty-illustration">
            <i class="fas fa-users"></i>
        </div>
        <h3>لا توجد عملاء بعد</h3>
        <p>ابدأ رحلتك بإضافة أول عميل</p>
    </div>

    <style>
        .empty-state-elegant {
            text-align: center;
            padding: 80px 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 20px;
            margin: 20px 0;
        }

        .empty-illustration {
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            font-size: 40px;
        }

        .empty-state-elegant h3 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .empty-state-elegant p {
            font-size: 16px;
            opacity: 0.9;
            margin: 0;
        }
    </style>
@endif
