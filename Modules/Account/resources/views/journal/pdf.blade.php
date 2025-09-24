<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>قيد يومية</title>
    <style>
        body {
            direction: rtl;
            font-family: 'Cairo', sans-serif;
            padding: 20px;
            margin: 0;
            font-size: 14px;
            background-color: #d3d9e1;
        }
        .entry-header {
            margin-bottom: 20px;
            text-align: right;
        }
        .entry-header h2 {
            margin: 0;
            font-size: 20px;
        }
        .entry-header .entry-number {
            font-size: 18px;
            color: #000;
        }
        .entry-info {
            margin-bottom: 10px;
            text-align: right;
        }
        .entry-info p {
            margin: 5px 0;
            font-size: 14px;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
        }
        th {
            background-color: #e8e8e8;
            border: 1px solid #aaa;
            padding: 10px;
            font-size: 14px;
            text-align: center;
        }
        td {
            border: 1px solid #aaa;
            padding: 10px;
            font-size: 14px;
            text-align: right;
        }
        .total-row td {
            font-weight: bold;
            background-color: #f4f4f4;
        }
        .total-row td:nth-child(3),
        .total-row td:nth-child(4) {
            color: #000;
        }
    </style>
</head>
<body>
    @php
        $total_debit = 0;
        $total_credit = 0;
    @endphp

    <div class="entry-header">
        <h2>قيد يومية <span class="entry-number">#{{$entry->id}}</span></h2>
        <div class="entry-info"></div>
            <p>التاريخ: {{ $entry->date}}</p>
            <p>الوصف: {{$entry->description }}</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th colspan="2">الحساب</th>

                <th>مدين</th>
                <th>دائن</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($entry) && is_array($entry))
                @foreach($entry as $item)
                    @php
                        $total_debit += $item->debit ?? 0;
                        $total_credit += $item->credit ?? 0;
                    @endphp
                    <tr>
                        <td>
                            @if(isset($item->account))
                                {{ $item->account->code }}
                            @endif
                        </td>
                        <td>{{ $item->account->name  }}</td>
                        <td>{{ isset($item->debit) ? number_format($item->debit, 2) : '' }}</td>
                        <td>{{ isset($item->credit) ? number_format($item->credit, 2) : '' }}</td>
                    </tr>
                @endforeach
            @elseif(isset($entry) && is_object($entry))
                @foreach($entry->details as $item)
                    @php
                        $total_debit += $item->debit ?? 0;
                        $total_credit += $item->credit ?? 0;
                    @endphp
                    <tr>
                        <td>
                            @if(isset($item->account))
                                {{ $item->account->code ?? '' }}
                            @endif
                        </td>
                        <td>{{ $item->account->name ?? '' }}</td>
                        <td>{{ isset($item->debit) ? number_format($item->debit, 2) : '' }}</td>
                        <td>{{ isset($item->credit) ? number_format($item->credit, 2) : '' }}</td>
                    </tr>
                @endforeach
            @endif
            <tr class="total-row">
                <td colspan="2">الإجمالى</td>
                <td>{{ number_format($total_debit, 2) }} ر.س</td>
                <td>{{ number_format($total_credit, 2) }} ر.س</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
