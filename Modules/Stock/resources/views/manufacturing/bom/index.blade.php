@extends('master')

@section('title')
قوائم مواد الأنتاج
@stop

@section('content')

<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">قوائم مواد الأنتاج</h2>
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

@include('layouts.alerts.success')
@include('layouts.alerts.error')

<div class="content-body">
    <!-- بطاقة البحث -->
    <div class="card">
        <div class="card-content">
            <div class="card-body">
                <div class="card-title">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>بحث</div>
                        <div>
                            <a href="{{ route('BOM.create') }}" class="btn btn-outline-success">
                                <i class="fa fa-plus me-2"></i>أضف قائمة مواد الأنتاج
                            </a>
                        </div>
                    </div>
                </div>

                <!-- نموذج البحث -->
                <form id="searchForm" class="row mb-3 mt-3">
                    <div class="col">
                        <input type="text" id="search" name="search" class="form-control" placeholder="البحث بواسطة الاسم أو الكود">
                    </div>
                    <div class="col">
                        <select id="product_id" name="product_id" class="form-control" aria-label="فرز بواسطة المنتج">
                            <option value="">فرز بواسطة المنتج</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col">
                        <select id="status" name="status" class="form-control" aria-label="إختر الحالة">
                            <option value="">إختر الحالة</option>
                            <option value="1">نشط</option>
                            <option value="0">غير نشط</option>
                        </select>
                    </div>
                    <div class="col">
                        <select id="operation_id" name="operation_id" class="form-control" aria-label="Filter by Production Operation">
                            <option value="">Filter by Production Operation</option>
                            @foreach($operations as $operation)
                                <option value="{{ $operation->id }}">{{ $operation->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary">بحث</button>
                        <button type="button" id="resetBtn" class="btn btn-secondary">إعادة تعيين</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- بطاقة الجدول -->
    <div class="card mt-4">
        <div class="card-body">
            <!-- Loading Spinner -->
            <div id="loading" class="text-center" style="display: none;">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">جاري التحميل...</span>
                </div>
            </div>

            <!-- جدول البيانات -->
            <div id="tableContainer">
                <table class="table table-striped">
                    <thead class="thead-light">
                        <tr>
                            <th>الاسم</th>
                            <th>المنتج الرئيسي</th>
                            <th>كمية الإنتاج</th>
                            <th>إجمالي التكلفة</th>
                            <th>الحالة</th>
                            <th>الافتراضي</th>
                            <th>اجراء</th>
                        </tr>
                    </thead>
                    <tbody id="materialsTableBody">
                        <!-- سيتم ملء البيانات عبر AJAX -->
                    </tbody>
                </table>
            </div>

            <!-- رسالة عدم وجود بيانات -->
            <div id="noDataMessage" class="alert alert-danger text-xl-center" role="alert" style="display: none;">
                <p class="mb-0">لا يوجد قوائم مواد الأنتاج</p>
            </div>

            <!-- التصفح بين الصفحات -->
            <div id="paginationContainer" class="d-flex justify-content-center mt-3">
                <!-- سيتم إضافة التصفح عبر JavaScript -->
            </div>
        </div>
    </div>
</div>

<!-- Modal للحذف -->
<div class="modal fade text-left" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #EA5455 !important;">
                <h4 class="modal-title" id="deleteModalLabel" style="color: #FFFFFF">حذف قائمة المواد</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" style="color: #FFFFFF">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <strong>هل انت متاكد من انك تريد الحذف ؟</strong>
                <p id="deleteItemName" class="mt-2"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light waves-effect waves-light" data-dismiss="modal">الغاء</button>
                <button type="button" id="confirmDelete" class="btn btn-danger waves-effect waves-light">تأكيد</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    const routes = {
        show: "{{ route('Bom.show', ':id') }}",
        edit: "{{ route('Bom.edit', ':id') }}",
        delete: "{{ route('Bom.destroy', ':id') }}"
    };
</script>
<script>
$(document).ready(function() {
    let currentPage = 1;
    let deleteId = null;

    // تحميل البيانات عند تحميل الصفحة
    loadMaterials();

    // البحث عند الإرسال
    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        currentPage = 1;
        loadMaterials();
    });

    // إعادة تعيين الفلاتر
    $('#resetBtn').on('click', function() {
        $('#searchForm')[0].reset();
        currentPage = 1;
        loadMaterials();
    });

    // البحث التلقائي عند الكتابة
    $('#search').on('keyup', debounce(function() {
        currentPage = 1;
        loadMaterials();
    }, 500));

    // البحث عند تغيير الفلاتر
    $('#product_id, #status, #operation_id').on('change', function() {
        currentPage = 1;
        loadMaterials();
    });

    // فتح modal الحذف
    $(document).on('click', '.delete-btn', function() {
        deleteId = $(this).data('id');
        const itemName = $(this).data('name');
        $('#deleteItemName').text('العنصر: ' + itemName);
        $('#deleteModal').modal('show');
    });

    // تأكيد الحذف
    $('#confirmDelete').on('click', function() {
        if (deleteId) {
            deleteMaterial(deleteId);
        }
    });

    // دالة تحميل البيانات
    function loadMaterials(page = currentPage) {
        $('#loading').show();
        $('#tableContainer').hide();
        $('#noDataMessage').hide();

        const formData = $('#searchForm').serialize() + '&page=' + page;

        $.ajax({
            url: '{{ route("BOM.getData") }}',
            method: 'GET',
            data: formData,
            success: function(response) {
                $('#loading').hide();

                if (response.success && response.data.length > 0) {
                    renderTable(response.data);
                    renderPagination(response.pagination);
                    $('#tableContainer').show();
                } else {
                    $('#noDataMessage').show();
                    $('#paginationContainer').html('');
                }
            },
            error: function() {
                $('#loading').hide();
                alert('حدث خطأ أثناء تحميل البيانات');
            }
        });
    }

    // رسم الجدول
function renderTable(materials) {
    let html = '';

    materials.forEach(function(material) {
        const statusText = material.status == 1 ?
            '<span class="text-success">نشط</span>' :
            '<span class="text-danger">غير نشط</span>';

        const defaultFlag = material.default == 1 ?
            '<i class="fa fa-flag text-info"></i> افتراضي' : '';

        const showUrl = routes.show.replace(':id', material.id);
        const editUrl = routes.edit.replace(':id', material.id);
        const deleteUrl = routes.delete.replace(':id', material.id);

        html += `
            <tr>
                <td>${material.name}</td>
                <td>${material.product ? material.product.name : '-'}</td>
                <td>${material.quantity}</td>
                <td>${material.last_total_cost} ر.س</td>
                <td>${statusText}</td>
                <td>${defaultFlag}</td>
                <td>
                    <div class="btn-group">
                        <div class="dropdown">
                            <button class="btn btn-sm bg-gradient-info fa fa-ellipsis-v mr-1 mb-1"
                                    type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="${showUrl}">
                                    <i class="fa fa-eye me-2 text-primary"></i>عرض
                                </a>
                                <a class="dropdown-item" href="${editUrl}">
                                    <i class="fa fa-edit me-2 text-success"></i>تعديل
                                </a>
                                <a class="dropdown-item text-danger delete-btn" href="#"
                                   data-id="${material.id}" data-name="${material.name}" data-url="${deleteUrl}">
                                    <i class="fa fa-trash me-2"></i>حذف
                                </a>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        `;
    });

    $('#materialsTableBody').html(html);
}

    // رسم التصفح
    function renderPagination(pagination) {
        if (pagination.last_page <= 1) {
            $('#paginationContainer').html('');
            return;
        }

        let html = '<nav aria-label="Page navigation"><ul class="pagination">';

        // زر السابق
        if (pagination.current_page > 1) {
            html += `<li class="page-item">
                        <a class="page-link" href="#" data-page="${pagination.current_page - 1}">السابق</a>
                     </li>`;
        }

        // أرقام الصفحات
        for (let i = 1; i <= pagination.last_page; i++) {
            const activeClass = i === pagination.current_page ? 'active' : '';
            html += `<li class="page-item ${activeClass}">
                        <a class="page-link" href="#" data-page="${i}">${i}</a>
                     </li>`;
        }

        // زر التالي
        if (pagination.current_page < pagination.last_page) {
            html += `<li class="page-item">
                        <a class="page-link" href="#" data-page="${pagination.current_page + 1}">التالي</a>
                     </li>`;
        }

        html += '</ul></nav>';
        $('#paginationContainer').html(html);
    }

    // التنقل بين الصفحات
    $(document).on('click', '.page-link', function(e) {
        e.preventDefault();
        const page = $(this).data('page');
        if (page) {
            currentPage = page;
            loadMaterials(page);
        }
    });

    // دالة الحذف
    function deleteMaterial(id) {
        $.ajax({
            url: `/bom/${id}`,
            method: 'DELETE',
            data: {
                '_token': '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#deleteModal').modal('hide');
                if (response.success) {
                    // إظهار رسالة نجاح
                    showAlert('success', response.message);
                    // إعادة تحميل البيانات
                    loadMaterials();
                } else {
                    showAlert('danger', response.message);
                }
            },
            error: function() {
                $('#deleteModal').modal('hide');
                showAlert('danger', 'حدث خطأ أثناء الحذف');
            }
        });
    }

    // دالة إظهار التنبيهات
    function showAlert(type, message) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `;
        $('.content-header').after(alertHtml);

        // إخفاء التنبيه بعد 5 ثوان
        setTimeout(function() {
            $('.alert').fadeOut();
        }, 5000);
    }

    // دالة debounce للبحث
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
});
</script>
@endsection
