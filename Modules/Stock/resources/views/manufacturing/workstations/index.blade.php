@extends('master')

@section('title')
محطات العمل
@stop

@section('content')

<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">محطات العمل</h2>
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

<!-- Search Card -->
<div class="card">
    <div class="card-content">
        <div class="card-body">
            <div class="card-title">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div>بحث</div>
                    <div>
                        <a href="{{ route('manufacturing.workstations.create') }}" class="btn btn-outline-success waves-effect waves-light">
                            <i class="fa fa-plus me-2"></i>أضف محطة العمل
                        </a>
                    </div>
                </div>
            </div>

            <div class="row mb-3 mt-3">
                <div class="col">
                    <input type="text" id="searchInput" class="form-control" placeholder="البحث بواسطة الاسم أو الكود">
                </div>
                <div class="col-auto">
                    <select id="perPageSelect" class="form-select">
                        <option value="10">10 عنصر</option>
                        <option value="25">25 عنصر</option>
                        <option value="50">50 عنصر</option>
                        <option value="100">100 عنصر</option>
                    </select>
                </div>
                <div class="col-auto">
                    <button type="button" id="resetBtn" class="btn btn-secondary waves-effect waves-light">إعادة تعيين</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Data Card -->
<div class="card mt-4">
    <div class="card-body">
        <!-- Loading Spinner -->
        <div id="loadingSpinner" class="text-center p-4" style="display: none;">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">جاري التحميل...</span>
            </div>
        </div>

        <!-- Table Container -->
        <div id="tableContainer">
            <table class="table table-bordered table-striped">
                <thead class="table-light">
                    <tr>
                        <th data-sort="name" class="sortable">
                            الاسم <i class="fa fa-sort"></i>
                        </th>
                        <th data-sort="description" class="sortable">
                            الوصف <i class="fa fa-sort"></i>
                        </th>
                        <th data-sort="total_cost" class="sortable">
                            التكلفة الاجمالية <i class="fa fa-sort"></i>
                        </th>
                        <th style="width: 10%">الإجراءات</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <!-- Data will be loaded here -->
                </tbody>
            </table>
        </div>

        <!-- No Data Alert -->
        <div id="noDataAlert" class="alert alert-danger text-center" role="alert" style="display: none;">
            <p class="mb-0">لا توجد محطات عمل مضافة حتى الان !!</p>
        </div>

        <!-- Pagination -->
        <div id="paginationContainer" class="d-flex justify-content-between align-items-center mt-3">
            <div id="paginationInfo"></div>
            <div id="paginationLinks"></div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #EA5455 !important;">
                <h4 class="modal-title" id="deleteModalLabel" style="color: #FFFFFF">حذف محطة العمل</h4>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" style="color: #FFFFFF">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <strong>هل انت متاكد من انك تريد الحذف ؟</strong>
                <p id="itemToDelete" class="mt-2"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light waves-effect waves-light" data-bs-dismiss="modal">الغاء</button>
                <button type="button" id="confirmDeleteBtn" class="btn btn-danger waves-effect waves-light">تأكيد</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    let currentPage = 1;
    let currentSort = 'created_at';
    let currentOrder = 'desc';
    let deleteId = null;

    // Load initial data
    loadData();

    // Search functionality with debounce
    let searchTimeout;
    $('#searchInput').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            currentPage = 1;
            loadData();
        }, 500);
    });

    // Per page change
    $('#perPageSelect').on('change', function() {
        currentPage = 1;
        loadData();
    });

    // Reset button
    $('#resetBtn').on('click', function() {
        $('#searchInput').val('');
        $('#perPageSelect').val('10');
        currentPage = 1;
        currentSort = 'created_at';
        currentOrder = 'desc';
        loadData();
    });

    // Sorting
    $(document).on('click', '.sortable', function() {
        const sortBy = $(this).data('sort');
        if (currentSort === sortBy) {
            currentOrder = currentOrder === 'asc' ? 'desc' : 'asc';
        } else {
            currentSort = sortBy;
            currentOrder = 'asc';
        }
        currentPage = 1;
        updateSortIcons();
        loadData();
    });

    // Pagination
    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        const url = $(this).attr('href');
        if (url) {
            const page = new URL(url).searchParams.get('page');
            if (page) {
                currentPage = parseInt(page);
                loadData();
            }
        }
    });

    // Delete functionality
    $(document).on('click', '.delete-btn', function() {
        deleteId = $(this).data('id');
        const itemName = $(this).data('name');
        $('#itemToDelete').text(itemName);
        $('#deleteModal').modal('show');
    });

    $('#confirmDeleteBtn').on('click', function() {
        if (deleteId) {
            deleteItem(deleteId);
        }
    });

    function loadData() {
        showLoading();

        const params = {
            page: currentPage,
            search: $('#searchInput').val(),
            per_page: $('#perPageSelect').val(),
            sort_by: currentSort,
            sort_order: currentOrder
        };

        $.ajax({
            url: '{{ route("manufacturing.workstations.getData") }}',
            type: 'GET',
            data: params,
            success: function(response) {
                hideLoading();
                if (response.success) {
                    renderTable(response.data);
                    renderPagination(response.pagination);
                } else {
                    showError('حدث خطأ أثناء تحميل البيانات');
                }
            },
            error: function() {
                hideLoading();
                showError('حدث خطأ أثناء تحميل البيانات');
            }
        });
    }

    function renderTable(data) {
        const tbody = $('#tableBody');
        tbody.empty();

        if (data && data.length > 0) {
            $('#tableContainer').show();
            $('#noDataAlert').hide();

            data.forEach(function(item) {
                const row = `
                    <tr>
                        <td>
                            <div>${item.name}</div>
                            <small class="text-muted">#${item.code}</small>
                        </td>
                        <td>${item.description || '-'}</td>
                        <td>${item.total_cost || '-'}</td>
                        <td class="text-center">
                            <div class="btn-group">
                                <div class="dropdown">
                                    <button class="btn btn-sm bg-gradient-info fa fa-ellipsis-v" type="button" data-bs-toggle="dropdown" aria-expanded="false"></button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a class="dropdown-item" href="${getShowUrl(item.id)}">
                                                <i class="fa fa-eye me-2 text-primary"></i>عرض
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="${getEditUrl(item.id)}">
                                                <i class="fa fa-edit me-2 text-success"></i>تعديل
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item text-danger delete-btn" href="#" data-id="${item.id}" data-name="${item.name}">
                                                <i class="fa fa-trash me-2"></i>حذف
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </td>
                    </tr>
                `;
                tbody.append(row);
            });
        } else {
            $('#tableContainer').hide();
            $('#noDataAlert').show();
        }
    }

    function renderPagination(pagination) {
        if (pagination.total > 0) {
            $('#paginationInfo').html(`عرض ${pagination.from}-${pagination.to} من ${pagination.total} عنصر`);
            $('#paginationLinks').html(pagination.links);
            $('#paginationContainer').show();
        } else {
            $('#paginationContainer').hide();
        }
    }

    function updateSortIcons() {
        $('.sortable i').removeClass('fa-sort-up fa-sort-down').addClass('fa-sort');
        $(`.sortable[data-sort="${currentSort}"] i`)
            .removeClass('fa-sort')
            .addClass(currentOrder === 'asc' ? 'fa-sort-up' : 'fa-sort-down');
    }

    function deleteItem(id) {
        $.ajax({
            url: `{{ route('manufacturing.workstations.delete', '') }}/${id}`,
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#deleteModal').modal('hide');
                if (response.success) {
                    showSuccess(response.message);
                    loadData();
                } else {
                    showError(response.message);
                }
            },
            error: function() {
                $('#deleteModal').modal('hide');
                showError('حدث خطأ أثناء حذف العنصر');
            }
        });
    }

    function getShowUrl(id) {
        return `{{ route('manufacturing.workstations.show', '') }}/${id}`;
    }

    function getEditUrl(id) {
        return `{{ route('manufacturing.workstations.edit', '') }}/${id}`;
    }

    function showLoading() {
        $('#loadingSpinner').show();
        $('#tableContainer').hide();
        $('#noDataAlert').hide();
    }

    function hideLoading() {
        $('#loadingSpinner').hide();
    }

    function showSuccess(message) {
        // يمكنك استخدام نظام الإشعارات المفضل لديك
        alert(message);
    }

    function showError(message) {
        // يمكنك استخدام نظام الإشعارات المفضل لديك
        alert(message);
    }
});
</script>
@endsection

@section('styles')
<style>
.sortable {
    cursor: pointer;
    user-select: none;
}

.sortable:hover {
    background-color: #f8f9fa;
}

#loadingSpinner {
    min-height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.pagination {
    margin: 0;
}

.btn-group .dropdown-menu {
    min-width: 120px;
}
</style>
@endsection
