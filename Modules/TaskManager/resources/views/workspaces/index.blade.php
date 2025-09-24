@extends('master')

@section('title', ' مساحات العمل')

@section('css')
    <style>
        .analytics-card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            transition: transform 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .analytics-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .stat-icon {
            font-size: 2rem;
            opacity: 0.8;
        }

        .workspace-status-badge {
            font-size: 0.85rem;
            padding: 0.35rem 0.65rem;
        }

        .table-hover-custom tr:hover {
            background-color: #f8f9fa;
        }

        .avatar-content {
            font-size: 1.1rem;
        }

        .select2-container--default .select2-selection--single {
            height: calc(1.5em + 0.75rem + 2px);
        }

        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .progress-custom {
            height: 8px;
            border-radius: 4px;
        }

        .chart-container {
            position: relative;
            height: 300px;
            margin-bottom: 2rem;
        }
    </style>
@endsection

@section('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">تحليلات مساحات العمل</h2>
                    <div class="breadcrumb-wrapper">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسية</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('workspaces.index') }}">مساحات العمل</a></li>
                            <li class="breadcrumb-item active">التحليلات</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">
        <!-- إحصائيات عامة -->
        <div class="row mb-4" id="statsContainer">
            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card analytics-card">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="stat-icon bg-primary text-white rounded me-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="fas fa-project-diagram"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h3 class="mb-0" id="totalWorkspaces">0</h3>
                                        <p class="mb-0 text-muted">إجمالي المساحات</p>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge badge-light-primary" id="workspacesGrowth">+0%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card analytics-card">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="stat-icon bg-success text-white rounded me-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="fas fa-tasks"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h3 class="mb-0" id="activeProjects">0</h3>
                                        <p class="mb-0 text-muted">المشاريع النشطة</p>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge badge-light-success" id="projectsGrowth">+0%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card analytics-card">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="stat-icon bg-info text-white rounded me-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h3 class="mb-0" id="totalMembers">0</h3>
                                        <p class="mb-0 text-muted">إجمالي الأعضاء</p>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge badge-light-info" id="membersGrowth">+0%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card analytics-card">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="stat-icon bg-warning text-white rounded me-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="fas fa-percentage"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h3 class="mb-0" id="completionRate">0%</h3>
                                        <p class="mb-0 text-muted">معدل الإكمال</p>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge badge-light-warning" id="completionGrowth">+0%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- شريط الأدوات العلوي -->
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <!-- زر التصدير -->
                        <button class="btn btn-outline-success btn-sm d-flex align-items-center rounded-pill px-3" id="exportAnalyticsBtn">
                            <i class="fas fa-file-excel me-1"></i>تصدير التحليلات
                        </button>

                        <!-- زر الطباعة -->
                        <button class="btn btn-outline-secondary btn-sm d-flex align-items-center rounded-pill px-3" id="printAnalyticsBtn">
                            <i class="fas fa-print me-1"></i>طباعة
                        </button>

                        <!-- زر التحديث -->
                        <button class="btn btn-outline-primary btn-sm d-flex align-items-center rounded-pill px-3" id="refreshAnalyticsBtn">
                            <i class="fas fa-sync-alt me-1"></i>تحديث
                        </button>
                    </div>

                    <!-- معلومات النتائج -->
                    <div class="d-flex align-items-center gap-2" id="top-pagination-info">
                        <span class="text-muted mx-2 results-info">0 مساحة عمل</span>
                    </div>
                                            <a href="{{ route('workspaces.create') }}" class="btn btn-outline-primary btn-sm d-flex align-items-center rounded-pill px-3" id="refreshAnalyticsBtn">
                            <i class="fas fa-sync-alt me-1"></i>اضافة مساحة عمل
                        </a>

                </div>
            </div>
        </div>

        <!-- نموذج البحث والفلترة -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center p-2">
                <div class="d-flex gap-2">
                    <span class="hide-button-text">بحث وتصفية</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-outline-secondary btn-sm" onclick="toggleSearchFields(this)">
                        <i class="fa fa-times"></i>
                        <span class="hide-button-text">اخفاء</span>
                    </button>
                    <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="collapse"
                        data-bs-target="#advancedSearchForm" onclick="toggleSearchText(this)">
                        <i class="fa fa-filter"></i>
                        <span class="button-text">متقدم</span>
                    </button>
                    <button type="button" id="resetSearch" class="btn btn-outline-warning btn-sm">
                        <i class="fa fa-refresh"></i>
                        إعادة تعيين
                    </button>
                </div>
            </div>

            <div class="card-body">
                <form class="form" id="searchForm">
                    <div class="row g-3" id="basicSearchFields">
                        <!-- 1. عنوان المساحة -->
                        <div class="col-md-4 mb-3">
                            <input type="text" id="title" class="form-control" placeholder="عنوان مساحة العمل"
                                name="title">
                        </div>

                        <!-- 2. المالك -->
                        <div class="col-md-4 mb-3">
                            <select name="admin_id" class="form-control select2" id="admin_id">
                                <option value="">اختر المالك</option>
                                @foreach ($admins as $admin)
                                    <option value="{{ $admin->id }}">{{ $admin->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- 3. المساحة الرئيسية -->
                        <div class="col-md-4 mb-3">
                            <select name="is_primary" class="form-control select2" id="is_primary">
                                <option value="">نوع المساحة</option>
                                <option value="1">رئيسية</option>
                                <option value="0">ثانوية</option>
                            </select>
                        </div>
                    </div>

                    <!-- البحث المتقدم -->
                    <div class="collapse" id="advancedSearchForm">
                        <div class="row g-3 mt-2">
                            <!-- 4. عدد المشاريع (من) -->
                            <div class="col-md-4 mb-3">
                                <input type="number" id="projects_min" class="form-control" placeholder="عدد المشاريع (من)"
                                    name="projects_min" min="0">
                            </div>

                            <!-- 5. عدد المشاريع (إلى) -->
                            <div class="col-md-4 mb-3">
                                <input type="number" id="projects_max" class="form-control" placeholder="عدد المشاريع (إلى)"
                                    name="projects_max" min="0">
                            </div>

                            <!-- 6. عدد الأعضاء (من) -->
                            <div class="col-md-4 mb-3">
                                <input type="number" id="members_min" class="form-control" placeholder="عدد الأعضاء (من)"
                                    name="members_min" min="0">
                            </div>

                            <!-- 7. عدد الأعضاء (إلى) -->
                            <div class="col-md-4 mb-3">
                                <input type="number" id="members_max" class="form-control" placeholder="عدد الأعضاء (إلى)"
                                    name="members_max" min="0">
                            </div>

                            <!-- 8. من (التاريخ) -->
                            <div class="col-md-4 mb-3">
                                <input type="date" id="from_date" class="form-control" placeholder="من"
                                    name="from_date">
                            </div>

                            <!-- 9. إلى (التاريخ) -->
                            <div class="col-md-4 mb-3">
                                <input type="date" id="to_date" class="form-control" placeholder="إلى"
                                    name="to_date">
                            </div>

                            <!-- 10. معدل الإكمال (من) -->
                            <div class="col-md-4 mb-3">
                                <input type="number" id="completion_min" class="form-control"
                                    placeholder="معدل الإكمال (من %)" name="completion_min" min="0" max="100">
                            </div>

                            <!-- 11. معدل الإكمال (إلى) -->
                            <div class="col-md-4 mb-3">
                                <input type="number" id="completion_max" class="form-control"
                                    placeholder="معدل الإكمال (إلى %)" name="completion_max" min="0" max="100">
                            </div>
                        </div>
                    </div>

                    <!-- الأزرار -->
                    <div class="form-actions mt-2">
                        <button type="submit" class="btn btn-primary">بحث</button>
                        <button type="button" id="resetSearchBtn" class="btn btn-outline-warning">إلغاء</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- بطاقة النتائج -->
        <div class="card">
            <div class="card-body position-relative">
                <!-- مؤشر التحميل -->


                <!-- نتائج البحث -->
                <div id="resultsContainer">
                    @include('taskmanager::workspaces.partials.table', [
                        'workspaces' => $workspaces,
                    ])
                </div>
            </div>
        </div>


        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function() {
            let currentPage = 1;
            let isLoading = false;
            let searchXHR = null;
            let projectsChart = null;
            let membersChart = null;

            // تهيئة Select2
            $('.select2').select2({
                width: '100%',
                placeholder: 'اختر...',
                allowClear: true,
                language: {
                    noResults: function() {
                        return "لا توجد نتائج";
                    }
                }
            });

            // تهيئة الرسوم البيانية
            initCharts();

            // إرسال نموذج البحث
            $('#searchForm').submit(function(e) {
                e.preventDefault();
                if (!isLoading) {
                    currentPage = 1;
                    loadData();
                }
            });

            // البحث الفوري مع تأخير
            $('#searchForm input, #searchForm select').on('change input', function() {
                if (searchXHR) {
                    searchXHR.abort();
                }

                clearTimeout(window.searchTimeout);
                window.searchTimeout = setTimeout(function() {
                    if (!isLoading) {
                        currentPage = 1;
                        loadData();
                    }
                }, 500);
            });

            // إعادة تعيين البحث
            $('#resetSearch, #resetSearchBtn').click(function() {
                $('#searchForm')[0].reset();
                $('.select2').val(null).trigger('change');
                currentPage = 1;
                loadData();
            });

            // تحديث البيانات
            $('#refreshAnalyticsBtn').click(function() {
                if (!isLoading) {
                    loadData();
                    loadStats();
                }
            });

            // التصدير
            $('#exportAnalyticsBtn').click(function() {
                exportAnalytics();
            });

            // الطباعة
            $('#printAnalyticsBtn').click(function() {
                window.print();
            });

            // الترقيم
            $(document).on('click', '.page-link:not(.disabled):not(.active)', function(e) {
                e.preventDefault();
                if (!isLoading) {
                    currentPage = $(this).data('page');
                    loadData();
                    $('html, body').animate({
                        scrollTop: $("#resultsContainer").offset().top - 20
                    }, 300);
                }
            });

            // دالة تحميل البيانات
            function loadData() {
                if (isLoading) return;

                isLoading = true;
                showLoading();

                // جمع بيانات النموذج
                let formData = $('#searchForm').serializeArray()
                    .filter(item => item.name !== '_token')
                    .reduce((obj, item) => {
                        obj[item.name] = item.value;
                        return obj;
                    }, {});

                formData.page = currentPage;

                // إلغاء أي طلب سابق
                if (searchXHR) {
                    searchXHR.abort();
                }

                searchXHR = $.ajax({
                    url: "",
                    method: 'GET',
                    data: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#resultsContainer').html(response.html);
                            updatePaginationInfo(response);
                            updateCharts(response.chartData);
                        } else {
                            showError('حدث خطأ أثناء جلب البيانات');
                        }
                    },
                    error: function(xhr) {
                        if (xhr.statusText !== 'abort') {
                            handleAjaxError(xhr);
                        }
                    },
                    complete: function() {
                        isLoading = false;
                        hideLoading();
                        searchXHR = null;
                    }
                });
            }

            // دالة تحميل الإحصائيات
            function loadStats() {
                $.ajax({
                    url: "{{ route('workspaces.analytics.stats') }}",
                    method: 'GET',
                    success: function(response) {
                        if (response.success) {
                            updateStats(response.data);
                        }
                    },
                    error: function(xhr) {
                        console.error('Error loading stats:', xhr);
                    }
                });
            }

            // تحديث الإحصائيات
            function updateStats(stats) {
                $('#totalWorkspaces').text(stats.total_workspaces || 0);
                $('#activeProjects').text(stats.active_projects || 0);
                $('#totalMembers').text(stats.total_members || 0);
                $('#completionRate').text((stats.completion_rate || 0) + '%');

                // تحديث معدلات النمو (يمكن تخصيصها حسب البيانات المتوفرة)
                $('#workspacesGrowth').text('+' + (stats.workspaces_growth || 0) + '%');
                $('#projectsGrowth').text('+' + (stats.projects_growth || 0) + '%');
                $('#membersGrowth').text('+' + (stats.members_growth || 0) + '%');
                $('#completionGrowth').text('+' + (stats.completion_growth || 0) + '%');
            }

            // تهيئة الرسوم البيانية
            function initCharts() {
                // رسم بياني للمشاريع
                const projectsCtx = document.getElementById('projectsChart').getContext('2d');
                projectsChart = new Chart(projectsCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['مشاريع نشطة', 'مشاريع مكتملة', 'مشاريع متوقفة'],
                        datasets: [{
                            data: [0, 0, 0],
                            backgroundColor: ['#28a745', '#007bff', '#ffc107']
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                            }
                        }
                    }
                });

                // رسم بياني للأعضاء
                const membersCtx = document.getElementById('membersChart').getContext('2d');
                membersChart = new Chart(membersCtx, {
                    type: 'bar',
                    data: {
                        labels: [],
                        datasets: [{
                            label: 'عدد الأعضاء',
                            data: [],
                            backgroundColor: '#007bff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }

            // تحديث الرسوم البيانية
            function updateCharts(chartData) {
                if (chartData && projectsChart && membersChart) {
                    // تحديث رسم المشاريع
                    if (chartData.projects) {
                        projectsChart.data.datasets[0].data = [
                            chartData.projects.active || 0,
                            chartData.projects.completed || 0,
                            chartData.projects.on_hold || 0
                        ];
                        projectsChart.update();
                    }

                    // تحديث رسم الأعضاء
                    if (chartData.members) {
                        membersChart.data.labels = chartData.members.labels || [];
                        membersChart.data.datasets[0].data = chartData.members.data || [];
                        membersChart.update();
                    }
                }
            }

            // إظهار مؤشر التحميل
            function showLoading() {
                $('#loadingIndicator').show();
            }

            // إخفاء مؤشر التحميل
            function hideLoading() {
                $('#loadingIndicator').hide();
            }

            // تحديث معلومات الترقيم
            function updatePaginationInfo(response) {
                $('.results-info').text(`عرض ${response.from} إلى ${response.to} من ${response.total} مساحة عمل`);
            }

            // معالجة أخطاء AJAX
            function handleAjaxError(xhr) {
                let errorMsg = 'حدث خطأ في الاتصال بالخادم';
                if (xhr.status === 422) {
                    errorMsg = 'بيانات البحث غير صالحة';
                } else if (xhr.status === 404) {
                    errorMsg = 'الصفحة المطلوبة غير موجودة';
                } else if (xhr.status === 500) {
                    errorMsg = 'خطأ في الخادم الداخلي';
                }

                $('#resultsContainer').html(`
                    <div class="alert alert-danger text-center py-4">
                        <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                        <h5>${errorMsg}</h5>
                        <button class="btn btn-sm btn-outline-danger mt-2" onclick="loadData()">
                            <i class="fas fa-sync-alt mr-1"></i> إعادة المحاولة
                        </button>
                    </div>
                `);
            }

            // تصدير التحليلات
            function exportAnalytics() {
                let formData = $('#searchForm').serialize();
                let url = "{{ route('workspaces.analytics.export') }}?" + formData;

                // إنشاء رابط تحميل
                const link = document.createElement('a');
                link.href = url;
                link.download = 'workspace-analytics.xlsx';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }

            // دالة تحميل البيانات مع رقم الصفحة (للاستخدام من pagination.blade.php)
            window.loadDataWithPage = function(page) {
                if (!isLoading) {
                    currentPage = page;
                    loadData();
                    $('html, body').animate({
                        scrollTop: $("#resultsContainer").offset().top - 20
                    }, 300);
                }
            };

            // تحميل البيانات الأولية
            loadData();
            loadStats();
        });

        // دوال تبديل البحث
        function toggleSearchFields(button) {
            $('#searchForm').toggle();
            const isVisible = $('#searchForm').is(':visible');
            $(button).find('.hide-button-text').text(isVisible ? 'اخفاء' : 'اظهار');
            $(button).find('i').removeClass('fa-times fa-search').addClass(isVisible ? 'fa-times' : 'fa-search');
        }

        function toggleSearchText(button) {
            const isExpanded = $('#advancedSearchForm').hasClass('show');
            $(button).find('.button-text').text(isExpanded ? 'بسيط' : 'متقدم');
        }
    </script>
@endsection
