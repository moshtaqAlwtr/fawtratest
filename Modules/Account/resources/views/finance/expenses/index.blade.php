@extends('master')

@section('title')
    مصروفات
@stop

@section('css')
    <style>
        .ex-card {
            background: linear-gradient(135deg, #4a90e2, #13d7fe);
            border-radius: 10px;
            color: white;
        }

        .card-title {
            font-weight: bold;
        }

        .text-muted {
            color: rgba(255, 255, 255, 0.7);
        }

        .text-white {
            font-size: 1.5rem;
        }

        .loading-spinner {
            display: none;
            text-align: center;
            padding: 20px;
        }

        .loading-spinner .spinner-border {
            width: 3rem;
            height: 3rem;
        }

        .fade-in {
            animation: fadeIn 0.5s;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }
    </style>
@endsection

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">المصروفات</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a>
                            </li>
                            <li class="breadcrumb-item active">عرض
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body">

        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                    </div>
                    <nav aria-label="Page navigation" id="pagination-container">
                        <!-- سيتم تحديث التصفح عبر AJAX -->
                    </nav>

                    <div>
                        <a href="#" class="btn btn-outline-dark">
                            <i class="fas fa-upload"></i>استيراد
                        </a>
                        <a href="{{ route('expenses.create') }}" class="btn btn-outline-primary">
                            <i class="fa fa-plus"></i>سند صرف
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @include('layouts.alerts.error')
        @include('layouts.alerts.success')

        <!-- إجمالي المصروفات -->
        <div class="card ex-card shadow-sm border-light" id="totals-card">
            <div class="card-body">
                <h5 class="card-title text-center">إجمالي المصروفات</h5>
                <div class="d-flex justify-content-between align-items-center flex-wrap" id="totals-container">
                    <!-- سيتم تحديث هذا القسم بواسطة AJAX -->
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
                        <span class="hide-button-text">اخفاء</span>
                    </button>
                    <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="collapse"
                        data-bs-target="#advancedSearchForm" onclick="toggleSearchText(this)">
                        <i class="fa fa-filter"></i>
                        <span class="button-text">متقدم</span>
                    </button>
                </div>
            </div>

            <div class="card-body">
                <form class="form" id="searchForm">
                    <div class="row g-3">
                        <!-- 1. Keyword Search -->
                        <div class="col-md-4">
                            <label for="keywords">البحث بكلمة مفتاحية</label>
                            <input type="text" id="keywords" class="form-control" placeholder="ادخل الإسم او الكود"
                                name="keywords">
                        </div>

                        <!-- 2. From Date -->
                        <div class="col-md-2">
                            <label for="from_date">من تاريخ</label>
                            <input type="date" id="from_date" class="form-control" name="from_date">
                        </div>

                        <!-- 3. To Date -->
                        <div class="col-md-2">
                            <label for="to_date">إلى تاريخ</label>
                            <input type="date" id="to_date" class="form-control" name="to_date">
                        </div>

                        <!-- 4. Added By -->
                        <div class="col-md-4">
                            <label for="added_by">أضيفت بواسطة</label>
                            <select name="added_by" class="form-control select2" id="added_by">
                                <option value="">-- الكل --</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- 5. Category -->
                        <div class="col-md-4">
                            <label for="category">التصنيف</label>
                            <select name="category" class="form-control select2" id="category">
                                <option value="">جميع التصنيفات</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- 6. Status -->
                        <div class="col-md-4">
                            <label for="status">الحالة</label>
                            <select name="status" class="form-control" id="status">
                                <option value="">الحالة</option>
                                <option value="1">نشط</option>
                                <option value="2">متوقف</option>
                                <option value="3">غير نشط</option>
                            </select>
                        </div>
                    </div>

                    <!-- Advanced Search Section -->
                    <div class="collapse" id="advancedSearchForm">
                        <div class="row g-3 mt-2">
                            <!-- 7. Description -->
                            <div class="col-md-4">
                                <label for="description">الوصف</label>
                                <input type="text" id="description" class="form-control" placeholder="الوصف"
                                    name="description">
                            </div>

                            <!-- 8. Vendor -->
                            <div class="col-md-4">
                                <label for="vendor">البائع</label>
                                <select name="vendor" class="form-control select2" id="vendor">
                                    <option value="">أي بائع</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- 9. Amount From -->
                            <div class="col-md-2">
                                <label for="amount_from">أكبر مبلغ</label>
                                <input type="text" id="amount_from" class="form-control" placeholder="أكبر مبلغ"
                                    name="amount_from">
                            </div>

                            <!-- 10. Amount To -->
                            <div class="col-md-2">
                                <label for="amount_to">أقل مبلغ</label>
                                <input type="text" id="amount_to" class="form-control" placeholder="أقل مبلغ"
                                    name="amount_to">
                            </div>

                            <!-- 11. Created At From -->
                            <div class="col-md-2">
                                <label for="created_at_from">من تاريخ الإنشاء</label>
                                <input type="date" id="created_at_from" class="form-control" name="created_at_from">
                            </div>

                            <!-- 12. Created At To -->
                            <div class="col-md-2">
                                <label for="created_at_to">إلى تاريخ الإنشاء</label>
                                <input type="date" id="created_at_to" class="form-control" name="created_at_to">
                            </div>

                            <!-- 13. Sub Account -->
                            <div class="col-md-4">
                                <label for="sub_account">الحساب الفرعي</label>
                                <select name="sub_account" class="form-control" id="sub_account">
                                    <option value="">أي حساب</option>
                                    <!-- يمكن إضافة الخيارات هنا -->
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="form-actions mt-2">
                        <button type="submit" class="btn btn-primary">بحث</button>
                        <button type="button" class="btn btn-outline-warning" id="resetForm">إلغاء</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- النتائج -->
        <div class="card">
            <div class="card-header">النتائج</div>
            <div class="card-body">
                <!-- Loading Spinner -->
                <div class="loading-spinner" id="loading-spinner">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">جاري التحميل...</span>
                    </div>
                </div>

                <!-- محتوى النتائج -->
                <div id="results-container">
                    <!-- سيتم تحديث هذا القسم بواسطة AJAX -->
                </div>

                <!-- Pagination -->
                <div id="pagination-results">
                    <!-- سيتم تحديث هذا القسم بواسطة AJAX -->
                </div>
            </div>
        </div>

    </div><!-- content-body -->
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/search.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // إعداد المتغيرات العامة والـ routes
        window.baseUrl = "{{ url('') }}";
        window.csrfToken = "{{ csrf_token() }}";
        window.routes = {
            expenses: {
                show: "{{ route('expenses.show', ':id') }}",
                print_thermal: "{{ route('expenses.print', [':id', 'thermal']) }}",
                print_normal: "{{ route('expenses.print', [':id', 'normal']) }}",
                cancel: "{{ route('expenses.cancel', ':id') }}",
                edit: "{{ route('expenses.edit', ':id') }}"
            }
        };

        let currentPage = 1;
        let currentFilters = {};

        $(document).ready(function() {
            // تحميل البيانات عند تحميل الصفحة
            loadData();

            // البحث عند إرسال النموذج
            $('#searchForm').on('submit', function(e) {
                e.preventDefault();
                currentPage = 1;
                loadData();
            });

            // إعادة تعيين النموذج
            $('#resetForm').on('click', function() {
                $('#searchForm')[0].reset();
                $('.select2').val(null).trigger('change');
                currentPage = 1;
                currentFilters = {};
                loadData();
            });
        });

        function loadData(page = 1) {
            currentPage = page;

            // جمع بيانات النموذج
            const formData = new FormData($('#searchForm')[0]);
            const params = new URLSearchParams();

            for (let [key, value] of formData.entries()) {
                if (value) {
                    params.append(key, value);
                }
            }

            params.append('page', page);

            // إظهار Loading
            $('#loading-spinner').show();
            $('#results-container').hide();

            // طلب AJAX
            $.ajax({
                url: '{{ route('expenses.data') }}',
                method: 'GET',
                data: params.toString(),
                success: function(response) {
                    updateTotals(response.totals, response.account_setting);
                    updateResults(response.expenses.data);
                    updatePagination(response.pagination);

                    // إخفاء Loading وإظهار النتائج
                    $('#loading-spinner').hide();
                    $('#results-container').show().addClass('fade-in');
                },
                error: function(xhr, status, error) {
                    console.error('خطأ في تحميل البيانات:', error);
                    $('#loading-spinner').hide();
                    $('#results-container').html(
                        '<div class="alert alert-danger">حدث خطأ في تحميل البيانات</div>').show();
                }
            });
        }

        function updateTotals(totals, accountSetting) {
            const currency = accountSetting?.currency || 'SAR';
            const currencySymbol = currency === 'SAR' || !currency ?
                '<img src="{{ asset('assets/images/Saudi_Riyal.svg') }}" alt="ريال سعودي" width="15" style="vertical-align: middle;">' :
                currency;

            const totalsHtml = `
            <div class="text-center">
                <p class="text-muted">آخر 7 أيام</p>
                <h2 class="text-white">${currencySymbol} ${totals.totalLast7Days || 0}</h2>
            </div>
            <div class="text-center">
                <p class="text-muted">آخر 30 يوم</p>
                <h2 class="text-white">${currencySymbol} ${totals.totalLast30Days || 0}</h2>
            </div>
            <div class="text-center">
                <p class="text-muted">آخر 365 يوم</p>
                <h2 class="text-white">${currencySymbol} ${totals.totalLast365Days || 0}</h2>
            </div>
        `;

            $('#totals-container').html(totalsHtml);
        }

        function updateResults(expenses) {
            if (expenses.length === 0) {
                $('#results-container').html(`
                <div class="alert alert-danger text-xl-center" role="alert">
                    <p class="mb-0">لا توجد سندات صرف</p>
                </div>
            `);
                return;
            }

            let html = '<table class="table"><tbody>';

            expenses.forEach(function(expense) {
                const attachmentImg = expense.attachments ?
                    `<img src="${window.baseUrl}/assets/uploads/expenses/${expense.attachments}" alt="img" width="100"><br>` :
                    '';

                html += `
                <tr>
                    <td style="width: 80%">
                        <p><strong>${expense.expenses_category?.name || ''}</strong></p>
                        <p><small>${expense.date} | ${expense.description}</small></p>
                        ${attachmentImg}
                        <i class="fa fa-user"></i> <small>اضيفت بواسطة :</small>
                        <strong>${expense.user?.name || ''}</strong>
                    </td>
                    <td>
                        <p><strong>${expense.amount} رس</strong></p>
                        <i class="fa fa-archive"></i> <small>${expense.store_id || ''}</small>
                    </td>
                    <td>
                        <div class="btn-group">
                            <div class="dropdown">
                                <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1"
                                    type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                                <div class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="${window.routes.expenses.show.replace(':id', expense.id)}">
                                            <i class="fa fa-eye me-2 text-primary"></i>عرض
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="${window.routes.expenses.edit.replace(':id', expense.id)}">
                                            <i class="fa fa-edit me-2 text-success"></i>تعديل
                                        </a>
                                    </li>
                                    <li>
                                        <button type="button" class="dropdown-item text-danger"
                                            onclick="confirmCancel(${expense.id})">
                                            <i class="fa fa-times me-2"></i>إلغاء
                                        </button>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="${window.routes.expenses.print_thermal.replace(':id', expense.id)}">
                                            <i class="fa fa-print me-2 text-info"></i>طباعة سند صرف حراري
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="${window.routes.expenses.print_normal.replace(':id', expense.id)}">
                                            <i class="fa fa-print me-2 text-info"></i>طباعة سند صرف عادي
                                        </a>
                                    </li>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            `;
            });

            html += '</tbody></table>';
            $('#results-container').html(html);
        }

        function updatePagination(pagination) {
            let paginationHtml = `
            <ul class="pagination pagination-sm mb-0">
        `;

            // زر الانتقال إلى أول صفحة
            if (pagination.on_first_page) {
                paginationHtml += `
                <li class="page-item disabled">
                    <span class="page-link border-0 rounded-pill" aria-label="First">
                        <i class="fas fa-angle-double-right"></i>
                    </span>
                </li>
            `;
            } else {
                paginationHtml += `
                <li class="page-item">
                    <a class="page-link border-0 rounded-pill" href="javascript:void(0)" onclick="loadData(1)" aria-label="First">
                        <i class="fas fa-angle-double-right"></i>
                    </a>
                </li>
            `;
            }

            // زر الانتقال إلى الصفحة السابقة
            if (pagination.on_first_page) {
                paginationHtml += `
                <li class="page-item disabled">
                    <span class="page-link border-0 rounded-pill" aria-label="Previous">
                        <i class="fas fa-angle-right"></i>
                    </span>
                </li>
            `;
            } else {
                paginationHtml += `
                <li class="page-item">
                    <a class="page-link border-0 rounded-pill" href="javascript:void(0)" onclick="loadData(${pagination.current_page - 1})" aria-label="Previous">
                        <i class="fas fa-angle-right"></i>
                    </a>
                </li>
            `;
            }

            // عرض رقم الصفحة الحالية
            paginationHtml += `
            <li class="page-item">
                <span class="page-link border-0 bg-light rounded-pill px-3">
                    صفحة ${pagination.current_page} من ${pagination.last_page}
                </span>
            </li>
        `;

            // زر الانتقال إلى الصفحة التالية
            if (pagination.has_more_pages) {
                paginationHtml += `
                <li class="page-item">
                    <a class="page-link border-0 rounded-pill" href="javascript:void(0)" onclick="loadData(${pagination.current_page + 1})" aria-label="Next">
                        <i class="fas fa-angle-left"></i>
                    </a>
                </li>
            `;
            } else {
                paginationHtml += `
                <li class="page-item disabled">
                    <span class="page-link border-0 rounded-pill" aria-label="Next">
                        <i class="fas fa-angle-left"></i>
                    </span>
                </li>
            `;
            }

            // زر الانتقال إلى آخر صفحة
            if (pagination.has_more_pages) {
                paginationHtml += `
                <li class="page-item">
                    <a class="page-link border-0 rounded-pill" href="javascript:void(0)" onclick="loadData(${pagination.last_page})" aria-label="Last">
                        <i class="fas fa-angle-double-left"></i>
                    </a>
                </li>
            `;
            } else {
                paginationHtml += `
                <li class="page-item disabled">
                    <span class="page-link border-0 rounded-pill" aria-label="Last">
                        <i class="fas fa-angle-double-left"></i>
                    </span>
                </li>
            `;
            }

            paginationHtml += '</ul>';

            $('#pagination-container').html(paginationHtml);
            $('#pagination-results').html(paginationHtml);
        }

        // دالة تأكيد الإلغاء مع استخدام المتغيرات العامة
        function confirmCancel(id) {
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: 'سيتم استعادة المبلغ إلى الخزينة وإلغاء سند الصرف',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'نعم، إلغاء السند',
                cancelButtonText: 'لا، تراجع',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6'
            }).then((result) => {
                if (result.isConfirmed) {
                    // استخدام المتغيرات العامة المُعرفة في الأعلى
                    const cancelUrl = window.routes.expenses.cancel.replace(':id', id);

                    $.ajax({
                        url: cancelUrl,
                        method: 'POST',
                        data: {
                            _token: window.csrfToken
                        },
                        success: function(response) {
                            Swal.fire('تم!', 'تم إلغاء السند بنجاح', 'success');
                            loadData(currentPage);
                        },
                        error: function(xhr, status, error) {
                            console.log('Error details:', xhr.responseText);
                            console.log('URL used:', cancelUrl);

                            let errorMessage = 'حدث خطأ أثناء إلغاء السند';

                            if (xhr.status === 404) {
                                errorMessage = 'الصفحة غير موجودة - تحقق من الرابط';
                            } else if (xhr.status === 403) {
                                errorMessage = 'ليس لديك صلاحية لهذا الإجراء';
                            } else if (xhr.status === 419) {
                                errorMessage = 'انتهت صلاحية الجلسة - يرجى تحديث الصفحة';
                            }

                            Swal.fire('خطأ!', errorMessage, 'error');
                        }
                    });
                }
            });
        }

        function showPermissionError() {
            Swal.fire({
                icon: 'error',
                title: 'صلاحيات غير كافية',
                text: 'أنت لا تملك صلاحية لإلغاء هذا السند.',
                confirmButtonText: 'موافق'
            });
        }

        // دوال البحث المتقدم
        function toggleSearchFields(button) {
            // يمكن إضافة منطق إخفاء/إظهار حقول البحث هنا
        }

        function toggleSearchText(button) {
            const buttonText = button.querySelector('.button-text');
            if (buttonText.textContent === 'متقدم') {
                buttonText.textContent = 'بسيط';
            } else {
                buttonText.textContent = 'متقدم';
            }
        }
    </script>
@endsection
