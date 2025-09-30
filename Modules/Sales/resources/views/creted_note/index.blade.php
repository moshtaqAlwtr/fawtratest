@extends('master')

@section('title')
    الإشعارات الدائنة
@stop

@section('css')
<style>
    .form-control {
        margin-bottom: 10px;
    }

    #loading-indicator {
        background: rgba(255, 255, 255, 0.9);
        border-radius: 0.375rem;
    }

    .spinner-border {
        width: 2rem;
        height: 2rem;
    }

    @media (max-width: 768px) {
        .content-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .content-header-title {
            font-size: 1.5rem;
        }

        .btn {
            width: 100%;
            margin-bottom: 10px;
        }

        .card {
            margin: 10px;
            padding: 10px;
        }

        .table {
            font-size: 0.8rem;
            width: 100%;
            overflow-x: auto;
        }

        .table th,
        .table td {
            white-space: nowrap;
        }

        .form-check {
            margin-bottom: 10px;
        }

        .form-control {
            width: 100%;
        }

        .dropdown-menu {
            min-width: 200px;
        }
    }

    @media (max-width: 480px) {
        .table th,
        .table td {
            font-size: 0.7rem;
        }
    }
</style>
@endsection

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">إدارة الإشعارات الدائنة</h2>
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard_sales.index') }}">الرئيسية</a></li>
                        <li class="breadcrumb-item active">عرض</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger">
        @foreach ($errors->all() as $error)
            <p class="mb-0">{{ $error }}</p>
        @endforeach
    </div>
@endif

