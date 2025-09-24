@extends('master')

@section('title')
    تقرير أعمار ديون الأستاذ العام
@stop

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background: #f8f9fa;
        }

        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 20px 20px;
        }

        .card-modern {
            background: white;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
            border: none;
        }

        .card-header-modern {
            background: #f8f9fa;
            border-radius: 16px 16px 0 0;
            padding: 1.5rem;
            border-bottom: 1px solid #e9ecef;
        }

        .card-body-modern {
            padding: 1.5rem;
        }

        .form-control {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 0.5rem 0.75rem;
            font-size: 0.9rem;
        }

        .btn-modern {
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
            font-weight: 600;
            border: none;
            transition: all 0.3s;
        }

        .btn-primary-modern {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .btn-primary-modern:hover {
            background: linear-gradient(135deg, #5a67d8, #6b46c1);
            transform: translateY(-2px);
        }

        .btn-success-modern {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }

        .btn-success-modern:hover {
            background: linear-gradient(135deg, #218838, #1e7e74);
            transform: translateY(-2px);
        }

        .btn-warning-modern {
            background: linear-gradient(135deg, #ffc107, #fd7e14);
            color: white;
        }

        .btn-warning-modern:hover {
            background: linear-gradient(135deg, #e0a800, #e8590c);
            transform: translateY(-2px);
        }

        .btn-outline-secondary {
            border: 2px solid #6c757d;
            color: #6c757d;
            background: transparent;
        }

        .btn-outline-secondary:hover {
            background: #6c757d;
            color: white;
        }

        .stats-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            cursor: pointer;
            border: 2px solid transparent;
        }

        .stats-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.15);
        }

        .stats-card.active {
            border: 2px solid #667eea;
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.05), rgba(118, 75, 162, 0.05));
        }

        .stats-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        .stats-label {
            font-size: 0.9rem;
            color: #6c757d;
            font-weight: 600;
        }

        .table-modern {
            font-size: 0.85rem;
        }

        .table-modern thead th {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            font-weight: 600;
            padding: 1rem 0.5rem;
            border: none;
            white-space: nowrap;
            position: sticky;
            top: 0;
            z-index: 10;
            text-align: center;
        }

        .table-modern tbody tr {
            transition: all 0.2s ease;
        }

        .table-modern tbody tr:hover {
            background: #f8f9fa;
            transform: scale(1.01);
        }

        .table-modern tbody td {
            vertical-align: middle;
            padding: 0.75rem 0.5rem;
            border-bottom: 1px solid #e9ecef;
        }

        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.95);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            border-radius: 16px;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .table-responsive {
            max-height: 600px;
            overflow-y: auto;
            border-radius: 0 0 16px 16px;
        }

        .badge {
            padding: 0.4rem 0.6rem;
            border-radius: 6px;
            font-size: 0.75rem;
        }

        /* تحسين أنماط روابط العملاء */
        .client-link {
            color: #667eea;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
            border: 1px solid transparent;
            position: relative;
        }

        .client-link:hover {
            color: #764ba2;
            text-decoration: none;
            background-color: rgba(102, 126, 234, 0.1);
            border-color: rgba(102, 126, 234, 0.3);
            transform: translateY(-1px);
        }

        .client-link:active {
            transform: translateY(0);
        }

        .client-link::after {
            content: "\f35d";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            margin-right: 0.5rem;
            font-size: 0.8em;
            opacity: 0.6;
            transition: all 0.3s ease;
        }

        .client-link:hover::after {
            opacity: 1;
            transform: translateX(2px);
        }

        /* ألوان الحالات */
        .status-badge {
            padding: 0.4rem 0.8rem;
            border-radius: 8px;
            display: inline-block;
            font-weight: 600;
            font-size: 0.8rem;
            color: white;
            text-shadow: 0 1px 2px rgba(0,0,0,0.2);
        }

        /* تحسين التصميد المسؤول */
        @media (max-width: 768px) {
            .stats-card {
                margin-bottom: 1rem;
            }

            .table-responsive {
                max-height: 400px;
            }

            .btn-modern {
                padding: 0.5rem 1rem;
                font-size: 0.9rem;
            }
        }

        @media print {
            .no-print {
                display: none !important;
            }

            .page-header {
                background: #667eea !important;
                -webkit-print-color-adjust: exact;
            }

            .table-modern thead th {
                background: #667eea !important;
                -webkit-print-color-adjust: exact;
            }
        }

        /* تأثيرات إضافية */
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(102, 126, 234, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(102, 126, 234, 0); }
            100% { box-shadow: 0 0 0 0 rgba(102, 126, 234, 0); }
        }
    </style>
