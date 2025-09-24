@extends('master')

@section('title')
    إدارة المنتجات
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">
                        <i class="fa fa-boxes me-2"></i>إدارة المنتجات والخدمات
                    </h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسية</a></li>
                            <li class="breadcrumb-item active">المنتجات</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-header-right col-md-3 col-12">
            <div class="btn-group float-right">
                <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fa fa-plus"></i> إضافة جديد
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    @if (optional($account_setting)->business_type == 'products')
                        <a class="dropdown-item" href="{{ route('products.create') }}">
                            <i class="fa fa-box me-2"></i>منتج جديد
                        </a>
                    @elseif(optional($account_setting)->business_type == 'services')
                        <a class="dropdown-item" href="{{ route('products.create_services') }}">
                            <i class="fa fa-cogs me-2"></i>خدمة جديدة
                        </a>
                    @elseif(optional($account_setting)->business_type == 'both')
                        <a class="dropdown-item" href="{{ route('products.create') }}">
                            <i class="fa fa-box me-2"></i>منتج جديد
                        </a>
                        <a class="dropdown-item" href="{{ route('products.create_services') }}">
                            <i class="fa fa-cogs me-2"></i>خدمة جديدة
                        </a>
                    @else
                        <a class="dropdown-item" href="{{ route('products.create') }}">
                            <i class="fa fa-box me-2"></i>منتج جديد
                        </a>
                    @endif
                    @unless ($role === false)
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{ route('products.compiled') }}">
                            <i class="fa fa-layer-group me-2"></i>منتج تجميعي
                        </a>
                    @endunless
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">
        @include('layouts.alerts.error')
        @include('layouts.alerts.success')

        <!-- Loading Overlay -->

        <!-- أدوات الإدارة السريعة -->
        <div class="card mb-3">
            <div class="card-header">
                <h4 class="card-title">
                    <i class="fa fa-tools me-2"></i>أدوات الإدارة السريعة
                </h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- استيراد المنتجات -->
                    <div class="col-md-12">
                        <div class="import-section p-3 border rounded">
                            <h5 class="mb-3">
                                <i class="fa fa-upload text-primary me-2"></i>استيراد المنتجات
                            </h5>
                            <form action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data" class="import-form">
                                @csrf
                                <div class="mb-3">
                                    <label for="import_file" class="form-label">اختر ملف Excel أو CSV</label>
                                    <input type="file" name="file" id="import_file" class="form-control"
                                           accept=".xlsx,.xls,.csv" required>
                                    <div class="form-text">الصيغ المدعومة: Excel (.xlsx, .xls) أو CSV</div>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-upload me-2"></i>استيراد الآن
                                </button>
                                <a href="#" class="btn btn-outline-info">
                                    <i class="fa fa-download me-2"></i>تحميل نموذج
                                </a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- بطاقة البحث والفلترة -->
        <div class="card mb-3">
            <div class="card-header">
                <h4 class="card-title">
                    <i class="fa fa-search me-2"></i>البحث والفلترة
                </h4>
                <div class="card-header-elements">
                    <button class="btn btn-outline-primary btn-sm" type="button" data-bs-toggle="collapse"
                            data-bs-target="#searchCollapse" aria-expanded="false">
                        <i class="fa fa-filter me-2"></i>عرض/إخفاء البحث
                    </button>
                </div>
            </div>

            <div class="collapse show" id="searchCollapse">
                <div class="card-body">
                    <form class="form" id="filterForm" data-search-url="{{ route('products.search') }}">
                        <!-- البحث الأساسي -->
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="keywords">البحث بكلمة مفتاحية</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa fa-search"></i></span>
                                    <input type="text" class="form-control" placeholder="ادخل الإسم، الكود، أو الباركود"
                                           name="keywords" id="keywords">
                                </div>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="category">التصنيف</label>
                                <select name="category" class="form-control" id="category">
                                    <option value="">جميع التصنيفات</option>
                                    @if(isset($categories))
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="brand">الماركة</label>
                                <select name="brand" class="form-control" id="brand">
                                    <option value="">جميع الماركات</option>
                                    @if(isset($brands))
                                        @foreach($brands as $brand)
                                            <option value="{{ $brand }}">{{ $brand }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-3">
                                <label for="status">الحالة</label>
                                <select class="form-control" name="status" id="status">
                                    <option value="">جميع الحالات</option>
                                    <option value="1">نشط</option>
                                    <option value="2">متوقف</option>
                                    <option value="3">غير نشط</option>
                                </select>
                            </div>

                            <div class="form-group col-md-3">
                                <label for="track_inventory">نوع التتبع</label>
                                <select class="form-control" name="track_inventory" id="track_inventory">
                                    <option value="">جميع أنواع التتبع</option>
                                    <option value="0">الرقم التسلسلي</option>
                                    <option value="1">رقم الشحنة</option>
                                    <option value="2">تاريخ الانتهاء</option>
                                    <option value="3">رقم الشحنة وتاريخ الانتهاء</option>
                                    <option value="4">الكمية فقط</option>
                                </select>
                            </div>

                            <div class="form-group col-md-3">
                                <label for="product_type">نوع المنتج</label>
                                <select class="form-control" name="product_type" id="product_type">
                                    <option value="">جميع الأنواع</option>
                                    <option value="products">منتجات</option>
                                    <option value="services">خدمات</option>
                                    <option value="compiled">منتجات تجميعية</option>
                                </select>
                            </div>

                            <div class="form-group col-md-3">
                                <label for="barcode">الباركود</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa fa-barcode"></i></span>
                                    <input type="text" id="barcode" class="form-control" placeholder="رقم الباركود" name="barcode">
                                </div>
                            </div>
                        </div>

                        <!-- البحث المتقدم -->
                        <div class="collapse mt-3" id="advancedSearchForm">
                            <div class="card card-body bg-light">
                                <h6 class="mb-3"><i class="fa fa-sliders-h me-2"></i>البحث المتقدم</h6>
                                <div class="row">
                                    <div class="form-group col-md-3">
                                        <label for="from_date">من تاريخ</label>
                                        <input type="date" class="form-control" name="from_date" id="from_date">
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="to_date">إلى تاريخ</label>
                                        <input type="date" class="form-control" name="to_date" id="to_date">
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="product_code">كود المنتج</label>
                                        <input type="text" class="form-control" name="product_code" id="product_code">
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="stock_status">حالة المخزون</label>
                                        <select class="form-control" name="stock_status" id="stock_status">
                                            <option value="">جميع الحالات</option>
                                            <option value="in_stock">متوفر</option>
                                            <option value="low_stock">مخزون منخفض</option>
                                            <option value="out_of_stock">نفد المخزون</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- أزرار الإجراءات -->
                        <div class="form-actions mt-3">
                            <div class="d-flex justify-content-between flex-wrap">
                                <div>
                                    <button type="submit" class="btn btn-primary me-2">
                                        <i class="fa fa-search me-2"></i>بحث
                                    </button>

                                    <button type="button" class="btn btn-outline-secondary me-2"
                                            data-bs-toggle="collapse" data-bs-target="#advancedSearchForm">
                                        <i class="fa fa-sliders-h me-2"></i>بحث متقدم
                                    </button>

                                    <button type="button" id="clearFilters" class="btn btn-outline-danger">
                                        <i class="fa fa-times me-2"></i>مسح الفلاتر
                                    </button>
                                </div>

                                <div>
                                    <button type="button" class="btn btn-outline-success" onclick="exportProducts()">
                                        <i class="fa fa-download me-2"></i>تصدير النتائج
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- جدول المنتجات -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">
                    <i class="fa fa-list me-2"></i>قائمة المنتجات
                </h4>
                <div class="card-header-elements">
                    <span class="badge bg-primary" id="products-count">
                        إجمالي: {{ $products->total() ?? 0 }} منتج
                    </span>
                </div>
            </div>

            <div class="card-body p-0">
                <!-- Products Container -->
                <div id="productsContainer">
                    @include('stock::products.partials.products_list', ['products' => $products])
                </div>

                <!-- Pagination Container -->
                <div class="pagination-container">
                    @include('stock::products.partials.pagination', ['products' => $products])
                </div>
            </div>
        </div>
    </div>

    <style>
        /* تحسينات التصميم */
        .content-header-title {
            color: #2c3e50;
            font-weight: 600;
        }

        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.7);
            z-index: 9999;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        .loading-spinner {
            text-align: center;
            color: white;
        }

        /* بطاقات الإحصائيات المتدرجة */
        .gradient-card-1 {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        .gradient-card-2 {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            border: none;
        }

        .gradient-card-3 {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            border: none;
        }

        .gradient-card-4 {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            border: none;
        }

        .gradient-card-1:hover,
        .gradient-card-2:hover,
        .gradient-card-3:hover,
        .gradient-card-4:hover {
            transform: translateY(-5px);
            transition: all 0.3s ease;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        /* تحسين أقسام الإدارة */
        .import-section,
        .bulk-actions-section {
            background: #f8f9fa;
            border: 2px dashed #dee2e6 !important;
            transition: all 0.3s ease;
        }

        .import-section:hover,
        .bulk-actions-section:hover {
            border-color: #007bff !important;
            background: #f0f8ff;
        }

        /* تحسين البحث */
        .input-group-text {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
        }

        /* تحسين الأزرار */
        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #5a67d8, #6c5ce7);
            transform: translateY(-2px);
        }

        .card {
            border: none;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            border-radius: 15px;
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-bottom: 1px solid #dee2e6;
        }

        /* انيميشن للبطاقات */
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card {
            animation: slideInUp 0.6s ease-out;
        }

        /* تحسين badge العداد */
        #products-count {
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
        }

        /* responsive design */
        @media (max-width: 768px) {
            .content-header-right {
                margin-top: 1rem;
            }

            .form-actions .d-flex {
                flex-direction: column;
                gap: 1rem;
            }
        }
    </style>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    let searchTimeout;

    // AJAX Filter Function
    function filterProducts(page = 1) {
        const formData = new FormData($('#filterForm')[0]);
        formData.append('page', page);

        $('#loadingOverlay').show();

        $.ajax({
            url: '{{ route("products.search") }}',
            method: 'GET',
            data: Object.fromEntries(formData),
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                $('#productsContainer').html(response.html);
                $('#loadingOverlay').hide();
                updateSelectedCount();

                // تحديث العدادات من response
                if (response.total) {
                    $('#products-count').text(`إجمالي: ${response.total} منتج`);
                }

                // تحديث pagination إذا وجد
                if (response.pagination) {
                    $('.pagination-container').html(response.pagination);
                }
            },
            error: function(xhr, status, error) {
                $('#loadingOverlay').hide();
                console.error('Error:', error);

                Swal.fire({
                    title: 'خطأ!',
                    text: 'حدث خطأ أثناء البحث. يرجى المحاولة مرة أخرى.',
                    icon: 'error',
                    confirmButtonText: 'موافق'
                });
            }
        });
    }

    // Form submission
    $('#filterForm').on('submit', function(e) {
        e.preventDefault();
        filterProducts();
    });

    // Real-time search for keywords input
    $('#filterForm input[name="keywords"]').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            filterProducts();
        }, 500);
    });

    // Filter changes for select elements
    $('#filterForm select').on('change', function() {
        filterProducts();
    });

    // Date inputs change
    $('#filterForm input[type="date"]').on('change', function() {
        filterProducts();
    });

    // Clear filters
    $('#clearFilters').on('click', function() {
        $('#filterForm')[0].reset();
        filterProducts();
    });

    // Pagination clicks - تحديث بدون تغيير URL
    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        const url = new URL($(this).attr('href'));
        const page = url.searchParams.get('page');
        filterProducts(page);

        // Smooth scroll to results
        $('html, body').animate({
            scrollTop: $('#productsContainer').offset().top - 100
        }, 500);
    });

    // تحديث عداد المنتجات المحددة
    function updateSelectedCount() {
        const selectedCount = $('.product-checkbox:checked').length;
        $('#selected-count').text(selectedCount);

        if (selectedCount > 0) {
            $('.bulk-actions').show();
            $('.no-selection').hide();
        } else {
            $('.bulk-actions').hide();
            $('.no-selection').show();
        }
    }

    // مراقبة تغيير حالة الـ checkboxes
    $(document).on('change', '.product-checkbox, #selectAll', function() {
        updateSelectedCount();
    });

    // Initialize count on page load
    updateSelectedCount();
});

