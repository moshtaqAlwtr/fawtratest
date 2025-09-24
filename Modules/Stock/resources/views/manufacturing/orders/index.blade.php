@extends('master')

@section('title')
أوامر التصنيع
@stop

@section('content')

    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">أوامر التصنيع</h2>
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

    <div class="content-body">
        <!-- بطاقة الإضافة -->
        <div class="card">
            <div class="card-content">
                <div class="card-body">
                    <div class="card-title">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div>
                                <span class="badge badge-info" id="total-orders">إجمالي: 0</span>
                            </div>
                            <div>
                                <button class="btn btn-outline-primary btn-sm me-2" id="export-btn">
                                    <i class="fa fa-download me-1"></i>تصدير
                                </button>
                                <a href="{{ route('manufacturing.orders.create') }}" class="btn btn-outline-success">
                                    <i class="fa fa-plus me-2"></i>أضف أمر تصنيع
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('layouts.alerts.error')
        @include('layouts.alerts.success')

        <!-- كرت البحث -->
        <div class="card mt-3">
            <div class="card-body">
                <h4 class="card-title">بحث وتصنيف</h4>
                <form id="filter-form">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="search" name="search" placeholder="البحث بواسطة الاسم أو الكود">
                        </div>
                        <div class="col-md-4">
                            <select class="form-control" id="product_id" name="product_id">
                                <option value="">فرز بواسطة المنتج</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select class="form-control" id="material_list_id" name="material_list_id">
                                <option value="">فرز بواسطة قائمة المواد</option>
                                @foreach($materialLists as $list)
                                    <option value="{{ $list->id }}">{{ $list->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <select class="form-control" id="status" name="status">
                                <option value="">إختر الحالة</option>
                                <option value="active">نشط</option>
                                <option value="in_progress">قيد التنفيذ</option>
                                <option value="completed">منتهي</option>
                                <option value="cancelled">ملغى</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select class="form-control" id="client_id" name="client_id">
                                <option value="">إختر عميل</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}">{{ $client->trade_name }} - #{{ $client->code }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select class="form-control" id="production_stage_id" name="production_stage_id">
                                <option value="">إختر المرحلة الإنتاجية</option>
                                @foreach($productionStages as $stage)
                                    <option value="{{ $stage->id }}">{{ $stage->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center">
                                <label class="me-2">عرض:</label>
                                <select class="form-control" id="per_page" name="per_page" style="width: auto;">
                                    <option value="10">10</option>
                                    <option value="15" selected>15</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                                <span class="me-2 ms-2">سجل لكل صفحة</span>
                            </div>
                        </div>
                        <div class="col-md-4 text-right">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fa fa-search me-1"></i>بحث
                            </button>
                            <button type="button" class="btn btn-secondary" id="reset-btn">
                                <i class="fa fa-refresh me-1"></i>إعادة تعيين
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Loading -->
        <div class="text-center" id="loading" style="display: none;">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">جاري التحميل...</span>
            </div>
        </div>

        <!-- كرت الجدول -->
        <div class="card mt-4">
            <div class="card-body">
                <div id="orders-container">
                    <!-- سيتم عرض البيانات هنا عبر AJAX -->
                </div>

                <!-- Pagination -->
                <div id="pagination-container" class="d-flex justify-content-center mt-3">
                    <!-- سيتم عرض التقسيم هنا -->
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    let currentPage = 1;
    let isLoading = false;

    // تحميل البيانات
    function loadOrders(page = 1) {
        if (isLoading) return;

        isLoading = true;
        $('#loading').show();

        let formData = $('#filter-form').serialize();
        formData += '&page=' + page;

        $.ajax({
            url: '{{ route("manufacturing.orders.data") }}',
            type: 'GET',
            data: formData,
            success: function(response) {
                if (response.success) {
                    displayOrders(response.data.orders);
                    updatePagination(response.data.pagination);
                    updateTotalCount(response.data.pagination.total);
                } else {
                    showAlert('error', response.message || 'حدث خطأ في جلب البيانات');
                }
            },
            error: function(xhr) {
                console.error('Error:', xhr);
                showAlert('error', 'حدث خطأ في الاتصال بالخادم');
            },
            complete: function() {
                isLoading = false;
                $('#loading').hide();
            }
        });
    }

    // عرض الأوامر
    function displayOrders(orders) {
        if (orders.length === 0) {
            $('#orders-container').html(`
                <div class="alert alert-danger text-center" role="alert">
                    <p class="mb-0">لا توجد أوامر تصنيع تطابق معايير البحث!</p>
                </div>
            `);
            return;
        }

        let html = `
            <table class="table table-striped">
                <thead class="thead-light">
                    <tr>
                        <th>الاسم</th>
                        <th>المنتج الرئيسي</th>
                        <th>الكمية</th>
                        <th>التاريخ</th>
                        <th>التكلفة الإجمالية</th>
                        <th>العميل</th>
                        <th>الحالة</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
        `;

        orders.forEach(function(order) {
            let statusBadge = getStatusBadge(order.status);
// في دالة displayOrders، عدّل الروابط كالتالي:
html += `
    <tr>
        <td>
            <strong>${order.name}</strong><br>
            <small class="text-muted">${order.code}</small>
        </td>
        <td>${order.product ? order.product.name : '-'}</td>
        <td><strong>${order.quantity}</strong></td>
        <td>
            <div>يبدأ : <strong>${formatDate(order.from_date)}</strong></div>
            <div>ينتهي : <strong>${formatDate(order.to_date)}</strong></div>
        </td>
        <td><strong>${formatNumber(order.last_total_cost)} ر.س</strong></td>
        <td>
            <strong>${order.client ? order.client.trade_name : '-'}</strong><br>
            <small class="text-muted">#${order.client ? order.client.code : '-'}</small>
        </td>
        <td>${statusBadge}</td>
        <td>
            <div class="btn-group">
                <div class="dropdown">
                    <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 btn-sm" type="button" data-toggle="dropdown"></button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="{{ route('manufacturing.orders.show', '') }}/${order.id}">
                            <i class="fa fa-eye me-2 text-primary"></i>عرض
                        </a>
                        <a class="dropdown-item" href="{{ route('manufacturing.orders.edit', '') }}/${order.id}">
                            <i class="fa fa-edit me-2 text-success"></i>تعديل
                        </a>
                        <a class="dropdown-item text-danger delete-order" href="#" data-id="${order.id}" data-name="${order.name}">
                            <i class="fa fa-trash me-2"></i>حذف
                        </a>
                    </div>
                </div>
            </div>
        </td>
    </tr>
`;
        });

        html += `
                </tbody>
            </table>
        `;

        $('#orders-container').html(html);
    }

    // تحديث التقسيم
    function updatePagination(pagination) {
        if (pagination.last_page <= 1) {
            $('#pagination-container').html('');
            return;
        }

        let html = `
            <nav>
                <ul class="pagination">
                    <li class="page-item ${pagination.current_page === 1 ? 'disabled' : ''}">
                        <a class="page-link" href="#" data-page="${pagination.current_page - 1}">السابق</a>
                    </li>
        `;

        // عرض أرقام الصفحات
        let start = Math.max(1, pagination.current_page - 2);
        let end = Math.min(pagination.last_page, pagination.current_page + 2);

        for (let i = start; i <= end; i++) {
            html += `
                <li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>
            `;
        }

        html += `
                    <li class="page-item ${pagination.current_page === pagination.last_page ? 'disabled' : ''}">
                        <a class="page-link" href="#" data-page="${pagination.current_page + 1}">التالي</a>
                    </li>
                </ul>
            </nav>
            <div class="text-center mt-2">
                <small class="text-muted">
                    عرض ${pagination.from} إلى ${pagination.to} من أصل ${pagination.total} سجل
                </small>
            </div>
        `;

        $('#pagination-container').html(html);
    }

    // تحديث العداد الكلي
    function updateTotalCount(total) {
        $('#total-orders').text('إجمالي: ' + total);
    }

    // دالة للحصول على شارة الحالة
    function getStatusBadge(status) {
        const statuses = {
            'active': '<span class="badge badge-success">نشط</span>',
            'in_progress': '<span class="badge badge-primary">قيد التنفيذ</span>',
            'completed': '<span class="badge badge-info">منتهي</span>',
            'cancelled': '<span class="badge badge-danger">ملغى</span>'
        };
        return statuses[status] || '<span class="badge badge-secondary">غير محدد</span>';
    }

    // تنسيق التاريخ
    function formatDate(date) {
        if (!date) return '-';
        return new Date(date).toLocaleDateString('ar-SA');
    }

    // تنسيق الأرقام
    function formatNumber(number) {
        return parseFloat(number).toLocaleString('ar-SA');
    }

    // عرض التنبيهات
    function showAlert(type, message) {
        const alertClass = type === 'error' ? 'alert-danger' : 'alert-success';
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        `;
        $('.content-body').prepend(alertHtml);

        // إخفاء التنبيه بعد 5 ثوانٍ
        setTimeout(function() {
            $('.alert').fadeOut();
        }, 5000);
    }

    // الأحداث
    $('#filter-form').on('submit', function(e) {
        e.preventDefault();
        currentPage = 1;
        loadOrders(1);
    });

    // البحث المباشر
    $('#search').on('input', debounce(function() {
        currentPage = 1;
        loadOrders(1);
    }, 500));

    // تغيير الفلاتر
    $('#product_id, #material_list_id, #status, #client_id, #production_stage_id, #per_page').on('change', function() {
        currentPage = 1;
        loadOrders(1);
    });

    // إعادة تعيين الفلاتر
    $('#reset-btn').on('click', function() {
        $('#filter-form')[0].reset();
        currentPage = 1;
        loadOrders(1);
    });

    // التقسيم
    $(document).on('click', '.page-link', function(e) {
        e.preventDefault();
        let page = $(this).data('page');
        if (page && page !== currentPage) {
            currentPage = page;
            loadOrders(page);
        }
    });

    // حذف الأمر
    $(document).on('click', '.delete-order', function(e) {
        e.preventDefault();
        let orderId = $(this).data('id');
        let orderName = $(this).data('name');

        if (confirm(`هل أنت متأكد من حذف الأمر: ${orderName}؟`)) {
            $.ajax({
                url: `/manufacturing/orders/${orderId}`,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        showAlert('success', 'تم حذف الأمر بنجاح');
                        loadOrders(currentPage);
                    } else {
                        showAlert('error', response.message || 'حدث خطأ في الحذف');
                    }
                },
                error: function(xhr) {
                    showAlert('error', 'حدث خطأ في الحذف');
                }
            });
        }
    });

    // التصدير
    $('#export-btn').on('click', function() {
        let formData = $('#filter-form').serialize();

    });

    // دالة التأخير للبحث
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // تحميل البيانات عند بداية الصفحة
    loadOrders(1);
});
</script>
@endsection