@endsection

@section('content')
    <div class="page-header">
        <div class="container-fluid">
            <div class="d-flex align-items-center justify-content-between">
                <h1><i class="fas fa-chart-line me-3"></i>تقرير أعمار ديون الأستاذ العام</h1>
                <div class="text-end">
                    <small class="opacity-75">آخر تحديث: {{ now()->format('d/m/Y H:i') }}</small>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <!-- Filters -->
        <div class="card-modern fade-in">
            <div class="card-header-modern">
                <h5 class="mb-0"><i class="fas fa-filter me-2"></i>فلاتر التقرير</h5>
            </div>
            <div class="card-body-modern">
                <form id="reportForm">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label"><i class="fas fa-user me-1"></i>العميل</label>
                            <select name="customer" id="customer" class="form-control select2">
                                <option value="">جميع العملاء</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->trade_name ?? $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label"><i class="fas fa-building me-1"></i>الفرع</label>
                            <select name="branch" id="branch" class="form-control select2">
                                <option value="">جميع الفروع</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label"><i class="fas fa-tags me-1"></i>التصنيف</label>
                            <select name="customer_type" id="customer_type" class="form-control select2">
                                <option value="">جميع التصنيفات</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label"><i class="fas fa-map-marker-alt me-1"></i>المجموعة (المنطقة)</label>
                            <select name="group" id="group" class="form-control select2">
                                <option value="">جميع المجموعات</option>
                                @foreach ($groups as $group)
                                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label"><i class="fas fa-info-circle me-1"></i>الحالة</label>
                            <select name="status" id="status" class="form-control select2">
                                <option value="">جميع الحالات</option>
                                @foreach ($statuses as $status)
                                    <option value="{{ $status->id }}">{{ $status->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label"><i class="fas fa-clock me-1"></i>فلتر حسب الفترة</label>
                            <select name="aging_filter" id="aging_filter" class="form-control select2">
                                <option value="">جميع الفترات</option>
                                <option value="today">اليوم فقط</option>
                                <option value="1-30">1-30 يوم</option>
                                <option value="31-60">31-60 يوم</option>
                                <option value="61-90">61-90 يوم</option>
                                <option value="91-120">91-120 يوم</option>
                                <option value="120+">أكثر من 120 يوم</option>
                                <option value="150">121-150 يوم</option>
                                <option value="180">151-180 يوم</option>
                                <option value="210">181-210 يوم</option>
                                <option value="240">211-240 يوم</option>
                                <option value="240+">أكثر من 240 يوم</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label"><i class="fas fa-user-tie me-1"></i>مسؤول المبيعات</label>
                            <select name="sales_manager" id="sales_manager" class="form-control select2">
                                <option value="">جميع مسؤولي المبيعات</option>
                                @foreach ($salesManagers as $manager)
                                    <option value="{{ $manager->id }}">{{ $manager->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label"><i class="fas fa-calendar me-1"></i>من تاريخ</label>
                            <input type="date" name="from_date" class="form-control" value="{{ date('Y-m-01') }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label"><i class="fas fa-calendar me-1"></i>إلى تاريخ</label>
                            <input type="date" name="to_date" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>

                        <div class="col-12 text-center mt-4">
                            <button type="button" class="btn-modern btn-primary-modern me-2" id="filterBtn">
                                <i class="fas fa-search me-1"></i> عرض التقرير
                            </button>
                            <button type="button" class="btn-modern btn-outline-secondary" id="resetBtn">
                                <i class="fas fa-redo me-1"></i> إعادة تعيين
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Actions -->
        <div class="card-modern no-print fade-in">
            <div class="card-body-modern text-center">
                <button class="btn-modern btn-success-modern me-2" id="exportExcel">
                    <i class="fas fa-file-excel me-1"></i> تصدير Excel
                </button>
                <button class="btn-modern btn-warning-modern" onclick="window.print()">
                    <i class="fas fa-print me-1"></i> طباعة التقرير
                </button>
            </div>
        </div>

        <!-- Stats -->
        <div class="row" id="totalsSection">
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
                <div class="stats-card" data-filter="today">
                    <div class="stats-value" id="todayAmount">0</div>
                    <div class="stats-label">اليوم</div>
                </div>
            </div>
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
                <div class="stats-card" data-filter="1-30">
                    <div class="stats-value" id="days1to30">0</div>
                    <div class="stats-label">1-30 يوم</div>
                </div>
            </div>
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
                <div class="stats-card" data-filter="31-60">
                    <div class="stats-value" id="days31to60">0</div>
                    <div class="stats-label">31-60 يوم</div>
                </div>
            </div>
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
                <div class="stats-card" data-filter="61-90">
                    <div class="stats-value" id="days61to90">0</div>
                    <div class="stats-label">61-90 يوم</div>
                </div>
            </div>
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
                <div class="stats-card" data-filter="91-120">
                    <div class="stats-value" id="days91to120">0</div>
                    <div class="stats-label">91-120 يوم</div>
                </div>
            </div>
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
                <div class="stats-card" data-filter="120+">
                    <div class="stats-value" id="daysOver120">0</div>
                    <div class="stats-label">+120 يوم</div>
                </div>
            </div>
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
                <div class="stats-card" data-filter="150">
                    <div class="stats-value" id="days150">0</div>
                    <div class="stats-label">121-150 يوم</div>
                </div>
            </div>
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
                <div class="stats-card" data-filter="180">
                    <div class="stats-value" id="days180">0</div>
                    <div class="stats-label">151-180 يوم</div>
                </div>
            </div>
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
                <div class="stats-card" data-filter="210">
                    <div class="stats-value" id="days210">0</div>
                    <div class="stats-label">181-210 يوم</div>
                </div>
            </div>
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
                <div class="stats-card" data-filter="240">
                    <div class="stats-value" id="days240">0</div>
                    <div class="stats-label">211-240 يوم</div>
                </div>
            </div>
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
                <div class="stats-card" data-filter="240+">
                    <div class="stats-value" id="daysOver240">0</div>
                    <div class="stats-label">+240 يوم</div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="card-modern fade-in" id="reportContainer" style="position: relative;">
            <div class="loading-overlay" style="display: none;">
                <div class="spinner"></div>
            </div>
            <div class="card-header-modern">
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="mb-0"><i class="fas fa-table me-2"></i>بيانات التقرير</h5>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-primary me-2" id="recordsCount">0 سجل</span>
                        <span class="badge bg-success" id="customersCount">0 عميل</span>
                    </div>
                </div>
            </div>
            <div class="card-body-modern p-0">
                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead>
                            <tr>
                                <th><i class="fas fa-barcode me-1"></i>كود</th>
                                <th><i class="fas fa-user me-1"></i>العميل</th>
                                <th><i class="fas fa-building me-1"></i>الفرع</th>
                                <th><i class="fas fa-map-marker-alt me-1"></i>المجموعة</th>
                                <th><i class="fas fa-home me-1"></i>الحي</th>
                                <th><i class="fas fa-info-circle me-1"></i>الحالة</th>
                                <th><i class="fas fa-calendar-day me-1"></i>اليوم</th>
                                <th>1-30</th>
                                <th>31-60</th>
                                <th>61-90</th>
                                <th>91-120</th>
                                <th>+120</th>
                                <th><i class="fas fa-calculator me-1"></i>الإجمالي</th>
                            </tr>
                        </thead>
                        <tbody id="reportTableBody">
                            <tr>
                                <td colspan="13" class="text-center p-4">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <div class="spinner-border text-primary me-2"></div>
                                        <span>جاري تحميل البيانات...</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot id="reportTableFooter" style="display: none;">
                            <tr style="background: linear-gradient(135deg, #28a745, #20c997); color: white; font-weight: bold;">
                                <td colspan="6" class="text-center">
                                    <i class="fas fa-calculator me-2"></i>الإجمالي الكلي
                                </td>
                                <td id="footerToday">0.00</td>
                                <td id="footer1to30">0.00</td>
                                <td id="footer31to60">0.00</td>
                                <td id="footer61to90">0.00</td>
                                <td id="footer91to120">0.00</td>
                                <td id="footerOver120">0.00</td>
                                <td id="footerTotal">0.00</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card-modern">
                    <div class="card-body-modern text-center">
                        <i class="fas fa-exclamation-triangle text-warning fa-2x mb-2"></i>
                        <h5 class="mb-1">العملاء المتأخرين</h5>
                        <h3 class="text-warning" id="overdueCustomers">0</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card-modern">
                    <div class="card-body-modern text-center">
                        <i class="fas fa-credit-card text-danger fa-2x mb-2"></i>
                        <h5 class="mb-1">تجاوز حد الائتمان</h5>
                        <h3 class="text-danger" id="overCreditLimit">0</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card-modern">
                    <div class="card-body-modern text-center">
                        <i class="fas fa-money-bill-wave text-success fa-2x mb-2"></i>
                        <h5 class="mb-1">إجمالي المتأخر</h5>
                        <h3 class="text-success" id="totalOverdueAmount">0.00</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card-modern">
                    <div class="card-body-modern text-center">
                        <i class="fas fa-calendar-alt text-info fa-2x mb-2"></i>
                        <h5 class="mb-1">متوسط الأيام</h5>
                        <h3 class="text-info" id="averageDaysLate">0</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.5/xlsx.full.min.js"></script>

    <script>
        let currentRequest = null;

        $(document).ready(function() {
            // Initialize Select2 with enhanced styling
            $('.select2').select2({
                dir: 'rtl',
                width: '100%',
                placeholder: 'اختر من القائمة...',
                allowClear: true,
                theme: 'classic'
            });

            // Add custom styling to Select2
            $('.select2-container--classic .select2-selection--single').css({
                'border': '1px solid #dee2e6',
                'border-radius': '8px',
                'height': 'calc(2.25rem + 2px)'
            });

            // Load initial data
            loadReportData();

            // Event handlers
            $('#filterBtn').on('click', function() {
                $(this).addClass('pulse');
                loadReportData();
                setTimeout(() => $(this).removeClass('pulse'), 2000);
            });

            $('#resetBtn').on('click', function() {
                $('#reportForm')[0].reset();
                $('.select2').val(null).trigger('change');
                $('.stats-card').removeClass('active');
                loadReportData();
            });

            // Stats card interaction
            $('.stats-card').on('click', function() {
                const filter = $(this).data('filter');
                $('.stats-card').removeClass('active');
                $(this).addClass('active');

                $('#aging_filter').val(filter).trigger('change');
                loadReportData();
            });

            // Aging filter change
            $('#aging_filter').on('change', function() {
                const filter = $(this).val();
                $('.stats-card').removeClass('active');
                if (filter) {
                    $(`.stats-card[data-filter="${filter}"]`).addClass('active');
                }
            });

            // Export functionality
            $('#exportExcel').on('click', exportToExcel);

            // Auto-refresh every 5 minutes
            setInterval(loadReportData, 300000);
        });

        function loadReportData() {
            // Cancel previous request
            if (currentRequest) currentRequest.abort();

            $('.loading-overlay').show();
            $('#filterBtn').prop('disabled', true);

            currentRequest = $.ajax({
                url: '{{ route("ClientReport.debtAgingGeneralLedgerAjax") }}',
                method: 'GET',
                data: $('#reportForm').serialize(),
                success: function(response) {
                    if (response.success) {
                        updateDisplay(response);
                        showSuccessMessage('تم تحديث البيانات بنجاح');
                    } else {
                        showError('لا توجد بيانات متاحة');
                    }
                },
                error: function(xhr) {
                    if (xhr.statusText !== 'abort') {
                        console.error('Ajax Error:', xhr);
                        showError('حدث خطأ في تحميل البيانات: ' + (xhr.responseJSON?.message || 'خطأ غير معروف'));
                    }
                },
                complete: function() {
                    $('.loading-overlay').hide();
                    $('#filterBtn').prop('disabled', false);
                    currentRequest = null;
                }
            });
        }

        function updateDisplay(data) {
            // Update statistics cards
            updateStatsCards(data.totals);

            // Update summary cards
            updateSummaryCards(data.summary);

            // Update counters
            $('#recordsCount').text(`${data.records_count || 0} سجل`);
            $('#customersCount').text(`${data.customers_count || 0} عميل`);

            // Update footer totals
            updateFooterTotals(data.totals);

            // Update table content
            updateTableContent(data.data);
        }

        function updateStatsCards(totals) {
            $('#todayAmount').text(formatNumber(totals.today || 0));
            $('#days1to30').text(formatNumber(totals.days1to30 || 0));
            $('#days31to60').text(formatNumber(totals.days31to60 || 0));
            $('#days61to90').text(formatNumber(totals.days61to90 || 0));
            $('#days91to120').text(formatNumber(totals.days91to120 || 0));
            $('#daysOver120').text(formatNumber(totals.daysOver120 || 0));

            // Extended periods
            $('#days150').text(formatNumber(totals.days150 || 0));
            $('#days180').text(formatNumber(totals.days180 || 0));
            $('#days210').text(formatNumber(totals.days210 || 0));
            $('#days240').text(formatNumber(totals.days240 || 0));
            $('#daysOver240').text(formatNumber(totals.daysOver240 || 0));
        }

        function updateSummaryCards(summary) {
            $('#overdueCustomers').text(summary.overdue_customers || 0);
            $('#overCreditLimit').text(summary.over_credit_limit || 0);
            $('#totalOverdueAmount').text(formatNumber(summary.total_overdue_amount || 0));
            $('#averageDaysLate').text(Math.round(summary.average_days_late || 0));
        }

        function updateFooterTotals(totals) {
            $('#footerToday').text(formatNumber(totals.today || 0));
            $('#footer1to30').text(formatNumber(totals.days1to30 || 0));
            $('#footer31to60').text(formatNumber(totals.days31to60 || 0));
            $('#footer61to90').text(formatNumber(totals.days61to90 || 0));
            $('#footer91to120').text(formatNumber(totals.days91to120 || 0));
            $('#footerOver120').text(formatNumber(totals.daysOver120 || 0));
            $('#footerTotal').text(formatNumber(totals.total_due || 0));
            $('#reportTableFooter').show();
        }

        function updateTableContent(data) {
            let html = '';

            if (data && data.length > 0) {
                data.forEach(item => {
                    const statusStyle = item.status_color ?
                        `background-color: ${item.status_color}; color: white;` :
                        'background-color: #6c757d; color: white;';

                    // Create client link with proper URL generation
                    const clientUrl = item.client_id ?
                        `{{ route('clients.show', '') }}/${item.client_id}` :
                        '#';

                    html += `
                        <tr class="fade-in">
                            <td>
                                <span class="badge bg-info">${item.client_code || 'غير محدد'}</span>
                            </td>
                            <td>
                                ${item.client_id ?
                                    `<a href="${clientUrl}" class="client-link" target="_blank" title="عرض تفاصيل العميل">
                                        ${item.client_name || 'غير محدد'}
                                    </a>` :
                                    `<span class="text-muted">${item.client_name || 'غير محدد'}</span>`
                                }
                            </td>
                            <td>
                                <span class="badge bg-secondary">${item.branch || 'غير محدد'}</span>
                            </td>
                            <td>${item.group || 'غير محدد'}</td>
                            <td>${item.neighborhood || 'غير محدد'}</td>
                            <td>
                                <span class="status-badge" style="${statusStyle}">
                                    ${item.status || 'غير محدد'}
                                </span>
                            </td>
                            <td class="text-center">${formatNumber(item.today)}</td>
                            <td class="text-center">${formatNumber(item.days1to30)}</td>
                            <td class="text-center">${formatNumber(item.days31to60)}</td>
                            <td class="text-center">${formatNumber(item.days61to90)}</td>
                            <td class="text-center">${formatNumber(item.days91to120)}</td>
                            <td class="text-center">${formatNumber(item.daysOver120)}</td>
                            <td class="text-center fw-bold text-primary">${formatNumber(item.total_due)}</td>
                        </tr>
                    `;
                });
            } else {
                html = `
                    <tr>
                        <td colspan="13" class="text-center p-4">
                            <div class="d-flex flex-column align-items-center">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">لا توجد بيانات</h5>
                                <p class="text-muted">جرب تغيير فلاتر البحث</p>
                            </div>
                        </td>
                    </tr>
                `;
                $('#reportTableFooter').hide();
            }

            $('#reportTableBody').html(html);
        }

        function formatNumber(num) {
            return parseFloat(num || 0).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function exportToExcel() {
            try {
                const table = document.querySelector('.table-modern');
                if (!table) {
                    showError('لا يمكن العثور على الجدول للتصدير');
                    return;
                }

                const wb = XLSX.utils.table_to_book(table, {
                    sheet: "تقرير أعمار الديون",
                    raw: false
                });

                const filename = `debt_aging_report_${new Date().toISOString().slice(0,10)}.xlsx`;
                XLSX.writeFile(wb, filename);

                showSuccessMessage('تم تصدير الملف بنجاح');
            } catch (error) {
                console.error('Export error:', error);
                showError('حدث خطأ أثناء تصدير الملف');
            }
        }

        function showError(message) {
            // Create toast notification
            const toast = $(`
                <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999">
                    <div class="toast align-items-center text-white bg-danger border-0" role="alert">
                        <div class="d-flex">
                            <div class="toast-body">
                                <i class="fas fa-exclamation-circle me-2"></i>${message}
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto"></button>
                        </div>
                    </div>
                </div>
            `);

            $('body').append(toast);
            const bsToast = new bootstrap.Toast(toast.find('.toast')[0]);
            bsToast.show();

            setTimeout(() => toast.remove(), 5000);
        }

        function showSuccessMessage(message) {
            const toast = $(`
                <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999">
                    <div class="toast align-items-center text-white bg-success border-0" role="alert">
                        <div class="d-flex">
                            <div class="toast-body">
                                <i class="fas fa-check-circle me-2"></i>${message}
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto"></button>
                        </div>
                    </div>
                </div>
            `);

            $('body').append(toast);
            const bsToast = new bootstrap.Toast(toast.find('.toast')[0]);
            bsToast.show();

            setTimeout(() => toast.remove(), 3000);
        }
    </script>
@endsection