// Delete confirmation function
function confirmDelete(productId, productName) {
    Swal.fire({
        title: 'تأكيد الحذف',
        text: `هل أنت متأكد من حذف "${productName}"؟`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'نعم، احذف',
        cancelButtonText: 'إلغاء',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'جاري الحذف...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading()
                }
            });

            let url = `{{ route('products.delete', ':id') }}`;
            url = url.replace(':id', productId);

            let form = document.createElement('form');
            form.method = 'POST';
            form.action = url;

            let csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = `{{ csrf_token() }}`;
            form.appendChild(csrf);

            let method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            method.value = 'DELETE';
            form.appendChild(method);

            document.body.appendChild(form);
            form.submit();
        }
    });
}

// الإجراءات المجمعة
function getSelectedProducts() {
    return $('.product-checkbox:checked').map(function() {
        return $(this).val();
    }).get();
}

function bulkActivate() {
    const selectedIds = getSelectedProducts();
    if (selectedIds.length === 0) return;

    Swal.fire({
        title: 'تفعيل المنتجات',
        text: `هل تريد تفعيل ${selectedIds.length} منتج؟`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'نعم، فعّل',
        cancelButtonText: 'إلغاء'
    }).then((result) => {
        if (result.isConfirmed) {
            console.log('تفعيل المنتجات:', selectedIds);
        }
    });
}

