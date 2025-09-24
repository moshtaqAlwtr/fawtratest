@extends('master')

@section('title')
التكاليف غير المباشرة
@stop

@section('css')
<style>
    .filter-card {
        background: linear-gradient(135deg, #ffffff 0%, #ffffff 100%);
        color: black;
        border: none;
        border-radius: 15px;
    }
    .filter-card .card-header {
        background: transparent;
        border-bottom: 1px solid rgba(255,255,255,0.2);
    }
    .stats-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }
    .stats-card:hover {
        transform: translateY(-5px);
    }
    .table-container {
        background: white;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255,255,255,0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
    }
    .btn-filter {
        background: linear-gradient(45deg, #28a745, #20c997);
        border: none;
        color: white;
        border-radius: 25px;
        padding: 10px 30px;
    }
    .btn-reset {
        background: linear-gradient(45deg, #6c757d, #495057);
        border: none;
        color: white;
        border-radius: 25px;
        padding: 10px 30px;
    }
    .pagination-wrapper {
        display: flex;
        justify-content: between;
        align-items: center;
        margin-top: 20px;
    }


    .table-actions .dropdown-toggle::after {
        display: none;
    }
</style>
@endsection

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">
                        <i class="feather icon-layers mr-1"></i>التكاليف غير المباشرة
                    </h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسية</a></li>
                            <li class="breadcrumb-item active">التكاليف غير المباشرة</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.alerts.success')
    @include('layouts.alerts.error')

    <!-- إحصائيات سريعة -->
    <div class="row mb-4" id="statsContainer">
        <div class="col-lg-3 col-md-6 col-12">
            <div class="card stats-card text-center">
                <div class="card-body">
                    <div class="text-primary mb-2">
                        <i class="feather icon-layers" style="font-size: 2rem;"></i>
                    </div>
                    <h4 class="text-primary mb-1" id="totalCosts">0</h4>
                    <small class="text-muted">إجمالي السجلات</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-12">
            <div class="card stats-card text-center">
                <div class="card-body">
                    <div class="text-success mb-2">
                        <i class="feather icon-dollar-sign" style="font-size: 2rem;"></i>
                    </div>
                    <h4 class="text-success mb-1" id="totalAmount">0 ر.س</h4>
                    <small class="text-muted">إجمالي المبلغ</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-12">
            <div class="card stats-card text-center">
                <div class="card-body">
                    <div class="text-warning mb-2">
                        <i class="feather icon-trending-up" style="font-size: 2rem;"></i>
                    </div>
                    <h4 class="text-warning mb-1" id="avgAmount">0 ر.س</h4>
                    <small class="text-muted">متوسط التكلفة</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-12">
            <div class="card stats-card text-center">
                <div class="card-body">
                    <div class="text-info mb-2">
                        <i class="feather icon-calendar" style="font-size: 2rem;"></i>
                    </div>
                    <h4 class="text-info mb-1" id="thisMonth">0 ر.س</h4>
                    <small class="text-muted">هذا الشهر</small>
                </div>
            </div>
        </div>
    </div>

    <!-- بطاقة الإضافة -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">إدارة التكاليف غير المباشرة</h5>
                    <small class="text-muted">يمكنك إضافة وإدارة التكاليف غير المباشرة من هنا</small>
                </div>
                <div class="btn-group">
                    <a href="{{ route('manufacturing.indirectcosts.create') }}" class="btn btn-gradient-primary">
                        <i class="feather icon-plus mr-1"></i>أضف تكاليف جديدة
                    </a>
                    <button type="button" class="btn btn-outline-info" id="exportBtn">
                        <i class="feather icon-download mr-1"></i>تصدير
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- بطاقة الفلترة -->
    <div class="card filter-card mb-4">
        <div class="card-header">
            <h4 class="card-title mb-0">
                <i class="feather icon-filter mr-2"></i>البحث والفلترة
            </h4>
        </div>
        <div class="card-body">
            <form id="filterForm" class="filter-form">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">الحساب</label>
                        <select name="account_id" class="form-control select2">
                            <option value="">-- جميع الحسابات --</option>
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}">{{ $account->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">المنتجات</label>
                        <select name="product_id" class="form-control select2">
                            <option value="">-- جميع المنتجات --</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">أمر التصنيع</label>
                        <select name="manufacturing_order_id" class="form-control select2">
                            <option value="">-- جميع الأوامر --</option>
                            @foreach($manufacturing_orders as $order)
                                <option value="{{ $order->id }}">{{ $order->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">نوع التوزيع</label>
                        <select name="based_on" class="form-control select2">
                            <option value="">-- جميع الأنواع --</option>
                            <option value="1">بناءً على الكمية</option>
                            <option value="2">بناءً على التكلفة</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">التاريخ من (البداية)</label>
                        <input type="date" name="date_from_start" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">التاريخ من (النهاية)</label>
                        <input type="date" name="date_from_end" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">التاريخ إلى (البداية)</label>
                        <input type="date" name="date_to_start" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">التاريخ إلى (النهاية)</label>
                        <input type="date" name="date_to_end" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">المبلغ من</label>
                        <input type="number" name="total_min" class="form-control" placeholder="0.00" step="0.01">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">المبلغ إلى</label>
                        <input type="number" name="total_max" class="form-control" placeholder="0.00" step="0.01">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">ترتيب حسب</label>
                        <select name="sort_field" class="form-control select2">
                            <option value="created_at">تاريخ الإنشاء</option>
                            <option value="total">المبلغ</option>
                            <option value="from_date">التاريخ من</option>
                            <option value="to_date">التاريخ إلى</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">اتجاه الترتيب</label>
                        <select name="sort_direction" class="form-control select2">
                            <option value="desc">تنازلي</option>
                            <option value="asc">تصاعدي</option>
                        </select>
                    </div>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    <button type="submit" class="btn btn-filter me-3">
                        <i class="feather icon-search mr-1"></i>بحث
                    </button>
                    <button type="button" class="btn btn-reset" id="resetFilters">
                        <i class="feather icon-refresh-ccw mr-1"></i>إعادة تعيين
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- بطاقة الجدول -->
    <div class="table-container position-relative">
        <div class="loading-overlay" id="loadingOverlay" style="display: none;">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">جاري التحميل...</span>
            </div>
        </div>

        <div class="card-body">
            <!-- معلومات النتائج -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="mb-0">النتائج</h5>
                    <small class="text-muted" id="resultsInfo">جاري التحميل...</small>
                </div>
                <div>
                    <select id="perPageSelect" class="form-control form-control-sm" style="width: auto;">
                        <option value="10">10 سجل</option>
                        <option value="25">25 سجل</option>
                        <option value="50">50 سجل</option>
                        <option value="100">100 سجل</option>
                    </select>
                </div>
            </div>

            <!-- الجدول -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>الحساب</th>
                            <th>النوع</th>
                            <th>فترة التكلفة</th>
                            <th>عدد العناصر</th>
                            <th>إجمالي التكلفة</th>
                            <th>تاريخ الإنشاء</th>
                            <th width="120">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody id="dataTableBody">
                        <!-- سيتم ملؤها عبر Ajax -->
                    </tbody>
                </table>
            </div>

            <!-- رسالة عدم وجود بيانات -->
            <div id="noDataMessage" class="text-center py-5" style="display: none;">
                <div class="mb-3">
                    <i class="feather icon-inbox text-muted" style="font-size: 4rem;"></i>
                </div>
                <h5 class="text-muted">لا توجد بيانات</h5>
                <p class="text-muted">لم يتم العثور على تكاليف غير مباشرة تطابق معايير البحث</p>
                <button class="btn btn-outline-primary" id="clearFiltersBtn">
                    <i class="feather icon-refresh-ccw mr-1"></i>مسح الفلاتر
                </button>
            </div>

            <!-- التصفح -->
            <div class="pagination-wrapper" id="paginationWrapper" style="display: none;">
                <div class="pagination-info">
                    <small class="text-muted" id="paginationInfo"></small>
                </div>
                <nav aria-label="صفحات النتائج">
                    <ul class="pagination mb-0" id="paginationLinks">
                        <!-- سيتم ملؤها عبر Ajax -->
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <!-- Modal للحذف -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="feather icon-trash-2 mr-2"></i>تأكيد الحذف
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <i class="feather icon-alert-triangle text-warning" style="font-size: 3rem;"></i>
                        <h5 class="mt-3">هل أنت متأكد من الحذف؟</h5>
                        <p class="text-muted">هذا الإجراء لا يمكن التراجع عنه</p>
                        <div class="alert alert-warning">
                            <strong>الحساب:</strong> <span id="deleteAccountName"></span><br>
                            <strong>المبلغ:</strong> <span id="deleteAmount"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="feather icon-x mr-1"></i>إلغاء
                    </button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                        <i class="feather icon-trash-2 mr-1"></i>حذف
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.all.min.js"></script>

<script>
    let currentPage = 1;
    let currentFilters = {};
    let deleteId = null;

    $(document).ready(function() {
        // تحميل البيانات في البداية
        loadData();
        loadStats();

        // معالج إرسال الفلترة
        $('#filterForm').on('submit', function(e) {
            e.preventDefault();
            currentPage = 1;
            loadData();
        });

        // إعادة تعيين الفلاتر
        $('#resetFilters, #clearFiltersBtn').on('click', function() {
            $('#filterForm')[0].reset();
            currentPage = 1;
            currentFilters = {};
            loadData();
        });

        // تغيير عدد السجلات في الصفحة
        $('#perPageSelect').on('change', function() {
            currentPage = 1;
            loadData();
        });

        // معالج الحذف
        $(document).on('click', '.delete-btn', function() {
            deleteId = $(this).data('id');
            const accountName = $(this).data('account');
            const amount = $(this).data('amount');

            $('#deleteAccountName').text(accountName);
            $('#deleteAmount').text(amount + ' ر.س');
            $('#deleteModal').modal('show');
        });

        // تأكيد الحذف
        $('#confirmDeleteBtn').on('click', function() {
            if (deleteId) {
                deleteRecord(deleteId);
            }
        });

        // معالج التصدير
        $('#exportBtn').on('click', function() {
            exportData();
        });

        // معالج التصفح
        $(document).on('click', '.pagination a', function(e) {
            e.preventDefault();
            const page = $(this).data('page');
            if (page) {
                currentPage = page;
                loadData();
            }
        });
    });

    function loadData() {
        showLoading();

        // جمع بيانات الفلترة
        const formData = new FormData($('#filterForm')[0]);
        const filters = {};
        for (let [key, value] of formData.entries()) {
            if (value) filters[key] = value;
        }

        filters.page = currentPage;
        filters.per_page = $('#perPageSelect').val();
        currentFilters = filters;

        $.ajax({
            url: '{{ route("manufacturing.indirectcosts.getData") }}',
            method: 'GET',
            data: filters,
            success: function(response) {
                hideLoading();

                if (response.success) {
                    renderTable(response.data);
                    renderPagination(response.pagination);
                    updateResultsInfo(response.pagination, response.summary);

                    if (response.data.length === 0) {
                        showNoData();
                    } else {
                        hideNoData();
                    }
                } else {
                    showError('فشل في تحميل البيانات');
                }
            },
            error: function() {
                hideLoading();
                showError('حدث خطأ في الاتصال بالخادم');
            }
        });
    }

    function renderTable(data) {
        let html = '';

        data.forEach(function(item) {
            const basedOnText = item.based_on == 1 ? 'بناءً على الكمية' : 'بناءً على التكلفة';
            const badgeClass = item.based_on == 1 ? 'badge-primary' : 'badge-success';

            html += `
                <tr>
                    <td>
                        <div>
                            <strong>${item.account.name}</strong>
                            <br>
                            <small class="text-muted">#${item.account.code || 'غير محدد'}</small>
                        </div>
                    </td>
                    <td>
                        <span class="badge ${badgeClass}">${basedOnText}</span>
                    </td>
                    <td>
                        <small class="text-muted">من:</small> ${formatDate(item.from_date)}<br>
                        <small class="text-muted">إلى:</small> ${formatDate(item.to_date)}
                    </td>
                    <td>
                        <span class="badge badge-info">${item.indirect_cost_items.length} عنصر</span>
                    </td>
                    <td>
                        <strong class="text-success">${formatNumber(item.total)} ر.س</strong>
                    </td>
                    <td>
                        <small class="text-muted">${formatDateTime(item.created_at)}</small>
                    </td>
                    <td>
                          <div class="btn-group">
                                    <div class="dropdown">
                                        <button class="btn bg-gradient-info fa fa-ellipsis-v btn-sm" type="button" id="dropdownMenuButton1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton1">

                                <a class="dropdown-item" href="{{ route('manufacturing.indirectcosts.show', '') }}/${item.id}">
                                    <i class="feather icon-eye mr-2 text-info"></i>عرض
                                </a>
                                <a class="dropdown-item" href="{{ route('manufacturing.indirectcosts.edit', '') }}/${item.id}">
                                    <i class="feather icon-edit mr-2 text-warning"></i>تعديل
                                </a>
                                <div class="dropdown-divider"></div>
                                <button class="dropdown-item text-danger delete-btn"
                                        data-id="${item.id}"
                                        data-account="${item.account.name}"
                                        data-amount="${formatNumber(item.total)}">
                                    <i class="feather icon-trash-2 mr-2"></i>حذف
                                </button>
                            </div>
                        </div>
                    </td>
                </tr>
            `;
        });

        $('#dataTableBody').html(html);
    }



    function renderPagination(pagination) {
        if (pagination.last_page <= 1) {
            $('#paginationWrapper').hide();
            return;
        }

        let html = '';

        // السابق
        if (pagination.current_page > 1) {
            html += `<li class="page-item">
                        <a class="page-link" href="#" data-page="${pagination.current_page - 1}">السابق</a>
                     </li>`;
        }

        // الصفحات
        for (let i = 1; i <= pagination.last_page; i++) {
            if (i === pagination.current_page) {
                html += `<li class="page-item active">
                            <span class="page-link">${i}</span>
                         </li>`;
            } else if (Math.abs(i - pagination.current_page) <= 2 || i === 1 || i === pagination.last_page) {
                html += `<li class="page-item">
                            <a class="page-link" href="#" data-page="${i}">${i}</a>
                         </li>`;
            } else if (Math.abs(i - pagination.current_page) === 3) {
                html += `<li class="page-item disabled">
                            <span class="page-link">...</span>
                         </li>`;
            }
        }

        // التالي
        if (pagination.current_page < pagination.last_page) {
            html += `<li class="page-item">
                        <a class="page-link" href="#" data-page="${pagination.current_page + 1}">التالي</a>
                     </li>`;
        }

        $('#paginationLinks').html(html);
        $('#paginationWrapper').show();
    }

    function updateResultsInfo(pagination, summary) {
        const from = pagination.from || 0;
        const to = pagination.to || 0;
        const total = pagination.total || 0;

        $('#resultsInfo').text(`عرض ${from} إلى ${to} من ${total} سجل`);
        $('#paginationInfo').text(`الصفحة ${pagination.current_page} من ${pagination.last_page}`);
    }

    function loadStats() {
        $.ajax({
            url: '{{ route("manufacturing.indirectcosts.getStats") }}',
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    const stats = response.stats;
                    $('#totalCosts').text(formatNumber(stats.total_costs));
                    $('#totalAmount').text(formatNumber(stats.total_amount) + ' ر.س');
                    $('#avgAmount').text(formatNumber(stats.avg_amount) + ' ر.س');
                    $('#thisMonth').text(formatNumber(stats.this_month) + ' ر.س');
                }
            }
        });
    }

    function deleteRecord(id) {
        $.ajax({
            url: `{{ route('manufacturing.indirectcosts.destroy', '') }}/${id}`,
            method: 'DELETE',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#deleteModal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'تم الحذف بنجاح',
                    text: 'تم حذف السجل بنجاح',
                    timer: 2000,
                    showConfirmButton: false
                });
                loadData();
                loadStats();
            },
            error: function() {
                $('#deleteModal').modal('hide');
                showError('فشل في حذف السجل');
            }
        });
    }

    function exportData() {
        Swal.fire({
            title: 'تصدير البيانات',
            text: 'هل تريد تصدير البيانات الحالية؟',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'نعم، صدر',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("manufacturing.indirectcosts.export") }}',
                    method: 'POST',
                    data: {
                        ...currentFilters,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'جاري التصدير',
                                text: response.message
                            });
                        }
                    }
                });
            }
        });
    }

    function showLoading() {
        $('#loadingOverlay').show();
    }

    function hideLoading() {
        $('#loadingOverlay').hide();
    }

    function showNoData() {
        $('#noDataMessage').show();
        $('#paginationWrapper').hide();
    }

    function hideNoData() {
        $('#noDataMessage').hide();
    }

    function showError(message) {
        Swal.fire({
            icon: 'error',
            title: 'خطأ',
            text: message
        });
    }

    function formatNumber(number) {
        return new Intl.NumberFormat('ar-SA', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(number || 0);
    }

    function formatDate(date) {
        return new Date(date).toLocaleDateString('ar-SA');
    }

    function formatDateTime(datetime) {
        return new Date(datetime).toLocaleString('ar-SA');
    }
</script>
@endsection