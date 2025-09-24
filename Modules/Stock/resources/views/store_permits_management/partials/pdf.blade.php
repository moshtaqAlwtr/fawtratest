<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>إذن مخزني</title>
    <style>
        body {
            direction: rtl;
            font-family: aealarabiya;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border: 1px solid #000;
            padding: 10px;
        }

        .info-table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 5px;
            border-bottom: 1px dotted #999;
        }

        .products-table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
        }

        .products-table th,
        .products-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }

        .totals-table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
        }

        .totals-table td {
            padding: 5px;
            border: 1px solid #000;
        }

        .signatures {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>إذن مخزني</h2>
        <p>{{ $permit->created_at ?? now()->format('Y-m-d') }}</p>
        <p>مؤسسة اعمال عتمة للتجارة</p>
        <p>رقم الإذن: {{ $permit->code ?? 'WP-001' }}</p>
        <p>الحالة:
            @if(($permit->status ?? 'pending') == 'pending')
                قيد الانتظار
            @elseif(($permit->status ?? 'pending') == 'approved')
                موافق عليه
            @elseif(($permit->status ?? 'pending') == 'rejected')
                مرفوض
            @else
                قيد المعالجة
            @endif
        </p>
    </div>

    <table class="info-table">
        <tr>
            @if(($permit->permission_type ?? 1) == 2)
                <td>العميل:</td>
                <td>{{ optional($permit->client)->trade_name ?? 'غير محدد' }}</td>
            @elseif(($permit->permission_type ?? 1) == 1)
                <td>المورد:</td>
                <td>{{ optional($permit->supplier)->trade_name ?? 'غير محدد' }}</td>
            @else
                <td>نوع العملية:</td>
                <td>تحويل يدوي</td>
            @endif
            <td>رقم الإذن:</td>
            <td>{{ $permit->code ?? 'WP-001' }}</td>
        </tr>
        <tr>
            <td>الفرع:</td>
            <td>{{ optional($permit->branch)->name ?? 'الفرع الرئيسي' }}</td>
            <td>تاريخ الإذن:</td>
            <td>{{ $permit->created_at ?? now()->format('Y-m-d') }}</td>
        </tr>
        <tr>
            <td>المستودع:</td>
            <td>{{ optional($permit->storeHouse)->name ?? 'المستودع الرئيسي' }}</td>
            <td>مصدر الإذن:</td>
            <td>{{ optional($permit->permissionSource)->name ?? 'يدوي' }}</td>
        </tr>
        <tr>
            <td>أنشئ بواسطة:</td>
            <td>{{ optional($permit->createdBy)->name ?? 'المدير' }}</td>
            <td>الرقم المرجعي:</td>
            <td>{{ $permit->reference_number ?? '-' }}</td>
        </tr>
    </table>

    <h3>تفاصيل المنتجات:</h3>
    <table class="products-table">
        <thead>
            <tr>
                <th>#</th>
                <th>المنتج</th>
                <th>الوحدة</th>
                <th>الكمية</th>
                <th>سعر الوحدة</th>
                <th>إجمالي القيمة</th>
                <th>نوع العملية</th>
            </tr>
        </thead>
        <tbody>
            @if (isset($permit->items) && $permit->items->count() > 0)
                @foreach ($permit->items as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ optional($item->product)->name ?? 'منتج غير محدد' }}</td>
                        <td>{{ optional($item->unit)->name ?? 'قطعة' }}</td>
                        <td>{{ number_format($item->quantity ?? 0, 2) }}</td>
                        <td>{{ number_format($item->unit_price ?? 0, 2) }}</td>
                        <td>{{ number_format(($item->quantity ?? 0) * ($item->unit_price ?? 0), 2) }}</td>
                        <td>
                            @if(($permit->permission_type ?? 1) == 1)
                                إضافة
                            @elseif(($permit->permission_type ?? 1) == 2)
                                صرف
                            @else
                                تحويل
                            @endif
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td>1</td>
                    <td>منتج تجريبي</td>
                    <td>قطعة</td>
                    <td>10.00</td>
                    <td>25.50</td>
                    <td>255.00</td>
                    <td>
                        @if(($permit->permission_type ?? 1) == 1)
                            إضافة
                        @elseif(($permit->permission_type ?? 1) == 2)
                            صرف
                        @else
                            تحويل
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>منتج آخر</td>
                    <td>كيلو</td>
                    <td>5.00</td>
                    <td>45.00</td>
                    <td>225.00</td>
                    <td>
                        @if(($permit->permission_type ?? 1) == 1)
                            إضافة
                        @elseif(($permit->permission_type ?? 1) == 2)
                            صرف
                        @else
                            تحويل
                        @endif
                    </td>
                </tr>
            @endif
        </tbody>
    </table>

    <table class="totals-table">
        <tr>
            <td>عدد الأصناف:</td>
            <td>{{ $permit->items->count() ?? 2 }} صنف</td>
        </tr>
        <tr>
            <td>إجمالي الكمية:</td>
            <td>{{ number_format($permit->items->sum('quantity') ?? 15.00, 2) }}</td>
        </tr>

        @php
            $currency = $account_setting->currency ?? 'SAR';
            $currencySymbol =
                $currency == 'SAR' || empty($currency)
                    ? '<img src="' . asset('assets/images/Saudi_Riyal.svg') . '" alt="ريال سعودي" width="15">'
                    : $currency;
        @endphp

        <tr>
            <td>إجمالي القيمة:</td>
            <td>{{ number_format($permit->total_amount ?? 480.00, 2) }} {!! $currencySymbol !!}</td>
        </tr>
        <tr>
            <td>نوع الإذن:</td>
            <td>
                @if(($permit->permission_type ?? 1) == 1)
                    إذن إضافة
                @elseif(($permit->permission_type ?? 1) == 2)
                    إذن صرف
                @else
                    تحويل يدوي
                @endif
            </td>
        </tr>
        <tr>
            <td>حالة الإذن:</td>
            <td>
                @if(($permit->status ?? 'pending') == 'pending')
                    قيد الانتظار
                @elseif(($permit->status ?? 'pending') == 'approved')
                    موافق عليه
                @elseif(($permit->status ?? 'pending') == 'rejected')
                    مرفوض
                @else
                    قيد المعالجة
                @endif
            </td>
        </tr>
    </table>

    @if ($wareHousePermit->notes ?? $wareHousePermit->description ?? '')
        <div class="notes">
            <strong>ملاحظات:</strong> {{ $wareHousePermit->notes ?? $wareHousePermit->description }}
        </div>
    @endif

    <div class="signatures">
        <div>
            <p>توقيع مدير المستودع</p>
            <div style="margin-top: 40px; border-top: 1px solid #000; width: 150px;"></div>
        </div>
        <div>
            <p>توقيع المستلم</p>
            <div style="margin-top: 40px; border-top: 1px solid #000; width: 150px;"></div>
        </div>
        <div>
            <p>ختم الشركة</p>
            <div style="margin-top: 40px; border-top: 1px solid #000; width: 150px;"></div>
        </div>
    </div>
</body>

</html>