<div class="content-body">
    <div class="container-fluid">
        <!-- شريط الأدوات العلوي -->
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <!-- معلومات الترقيم -->
                        <nav aria-label="Page navigation">
                            <ul class="pagination pagination-sm mb-0">
                                <li class="page-item mx-2">
                                    <span class="text-muted pagination-info">صفحة 1 من 1</span>
                                </li>
                            </ul>
                        </nav>
                        <!-- عداد النتائج -->
                        <span class="text-muted mx-2 results-info">0 نتيجة</span>
                    </div>

                    <div class="d-flex flex-wrap justify-content-between">
                        <a href="{{ route('CreditNotes.create') }}" class="btn btn-success btn-sm flex-fill me-1 mb-1">
                            <i class="fas fa-plus-circle me-1"></i>إشعار دائن جديد
                        </a>
                        <button class="btn btn-outline-primary btn-sm flex-fill mb-1">
                            <i class="fas fa-cloud-upload-alt me-1"></i>استيراد
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- نموذج البحث -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center p-2">
                <div class="d-flex gap-2">
                    <span class="hide-button-text">بحث وتصفية</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-outline-secondary btn-sm" onclick="toggleSearchFields(this)">
                        <i class="fa fa-times"></i>
                        <span class="hide-button-text">إخفاء</span>
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
                    @csrf
                    <div class="row g-3">
                        <!-- الحقول الأساسية -->
                        <div class="col-md-6">
                            <select name="client_id" class="form-control select2">
                                <option value="">أي العميل</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}">
                                        {{ $client->trade_name }} ({{ $client->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <input type="text" name="invoice_number" class="form-control"
                                placeholder="رقم الإشعار">
                        </div>
                    </div>

                    <!-- البحث المتقدم -->
                    <div class="collapse" id="advancedSearchForm">
                        <div class="row g-3 mt-2">
                            <div class="col-md-3">
                                <input type="text" name="total_from" class="form-control"
                                    placeholder="الإجمالي أكبر من">
                            </div>

                            <div class="col-md-3">
                                <input type="text" name="total_to" class="form-control"
                                    placeholder="الإجمالي أصغر من">
                            </div>

                            <div class="col-md-3">
                                <input type="date" name="from_date_1" class="form-control"
                                    placeholder="التاريخ من">
                            </div>

                            <div class="col-md-3">
                                <input type="date" name="to_date_1" class="form-control"
                                    placeholder="التاريخ إلى">
                            </div>

                            <div class="col-md-12">
                                <select name="created_by" class="form-control select2">
                                    <option value="">أضيفت بواسطة</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions mt-2">
                        <button type="submit" class="btn btn-primary">بحث</button>
                        <button type="button" id="resetSearch" class="btn btn-outline-warning">إلغاء</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- جدول النتائج -->
        <div class="card">
            <div class="card-body">
                <div id="results-container">
                    <!-- سيتم تحميل الجدول هنا عبر AJAX -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<meta name="csrf-token" content="{{ csrf_token() }}">

<script>
$(document).ready(function() {
    // تحميل البيانات الأولية
    loadData();

    // البحث عند إرسال النموذج
    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        loadData();
    });

    // البحث الفوري عند تغيير قيم المدخلات
    $('#searchForm input, #searchForm select').on('change input', function() {
        clearTimeout(window.searchTimeout);
        window.searchTimeout = setTimeout(function() {
            loadData();
        }, 500);
    });

    // إعادة تعيين الفلاتر
    $('#resetSearch').on('click', function() {
        $('#searchForm')[0].reset();
        loadData();
    });

    // التعامل مع الترقيم
    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        let url = $(this).attr('href');
        if (url) {
            let page = new URL(url).searchParams.get('page');
            loadData(page);
        }
    });

    // دالة تحميل البيانات
    function loadData(page = 1) {
        showLoading();

        let formData = $('#searchForm').serialize();
        if (page > 1) {
            formData += '&page=' + page;
        }

        $.ajax({
            url: '{{ route("CreditNotes.index") }}',
            method: 'GET',
            data: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.success) {
                    $('#results-container').html(response.data);
                    updatePaginationInfo(response);
                    initializeEvents();
                }
            },
            error: function(xhr, status, error) {
                console.error('خطأ في تحميل البيانات:', error);
                $('#results-container').html(
                    '<div class="alert alert-danger text-center">' +
                    '<p class="mb-0">حدث خطأ في تحميل البيانات. يرجى المحاولة مرة أخرى.</p>' +
                    '</div>'
                );
            },
            complete: function() {
                hideLoading();
            }
        });
    }

    function showLoading() {
        $('#results-container').css('opacity', '0.6');
        if ($('#loading-indicator').length === 0) {
            $('#results-container').prepend(`
                <div id="loading-indicator" class="text-center p-3">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            `);
        }
    }

    function hideLoading() {
        $('#loading-indicator').remove();
        $('#results-container').css('opacity', '1');
    }

    function updatePaginationInfo(response) {
        $('.pagination-info').text(`صفحة ${response.current_page} من ${response.last_page}`);
        if (response.total > 0) {
            $('.results-info').text(`${response.from}-${response.to} من ${response.total}`);
        } else {
            $('.results-info').text('لا توجد نتائج');
        }
    }

    function initializeEvents() {
        // أحداث الحذف
        $('.delete-credit').off('click').on('click', function(e) {
            e.preventDefault();
            const creditId = $(this).data('id');

            if (confirm('هل أنت متأكد من حذف هذا الإشعار الدائن؟')) {
                deleteCredit(creditId);
            }
        });

        // تحديد الكل
        $('#selectAll').off('change').on('change', function() {
            $('.credit-checkbox').prop('checked', $(this).prop('checked'));
        });

        $('.credit-checkbox').off('change').on('change', function() {
            let totalCheckboxes = $('.credit-checkbox').length;
            let checkedCheckboxes = $('.credit-checkbox:checked').length;
            $('#selectAll').prop('checked', totalCheckboxes === checkedCheckboxes);
        });
    }

    function deleteCredit(creditId) {
        const row = $(`.delete-credit[data-id="${creditId}"]`).closest('tr');
        row.css('opacity', '0.5');

        $.ajax({
            url: `/credit-notes/${creditId}`,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                alert('تم حذف الإشعار الدائن بنجاح');
                loadData();
            },
            error: function() {
                row.css('opacity', '1');
                alert('حدث خطأ أثناء حذف الإشعار الدائن');
            }
        });
    }

    initializeEvents();
});

// دوال التحكم في البحث المتقدم
function toggleSearchText(button) {
    const buttonText = button.querySelector('.button-text');
    const advancedFields = document.querySelectorAll('.advanced-field');

    if (buttonText.textContent.trim() === 'متقدم') {
        buttonText.textContent = 'بحث بسيط';
        advancedFields.forEach(field => field.style.display = 'block');
    } else {
        buttonText.textContent = 'متقدم';
        advancedFields.forEach(field => field.style.display = 'none');
    }
}

function toggleSearchFields(button) {
    const searchForm = document.getElementById('searchForm');
    const buttonText = button.querySelector('.hide-button-text');
    const icon = button.querySelector('i');

    if (buttonText.textContent === 'إخفاء') {
        searchForm.style.display = 'none';
        buttonText.textContent = 'إظهار';
        icon.classList.remove('fa-times');
        icon.classList.add('fa-eye');
    } else {
        searchForm.style.display = 'block';
        buttonText.textContent = 'إخفاء';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-times');
    }
}
</script>
@endsection