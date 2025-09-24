@extends('master')

@section('title')
   كشف حساب عميل
@stop

@section('content')
    <div class="content-body">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex gap-2">
                        <strong></strong>
                        <span></span>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-success d-inline-flex align-items-center print-button">
                            <i class="fas fa-print me-1"></i> طباعة
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div class="d-flex gap-2">
                        <!-- تعديل -->

                        <!-- طباعة -->

                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-body">
                        <ul class="nav nav-tabs" role="tablist">

                            <li class="nav-item">
                               <a class="nav-link active" id="entry-details-tab" data-toggle="tab" href="#entry-details"
    role="tab" aria-controls="entry-details" aria-selected="true">تفاصيل كشف الحساب</a>

                            </li>

                        </ul>

                        <div class="tab-content mt-3">
                            <!-- القيد -->
                       <div class="tab-pane fade" id="entry" role="tabpanel" aria-labelledby="entry-tab">


                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="tab-pane fade show active"
                                            style="background: lightslategray; min-height: 100vh; padding: 20px;">
                                            <div class="card shadow" style="max-width: 600px; margin: 20px auto;">
                                                <div class="card-body bg-white p-4"
                                                    style="min-height: 400px; overflow: auto;">
                                                    <div style="transform: scale(0.8); transform-origin: top center;">

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- تفاصيل القيد -->
                           <div class="tab-pane fade show active" id="entry-details" role="tabpanel" aria-labelledby="entry-details-tab">

                                <div class="pdf-view" style="background: lightslategray; min-height: 100vh; padding: 20px;">

                                    <div class="card shadow" style="max-width: 600px; margin: 20px auto;">
                                        <div class="card-body bg-white p-4">
                                            <div style="transform: scale(0.8); transform-origin: top center;">
                                                <!-- PDF Content -->
                                                <div id="print-section">
                                                <div dir="rtl" style="font-family: 'Cairo', sans-serif;">
                                                    <!-- معلومات الشركة -->
                                                    <div style="text-align: center; margin-bottom: 25px; border-bottom: 2px solid #333; padding-bottom: 15px;">
                                                        <div style="margin-bottom: 10px;">
                                                            @if(isset($company_logo) && $company_logo)
                                                                <img src="{{ $company_logo }}" alt="شعار الشركة" style="max-height: 60px; margin-bottom: 10px;">
                                                            @endif
                                                        </div>
                                                        <h1 style="margin: 0; font-size: 24px; font-weight: bold; color: #333;">
                                                            {{ $company_name ?? 'موسسة اعمال خاصة ' }}
                                                        </h1>
                                                        @if(isset($company_description) && $company_description)
                                                            <p style="margin: 5px 0; font-size: 14px; color: #666;">
                                                                {{ $company_description }}
                                                            </p>
                                                        @endif
                                                        <div style="font-size: 12px; color: #555; margin-top: 8px;">
                                                            @if(isset($company_address) && $company_address)
                                                                <span>{{ $company_address }}</span>
                                                                @if(isset($company_phone) && $company_phone) | @endif
                                                            @endif
                                                            @if(isset($company_phone) && $company_phone)
                                                                <span>{{ $company_phone }}</span>
                                                                @if(isset($company_email) && $company_email) | @endif
                                                            @endif
                                                            @if(isset($company_email) && $company_email)
                                                                <span>{{ $company_email }}</span>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <div style="text-align: center; margin-bottom: 20px;">
                                                        <h2 style="margin: 0; font-size: 20px; color: #333;">كشف حساب عميل</h2>
                                                        <p style="margin: 5px 0; font-size: 12px; color: #666;">
                                                            تاريخ الإصدار: {{ \Carbon\Carbon::now()->format('d/m/Y') }}
                                                        </p>
                                                    </div>

                                                    <!-- معلومات العميل -->
                                                    <div style="margin-bottom: 20px; background-color: #f8f9fa; padding: 15px; border-radius: 5px;">
                                                        <h3 style="margin: 0 0 10px 0; font-size: 16px; color: #333;">بيانات العميل:</h3>
                                                        <p style="margin: 5px 0; font-size: 14px;"><strong>الاسم التجاري:</strong> {{ $client->trade_name ?? "غير محدد" }}</p>
                                                        <p style="margin: 5px 0; font-size: 14px;"><strong>المنطقة:</strong> {{ $client->region ?? "غير محدد" }}</p>
                                                        <p style="margin: 5px 0; font-size: 14px;"><strong>رقم الهاتف:</strong> {{ $client->phone ?? "غير محدد" }}</p>
                                                    </div>

                                                    <!-- ملخص الحساب -->
                                                    <table style="width: 100%; border-collapse: collapse; direction: rtl; margin-bottom: 20px;">
                                                        <tr>
                                                            <td colspan="2" style="border: 1px solid #000; padding: 10px; text-align: center; direction: rtl; background-color: #e9ecef; font-weight: bold; font-size: 16px;">
                                                                ملخص الحساب حتى {{ \Carbon\Carbon::now()->format('d/m/Y') }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="border: 1px solid #000; padding: 10px; font-weight: bold;">الرصيد الافتتاحي</td>
                                                            <td style="border: 1px solid #000; padding: 10px; text-align: left; font-weight: bold;">{{number_format($client->opening_balance ?? 0, 2)}} ﷼</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="border: 1px solid #000; padding: 10px; color: #28a745;">إجمالي الإيداعات</td>
                                                            <td style="border: 1px solid #000; padding: 10px; text-align: left; color: #28a745;">{{ number_format($operationsPaginator->sum('deposit'), 2) }}  ﷼</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="border: 1px solid #000; padding: 10px; color: #dc3545;">إجمالي المسحوبات</td>
                                                            <td style="border: 1px solid #000; padding: 10px; text-align: left; color: #dc3545;">{{ number_format($operationsPaginator->sum('withdraw'), 2) }} ﷼</td>
                                                        </tr>
                                                        <tr style="background-color: #fff3cd;">
                                                            <td style="border: 1px solid #000; padding: 10px; font-weight: bold; font-size: 16px;">الرصيد الحالي</td>
                                                            <td style="border: 1px solid #000; padding: 10px; text-align: left; font-weight: bold; font-size: 16px;">{{number_format($account->balance ?? 0, 2)}} ﷼</td>
                                                        </tr>
                                                    </table>

                                                    <!-- حركة الحساب -->
                                                    <div style="text-align: right; font-weight: bold; font-size: 18px; margin-bottom: 15px; direction: rtl;">
                                                        تفاصيل حركة الحساب
                                                    </div>

                                                    <table style="width: 100%; border-collapse: collapse; direction: rtl;">
                                                        <thead style="background-color: #e9ecef;">
                                                            <tr>
                                                                <th style="border: 1px solid #000; padding: 10px; text-align: center; font-weight: bold;">التاريخ</th>
                                                                <th style="border: 1px solid #000; padding: 10px; text-align: center; font-weight: bold;">نوع العملية</th>
                                                                <th style="border: 1px solid #000; padding: 10px; text-align: center; font-weight: bold;">المبلغ</th>
                                                                <th style="border: 1px solid #000; padding: 10px; text-align: center; font-weight: bold;">الرصيد بعد العملية</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($operationsPaginator as $operation)
                                                            <tr>
                                                                <td style="border: 1px solid #000; padding: 8px; text-align: center;">
                                                                    {{ \Carbon\Carbon::parse($operation['date'])->format('d/m/Y') }}
                                                                </td>
                                                                <td style="border: 1px solid #000; padding: 8px; text-align: center;
                                                                    @if(in_array($operation['operation'], ['سند', 'دفعة']))
                                                                        color: #dc3545; font-weight: bold;
                                                                    @elseif($operation['operation'] == 'فاتورة')
                                                                        color: #28a745; font-weight: bold;
                                                                    @endif">
                                                                    {{ $operation['operation'] }}
                                                                </td>
                                                                <td style="border: 1px solid #000; padding: 8px; text-align: center;
                                                                    @if($operation['deposit'])
                                                                        color: #28a745; font-weight: bold;
                                                                    @elseif($operation['withdraw'])
                                                                        color: #dc3545; font-weight: bold;
                                                                    @endif">
                                                                    @if($operation['deposit'])
                                                                        +{{ number_format($operation['deposit'], 2) }} ﷼
                                                                    @elseif($operation['withdraw'])
                                                                        -{{ number_format($operation['withdraw'], 2) }} ﷼
                                                                    @else
                                                                        0.00 ﷼
                                                                    @endif
                                                                </td>
                                                                <td style="border: 1px solid #000; padding: 8px; text-align: center; font-weight: bold;">
                                                                    {{ number_format($operation['balance_after'], 2) }} ﷼
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                        <tfoot>
                                                            <tr style="background-color: #fff3cd;">
                                                                <td colspan="3" style="border: 1px solid #000; padding: 10px; text-align: center; font-weight: bold; font-size: 16px;">
                                                                    الرصيد النهائي المستحق
                                                                </td>
                                                                <td style="border: 1px solid #000; padding: 10px; text-align: center; font-weight: bold; font-size: 16px;">
                                                                    {{ number_format($account->balance ?? 0, 2) }} ﷼
                                                                </td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>

                                                    <!-- تذييل الكشف -->
                                                    <div style="margin-top: 30px; text-align: center; font-size: 12px; color: #666; border-top: 1px solid #ddd; padding-top: 15px;">
                                                        <p style="margin: 5px 0;">تم إنشاء هذا الكشف آليًا في {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>
                                                        <p style="margin: 5px 0;">{{ $company_name ?? 'اسم الشركة' }} - جميع الحقوق محفوظة</p>
                                                    </div>

                                                </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <!-- سجل النشاطات -->
                            <div class="tab-pane fade" id="activity-log" role="tabpanel" aria-labelledby="activity-log-tab">
                                <div class="activity-log">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        document.querySelector('.print-button').addEventListener('click', function () {
            var content = document.getElementById('print-section').innerHTML;
            var printWindow = window.open('', '', 'height=600,width=800');
            printWindow.document.write('<html><head><title>كشف حساب عميل - طباعة</title>');
            printWindow.document.write('<style>');
            printWindow.document.write('body { font-family: "Cairo", sans-serif; margin: 20px; direction: rtl; }');
            printWindow.document.write('table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }');
            printWindow.document.write('th, td { border: 1px solid #000; padding: 8px; text-align: right; }');
            printWindow.document.write('th { background-color: #f8f9fa; font-weight: bold; text-align: center; }');
            printWindow.document.write('h1, h2, h3 { color: #333; }');
            printWindow.document.write('.company-header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 15px; margin-bottom: 25px; }');
            printWindow.document.write('.client-info { background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px; }');
            printWindow.document.write('.summary-row { background-color: #fff3cd; }');
            printWindow.document.write('.positive { color: #28a745; }');
            printWindow.document.write('.negative { color: #dc3545; }');
            printWindow.document.write('.footer { margin-top: 30px; text-align: center; font-size: 12px; color: #666; border-top: 1px solid #ddd; padding-top: 15px; }');
            printWindow.document.write('</style>');
            printWindow.document.write('</head><body>');
            printWindow.document.write(content);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
            printWindow.close();
        });
    </script>

@endsection