function bulkDeactivate() {
    const selectedIds = getSelectedProducts();
    if (selectedIds.length === 0) return;

    Swal.fire({
        title: 'إيقاف المنتجات',
        text: `هل تريد إيقاف ${selectedIds.length} منتج؟`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'نعم، أوقف',
        cancelButtonText: 'إلغاء'
    }).then((result) => {
        if (result.isConfirmed) {
            console.log('إيقاف المنتجات:', selectedIds);
        }
    });
}

function bulkDelete() {
    const selectedIds = getSelectedProducts();
    if (selectedIds.length === 0) return;

    Swal.fire({
        title: 'حذف المنتجات',
        text: `هل أنت متأكد من حذف ${selectedIds.length} منتج؟ هذا الإجراء لا يمكن التراجع عنه!`,
        icon: 'error',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'نعم، احذف الكل',
        cancelButtonText: 'إلغاء',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            console.log('حذف المنتجات:', selectedIds);
        }
    });
}

function bulkExport() {
    const selectedIds = getSelectedProducts();
    if (selectedIds.length === 0) {
        exportProducts();
        return;
    }

    Swal.fire({
        title: 'تصدير المنتجات',
        text: `سيتم تصدير ${selectedIds.length} منتج`,
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'تصدير',
        cancelButtonText: 'إلغاء'
    }).then((result) => {
        if (result.isConfirmed) {
            console.log('تصدير المنتجات:', selectedIds);
        }
    });
}

