@extends('master')

@section('title')
    كشف حساب مورد
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
                            <div class="tab-pane fade show active" id="entry-details" role="tabpanel"
                                aria-labelledby="entry-details-tab">
                                <div class="pdf-view" style="background: lightslategray; min-height: 100vh; padding: 20px;">
                                    <div class="card shadow" style="max-width: 600px; margin: 20px auto;">
                                        <div class="card-body bg-white p-4">
                                            <div style="transform: scale(0.8); transform-origin: top center;">
                                                <div id="print-section">
                                                    <div dir="rtl" style="font-family: 'Cairo', sans-serif;">
                                                        <div style="text-align: center; margin-bottom: 20px;">
                                                            <h2 style="margin: 0;">كشف حساب مورد</h2>
                                                        </div>

                                                        <div style="margin-bottom: 20px;">
                                                            <p style="margin: 5px 0;">{{ $supplier->trade_name ?? '' }}</p>
                                                            <p style="margin: 5px 0;">{{ $supplier->region ?? '' }}</p>
                                                            <p style="margin: 5px 0;">{{ $supplier->phone ?? '' }}</p>
                                                        </div>

                                                        <table style="width: 100%; border-collapse: collapse; direction: rtl;">
                                                            <tr>
                                                                <td colspan="2"
                                                                    style="border: 1px solid #000; padding: 8px; text-align: right; direction: rtl;">
                                                                    مختصر الحساب حتى
                                                                    {{ \Carbon\Carbon::now()->format('d/m/Y') }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td style="border: 1px solid #000; padding: 8px;">الرصيد
                                                                    الافتتاحي</td>
                                                                <td style="border: 1px solid #000; padding: 8px; text-align: left;">
                                                                    {{ $supplier->opening_balance ?? 0 }} ﷼</td>
                                                            </tr>
                                                            <tr>
                                                                <td style="border: 1px solid #000; padding: 8px;">إجمالي المشتريات</td>
                                                                <td style="border: 1px solid #000; padding: 8px; text-align: left;">
                                                                    {{ $operationsPaginator->sum('deposit') }} ﷼</td>
                                                            </tr>
                                                            <tr>
                                                                <td style="border: 1px solid #000; padding: 8px;">إجمالي المدفوعات</td>
                                                                <td style="border: 1px solid #000; padding: 8px; text-align: left;">
                                                                    -{{ $operationsPaginator->sum('withdraw') }} ﷼</td>
                                                            </tr>
                                                            <tr>
                                                                <td style="border: 1px solid #000; padding: 8px;">المبلغ
                                                                    المستحق</td>
                                                                <td style="border: 1px solid #000; padding: 8px; text-align: left;">
                                                                    {{ $account->balance ?? 0 }} ﷼</td>
                                                            </tr>
                                                        </table>

                                                        </br>
                                                        </br>
                                                        <div style="text-align: right; font-weight: bold; font-size: 18px; margin-bottom: 10px; direction: rtl;">
                                                            حركة الحساب
                                                        </div>

                                                        <table style="width: 100%; border-collapse: collapse; direction: rtl;">
                                                            <thead>
                                                                <tr>
                                                                    <th style="border: 1px solid #000; padding: 8px; text-align: right;">التاريخ</th>
                                                                    <th style="border: 1px solid #000; padding: 8px; text-align: right;">العملية</th>
                                                                    <th style="border: 1px solid #000; padding: 8px; text-align: right;">المبلغ</th>
                                                                    <th style="border: 1px solid #000; padding: 8px; text-align: right;">الرصيد</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($operationsPaginator as $operation)
                                                                    <tr>
                                                                        <td style="border: 1px solid #000; padding: 8px;">
                                                                            {{ \Carbon\Carbon::parse($operation['date'])->format('Y-m-d') }}
                                                                        </td>
                                                                        <td style="border: 1px solid #000; padding: 8px;">
                                                                            {{ $operation['operation'] }}</td>
                                                                        <td style="border: 1px solid #000; padding: 8px;">
                                                                            @if ($operation['deposit'])
                                                                                {{ number_format($operation['deposit'], 2) }}
                                                                            @elseif($operation['withdraw'])
                                                                                -{{ number_format($operation['withdraw'], 2) }}
                                                                            @else
                                                                                0
                                                                            @endif
                                                                        </td>
                                                                        <td style="border: 1px solid #000; padding: 8px;">
                                                                            {{ number_format($operation['balance_after'], 2) }}
                                                                        </td>
                                                                    </tr>
                                                                @endforeach

                                                                <tr style="font-weight: bold;">
                                                                    <td colspan="3" style="border: 1px solid #000; padding: 8px;">المبلغ المستحق</td>
                                                                    <td style="border: 1px solid #000; padding: 8px;">
                                                                        {{ number_format($account->balance ?? 0, 2) }} ر.س
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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
        document.querySelector('.print-button').addEventListener('click', function() {
            var content = document.getElementById('print-section').innerHTML;
            var printWindow = window.open('', '', 'height=600,width=800');
            printWindow.document.write('<html><head><title>طباعة كشف حساب مورد</title>');
            printWindow.document.write(
                '<style>table { width: 100%; border-collapse: collapse; } th, td { border: 1px solid #000; padding: 8px; text-align: right; font-family: "Cairo", sans-serif; } h2 { font-family: "Cairo", sans-serif; }</style>'
            );
            printWindow.document.write('</head><body dir="rtl">');
            printWindow.document.write(content);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
            printWindow.close();
        });
    </script>
@endsection
