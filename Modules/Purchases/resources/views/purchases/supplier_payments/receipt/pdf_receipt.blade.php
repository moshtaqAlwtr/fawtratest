<!DOCTYPE html>
<html dir="rtl" lang="ar">

<head>
    <meta charset="UTF-8">
    <title>إيصال استلام #{{ $receipt->id }}</title>
    <style>
        body {
            font-family: 'Arial', 'Tahoma', sans-serif;
            direction: rtl;
            margin: 0;
            padding: 15px;
            background: #f5f5f5;
            font-size: 16px;
            line-height: 1.6;
        }

        .receipt-container {
            width: 100%;
            max-width: 350px;
            margin: 0 auto;
            background: white;
            padding: 25px;
            border: 2px solid #333;
            position: relative;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .receipt-header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px dashed #333;
        }

        .receipt-title {
            font-size: 28px;
            font-weight: bold;
            margin: 8px 0;
            color: #333;
        }

        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin: 5px 0;
            color: #555;
        }

        .company-address {
            font-size: 16px;
            color: #666;
            margin: 5px 0;
        }

        .receipt-info {
            margin: 20px 0;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 18px;
            padding: 8px 0;
            border-bottom: 1px dotted #ccc;
        }

        .info-label {
            font-weight: bold;
            color: #333;
            min-width: 80px;
        }

        .info-value {
            font-weight: 600;
            color: #000;
            text-align: left;
        }

        .amount-row {
            background: #f8f9fa;
            padding: 12px;
            margin: 15px 0;
            border: 2px solid #007bff;
            border-radius: 8px;
        }

        .amount-row .info-label {
            color: #007bff;
            font-size: 20px;
        }

        .amount-row .info-value {
            color: #007bff;
            font-size: 24px;
            font-weight: bold;
        }

        .payment-details {
            margin: 20px 0;
            padding: 15px;
            background: #f1f3f4;
            border-radius: 8px;
            text-align: center;
        }

        .payment-details div {
            font-size: 16px;
            margin: 8px 0;
            font-weight: 500;
        }

        .signature-area {
            margin-top: 30px;
            text-align: center;
            font-size: 16px;
        }

        .signature-line {
            border-top: 2px solid #333;
            width: 200px;
            margin: 25px auto;
            padding-top: 8px;
            font-size: 16px;
            font-weight: bold;
        }

        .thank-you {
            margin-top: 25px;
            font-size: 18px;
            font-weight: bold;
            color: #28a745;
        }

        .question {
            margin-top: 10px;
            font-size: 16px;
            color: #666;
        }

        .stamp {
            position: absolute;
            bottom: 40px;
            left: 40px;
            opacity: 0.8;
        }

        .stamp-content {
            border: 3px solid #28a745;
            color: #28a745;
            padding: 8px 15px;
            transform: rotate(-15deg);
            display: inline-block;
            font-weight: bold;
            font-size: 18px;
            border-radius: 5px;
            background: rgba(40, 167, 69, 0.1);
        }

        .receipt-number {
            font-size: 20px;
            font-weight: bold;
            color: #dc3545;
        }

        .receipt-date {
            font-size: 18px;
            font-weight: 600;
        }

        /* تحسينات للطباعة الحرارية */
        @media print {
            body {
                background: white;
                padding: 0;
                font-size: 14px;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }

            .receipt-container {
                border: 2px solid #000;
                box-shadow: none;
                max-width: 300px;
                padding: 20px;
            }

            .receipt-title {
                font-size: 24px;
            }

            .info-row {
                font-size: 16px;
            }

            .amount-row .info-value {
                font-size: 20px;
            }

            .stamp-content {
                border: 2px solid #000;
                color: #000;
            }
        }

        /* تحسينات للشاشات الصغيرة */
        @media screen and (max-width: 480px) {
            .receipt-container {
                max-width: 100%;
                margin: 0;
                border-radius: 0;
            }
        }

        /* أزرار التحكم - مخفية في الطباعة */
        .print-controls {
            text-align: center;
            margin: 20px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .print-controls button {
            background: #007bff;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            margin: 0 10px;
            font-family: inherit;
        }

        .print-controls button:hover {
            background: #0056b3;
        }

        .print-controls button.secondary {
            background: #6c757d;
        }

        .print-controls button.secondary:hover {
            background: #545b62;
        }

        @media print {
            .print-controls {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="receipt-container">
        <div class="receipt-header">
            <div class="receipt-title">دفعة لفاتورة مشتريات  {{ $receipt->purchase_invoice->id??'غير محدد' }}</div>
            <div class="company-name">{{ $receipt->branch->name ?? 'مؤسسة أعمال خاصة للنجارة' }}</div>
            <div class="company-address">{{ $receipt->branch->address ?? 'الرياض' }}</div>
        </div>

        <div class="receipt-info">
            <div class="info-row">
                <span class="info-label">رقم:</span>
                <span class="info-value receipt-number">{{ str_pad($receipt->id, 6, '0', STR_PAD_LEFT) }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">تاريخ:</span>
                <span class="info-value receipt-date">{{ $receipt->payment_date->format('d/m/Y') }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">من:</span>
                <span class="info-value">
                    {{ $receipt->purchase_invoice->supplier->trade_name ?? 'غير محدد' }}
                </span>
            </div>

            <div class="info-row amount-row">
                <span class="info-label">المبلغ:</span>
                <span class="info-value">{{ number_format($receipt->amount, 2) }} ريال</span>
            </div>

            <div class="info-row">
                <span class="info-label">المستلم:</span>
                <span class="info-value">{{ $receipt->employee->name ?? 'غير محدد' }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">الخزينة:</span>
                <span class="info-value">{{ $receipt->treasury->name ?? 'الخزينة الرئيسية' }}</span>
            </div>
        </div>

        <div class="payment-details">
            <div><strong>طريقة الدفع:</strong> @if ( $receipt->Payment_method == 1)
نقدي
            @elseif ( $receipt->Payment_method == 2)
            كاش
            @elseif ( $receipt->Payment_method == 3)
            شيك

            @endif</div>
            <div><strong>رقم المرجع:</strong> {{ $receipt->reference_number ?? 'غير محدد' }}</div>
        </div>

        <div class="signature-area">
            <div>......</div>
            <div class="signature-line">التوقيع: {{ $receipt->employee->name ?? 'غير محدد' }}</div>
            <div class="thank-you">شكراً لتعاملكم معنا</div>
            <div class="question">لديك سؤال؟ اتصل بنا</div>
        </div>

        @if ($receipt->payment_status == 1)
            <div class="stamp">
                <div class="stamp-content">مدفوع</div>
            </div>
        @endif
    </div>

    <!-- أزرار التحكم الاختيارية -->
    <div class="print-controls">
        <button onclick="printReceipt()">طباعة مرة أخرى</button>
        <button class="secondary" onclick="window.close()">إغلاق النافذة</button>
    </div>

    <script>
        // الطباعة التلقائية عند تحميل الصفحة
        window.addEventListener('load', function() {
            // تأخير قصير للتأكد من تحميل كامل للصفحة
            setTimeout(function() {
                window.print();
            }, 500);
        });

        // دالة للطباعة اليدوية
        function printReceipt() {
            window.print();
        }

        // إضافة خيار للطباعة بالضغط على Ctrl+P
        document.addEventListener('keydown', function(event) {
            if (event.ctrlKey && event.key === 'p') {
                event.preventDefault();
                printReceipt();
            }
        });

        // إخفاء أزرار التحكم أثناء الطباعة (احتياطي إضافي)
        window.addEventListener('beforeprint', function() {
            document.querySelector('.print-controls').style.display = 'none';
        });

        window.addEventListener('afterprint', function() {
            document.querySelector('.print-controls').style.display = 'block';
        });
    </script>
</body>

</html>