function exportProducts() {
    Swal.fire({
        title: 'تصدير المنتجات',
        text: 'سيتم تصدير جميع المنتجات المطابقة لمعايير البحث الحالية',
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'تصدير',
        cancelButtonText: 'إلغاء'
    }).then((result) => {
        if (result.isConfirmed) {
            const formData = new FormData($('#filterForm')[0]);
        }
    });
}

// تحسين تجربة استيراد الملفات
$('.import-form').on('submit', function(e) {
    const fileInput = $('#import_file')[0];
    if (!fileInput.files[0]) {
        e.preventDefault();
        Swal.fire({
            title: 'لم يتم اختيار ملف',
            text: 'يرجى اختيار ملف للاستيراد',
            icon: 'warning',
            confirmButtonText: 'موافق'
        });
        return;
    }

    e.preventDefault();
    Swal.fire({
        title: 'تأكيد الاستيراد',
        text: 'هل تريد المتابعة مع استيراد الملف؟',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'نعم، استورد',
        cancelButtonText: 'إلغاء'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'جاري الاستيراد...',
                text: 'يرجى الانتظار حتى اكتمال العملية',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading()
                }
            });
            this.submit();
        }
    });
});

// تحسين تجربة البحث المباشر
let typingTimer;
const doneTypingInterval = 800;

$('#keywords').on('keyup', function() {
    clearTimeout(typingTimer);
    if ($(this).val()) {
        typingTimer = setTimeout(function() {
            filterProducts();
        }, doneTypingInterval);
    }
});

$('#keywords').on('keydown', function() {
    clearTimeout(typingTimer);
});

// اختصارات لوحة المفاتيح
$(document).on('keydown', function(e) {
    if (e.ctrlKey && e.key === 'a') {
        e.preventDefault();
        $('#selectAll').prop('checked', true).trigger('change');
    }

    if (e.key === 'Escape') {
        $('#selectAll').prop('checked', false).trigger('change');
        $('.product-checkbox').prop('checked', false);
        updateSelectedCount();
    }

    if (e.ctrlKey && e.key === 'f') {
        e.preventDefault();
        $('#keywords').focus();
    }
});

// تحسين الاستجابة للشاشات الصغيرة
function handleResponsiveDesign() {
    if ($(window).width() < 768) {
        $('.bulk-actions-section .btn-group-vertical').removeClass('btn-group-vertical').addClass('btn-group');
    } else {
        $('.bulk-actions-section .btn-group').removeClass('btn-group').addClass('btn-group-vertical');
    }
}

$(window).on('resize', handleResponsiveDesign);
handleResponsiveDesign();

// إضافة تلميحات للمستخدم
$(function () {
    $('[data-bs-toggle="tooltip"]').tooltip();
});

// تأثيرات بصرية للتفاعل
$('.card').hover(
    function() {
        $(this).addClass('shadow-lg').css('transform', 'translateY(-2px)');
    },
    function() {
        $(this).removeClass('shadow-lg').css('transform', 'translateY(0)');
    }
);

</script>
@endsection