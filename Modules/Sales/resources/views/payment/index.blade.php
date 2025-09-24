@extends('master')

@section('title')
    مدفوعات العملاء
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">ادارة مدفوعات العملاء</h2>
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
    <div class="content-body">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <!-- Checkbox لتحديد الكل -->
                    <div class="form-check me-3">
                        <input class="form-check-input" type="checkbox" id="selectAll" onclick="toggleSelectAll()">
                    </div>



                    <!-- زر المواعيد -->
                    <a href="{{ route('appointments.index') }}"
                        class="btn btn-outline-primary btn-sm d-flex align-items-center rounded-pill px-3">
                        <i class="fas fa-calendar-alt me-1"></i>المواعيد
                    </a>

                    <!-- زر استيراد -->
                    <button class="btn btn-outline-primary btn-sm d-flex align-items-center rounded-pill px-3">
                        <i class="fas fa-cloud-upload-alt me-1"></i>استيراد
                    </button>

                    <!-- جزء التنقل بين الصفحات -->
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm mb-0">
                            <!-- زر الانتقال إلى أول صفحة -->
                            @if ($payments->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link border-0 rounded-pill" aria-label="First">
                                        <i class="fas fa-angle-double-right"></i>
                                    </span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link border-0 rounded-pill" href="{{ $payments->url(1) }}"
                                        aria-label="First">
                                        <i class="fas fa-angle-double-right"></i>
                                    </a>
                                </li>
                            @endif

                            <!-- زر الانتقال إلى الصفحة السابقة -->
                            @if ($payments->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link border-0 rounded-pill" aria-label="Previous">
                                        <i class="fas fa-angle-right"></i>
                                    </span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link border-0 rounded-pill" href="{{ $payments->previousPageUrl() }}"
                                        aria-label="Previous">
                                        <i class="fas fa-angle-right"></i>
                                    </a>
                                </li>
                            @endif

                            <!-- عرض رقم الصفحة الحالية -->
                            <li class="page-item">
                                <span class="page-link border-0 bg-light rounded-pill px-3">
                                    صفحة {{ $payments->currentPage() }} من {{ $payments->lastPage() }}
                                </span>
                            </li>

                            <!-- زر الانتقال إلى الصفحة التالية -->
                            @if ($payments->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link border-0 rounded-pill" href="{{ $payments->nextPageUrl() }}"
                                        aria-label="Next">
                                        <i class="fas fa-angle-left"></i>
                                    </a>
                                </li>
                            @else
                                <li class="page-item disabled">
                                    <span class="page-link border-0 rounded-pill" aria-label="Next">
                                        <i class="fas fa-angle-left"></i>
                                    </span>
                                </li>
                            @endif

                            <!-- زر الانتقال إلى آخر صفحة -->
                            @if ($payments->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link border-0 rounded-pill"
                                        href="{{ $payments->url($payments->lastPage()) }}" aria-label="Last">
                                        <i class="fas fa-angle-double-left"></i>
                                    </a>
                                </li>
                            @else
                                <li class="page-item disabled">
                                    <span class="page-link border-0 rounded-pill" aria-label="Last">
                                        <i class="fas fa-angle-double-left"></i>
                                    </span>
                                </li>
                            @endif
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center p-2">
                <div class="d-flex gap-2">
                    <span class="hide-button-text">
                        بحث وتصفية
                    </span>
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
                <form id="searchForm" class="form" method="GET" action="{{ route('paymentsClient.index') }}">
                    <div class="row g-3" id="basicSearchFields">
                        <!-- 1. رقم الفاتورة -->
                        <div class="col-md-4">
                            <label for="invoice_number" class="sr-only">رقم الفاتورة</label>
                            <input type="text" id="invoice_number" class="form-control" placeholder="رقم الفاتورة"
                                name="invoice_number" value="{{ request('invoice_number') }}">
                        </div>

                        <!-- 2. رقم عملية الدفع -->
                        <div class="col-md-4">
                            <label for="payment_number" class="sr-only">رقم عملية الدفع</label>
                            <input type="text" id="payment_number" class="form-control" placeholder="رقم عملية الدفع"
                                name="payment_number" value="{{ request('payment_number') }}">
                        </div>

                        <!-- 3. العميل -->
                        <div class="col-md-4">
                            <select name="customer" class="form-control" id="customer">
                                <option value="">اي العميل</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}"
                                        {{ request('customer') == $employee->id ? 'selected' : '' }}>{{ $employee->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- البحث المتقدم -->
                    <div class="collapse" id="advancedSearchForm">
                        <div class="row g-3 mt-2">
                            <!-- 4. حالة الدفع -->
                            <div class="col-md-4">
                                <select name="payment_status" class="form-control" id="payment_status">
                                    <option value="">حالة الدفع</option>
                                    <option value="1" {{ request('payment_status') == '1' ? 'selected' : '' }}>مدفوعة
                                    </option>
                                    <option value="0" {{ request('payment_status') == '0' ? 'selected' : '' }}>غير
                                        مدفوعة</option>
                                </select>
                            </div>

                            <!-- 5. التخصيص -->
                            <div class="col-md-2">
                                <select name="customization" class="form-control" id="customization">
                                    <option value="">تخصيص</option>
                                    <option value="1" {{ request('customization') == '1' ? 'selected' : '' }}>شهريًا
                                    </option>
                                    <option value="0" {{ request('customization') == '0' ? 'selected' : '' }}>
                                        أسبوعيًا</option>
                                    <option value="2" {{ request('customization') == '2' ? 'selected' : '' }}>يوميًا
                                    </option>
                                </select>
                            </div>

                            <!-- 6. من (التاريخ) -->
                            <div class="col-md-2">
                                <input type="date" id="from_date" class="form-control" placeholder="من"
                                    name="from_date" value="{{ request('from_date') }}">
                            </div>

                            <!-- 7. إلى (التاريخ) -->
                            <div class="col-md-2">
                                <input type="date" id="to_date" class="form-control" placeholder="إلى"
                                    name="to_date" value="{{ request('to_date') }}">
                            </div>

                            <!-- 8. رقم التعريفي -->
                            <div class="col-md-4">
                                <input type="text" id="identifier" class="form-control" placeholder="رقم التعريفي"
                                    name="identifier" value="{{ request('identifier') }}">
                            </div>

                            <!-- 9. رقم معرف التحويل -->
                            <div class="col-md-4">
                                <input type="text" id="transfer_id" class="form-control"
                                    placeholder="رقم معرف التحويل" name="transfer_id"
                                    value="{{ request('transfer_id') }}">
                            </div>

                            <!-- 10. الإجمالي أكبر من -->
                            <div class="col-md-4">
                                <input type="text" id="total_greater_than" class="form-control"
                                    placeholder="الاجمالي اكبر من" name="total_greater_than"
                                    value="{{ request('total_greater_than') }}">
                            </div>

                            <!-- 11. الإجمالي أصغر من -->
                            <div class="col-md-4">
                                <input type="text" id="total_less_than" class="form-control"
                                    placeholder="الاجمالي اصغر من" name="total_less_than"
                                    value="{{ request('total_less_than') }}">
                            </div>

                            <!-- 12. حقل مخصص -->
                            <div class="col-md-4">
                                <input type="text" id="custom_field" class="form-control" placeholder="حقل مخصص"
                                    name="custom_field" value="{{ request('custom_field') }}">
                            </div>

                            <!-- 13. منشأ الفاتورة -->
                            <div class="col-md-4">
                                <select name="invoice_origin" class="form-control" id="invoice_origin">
                                    <option value="">منشأ الفاتورة</option>
                                    <option value="1" {{ request('invoice_origin') == '1' ? 'selected' : '' }}>الكل
                                    </option>
                                </select>
                            </div>

                            <!-- 14. تم التحصيل بواسطة -->
                            <div class="col-md-4">
                                <select name="collected_by" class="form-control" id="collected_by">
                                    <option value="">تم التحصيل بواسطة</option>
                                    <option value="1" {{ request('collected_by') == '1' ? 'selected' : '' }}>الكل
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- الأزرار -->
                    <div class="form-actions mt-2">
                        <button type="submit" class="btn btn-primary">بحث</button>
                        <a class="btn btn-outline-secondary" data-toggle="collapse" href="#advancedSearchForm"
                            role="button">
                            <i class="bi bi-sliders"></i> بحث متقدم
                        </a>
                        <button type="reset" class="btn btn-outline-warning">إلغاء</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="card">
            <!-- الترويسة -->
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="d-flex gap-2 flex-wrap">
                    <button class="btn btn-sm btn-outline-primary">الكل</button>
                    <button class="btn btn-sm btn-outline-success">متأخر</button>
                    <button class="btn btn-sm btn-outline-danger">مستحقة الدفع</button>
                    <button class="btn btn-sm btn-outline-danger">غير مدفوع</button>
                    <button class="btn btn-sm btn-outline-secondary">مسودة</button>
                    <button class="btn btn-sm btn-outline-success">مدفوع بزيادة</button>
                </div>
            </div>

            <!-- بداية الصف -->
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="bg-light">
                            <tr>
                                <th width="20%">البيانات الأساسية</th>
                                <th width="15%">العميل</th>
                                <th width="15%">التاريخ والموظف</th>
                                <th width="15%" class="text-center">المبلغ</th>
                                <th width="15%" class="text-center">الحالة</th>
                                <th width="20%" class="text-end">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($payments->where('type', 'client payments') as $payment)
                                <tr>
                                    <td style="white-space: normal; word-wrap: break-word; min-width: 200px;">
                                        <div class="d-flex flex-column">
                                            <strong>#{{ $payment->id }}</strong>

                                            <small class="text-muted">
                                                @if ($payment->invoice)
                                                    الفاتورة: #{{ $payment->invoice->code ?? '--' }}
                                                @endif
                                            </small>

                                            @if ($payment->notes)
                                                <small class="text-muted mt-1" style="white-space: normal;">
                                                    <i class="fas fa-comment-alt"></i> {{ $payment->notes }}
                                                </small>
                                            @endif
                                        </div>
                                    </td>

                                    <td>
                                        @if ($payment->invoice->client)
                                            <div class="d-flex flex-column">
                                                <strong>{{ $payment->invoice->client->trade_name ?? '' }}</strong>
                                                <small class="text-muted">
                                                    <i class="fas fa-phone"></i>
                                                    {{ $payment->invoice->client->phone ?? '' }}
                                                </small>

                                            </div>
                                        @else
                                            <span class="text-danger">لا يوجد عميل</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <small><i class="fas fa-calendar"></i> {{ $payment->payment_date }}</small>
                                            @if ($payment->employee)
                                                <small class="text-muted mt-1">
                                                    <i class="fas fa-user"></i> {{ $payment->employee->name ?? '' }}
                                                </small>
                                            @endif
                                            <small class="text-muted mt-1">
                                                <i class="fas fa-clock"></i> {{ $payment->created_at->format('H:i') }}
                                            </small>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $currency = $account_setting->currency ?? 'SAR';
                                            $currencySymbol =
                                                $currency == 'SAR' || empty($currency)
                                                    ? '<img src="' .
                                                        asset('assets/images/Saudi_Riyal.svg') .
                                                        '" alt="ريال سعودي" width="15" style="vertical-align: middle;">'
                                                    : $currency;
                                        @endphp
                                        <h6 class="mb-0 font-weight-bold">
                                            {{ number_format($payment->amount, 2) }} {!! $currencySymbol !!}
                                        </h6>
                                        <small class="text-muted">
                                            {{ $payment->payment_method ?? 'غير محدد' }}
                                        </small>
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $statusClass = '';
                                            $statusText = '';
                                            $statusIcon = '';

                                            if ($payment->payment_status == 2) {
                                                $statusClass = 'badge-warning';
                                                $statusText = 'غير مكتمل';
                                                $statusIcon = 'fa-clock';
                                            } elseif ($payment->payment_status == 1) {
                                                $statusClass = 'badge-success';
                                                $statusText = 'مكتمل';
                                                $statusIcon = 'fa-check-circle';
                                            } elseif ($payment->payment_status == 4) {
                                                $statusClass = 'badge-info';
                                                $statusText = 'تحت المراجعة';
                                                $statusIcon = 'fa-sync';
                                            } elseif ($payment->payment_status == 5) {
                                                $statusClass = 'badge-danger';
                                                $statusText = 'فاشلة';
                                                $statusIcon = 'fa-times-circle';
                                            } elseif ($payment->payment_status == 3) {
                                                $statusClass = 'badge-secondary';
                                                $statusText = 'مسودة';
                                                $statusIcon = 'fa-file-alt';
                                            } else {
                                                $statusClass = 'badge-light';
                                                $statusText = 'غير معروف';
                                                $statusIcon = 'fa-question-circle';
                                            }
                                        @endphp
                                        <span class="badge {{ $statusClass }} rounded-pill">
                                            <i class="fas {{ $statusIcon }} me-1"></i>
                                            {{ $statusText }}
                                        </span>
                                        @if ($payment->payment_status == 1)
                                            <small class="d-block text-muted mt-1">
                                                <i class="fas fa-check-circle"></i> تم التأكيد
                                            </small>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group">
                                            <div class="dropdown">
                                                <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1 btn-sm"
                                                    type="button"id="dropdownMenuButton303" data-toggle="dropdown"
                                                    aria-haspopup="true"aria-expanded="false"></button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton303">
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li>
                                                            <a class="dropdown-item"
                                                                href="{{ route('paymentsClient.show', $payment->id) }}">
                                                                <i class="fas fa-eye me-2 text-primary"></i>عرض التفاصيل
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item"
                                                                href="{{ route('paymentsClient.edit', $payment->id) }}">
                                                                <i class="fas fa-edit me-2 text-success"></i>تعديل الدفع
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <form
                                                                action="{{ route('paymentsClient.destroy', $payment->id) }}"
                                                                method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="dropdown-item text-danger"
                                                                    onclick="return confirm('هل أنت متأكد من حذف هذه العملية؟')">
                                                                    <i class="fas fa-trash me-2"></i>حذف العملية
                                                                </button>
                                                            </form>
                                                        </li>
                                                        <li>
                                                            <hr class="dropdown-divider">
                                                        </li>

                                                        <li>
    @if (auth()->user()->role === 'employee')
        <button type="button" class="dropdown-item text-danger" onclick="showPermissionError()">
            <i class="fa fa-times me-2"></i>إلغاء عملية الدفع
        </button>
    @else
        <button type="button" class="dropdown-item text-danger" onclick="confirmCancelPayment({{ $payment->id }})">
            <i class="fa fa-times me-2"></i>إلغاء عملية الدفع
        </button>
    @endif
</li>

                                                        <li>
                                                            <a class="dropdown-item"
                                                                href="{{ route('paymentsClient.rereceipt', ['id' => $payment->id]) }}?type=a4"
                                                                target="_blank">
                                                                <i class="fas fa-file-pdf me-2 text-warning"></i>إيصال (A4)
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item"
                                                                href="{{ route('paymentsClient.rereceipt', ['id' => $payment->id]) }}?type=thermal"
                                                                target="_blank">
                                                                <i class="fas fa-receipt me-2 text-warning"></i>إيصال
                                                                (حراري)
                                                            </a>
                                                        </li>
                                                        @if ($payment->client)
                                                            <li>
                                                                <hr class="dropdown-divider">
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item"
                                                                    href="{{ route('clients.show', $payment->client->id) }}">
                                                                    <i class="fas fa-user me-2 text-info"></i>عرض بيانات
                                                                    العميل
                                                                </a>
                                                            </li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    </div>
@endsection
@section('scripts')
    <script src="{{ asset('assets/js/search.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function showPermissionError() {
        Swal.fire({
            icon: 'error',
            title: 'غير مصرح',
            text: 'ليس لديك صلاحية لإلغاء الدفع',
            confirmButtonText: 'حسناً'
        });
    }
</script>

    <script>
        // تعريف المتغيرات العامة
        window.routes = {
            payments: {
                cancel: '{{ route('paymentsClient.cancel', ':id') }}'
            }
        };
        window.csrfToken = '{{ csrf_token() }}';
    </script>

    <script>
        function confirmCancelPayment(id) {
            Swal.fire({
                title: 'هل أنت متأكد من إلغاء عملية الدفع؟',
                text: 'سيتم استعادة جميع الأرصدة كما كانت قبل عملية الدفع',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'نعم، إلغاء العملية',
                cancelButtonText: 'لا، تراجع',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return new Promise((resolve) => {
                        // استخدام المتغيرات العامة المعرفة في الأعلى
                        const cancelUrl = window.routes.payments.cancel.replace(':id', id);

                        $.ajax({
                            url: cancelUrl,
                            method: 'POST',
                            data: {
                                _token: window.csrfToken
                            },
                            success: function(response) {
                                resolve(response);
                            },
                            error: function(xhr) {
                                let errorMsg = 'حدث خطأ أثناء الإلغاء';

                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMsg = xhr.responseJSON.message;
                                } else if (xhr.status === 404) {
                                    errorMsg = 'عملية الدفع غير موجودة';
                                } else if (xhr.status === 403) {
                                    errorMsg = 'ليس لديك صلاحية لهذا الإجراء';
                                } else if (xhr.status === 419) {
                                    errorMsg = 'انتهت صلاحية الجلسة - يرجى تحديث الصفحة';
                                }

                                Swal.showValidationMessage(errorMsg);
                                resolve(false);
                            }
                        });
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    if (result.value) {
                        Swal.fire({
                            title: 'تم الإلغاء بنجاح',
                            text: 'تم إلغاء عملية الدفع واستعادة الأرصدة',
                            icon: 'success',
                            confirmButtonText: 'حسناً'
                        }).then(() => {
                            loadData(currentPage); // إعادة تحميل البيانات
                        });
                    }
                }
            });
        }
    </script>
@endsection
