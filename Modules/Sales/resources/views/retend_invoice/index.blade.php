@extends('master')

@section('title')
    ادارة الفواتير المرتجعة
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
                    <h2 class="content-header-title float-start mb-0">الفواتير المرتجعة</h2>
                    <div class="breadcrumb-wrapper">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسية</a></li>
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

    <div class="content-body">
        <div class="container-fluid">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">

                        <!-- زر استيراد -->
                        <button class="btn btn-outline-primary btn-sm d-flex align-items-center rounded-pill px-3">
                            <i class="fas fa-cloud-upload-alt me-1"></i>استيراد
                        </button>

                        <!-- جزء التنقل بين الصفحات -->

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
                    <form id="searchForm" class="form" method="GET" action="{{ route('ReturnIInvoices.index') }}">
                        <div class="row g-3">
                            <!-- 1. العميل -->
                            <div class="col-md-6">
                                <label for="client_id">أي العميل</label>
                                <select name="client_id" class="form-control select2" id="client_id">
                                    <option value="">أي العميل</option>
                                    @foreach ($clients as $client)
                                        <option value="{{ $client->id }}" data-client-number="{{ $client->id }}"
                                            data-client-name="{{ $client->trade_name }}"
                                            {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                            {{ $client->trade_name }} ({{ $client->code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- 2. رقم الفاتورة -->
                            <div class="col-md-6">
                                <label for="invoice_number">رقم الفاتورة</label>
                                <input type="text" id="invoice_number" class="form-control"
                                    placeholder="رقم الفاتورة" name="invoice_number" value="{{ request('invoice_number') }}">
                            </div>


                        </div>

                        <!-- البحث المتقدم -->
                        <div class="collapse" id="advancedSearchForm">
                            <div class="row g-3 mt-2">


                                <!-- 6. الإجمالي (من) -->
                                <div class="col-md-3">
                                    <label for="total_from">الإجمالي أكبر من</label>
                                    <input type="text" id="total_from" class="form-control"
                                        placeholder="الإجمالي أكبر من" name="total_from" value="{{ request('total_from') }}">
                                </div>

                                <!-- 7. الإجمالي (إلى) -->
                                <div class="col-md-3">
                                    <label for="total_to">الإجمالي أصغر من</label>
                                    <input type="text" id="total_to" class="form-control"
                                        placeholder="الإجمالي أصغر من" name="total_to" value="{{ request('total_to') }}">
                                </div>



                                <!-- 10. التاريخ (من) -->
                                <div class="col-md-3">
                                    <label for="from_date">التاريخ من</label>
                                    <input type="date" id="from_date" class="form-control" name="from_date"
                                        value="{{ request('from_date') }}">
                                </div>

                                <!-- 11. التاريخ (إلى) -->
                                <div class="col-md-3">
                                    <label for="to_date">التاريخ إلى</label>
                                    <input type="date" id="to_date" class="form-control" name="to_date"
                                        value="{{ request('to_date') }}">
                                </div>
                            </div>


                            <!-- 20. حالة التسليم -->
                            <div class="row g-3 mt-2">


                                <!-- 21. أضيفت بواسطة (الموظفين) -->
                                <div class="col-md-12">
                                    <label for="added_by_employee">أضيفت بواسطة (الموظفين)</label>
                                    <select name="added_by_employee" class="form-control" id="added_by_employee">
                                        <option value="">أضيفت بواسطة</option>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}" {{ request('added_by_employee') == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- 22. مسؤول المبيعات (المستخدمين) -->
                                <!--<div class="col-md-4">-->
                                <!--    <label for="sales_person_user">مسؤول المبيعات (المستخدمين)</label>-->
                                <!--    <select name="sales_person_user" class="form-control" id="sales_person_user">-->
                                <!--        <option value="">مسؤول المبيعات</option>-->
                                <!--        @foreach ($users as $user)-->
                                <!--            <option value="{{ $user->id }}" {{ request('sales_person_user') == $user->id ? 'selected' : '' }}>-->
                                <!--                {{ $user->name }}-->
                                <!--            </option>-->
                                <!--        @endforeach-->
                                <!--    </select>-->
                                <!--</div>-->
                            </div>


                        </div>

                        <!-- الأزرار -->
                        <div class="form-actions mt-2">
                            <button type="submit" class="btn btn-primary">بحث</button>
                            <a class="btn btn-outline-secondary" data-toggle="collapse" href="#advancedSearchForm" role="button">
                                <i class="bi bi-sliders"></i> بحث متقدم
                            </a>
                          <a href="{{ route('ReturnIInvoices.index') }}" type="reset"
                                class="btn btn-outline-warning">إلغاء</a>
                        </div>
                        </div>
                    </form>
                </div>
            </div>
<div class="card">
    <div class="table-responsive">
        <table id="returnInvoicesTable" class="table table-hover nowrap text-center" style="width:100%">
            <thead class="bg-light" style="background-color: #BABFC7 !important;">

                <tr>
                    <th style="min-width: 60px;">#</th>
                    <th style="min-width: 150px;">العميل</th>
                    <th style="min-width: 120px;">الرقم الضريبي</th>
                    <th style="min-width: 20px;">العنوان</th>
                    <th style="min-width: 150px;">التاريخ</th>
                    <th style="min-width: 120px;">المرجع</th>
                    <th  style="min-width: 100px;">المبلغ</th>
                    <th style="min-width: 120px;">بواسطة</th>
                    <th style="min-width: 80px;">الخيارات</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($return as $retur)
                    @php
                        $currency = $account_setting->currency ?? 'SAR';
                        $currencySymbol = $currency == 'SAR' || empty($currency)
                            ? '<img src="' . asset('assets/images/Saudi_Riyal.svg') . '" alt="ريال سعودي" width="15" style="vertical-align: middle;">'
                            : $currency;
                    @endphp
                    <tr>
                        <td><strong>#{{ $retur->id }}</strong></td>
                        <td>
                            {{ $retur->client ? ($retur->client->trade_name ?: $retur->client->first_name . ' ' . $retur->client->last_name) : 'عميل غير معروف' }}
                        </td>
                        <td>{{ $retur->client->tax_number ?? '-' }}</td>
                        <td>{{ $retur->client->full_address ?? '-' }}</td>
                        <td>{{ $retur->created_at->format('H:i:s d/m/Y') }}</td>
                        <td>
                            <span class="badge bg-warning">
                                <i class="fas fa-undo-alt"></i> #{{ $retur->reference_number ?? '--' }}
                            </span>
                        </td>
                        <td>
                            <strong class="text-danger">
                                {{ number_format($retur->grand_total ?? $retur->total, 2) }}
                                {!! $currencySymbol !!}
                            </strong>
                        </td>
                        <td>{{ $retur->createdByUser->name ?? 'غير محدد' }}</td>

                        <td>
                            <div class="dropdown">
                                <button class="btn bg-gradient-info fa fa-ellipsis-v" type="button"
                                    id="dropdownMenuButton{{ $retur->id }}" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $retur->id }}">
                                    <a class="dropdown-item" href="{{ route('ReturnIInvoices.edit', $retur->id) }}">
                                        <i class="fa fa-edit me-2 text-success"></i>تعديل
                                    </a>
                                    <a class="dropdown-item" href="{{ route('ReturnIInvoices.show', $retur->id) }}">
                                        <i class="fa fa-eye me-2 text-primary"></i>عرض
                                    </a>

                                    <a class="dropdown-item" href="{{ route('ReturnIInvoices.print', $retur->id) }}">
                                        <i class="fa fa-print me-2 text-dark"></i>طباعة
                                    </a>
                                    <a class="dropdown-item" href="">
                                        <i class="fa fa-envelope me-2 text-warning"></i>إرسال إلى العميل
                                    </a>
                                    <!--<a class="dropdown-item"-->
                                    <!--    href="{{ route('paymentsClient.create', ['id' => $retur->id]) }}">-->
                                    <!--    <i class="fa fa-credit-card me-2 text-info"></i>إضافة عملية دفع-->
                                    <!--</a>-->
                                    <form action="{{ route('invoices.destroy', $retur->id) }}" method="POST"
                                        class="d-inline">
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
                        <td colspan="10">
                            <div class="alert alert-warning m-0">
                                <i class="fas fa-exclamation-circle me-2"></i> لا توجد فواتير مرتجعة
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
        </div>
    </div>
@endsection

@section('scripts')

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="{{ asset('assets/js/search.js') }}"></script>

    <script>
        function filterInvoices(status) {
            const currentUrl = new URL(window.location.href);
            if (status) {
                currentUrl.searchParams.set('status', status);
            } else {
                currentUrl.searchParams.delete('status');
            }
            window.location.href = currentUrl.toString();
        }
    </script>


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
        $('#returnInvoicesTable').DataTable({
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

