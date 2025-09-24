@extends('master')

@section('title')
    الاشعارات الدائنة
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
                    <h2 class="content-header-title float-left mb-0">ادارة الاشعارات الدائنة </h2>
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
                    

                    <!-- زر اشعة دائنة جديدة -->
                    <a href="{{ route('CreditNotes.create') }}" class="btn btn-success btn-sm d-flex align-items-center rounded-pill px-3">
                        <i class="fas fa-plus-circle me-1"></i>اشعة دائنة جديدة
                    </a>

                  
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
                <form   id="searchForm"  action="{{ route('CreditNotes.index') }}" method="GET" class="form">
                    <div class="row g-3">
                        <!-- 1. العميل -->
                        <div class="col-md-6">
                            <label for="client_id">العميل</label>
                            <select name="client_id" class="form-control select2" id="client_id">
                                <option value="">اي العميل</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                        {{ $client->trade_name }} - {{ $client->code }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- 2. رقم الإشعار -->
                        <div class="col-md-6">
                            <label for="invoice_number">رقم الإشعار</label>
                            <input type="text" id="invoice_number" class="form-control"
                                placeholder="رقم الإشعار" name="invoice_number" value="{{ request('invoice_number') }}">
                        </div>
                    </div>

                    <!-- البحث المتقدم -->
                    <div class="collapse {{ request()->hasAny(['item_search', 'currency', 'total_from', 'total_to', 'date_type_1', 'date_type_2', 'source', 'custom_field', 'created_by', 'shipping_option', 'post_shift', 'order_source']) ? 'show' : '' }}" id="advancedSearchForm">
                        <div class="row g-3 mt-2">
                         


                            <!-- 5. الإجمالي أكبر من -->
                            <div class="col-md-3">
                                <label for="total_from">الإجمالي أكبر من</label>
                                <input type="number" id="total_from" class="form-control" step="0.01"
                                    placeholder="الإجمالي أكبر من" name="total_from" value="{{ request('total_from') }}">
                            </div>

                            <!-- 6. الإجمالي أصغر من -->
                            <div class="col-md-3">
                                <label for="total_to">الإجمالي أصغر من</label>
                                <input type="number" id="total_to" class="form-control" step="0.01"
                                    placeholder="الإجمالي أصغر من" name="total_to" value="{{ request('total_to') }}">
                            </div>
                        

                    
                         
                            

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

                           </div>

                    

                        <div class="row g-3 mt-2">
                         

                            <!-- 15. أضيفت بواسطة -->
                            <div class="col-md-12">
                                <label for="created_by">أضيفت بواسطة</label>
                                <select name="created_by" class="form-control select2" id="created_by">
                                    <option value="">اختر المستخدم</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}" {{ request('created_by') == $user->id ? 'selected' : '' }}>
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
                        <a href="{{ route('CreditNotes.index') }}" type="reset" class="btn btn-outline-warning">إلغاء</a>
                    </div>
                </form>
            </div>
        </div>
        <div class="card">
    <div class="table-responsive">
        <table id="creditsTable" class="table table-hover nowrap text-center" style="width:100%">
            <thead class="bg-light" style="background-color: #BABFC7 !important;">
                <tr class="bg-gradient-light text-center">
                    <th style="min-width: 60px;">رقم الإشعار</th>
                    <th style="min-width: 150px;">العميل</th>
                    <th style="min-width: 120px;">الرقم الضريبي</th>
                    <th style="min-width: 200px;">العنوان</th>
                    <th style="min-width: 150px;">تاريخ الإشعار</th>
                    <th style="min-width: 120px;">المبلغ</th>
                    <th style="min-width: 100px;">الحالة</th>
                    <th style="min-width: 120px;">بواسطة</th>
                    <th style="min-width: 80px;">الخيارات</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($credits as $credit)
                    @php
                        $currency = $account_setting->currency ?? 'SAR';
                        $currencySymbol = $currency == 'SAR' || empty($currency)
                            ? '<img src="' . asset('assets/images/Saudi_Riyal.svg') . '" alt="ريال سعودي" width="15" style="vertical-align: middle;">'
                            : $currency;

                        $statusMap = [
                            1 => ['مسودة', 'bg-success'],
                            2 => ['قيد الانتظار', 'bg-warning'],
                            3 => ['معتمد', 'bg-primary'],
                            4 => ['تم التحويل إلى فاتورة', 'bg-info'],
                            5 => ['ملغى', 'bg-danger'],
                        ];

                        $statusText = $statusMap[$credit->status][0] ?? 'غير معروف';
                        $statusClass = $statusMap[$credit->status][1] ?? 'bg-secondary';
                    @endphp
                    <tr>
                        <td><strong>#{{ $credit->credit_number }}</strong></td>
                        <td>
                            {{ $credit->client ? ($credit->client->trade_name ?: $credit->client->first_name . ' ' . $credit->client->last_name) : 'عميل غير معروف' }}
                        </td>
                        <td>{{ $credit->client->tax_number ?? '-' }}</td>
                        <td>{{ $credit->client->full_address ?? '-' }}</td>
                        <td>{{ $credit->credit_date ?? '--' }}</td>
                        <td>
                            <strong class="text-danger fs-6">
                                {{ number_format($credit->grand_total, 2) }}
                                {!! $currencySymbol !!}
                            </strong>
                        </td>
                        <td>
                            <span class="badge {{ $statusClass }}">
                                <i class="fas fa-circle me-1"></i> {{ $statusText }}
                            </span>
                        </td>
                        <td>{{ $credit->createdBy->name ?? 'غير محدد' }}</td>
                        <td>
                            <div class="dropdown">
                                <button class="btn bg-gradient-info fa fa-ellipsis-v" type="button"
                                    id="dropdownMenuButton{{ $credit->id }}" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $credit->id }}">
                                    <a class="dropdown-item" href="{{ route('CreditNotes.edit', $credit->id) }}">
                                        <i class="fa fa-edit me-2 text-success"></i>تعديل
                                    </a>
                                    <a class="dropdown-item" href="{{ route('CreditNotes.show', $credit->id) }}">
                                        <i class="fa fa-eye me-2 text-primary"></i>عرض
                                    </a>
                                    <a class="dropdown-item" href="{{ route('CreditNotes.send', $credit->id) }}">
                                        <i class="fa fa-envelope me-2 text-warning"></i>إرسال إلى العميل
                                    </a>
                                    <form action="{{ route('CreditNotes.destroy', $credit->id) }}" method="POST" class="d-inline">
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
                                <i class="fas fa-exclamation-circle me-2"></i> لا توجد إشعارات دائنة
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
        $('#creditsTable').DataTable({
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
