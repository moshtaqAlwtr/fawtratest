{{-- resources/views/pos/sessions/print.blade.php --}}
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقرير الجلسة - {{ $session->session_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
            direction: rtl;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .report-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .session-info {
            margin-bottom: 20px;
        }
        
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        .info-table th,
        .info-table td {
            border: 1px solid #333;
            padding: 8px;
            text-align: right;
        }
        
        .info-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        
        .stats-section {
            margin-bottom: 20px;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            border-bottom: 1px solid #333;
            padding-bottom: 5px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .stat-card {
            border: 1px solid #333;
            padding: 10px;
        }
        
        .stat-card h4 {
            font-size: 12px;
            margin-bottom: 5px;
            text-decoration: underline;
        }
        
        .transactions-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .transactions-table th,
        .transactions-table td {
            border: 1px solid #333;
            padding: 6px;
            font-size: 10px;
            text-align: center;
        }
        
        .transactions-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        
        .balance-section {
            border: 2px solid #333;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .balance-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .balance-table td {
            padding: 5px;
            border-bottom: 1px dotted #666;
        }
        
        .balance-table .label {
            width: 60%;
            font-weight: bold;
        }
        
        .balance-table .value {
            width: 40%;
            text-align: left;
        }
        
        .total-row {
            border-top: 2px solid #000 !important;
            font-weight: bold;
            font-size: 14px;
        }
        
        .difference-positive {
            color: #000;
            background-color: #e8f5e8;
        }
        
        .difference-negative {
            color: #000;
            background-color: #ffe8e8;
        }
        
        .footer {
            margin-top: 30px;
            border-top: 1px solid #333;
            padding-top: 15px;
            text-align: center;
            font-size: 10px;
        }
        
        .signature-section {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
            margin-top: 40px;
            text-align: center;
        }
        
        .signature-box {
            border-top: 1px solid #333;
            padding-top: 10px;
            font-size: 11px;
        }
        
        @media print {
            body {
                margin: 0;
                font-size: 11px;
            }
            
            .container {
                padding: 10px;
            }
            
            .page-break {
                page-break-before: always;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        {{-- Header --}}
        <div class="header">
            <div class="company-name">{{ config('app.company_name', 'اسم الشركة') }}</div>
            <div class="report-title">تقرير جلسة نقطة البيع</div>
            <div>طُبع في: {{ now()->format('d/m/Y H:i:s') }}</div>
        </div>

        {{-- Session Information --}}
        <div class="session-info">
            <div class="section-title">معلومات الجلسة</div>
            <table class="info-table">
                <tr>
                    <th width="25%">رقم الجلسة</th>
                    <td width="25%">{{ $session->session_number }}</td>
                    <th width="25%">الحالة</th>
                    <td width="25%">
                        @switch($session->status)
                            @case('active')
                                نشطة
                                @break
                            @case('closed')
                                مغلقة
                                @break
                            @case('suspended')
                                معلقة
                                @break
                            @default
                                {{ $session->status }}
                        @endswitch
                    </td>
                </tr>
                <tr>
                    <th>الكاشير</th>
                    <td>{{ $session->user->name }}</td>
                    <th>الجهاز</th>
                    <td>{{ $session->device->device_name ?? 'غير محدد' }}</td>
                </tr>
                <tr>
                    <th>الوردية</th>
                    <td>{{ $session->shift->name ?? 'غير محدد' }}</td>
                    <th>المتجر</th>
                    <td>{{ $session->device->store->name ?? 'غير محدد' }}</td>
                </tr>
                <tr>
                    <th>تاريخ البداية</th>
                    <td>{{ $session->started_at->format('d/m/Y H:i:s') }}</td>
                    <th>تاريخ النهاية</th>
                    <td>
                        @if($session->ended_at)
                            {{ $session->ended_at->format('d/m/Y H:i:s') }}
                        @else
                            لا زالت نشطة
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>مدة الجلسة</th>
                    <td colspan="3">
                        @if($session->ended_at)
                            {{ $session->started_at->diff($session->ended_at)->format('%H ساعة %I دقيقة') }}
                        @else
                            {{ $session->started_at->diffForHumans() }}
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        {{-- Statistics --}}
        <div class="stats-section">
            <div class="section-title">إحصائيات المبيعات</div>
            <div class="stats-grid">
                <div class="stat-card">
                    <h4>المعاملات</h4>
                    <div>إجمالي المعاملات: {{ $session->total_transactions }}</div>
                    <div>عدد المبيعات: {{ $session->details->where('transaction_type', 'sale')->count() }}</div>
                    <div>عدد المرتجعات: {{ $session->details->where('transaction_type', 'return')->count() }}</div>
                </div>
                <div class="stat-card">
                    <h4>المبالغ المالية</h4>
                    <div>إجمالي المبيعات: {{ number_format($session->total_sales, 2) }} ر.س</div>
                    <div>النقدي: {{ number_format($session->total_cash, 2) }} ر.س</div>
                    <div>البطاقات: {{ number_format($session->total_card, 2) }} ر.س</div>
                </div>
            </div>
        </div>

        {{-- Cash Reconciliation --}}
        @if($session->status == 'closed')
            <div class="balance-section">
                <div class="section-title">تسوية الصندوق</div>
                <table class="balance-table">
                    <tr>
                        <td class="label">الرصيد الافتتاحي:</td>
                        <td class="value">{{ number_format($session->opening_balance, 2) }} ر.س</td>
                    </tr>
                    <tr>
                        <td class="label">النقدي المحصل:</td>
                        <td class="value">+ {{ number_format($session->total_cash, 2) }} ر.س</td>
                    </tr>
                    <tr>
                        <td class="label">المرتجعات النقدية:</td>
                        <td class="value">- {{ number_format($session->total_returns, 2) }} ر.س</td>
                    </tr>
                    <tr class="total-row">
                        <td class="label">الرصيد المتوقع:</td>
                        <td class="value">{{ number_format($session->closing_balance, 2) }} ر.س</td>
                    </tr>
                    <tr>
                        <td class="label">الرصيد الفعلي:</td>
                        <td class="value">{{ number_format($session->actual_closing_balance, 2) }} ر.س</td>
                    </tr>
                    <tr class="{{ $session->difference >= 0 ? 'difference-positive' : 'difference-negative' }}">
                        <td class="label">الفرق:</td>
                        <td class="value">
                            {{ $session->difference >= 0 ? '+' : '' }}{{ number_format($session->difference, 2) }} ر.س
                            ({{ $session->difference >= 0 ? 'زيادة' : 'نقص' }})
                        </td>
                    </tr>
                </table>
                
                @if($session->closing_notes)
                    <div style="margin-top: 10px;">
                        <strong>ملاحظات الإغلاق:</strong><br>
                        {{ $session->closing_notes }}
                    </div>
                @endif
            </div>
        @endif

        {{-- Transactions Details --}}
        @if($session->details && $session->details->count() > 0)
            <div class="section-title">تفاصيل المعاملات</div>
            <table class="transactions-table">
                <thead>
                    <tr>
                        <th>الوقت</th>
                        <th>النوع</th>
                        <th>المرجع</th>
                        <th>المبلغ</th>
                        <th>النقدي</th>
                        <th>البطاقة</th>
                        <th>الوصف</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($session->details->sortBy('transaction_time') as $detail)
                        <tr>
                            <td>{{ $detail->transaction_time->format('H:i:s') }}</td>
                            <td>
                                @switch($detail->transaction_type)
                                    @case('sale')
                                        بيع
                                        @break
                                    @case('return')
                                        إرجاع
                                        @break
                                    @case('opening_balance')
                                        افتتاحي
                                        @break
                                    @case('closing_balance')
                                        ختامي
                                        @break
                                    @case('cash_adjustment')
                                        تعديل
                                        @break
                                    @default
                                        {{ $detail->transaction_type }}
                                @endswitch
                            </td>
                            <td>{{ $detail->reference_number ?? '-' }}</td>
                            <td>{{ number_format($detail->amount, 2) }}</td>
                            <td>{{ number_format($detail->cash_amount, 2) }}</td>
                            <td>{{ number_format($detail->card_amount, 2) }}</td>
                            <td>{{ $detail->description ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        {{-- Summary --}}
        <div class="balance-section">
            <div class="section-title">ملخص الجلسة</div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <strong>الأداء المالي:</strong><br>
                    متوسط قيمة المعاملة: {{ $session->total_transactions > 0 ? number_format($session->total_sales / $session->total_transactions, 2) : '0.00' }} ر.س<br>
                    نسبة النقدي: {{ $session->total_sales > 0 ? number_format(($session->total_cash / $session->total_sales) * 100, 1) : '0' }}%<br>
                    نسبة البطاقات: {{ $session->total_sales > 0 ? number_format(($session->total_card / $session->total_sales) * 100, 1) : '0' }}%
                </div>
                <div>
                    <strong>الأداء التشغيلي:</strong><br>
                    @if($session->ended_at)
                        معاملات في الساعة: {{ $session->started_at->diffInHours($session->ended_at) > 0 ? number_format($session->total_transactions / $session->started_at->diffInHours($session->ended_at), 1) : '0' }}<br>
                    @endif
                    آخر معاملة: {{ $session->details->sortByDesc('transaction_time')->first()?->transaction_time?->diffForHumans() ?? 'لا توجد معاملات' }}
                </div>
            </div>
        </div>

        {{-- Signature Section --}}
        <div class="signature-section">
            <div class="signature-box">
                <div>الكاشير</div>
                <div style="margin-top: 5px;">{{ $session->user->name }}</div>
            </div>
            <div class="signature-box">
                <div>المراجع</div>
                <div style="margin-top: 5px;">........................</div>
            </div>
            <div class="signature-box">
                <div>المدير</div>
                <div style="margin-top: 5px;">........................</div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="footer">
            <div>هذا التقرير تم إنشاؤه تلقائياً من نظام نقطة البيع</div>
            <div>{{ config('app.company_name', 'اسم الشركة') }} - {{ now()->format('Y') }}</div>
            @if(config('app.vat_number'))
                <div>الرقم الضريبي: {{ config('app.vat_number') }}</div>
            @endif
        </div>
    </div>

    <script>
        // طباعة تلقائية عند تحميل الصفحة
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>