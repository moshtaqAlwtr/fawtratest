@extends('client')

@section('title')
    ادارة عروض السعر
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">ادارة عروض السعر </h2>
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

                    <!-- زر عرض سعر جديد -->
                    <a href="{{ route('questions.create') }}" class="btn btn-success btn-sm d-flex align-items-center rounded-pill px-3">
                        <i class="fas fa-plus-circle me-1"></i>عرض سعر جديد
                    </a>

                    <!-- زر المواعيد -->
                    <a href="{{ route('appointments.index') }}" class="btn btn-outline-primary btn-sm d-flex align-items-center rounded-pill px-3">
                        <i class="fas fa-calendar-alt me-1"></i>المواعيد
                    </a>

                    <!-- زر استيراد -->
                    <a href="{{ route('questions.logsaction') }}" class="btn btn-outline-primary btn-sm d-flex align-items-center rounded-pill px-3">
                        <i class="fas fa-cloud-upload-alt me-1"></i>استيراد
                    </a>

                    <!-- جزء التنقل بين الصفحات -->
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm mb-0">
                            <!-- زر الانتقال إلى أول صفحة -->
                            @if ($quotes->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link border-0 rounded-pill" aria-label="First">
                                        <i class="fas fa-angle-double-right"></i>
                                    </span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link border-0 rounded-pill" href="{{ $quotes->url(1) }}" aria-label="First">
                                        <i class="fas fa-angle-double-right"></i>
                                    </a>
                                </li>
                            @endif

                            <!-- زر الانتقال إلى الصفحة السابقة -->
                            @if ($quotes->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link border-0 rounded-pill" aria-label="Previous">
                                        <i class="fas fa-angle-right"></i>
                                    </span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link border-0 rounded-pill" href="{{ $quotes->previousPageUrl() }}" aria-label="Previous">
                                        <i class="fas fa-angle-right"></i>
                                    </a>
                                </li>
                            @endif

                            <!-- عرض رقم الصفحة الحالية -->
                            <li class="page-item">
                                <span class="page-link border-0 bg-light rounded-pill px-3">
                                    صفحة {{ $quotes->currentPage() }} من {{ $quotes->lastPage() }}
                                </span>
                            </li>

                            <!-- زر الانتقال إلى الصفحة التالية -->
                            @if ($quotes->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link border-0 rounded-pill" href="{{ $quotes->nextPageUrl() }}" aria-label="Next">
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
                            @if ($quotes->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link border-0 rounded-pill" href="{{ $quotes->url($quotes->lastPage()) }}" aria-label="Last">
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
                <form id="searchForm" action="{{ route('questions.index') }}" method="GET" class="form">
                    <div class="row g-3">
                        <!-- 1. العميل -->
                        <div class="col-md-4">
                            <label for="clientSelect">العميل</label>
                            <select name="client_id" class="form-control select2" id="clientSelect">
                                <option value="">اي العميل</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                        {{ $client->trade_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- 2. رقم عرض السعر -->
                        <div class="col-md-4">
                            <label for="feedback2">رقم عرض السعر</label>
                            <input type="text" id="feedback2" class="form-control"
                                placeholder="رقم عرض السعر" name="id" value="{{ request('id') }}">
                        </div>

                        <!-- 3. الحالة -->
                        <div class="col-md-4">
                            <label for="statusSelect">الحالة</label>
                            <select name="status" class="form-control" id="statusSelect">
                                <option value="">الحالة</option>
                                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>مفتوح</option>
                                <option value="2" {{ request('status') == '2' ? 'selected' : '' }}> مغلق</option>
                            </select>
                        </div>
                    </div>

                    <!-- البحث المتقدم -->
                    <div class="collapse {{ request()->hasAny(['currency', 'total_from', 'total_to', 'date_type_1', 'date_type_2', 'item_search', 'created_by', 'sales_representative']) ? 'show' : '' }}" id="advancedSearchForm">
                        <div class="row g-3 mt-2">
                            <!-- 4. العملة -->
                            <div class="col-md-4">
                                <label for="currencySelect">العملة</label>
                                <select name="currency" class="form-control" id="currencySelect">
                                    <option value="">العملة</option>
                                    <option value="SAR" {{ request('currency') == 'SAR' ? 'selected' : '' }}>ريال سعودي</option>
                                    <option value="USD" {{ request('currency') == 'USD' ? 'selected' : '' }}>دولار أمريكي</option>
                                </select>
                            </div>

                            <!-- 5. الإجمالي أكبر من -->
                            <div class="col-md-2">
                                <label for="total_from">الإجمالي أكبر من</label>
                                <input type="number" class="form-control" placeholder="الإجمالي أكبر من"
                                    name="total_from" step="0.01" value="{{ request('total_from') }}">
                            </div>

                            <!-- 6. الإجمالي أصغر من -->
                            <div class="col-md-2">
                                <label for="total_to">الإجمالي أصغر من</label>
                                <input type="number" class="form-control" placeholder="الإجمالي أصغر من"
                                    name="total_to" step="0.01" value="{{ request('total_to') }}">
                            </div>

                            <!-- 7. الحالة -->

                        </div>

                        <div class="row g-3 mt-2">
                            <!-- 8. التخصيص -->
                            <div class="col-md-2">
                                <label for="date_type_1">التخصيص</label>
                                <select name="date_type_1" class="form-control">
                                    <option value="">تخصيص</option>
                                    <option value="monthly" {{ request('date_type_1') == 'monthly' ? 'selected' : '' }}>شهرياً</option>
                                    <option value="weekly" {{ request('date_type_1') == 'weekly' ? 'selected' : '' }}>أسبوعياً</option>
                                    <option value="daily" {{ request('date_type_1') == 'daily' ? 'selected' : '' }}>يومياً</option>
                                </select>
                            </div>

                            <!-- 9. التاريخ من -->
                            <div class="col-md-2">
                                <label for="from_date_1">التاريخ من</label>
                                <input type="date" class="form-control" placeholder="من"
                                    name="from_date_1" value="{{ request('from_date_1') }}">
                            </div>

                            <!-- 10. التاريخ إلى -->
                            <div class="col-md-2">
                                <label for="to_date_1">التاريخ إلى</label>
                                <input type="date" class="form-control" placeholder="إلى"
                                    name="to_date_1" value="{{ request('to_date_1') }}">
                            </div>

                            <!-- 11. التخصيص -->
                            <div class="col-md-2">
                                <label for="date_type_2">التخصيص</label>
                                <select name="date_type_2" class="form-control">
                                    <option value="">تخصيص</option>
                                    <option value="monthly" {{ request('date_type_2') == 'monthly' ? 'selected' : '' }}>شهرياً</option>
                                    <option value="weekly" {{ request('date_type_2') == 'weekly' ? 'selected' : '' }}>أسبوعياً</option>
                                    <option value="daily" {{ request('date_type_2') == 'daily' ? 'selected' : '' }}>يومياً</option>
                                </select>
                            </div>

                            <!-- 12. تاريخ الإنشاء من -->
                            <div class="col-md-2">
                                <label for="from_date_2">تاريخ الإنشاء من</label>
                                <input type="date" class="form-control" placeholder="من"
                                    name="from_date_2" value="{{ request('from_date_2') }}">
                            </div>

                            <!-- 13. تاريخ الإنشاء إلى -->
                            <div class="col-md-2">
                                <label for="to_date_2">تاريخ الإنشاء إلى</label>
                                <input type="date" class="form-control" placeholder="إلى"
                                    name="to_date_2" value="{{ request('to_date_2') }}">
                            </div>
                        </div>

                        <div class="row g-3 mt-2">
                            <!-- 14. تحتوي على البند -->
                            <div class="col-md-4">
                                <label for="item_search">تحتوي على البند</label>
                                <input type="text" class="form-control" placeholder="تحتوي على البند"
                                    name="item_search" value="{{ request('item_search') }}">
                            </div>

                            <!-- 15. أضيفت بواسطة -->
                            <div class="col-md-4">
                                <label for="created_by">أضيفت بواسطة</label>
                                <select name="created_by" class="form-control select2">
                                    <option value="">أضيفت بواسطة</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ request('created_by') == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- 16. مسؤول المبيعات -->
                            <div class="col-md-4">
                                <label for="sales_representative">مسؤول المبيعات</label>
                                <select name="sales_representative" class="form-control select2">
                                    <option value="">مسؤول المبيعات</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}" {{ request('sales_representative') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                        </div>
                    </div>

                    <!-- الأزرار -->
                    <div class="form-actions mt-2">
                        <button type="submit" class="btn btn-primary">بحث</button>
                        <a href="{{ route('questions.index') }}" type="reset" class="btn btn-outline-warning">إلغاء</a>
                    </div>
                </form>
            </div>
        </div>
        <div class="card">


            <!-- قائمة الفواتير -->
            @foreach ($quotes as $quote)
                <div class="card-body">
                    <div class="row border-bottom py-2 align-items-center">
                        <!-- معلومات الفاتورة -->
                        <div class="col-md-4">
                            <p class="mb-0">
                                <strong>#{{ $quote->id }}</strong>
                            </p>
                            <small class="text-muted">
                                <i class="fas fa-user me-1"></i>
                                {{ $quote->client ? ($quote->client->trade_name ?: $quote->client->first_name . ' ' . $quote->client->last_name) : 'عميل غير معروف' }}

                                الرقم الضريبي
                                @if ($quote->client && $quote->client->tax_number)
                                    <i class="fas fa- me-1"></i>{{ $quote->client->tax_number }}
                                @endif
                            </small>
                            <small class="d-block">
                                @if ($quote->client && $quote->client->full_address)
                                    <i class="fas fa-map-marker-alt me-1"></i>{{ $quote->client->full_address }}
                                @endif
                            </small>
                            <small class="text-muted">
                                <i class="fas fa-user-tie me-1"></i> بواسطة:
                                {{ $quote->creator->name ?? 'غير محدد' }}
                            </small>
                            <p class="mb-0 text-muted">
                                <i class="fas fa-mobile-alt me-1"></i> المصدر: تطبيق الهاتف المحمول
                            </p>
                        </div>

                        <!-- تاريخ الفاتورة -->
                        <div class="col-md-3">
                            <p class="mb-0">
                                <i class="fas fa-calendar-alt me-1"></i>
                                {{ $quote->created_at ? $quote->created_at->format('H:i:s d/m/Y') : '' }}
                            </p>
                            <small class="text-muted">
                                <i class="fas fa-user me-1"></i> بواسطة:
                                {{ $quote->creator->name ?? 'غير محدد' }}
                            </small>
                        </div>

                        <!-- المبلغ وحالة الدفع -->
                        <div class="col-md-3 text-center">
                            <!-- عرض المبلغ الإجمالي -->
                            <div class="mb-2">
                                <strong class="text-danger fs-2 d-block">
                                    {{ number_format($quote->grand_total ?? $quote->total, 2) }}
                                    {{ $quote->currency ?? 'SAR' }}
                                </strong>

                                <!-- عرض حالة الدفع مع تغيير اللون بناءً على الحالة -->
                                @php
                                    $statusClass = '';
                                    $statusText = '';

                                    if ($quote->status == 1) {
                                        $statusClass = 'bg-success';
                                        $statusText = 'مفتوح';
                                    } else {
                                        $statusClass = 'bg-info';
                                        $statusText = 'مغلق ';
                                    }
                                @endphp

                                <!-- عرض حالة الدفع -->
                                <span class="badge {{ $statusClass }} d-inline-block mt-2 p-1 rounded small"
                                    style="font-size: 0.8rem;">
                                    <i class="fas fa-circle me-1"></i> {{ $statusText }}
                                </span>
                            </div>
                        </div>

                        <!-- الأزرار -->
                        <div class="col-md-2 text-end">
                            <div class="btn-group">
                                <div class="dropdown">
                                    <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1" type="button"
                                        id="dropdownMenuButton{{ $quote->id }}" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $quote->id }}">
                                        <a class="dropdown-item" href="{{ route('questions.edit', $quote->id) }}">
                                            <i class="fa fa-edit me-2 text-success"></i>تعديل
                                        </a>
                                        <a class="dropdown-item" href="{{ route('questions.show', $quote->id) }}">
                                            <i class="fa fa-eye me-2 text-primary"></i>عرض
                                        </a>
                                        <a class="dropdown-item"
                                            href="{{ route('questions.create', ['id' => $quote->id]) }}">
                                            <i class="fa fa-money-bill me-2 text-success"></i>إضافة دفعة
                                        </a>
                                        <a class="dropdown-item" href="">
                                            <i class="fa fa-file-pdf me-2 text-danger"></i>PDF
                                        </a>
                                        <a class="dropdown-item" href="">
                                            <i class="fa fa-print me-2 text-dark"></i>طباعة
                                        </a>
                                        <a class="dropdown-item" href="">
                                            <i class="fa fa-envelope me-2 text-warning"></i>إرسال إلى العميل
                                        </a>

                                        <a class="dropdown-item" href="">
                                            <i class="fa fa-copy me-2 text-secondary"></i>نسخ
                                        </a>
                                        <form action="{{ route('questions.destroy', $quote->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="fa fa-trash me-2"></i>حذف
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            <!-- إذا لم تكن هناك فواتير -->
            @if ($quotes->isEmpty())
                <div class="alert alert-warning" role="alert">
                    <p class="mb-0"><i class="fas fa-exclamation-circle me-2"></i>لا توجد عروض اسعار </p>
                </div>
            @endif
        </div>

    </div>
@endsection
@section('scripts')
<script src="{{ asset('assets/js/search.js') }}"></script>
@endsection
