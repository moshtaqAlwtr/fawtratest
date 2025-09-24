@extends('master')

@section('title')
 سجل النشاطات
@stop

@section('css')
<link rel="stylesheet" href="{{asset('assets/css/style.css')}}">
<style>
    .timeline {
        position: relative;
        margin: 20px 0;
        padding: 0;
        list-style: none;
    }
    .timeline::before {
        content: '';
        position: absolute;
        top: 50px;
        bottom: 0;
        width: 4px;
        background: linear-gradient(180deg, #28a745 0%, #218838 100%);
        right: 50px;
        margin-right: -2px;
    }
    .timeline-item {
        margin: 0 0 20px;
        padding-right: 100px;
        position: relative;
        text-align: right;
    }
    .timeline-item::before {
        content: "\f067";
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
        position: absolute;
        right: 30px;
        top: 15px;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(145deg, #28a745, #218838);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: #ffffff;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    }
    .timeline-content {
        background: #ffffff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        position: relative;
    }
    .timeline-content .time {
        font-size: 14px;
        color: #6c757d;
        margin-bottom: 10px;
    }

    .filter-bar {
        background-color: #ffffff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
    }
    .timeline-day {
        background-color: #ffffff;
        padding: 10px 20px;
        border-radius: 30px;
        text-align: center;
        margin-bottom: 20px;
        font-weight: bold;
        color: #333;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        display: inline-block;
        position: relative;
        top: 0;
        right: 50px;
        transform: translateX(50%);
    }
    .filter-bar .form-control {
        border-radius: 8px;
        box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
    }
    .filter-bar .btn-outline-secondary {
        border-radius: 8px;
        box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
    }
    .timeline-date {
        text-align: center;
        font-size: 18px;
        font-weight: bold;
        margin: 20px 0;
        color: #333;
    }

    /* Loading Spinner */
    .loading-spinner {
        display: none;
        text-align: center;
        padding: 40px;
    }
    .spinner-border {
        width: 3rem;
        height: 3rem;
        border-width: 0.3em;
    }

    /* Pagination Styles */
    .pagination-container {
        background: #ffffff;
        padding: 20px;
        border-radius: 15px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        margin-top: 30px;
        text-align: center;
    }

    .pagination-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .pagination-btn {
        background: linear-gradient(145deg, #f8f9fa, #e9ecef);
        border: 2px solid #dee2e6;
        border-radius: 12px;
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 18px;
        color: #495057;
        position: relative;
        overflow: hidden;
    }

    .pagination-btn:hover:not(.disabled) {
        background: linear-gradient(145deg, #28a745, #20c997);
        border-color: #28a745;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(40, 167, 69, 0.3);
    }

    .pagination-btn.active {
        background: linear-gradient(145deg, #007bff, #0056b3);
        border-color: #007bff;
        color: white;
        box-shadow: 0 6px 15px rgba(0, 123, 255, 0.4);
    }

    .pagination-btn.disabled {
        background: #f8f9fa;
        border-color: #e9ecef;
        color: #6c757d;
        cursor: not-allowed;
        opacity: 0.6;
    }

    .pagination-info {
        background: linear-gradient(145deg, #e3f2fd, #bbdefb);
        padding: 12px 20px;
        border-radius: 25px;
        margin: 0 15px;
        font-weight: 600;
        color: #1976d2;
        font-size: 14px;
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    /* Animation for new content */
    .fade-in {
        animation: fadeIn 0.5s ease-in;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* RTL Support */
    .pagination-wrapper {
        direction: ltr;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .pagination-btn {
            width: 40px;
            height: 40px;
            font-size: 14px;
        }
        .pagination-info {
            margin: 10px 0;
            font-size: 12px;
        }
        .pagination-wrapper {
            flex-direction: column;
            gap: 15px;
        }
    }

    /* Tooltip for pagination buttons */
    .pagination-btn[data-tooltip] {
        position: relative;
    }

    .pagination-btn[data-tooltip]:hover::after {
        content: attr(data-tooltip);
        position: absolute;
        bottom: -35px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(0, 0, 0, 0.8);
        color: white;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 12px;
        white-space: nowrap;
        z-index: 1000;
    }
</style>
@endsection

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">سجل النشاطات</h2>
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="">الرئيسيه</a></li>
                        <li class="breadcrumb-item active">عرض</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card">
        <div class="container">
            <div class="row mt-4">
                <div class="col-12">
                    <!-- شريط التصفية -->
                    <div class="filter-bar d-flex justify-content-between align-items-center">
                        <div>
                            <button class="btn btn-outline-secondary"><i class="fas fa-th"></i></button>
                            <button class="btn btn-outline-secondary"><i class="fas fa-list"></i></button>
                        </div>
                        <div class="d-flex">
                            <form id="searchForm" class="d-flex">
                                <input type="text" id="searchInput" class="form-control me-2" placeholder="ابحث في الأحداث...">

                            </form>
                        </div>
                    </div>

                    <!-- Loading Spinner -->
                    <div class="loading-spinner" id="loadingSpinner">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only"></span>
                        </div>

                    </div>

                    <!-- محتوى السجلات -->
                    <div id="logsContent">
                        <!-- سيتم تحميل البيانات هنا بواسطة AJAX -->
                    </div>

                    <!-- Pagination -->
                    <div class="pagination-container" id="paginationContainer" style="display: none;">
                        <div class="pagination-wrapper">
                            <button class="pagination-btn" id="firstBtn" data-tooltip="الصفحة الأولى">
                                <i class="fas fa-angle-double-right"></i>
                            </button>
                            <button class="pagination-btn" id="prevBtn" data-tooltip="الصفحة السابقة">
                                <i class="fas fa-angle-right"></i>
                            </button>

                            <div class="pagination-info" id="paginationInfo">
                                صفحة 1 من 1
                            </div>

                            <button class="pagination-btn" id="nextBtn" data-tooltip="الصفحة التالية">
                                <i class="fas fa-angle-left"></i>
                            </button>
                            <button class="pagination-btn" id="lastBtn" data-tooltip="الصفحة الأخيرة">
                                <i class="fas fa-angle-double-left"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    let currentPage = 1;
    let totalPages = 1;
    let currentSearch = '';
    let isLoading = false;

    // تحميل البيانات الأولية
    loadLogs();

    // البحث
    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        currentSearch = $('#searchInput').val();
        currentPage = 1;
        loadLogs();
    });

    // البحث المباشر أثناء الكتابة (مع تأخير)
    let searchTimeout;
    $('#searchInput').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            currentSearch = $('#searchInput').val();
            currentPage = 1;
            loadLogs();
        }, 500);
    });

    // أزرار التنقل
    $('#firstBtn').on('click', function() {
        if (currentPage > 1) {
            currentPage = 1;
            loadLogs();
        }
    });

    $('#prevBtn').on('click', function() {
        if (currentPage > 1) {
            currentPage--;
            loadLogs();
        }
    });

    $('#nextBtn').on('click', function() {
        if (currentPage < totalPages) {
            currentPage++;
            loadLogs();
        }
    });

    $('#lastBtn').on('click', function() {
        if (currentPage < totalPages) {
            currentPage = totalPages;
            loadLogs();
        }
    });

    function loadLogs() {
        if (isLoading) return;

        isLoading = true;
        showLoading();

        $.ajax({
            url: '{{ route("logs.index") }}',
            method: 'GET',
            data: {
                page: currentPage,
                search: currentSearch
            },
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.success) {
                    renderLogs(response.data);
                    updatePagination(response.pagination);
                } else {
                    showError('حدث خطأ في تحميل البيانات');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                showError('حدث خطأ في الاتصال بالخادم');
            },
            complete: function() {
                isLoading = false;
                hideLoading();
            }
        });
    }

    function renderLogs(logs) {
        let html = '';

        if (Object.keys(logs).length === 0) {
            html = `
                <div class="alert alert-info text-center fade-in" role="alert">
                    <i class="fas fa-info-circle fa-2x mb-3"></i>
                    <h4>لا توجد سجلات</h4>
                    <p class="mb-0">لا توجد سجلات تطابق معايير البحث الحالية</p>
                </div>
            `;
        } else {
            let previousDate = null;

            Object.keys(logs).forEach(function(date) {
                const currentDate = new Date(date);
                const dayLogs = logs[date];

                // إضافة تاريخ إذا كان هناك فجوة كبيرة
                if (previousDate) {
                    const diffInDays = Math.abs((currentDate - previousDate) / (1000 * 60 * 60 * 24));
                    if (diffInDays > 7) {
                        html += `<div class="timeline-date fade-in">
                                    <h4>${formatDate(currentDate)}</h4>
                                </div>`;
                    }
                }

                html += `<div class="timeline-day fade-in">${getDayName(currentDate)}</div>`;
                html += '<ul class="timeline fade-in">';

                dayLogs.forEach(function(log) {
                    if (log) {
                        const logTime = new Date(log.created_at);
                        const userName = log.user ? log.user.name : 'مستخدم غير معروف';
                        const branchName = (log.user && log.user.branch) ? log.user.branch.name : 'فرع غير معروف';
                        const description = log.description || 'لا يوجد وصف';

                        html += `
                            <li class="timeline-item">
                                <div class="timeline-content">
                                    <div class="time">
                                        <i class="far fa-clock"></i> ${formatTime(logTime)}
                                    </div>
                                    <div>
                                        <strong>${userName}</strong>
                                        <div>${parseMarkdown(description)}</div>
                                        <div class="text-muted">${branchName}</div>
                                    </div>
                                </div>
                            </li>
                        `;
                    }
                });

                html += '</ul>';
                previousDate = currentDate;
            });
        }

        $('#logsContent').html(html);
    }

    function updatePagination(pagination) {
        currentPage = pagination.current_page;
        totalPages = pagination.last_page;

        // تحديث معلومات التصفح
        $('#paginationInfo').text(`صفحة ${currentPage} من ${totalPages} (${pagination.total} سجل)`);

        // تحديث حالة الأزرار
        $('#firstBtn, #prevBtn').toggleClass('disabled', !pagination.has_previous_pages);
        $('#nextBtn, #lastBtn').toggleClass('disabled', !pagination.has_more_pages);

        // إظهار/إخفاء التصفح
        $('#paginationContainer').toggle(totalPages > 1);
    }

    function showLoading() {
        $('#loadingSpinner').fadeIn(300);
        $('#logsContent').fadeTo(300, 0.3);
    }

    function hideLoading() {
        $('#loadingSpinner').fadeOut(300);
        $('#logsContent').fadeTo(300, 1);
    }

    function showError(message) {
        const errorHtml = `
            <div class="alert alert-danger text-center fade-in" role="alert">
                <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                <h4>خطأ</h4>
                <p class="mb-0">${message}</p>
                <button class="btn btn-outline-danger mt-3" onclick="location.reload()">
                    <i class="fas fa-redo"></i> إعادة المحاولة
                </button>
            </div>
        `;
        $('#logsContent').html(errorHtml);
    }

    // دوال مساعدة
    function formatDate(date) {
        return date.toLocaleDateString('ar-SA', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }

    function formatTime(date) {
        return date.toLocaleTimeString('ar-SA', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false
        });
    }

    function getDayName(date) {
        const days = ['الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'];
        return days[date.getDay()];
    }

    function parseMarkdown(text) {
        // تحويل Markdown بسيط إلى HTML
        return text
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\*(.*?)\*/g, '<em>$1</em>')
            .replace(/\n/g, '<br>');
    }
});
</script>
@endsection
