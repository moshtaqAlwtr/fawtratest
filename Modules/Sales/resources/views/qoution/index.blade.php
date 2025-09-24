@extends('master')

@section('title')
    ادارة عروض السعر
@stop
<style>
    .table td, .table th {
    white-space: normal !important;
    word-break: break-word;
    vertical-align: middle;
}

</style>
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


                    <!-- زر عرض سعر جديد -->
                    <a href="{{ route('questions.create') }}" class="btn btn-success btn-sm d-flex align-items-center rounded-pill px-3">
                        <i class="fas fa-plus-circle me-1"></i>عرض سعر جديد
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
                                        {{ $client->trade_name }} - {{ $client->code }}
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


                            <!-- 5. الإجمالي أكبر من -->
                            <div class="col-md-3">
                                <label for="total_from">الإجمالي أكبر من</label>
                                <input type="number" class="form-control" placeholder="الإجمالي أكبر من"
                                    name="total_from" step="0.01" value="{{ request('total_from') }}">
                            </div>

                            <!-- 6. الإجمالي أصغر من -->
                            <div class="col-md-3">
                                <label for="total_to">الإجمالي أصغر من</label>
                                <input type="number" class="form-control" placeholder="الإجمالي أصغر من"
                                    name="total_to" step="0.01" value="{{ request('total_to') }}">
                            </div>

                            <!-- 7. الحالة -->


                            <!-- 8. التخصيص -->


                            <!-- 9. التاريخ من -->
                            <!--<div class="col-md-3">-->
                            <!--    <label for="from_date_1">التاريخ من</label>-->
                            <!--    <input type="date" class="form-control" placeholder="من"-->
                            <!--        name="from_date_1" value="{{ request('from_date_1') }}">-->
                            <!--</div>-->

                            <!-- 10. التاريخ إلى -->
                            <!--<div class="col-md-3">-->
                            <!--    <label for="to_date_1">التاريخ إلى</label>-->
                            <!--    <input type="date" class="form-control" placeholder="إلى"-->
                            <!--        name="to_date_1" value="{{ request('to_date_1') }}">-->
                            <!--</div>-->
                            <!--<div class="row">-->
    <!-- التاريخ من -->
    <div class="col-md-3">
        <label for="from_date_1" class="form-label fw-bold">التاريخ من</label>
        <input type="date" class="form-control" name="from_date_1"
               value="{{ request('from_date_1') }}">
    </div>

    <!-- التاريخ إلى -->
    <div class="col-md-3">
        <label for="to_date_1" class="form-label fw-bold">التاريخ إلى</label>
        <input type="date" class="form-control" name="to_date_1"
               value="{{ request('to_date_1') }}">
    </div>

    <!-- زر البحث -->



                            <!-- 11. التخصيص -->



                        </div>

                        <div class="row g-3 mt-2">

                            <!-- 15. أضيفت بواسطة -->
                            <div class="col-md-12">
                                <label for="created_by">أضيفت بواسطة</label>
                                <select name="created_by" class="form-control select2">
                                    <option value="">أضيفت بواسطة</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ request('created_by') == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->name ?? "" }}
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
    <div class="table-responsive">
        <table id="testTable" class="table table-hover nowrap text-center align-middle" style="width:100%">
    <thead class="bg-light" style="background-color: #BABFC7 !important;">
        <tr class="bg-gradient-light text-center">
            <th style="min-width: 60px;">#</th>
            <th style="min-width: 150px;">العميل</th>
            <th style="min-width: 120px;">الرقم الضريبي</th>
            <th style="min-width: 200px;">العنوان</th>
            <th style="min-width: 150px;">تاريخ العرض</th>
            <th style="min-width: 120px;">المبلغ</th>
            <th style="min-width: 100px;">الحالة</th>
            <th style="min-width: 120px;">بواسطة</th>
            <th style="min-width: 80px;">الخيارات</th>

                </tr>
            </thead>
            <tbody>
                @forelse ($quotes as $quote)
                    @php
                        $currency = $account_setting->currency ?? 'SAR';
                        $currencySymbol = $currency == 'SAR' || empty($currency)
                            ? '<img src="' . asset('assets/images/Saudi_Riyal.svg') . '" alt="ريال سعودي" width="15" style="vertical-align: middle;">'
                            : $currency;

                        $statusClass = $quote->status == 1 ? 'badge bg-success' : 'badge bg-info';
                        $statusText = $quote->status == 1 ? 'مفتوح' : 'مغلق';
                    @endphp
                    <tr>
                        <td><strong>#{{ $quote->id }}</strong></td>
                        <td>
                            {{ $quote->client ? ($quote->client->trade_name ?: $quote->client->first_name . ' ' . $quote->client->last_name) : 'عميل غير معروف' }}
                        </td>
                        <td>
                            {{ $quote->client->tax_number ?? '-' }}
                        </td>
                        <td>
                            {{ $quote->client->full_address ?? '-' }}
                        </td>
                        <td>
                            {{ $quote->created_at ? $quote->created_at->format('H:i:s d/m/Y') : '-' }}
                        </td>
                        <td>
                            <strong class="text-danger fs-6">
                                {{ number_format($quote->grand_total ?? $quote->total, 2) }}
                                {!! $currencySymbol !!}
                            </strong>
                        </td>
                        <td>
                            <span class="{{ $statusClass }}">
                                <i class="fas fa-circle me-1"></i> {{ $statusText }}
                            </span>
                        </td>
                        <td>
                            {{ $quote->creator->name ?? 'غير محدد' }}
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn bg-gradient-info fa fa-ellipsis-v" type="button"
                                    id="dropdownMenuButton{{ $quote->id }}" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $quote->id }}">
                                    <a class="dropdown-item" href="{{ route('questions.show', $quote->id) }}">
                                        <i class="fa fa-eye me-2 text-primary"></i>عرض
                                    </a>
                                    <a class="dropdown-item" href="{{ route('questions.pdf', $quote->id) }}">
                                        <i class="fa fa-file-pdf me-2 text-danger"></i>PDF
                                    </a>
                                    <a class="dropdown-item" href="{{ route('questions.email', $quote->id) }}">
                                        <i class="fa fa-envelope me-2 text-warning"></i>إرسال إلى العميل
                                    </a>
                                    <form action="{{ route('questions.destroy', $quote->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fa fa-trash me-2"></i>حذف
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9">
                            <div class="alert alert-warning m-0">
                                <i class="fas fa-exclamation-circle me-2"></i> لا توجد عروض أسعار
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>


    </div>
@endsection
@section('scripts')




    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script src="{{ asset('assets/js/search.js') }}"></script>
    <script></script>

<!-- jQuery و DataTables -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<!-- DataTables الأساسية -->
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

<!-- أزرار التصدير -->
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.70/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.70/vfs_fonts.js"></script>

<!-- تعريب -->
<script src="https://cdn.datatables.net/plug-ins/1.13.4/i18n/ar.json"></script>

<script>
    $(document).ready(function () {
        $('#testTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/ar.json'
            },
            // dom: 'Bfrtip',
            // buttons: [
            //     {
            //         extend: 'excel',
            //         text: 'تصدير إلى Excel',
            //         className: 'btn btn-success btn-sm'
            //     },
            //     {
            //         extend: 'print',
            //         text: 'طباعة',
            //         className: 'btn btn-warning btn-sm'
            //     },
            //     {
            //         extend: 'copy',
            //         text: 'نسخ',
            //         className: 'btn btn-info btn-sm'
            //     }
            // ]
        });
    });
</script>



@endsection
