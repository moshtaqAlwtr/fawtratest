<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
  <meta charset="UTF-8" />
  <title>التقرير الموحد للموظف {{ $user->name }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <style>
    * { box-sizing: border-box; }
    body {
      font-family: 'Cairo', sans-serif;
      margin: 30px;
      background-color: #f4f6f9;
      color: #2c3e50;
      font-size: 15px;
    }
    h2 { margin-bottom: 20px; color: #2c3e50; }

    .card {
      background: #fff;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
      margin-bottom: 25px;
    }

    form label {
      font-weight: bold;
      margin-left: 10px;
    }

    form select, form input, form button {
      padding: 8px 12px;
      margin-left: 10px;
      border-radius: 8px;
      border: 1px solid #ccc;
      font-family: inherit;
    }

    form button {
      background-color: #3498db;
      color: #fff;
      border: none;
      cursor: pointer;
      transition: 0.3s;
    }

    form button:hover { background-color: #2980b9; }

    .btn-print {
      float: left;
      margin-bottom: 20px;
      background-color: #28a745;
      color: #fff;
      border: none;
      padding: 8px 16px;
      border-radius: 8px;
      cursor: pointer;
      font-size: 14px;
    }

    .btn-print:hover { background-color: #218838; }

    table {
      width: 100%;
      border-collapse: collapse;
      background-color: #fff;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      margin-top: 20px;
    }

    th, td {
      padding: 14px 10px;
      text-align: center;
      border-bottom: 1px solid #eee;
    }

    th {
      background-color: #2c3e50;
      color: white;
      font-size: 14px;
    }

    tr:nth-child(even) { background-color: #f9f9f9; }

    td.highlight-green {
      background-color: #d4edda;
      color: #155724;
      font-weight: bold;
    }

    td.highlight-red {
      background-color: #f8d7da;
      color: #721c24;
      font-weight: bold;
    }

    td.highlight-orange {
      background-color: #fff3cd;
      color: #856404;
      font-weight: bold;
    }

    h4 { margin-top: 40px; }

    ul {
      padding: 15px;
      background-color: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    }

    ul li { margin-bottom: 6px; }

    @media print {
      form, .btn-print, h4 { display: none; }
      body { background-color: #fff; margin: 0; padding: 0; }
      table { box-shadow: none; border: 1px solid #ccc; }
      table th, table td {
        font-size: 13px;
        padding: 10px 6px;
      }
    }

    @media(max-width: 768px) {
      form label, form input, form select, form button {
        display: block;
        width: 100%;
        margin: 10px 0;
      }

      .btn-print {
        float: none;
        width: 100%;
        margin-bottom: 20px;
      }

      table { font-size: 12px; }
    }
  </style>
</head>
<body>

<h2>📊 التقرير الموحد للموظف: {{ $user->name }}</h2>

<button class="btn-print" onclick="window.print()">🖨️ طباعة التقرير</button>

<div class="card">
  <form method="GET" action="{{ route('ABO_FALEH.reportTrac') }}">
    <label for="user_id">الموظف:</label>
    <select name="user_id" id="user_id" onchange="this.form.submit()">
      <option value="">المستخدم الحالي</option>
      @foreach($allUsers as $u)
        <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
      @endforeach
    </select>

    <label for="from_date">من تاريخ:</label>
    <input type="date" name="from_date" id="from_date" value="{{ request('from_date', $from->format('Y-m-d')) }}">

    <label for="to_date">إلى تاريخ:</label>
    <input type="date" name="to_date" id="to_date" value="{{ request('to_date', $to->format('Y-m-d')) }}">

    <button type="submit">تصفية</button>
  </form>
</div>

@if($all->isEmpty())
  <div class="card"><p>لا توجد بيانات للعرض.</p></div>
@else
  @php
      $grouped = $all->groupBy(fn($item) => $item['client'] . '_' . $item['date']);

      $total_receipt = $all->pluck('receipt')->filter(fn($v) => is_numeric($v))->sum();
      $total_payment = $all->pluck('payment')->filter(fn($v) => is_numeric($v))->sum();
      $total_invoice = $all->pluck('invoice')->filter(fn($v) => is_numeric($v))->sum();
      $total_expense = $all->pluck('expense')->filter(fn($v) => is_numeric($v))->sum();
      $total_minutes = 0;
  @endphp

  <table>
    <thead>
      <tr>
        <th>النوع</th>
        <th>المجموعة</th>
        <th>العميل</th>
        <th>الوصول</th>
        <th>الانصراف</th>
        <th>المدة (دقائق)</th>
        <th>التاريخ</th>
        <th>سند قبض</th>
        <th>المدفوع</th>
        <th>الفاتورة</th>
        <th>سند الصرف</th>
        <th>مرتجع أو أشعار</th>
       
        <th>ملاحظات المندوب</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($grouped as $rows)
        @php
          $row = $rows->first();
          $credit_note = $rows->pluck('credit_note')->filter(fn($v) => $v && $v !== '--')->first() ?? '--';

          $types = $rows->pluck('type')->unique()->implode(' + ');
          $arrival = $rows->pluck('arrival')->filter(fn($v) => $v && $v !== '--')->first() ?? '--';
          $departure = $rows->pluck('departure')->filter(fn($v) => $v && $v !== '--')->first() ?? '--';

          $duration = '--';
          if ($arrival !== '--' && $departure !== '--') {
              try {
                  $a = \Carbon\Carbon::parse($arrival);
                  $d = \Carbon\Carbon::parse($departure);
                  $duration = $d->diffInMinutes($a);
                  $total_minutes += $duration;
                  $duration .= ' د';
              } catch (\Exception $e) {
                  $duration = '--';
              }
          }

          $receipt = $rows->pluck('receipt')->filter(fn($v) => $v && $v !== '--')->first() ?? '--';
          $payment = $rows->pluck('payment')->filter(fn($v) => $v && $v !== '--')->first() ?? '--';
          $invoice = $rows->pluck('invoice')->filter(fn($v) => $v && $v !== '--')->first() ?? '--';
          $expense = $rows->pluck('expense')->filter(fn($v) => $v && $v !== '--')->first() ?? '--';
          $total_credit = $all->pluck('credit_note')->filter(fn($v) => is_numeric($v))->sum();
          $visitNote = $rows->pluck('description_visit')->filter(fn($v) => $v && $v !== '--')->first() ?? '--';
          $repNote = $rows->pluck('description_note')->filter()->first() ?? '--';
        @endphp
        <tr>
          <td>{{ $types }}</td>
          <td>{{ $row['group'] }}</td>
          <td>{{ $row['client'] }}</td>
          <td>{{ $arrival }}</td>
          <td>{{ $departure }}</td>
          <td>{{ $duration }}</td>
          <td>{{ $row['date'] }}</td>
          <td class="{{ $receipt !== '--' ? 'highlight-green' : '' }}">{{ $receipt }}</td>
          <td class="{{ $payment !== '--' ? 'highlight-green' : '' }}">{{ $payment }}</td>
          <td class="{{ $invoice !== '--' ? 'highlight-red' : '' }}">{{ $invoice }}</td>
          <td class="{{ $expense !== '--' ? 'highlight-orange' : '' }}">{{ $expense }}</td>
          <td class="{{ $credit_note !== '--' ? 'highlight-orange' : '' }}">{{ $credit_note }}</td> <!-- الجديد -->
          <td>{{ $repNote }}</td>
        </tr>
      @endforeach

      <tr style="background-color: #e2f0d9; font-weight: bold;">
        <td colspan="6">الإجمالي</td>
        <td>{{ $total_minutes }} د</td>
        <td>{{ number_format($total_receipt, 2) }}</td>
        <td>{{ number_format($total_payment, 2) }}</td>
        <td>{{ number_format($total_invoice, 2) }}</td>
        <td>{{ number_format($total_expense, 2) }}</td>
          <td>{{ number_format($total_credit, 2) }}</td> <!-- الجديد -->
        <td colspan="2">--</td>
      </tr>
    </tbody>
  </table>

  <h4>📝 الملاحظات غير المرتبطة</h4>
  <ul>
    @foreach ($all as $item)
      @if ($item['type'] === 'ملاحظة')
        <li>{{ $item['client'] }} - {{ $item['date'] }}: {{ $item['description_note'] }}</li>
      @endif
    @endforeach
  </ul>
@endif

</body>
</html>
