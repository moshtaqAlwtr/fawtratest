<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سند قبض حراري</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            direction: rtl;
            text-align: right;
            margin: 0;
            padding: 0;
            width: 80mm; /* عرض الطابعة الحرارية */
            background: white;
            -webkit-print-color-adjust: exact;
            color-adjust: exact;
        }
        .container {
            padding: 5px;
            width: 100%;
        }
        .header {
            text-align: center;
            margin: 5px 0;
            padding-bottom: 10px;
            border-bottom: 2px dashed #333;
        }
        .header h2 {
            font-size: 20px;
            margin: 5px 0;
            color: #333;
        }
        .header h3 {
            font-size: 24px;
            margin: 5px 0;
            color: #000;
            font-weight: bold;
        }
        .company-info {
            font-size: 14px;
            color: #555;
            margin: 3px 0;
        }
        .details {
            margin: 15px 0;
            font-size: 16px;
        }
        .details p {
            margin: 8px 0;
            padding: 5px 0;
            border-bottom: 1px dotted #ccc;
            display: flex;
            justify-content: space-between;
        }
        .details .label {
            font-weight: bold;
            color: #333;
        }
        .details .value {
            font-weight: 600;
            color: #000;
        }
        .amount-row {
            background: #f8f9fa;
            padding: 8px;
            margin: 15px 0;
            border: 2px solid #007bff;
            border-radius: 5px;
            font-weight: bold;
        }
        .amount-row .value {
            font-size: 20px;
            color: #007bff;
        }
        .signature {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            font-size: 14px;
            padding-top: 10px;
            border-top: 2px dashed #333;
        }
        .barcode {
            text-align: center;
            margin: 15px 0;
            font-family: 'Libre Barcode 39', sans-serif;
            font-size: 24px;
        }
        .footer {
            text-align: center;
            margin: 10px 0;
            font-weight: bold;
            color: #28a745;
            font-size: 16px;
        }
        .stamp {
            text-align: left;
            margin-top: 10px;
        }
        .stamp-content {
            border: 2px solid #28a745;
            color: #28a745;
            padding: 3px 10px;
            transform: rotate(-15deg);
            display: inline-block;
            font-weight: bold;
            font-size: 16px;
            border-radius: 3px;
            background: rgba(40, 167, 69, 0.1);
        }
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>مؤسسة أعمال خاصة</h2>
            <h3>سند القبض</h3>
            <div class="company-info">الرياض - المملكة العربية السعودية</div>
        </div>

        <div class="details">
            <p>
                <span class="label">رقم السند:</span>
                <span class="value">{{ $income->code }}</span>
            </p>
            <p>
                <span class="label">التاريخ:</span>
                <span class="value">{{ $income->date }}</span>
            </p>
            <p>
                <span class="label">استلمنا من:</span>
                <span class="value">{{ $income->account->name }}</span>
            </p>
            <div class="amount-row">
                <span class="label">المبلغ:</span>
                <span class="value">{{ $income->amount }} ر.س</span>
            </div>
            <p>
                <span class="label">وذلك مقابل:</span>
                <span class="value">{{ $income->description }}</span>
            </p>
            <p>
                <span class="label">طريقة الدفع:</span>
                <span class="value">{{ $income->payment_method ?? 'نقدي' }}</span>
            </p>
            <p>
                <span class="label">المستلم:</span>
                <span class="value">{{ $income->received_by ?? 'غير محدد' }}</span>
            </p>
        </div>

        <div class="signature">
            <div>توقيع المستلم</div>
            <div>ختم المؤسسة</div>
        </div>

        <div class="stamp">
            <div class="stamp-content">مدفوع</div>
        </div>

        <div class="barcode">
            *{{ $income->code }}*
        </div>

        <div class="footer">
            شكراً لتعاملكم معنا
        </div>
    </div>

    <div class="no-print" style="text-align:center;margin:20px;">
        <button onclick="window.print()" style="padding:10px 20px;background:#007bff;color:white;border:none;border-radius:5px;cursor:pointer;">
            طباعة السند
        </button>
        <button onclick="window.close()" style="padding:10px 20px;background:#6c757d;color:white;border:none;border-radius:5px;cursor:pointer;margin-right:10px;">
            إغلاق النافذة
        </button>
    </div>

    <script>
        // طباعة تلقائية عند فتح الصفحة
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 200);
        };
    </script>
</body>
</html>
