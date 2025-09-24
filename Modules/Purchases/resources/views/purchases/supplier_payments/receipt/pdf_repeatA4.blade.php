<!DOCTYPE html>
<html dir="rtl" lang="ar">

<head>
    <meta charset="UTF-8">
    <title>دفعة لفاتورة مشتريات #{{ $receipt->purchase_invoice->id }}</title>
    <style>
        body {
            font-family: 'Arial', 'Tahoma', sans-serif;
            direction: rtl;
            margin: 0;
            padding: 20mm;
            background: #f5f5f5;
            font-size: 16px;
            line-height: 1.6;
        }

        .receipt-container {
            width: 100%;
            max-width: 170mm; /* عرض مناسب لـ A4 */
            margin: 0 auto;
            background: white;
            padding: 30mm;
            border: 3px solid #333;
            position: relative;
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
            min-height: 200mm; /* ارتفاع مناسب لـ A4 */
        }

        .receipt-header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 3px dashed #333;
        }

        .receipt-title {
            font-size: 28px;
            font-weight: bold;
            margin: 12px 0;
            color: #333;
            letter-spacing: 1px;
        }

        .company-name {
            font-size: 22px;
            font-weight: bold;
            margin: 8px 0;
            color: #555;
        }

        .company-address {
            font-size: 18px;
            color: #666;
            margin: 8px 0;
        }

        .receipt-info {
            margin: 30px 0;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 18px;
            font-size: 20px;
            padding: 12px 0;
            border-bottom: 2px dotted #ccc;
        }

        .info-label {
            font-weight: bold;
            color: #333;
            min-width: 120px;
        }

        .info-value {
            font-weight: 600;
            color: #000;
            text-align: left;
            flex: 1;
        }

        .amount-row {
            background: #f8f9fa;
            padding: 20px;
            margin: 25px 0;
            border: 3px solid #007bff;
            border-radius: 12px;
        }

        .amount-row .info-label {
            color: #007bff;
            font-size: 24px;
        }

        .amount-row .info-value {
            color: #007bff;
            font-size: 28px;
            font-weight: bold;
        }

        .payment-details {
            margin: 30px 0;
            padding: 20px;
            background: #f1f3f4;
            border-radius: 12px;
            text-align: center;
        }

        .payment-details div {
            font-size: 18px;
            margin: 12px 0;
            font-weight: 500;
        }

        .signature-area {
            margin-top: 40mm;
            text-align: center;
            font-size: 18px;
        }

        .signature-line {
            border-top: 3px solid #333;
            width: 80mm;
            margin: 30px auto;
            padding-top: 12px;
            font-size: 18px;
            font-weight: bold;
        }

        .thank-you {
            margin-top: 30px;
            font-size: 22px;
            font-weight: bold;
            color: #28a745;
        }

        .question {
            margin-top: 15px;
            font-size: 18px;
            color: #666;
            font-weight: 500;
        }

        .stamp {
            position: absolute;
            bottom: 60mm;
            left: 50mm;
            opacity: 0.8;
        }

        .stamp-content {
            border: 4px solid #28a745;
            color: #28a745;
            padding: 12px 20px;
            transform: rotate(-15deg);
            display: inline-block;
            font-weight: bold;
            font-size: 24px;
            border-radius: 8px;
            background: rgba(40, 167, 69, 0.1);
        }

        .receipt-number {
            font-size: 22px;
            font-weight: bold;
            color: #dc3545;
        }

        .receipt-date {
            font-size: 20px;
            font-weight: 600;
        }

        .dots {
            font-size: 24px;
            color: #999;
            margin: 20px 0;
            letter-spacing: 10px;
        }

        /* تحسينات للطباعة على A4 */
        @media print {
            body {
                background: white;
                padding: 0;
                margin: 0;
                font-size: 14px;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }

            .receipt-container {
                border: 2px solid #000;
                box-shadow: none;
                max-width: none;
                width: 190mm;
                height: 277mm; /* A4 height */
                padding: 20mm;
                margin: 0;
                page-break-after: always;
            }

            .receipt-title {
                font-size: 24px;
            }

            .info-row {
                font-size: 18px;
            }

            .amount-row .info-value {
                font-size: 24px;
            }

            .stamp-content {
                border: 3px solid #000;
                color: #000;
                font-size: 20px;
            }

            .signature-area {
                margin-top: 30mm;
            }

            .print-controls {
                display: none;
            }
        }

        /* أزرار التحكم */
        .print-controls {
            text-align: center;
            margin: 30px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 12px;
        }

        .print-controls button {
            background: #007bff;
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 8px;
            font-size: 18px;
            cursor: pointer;
            margin: 0 15px;
            font-family: inherit;
            transition: background 0.3s ease;
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

        /* تحسينات للشاشات المختلفة */
        @media screen and (max-width: 768px) {
            body {
                padding: 10px;
            }

            .receipt-container {
                max-width: 100%;
                padding: 20px;
                min-height: auto;
            }

            .signature-area {
                margin-top: 30px;
            }
        }

        /* تأثير الظل للنص */
        .receipt-title {
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        }
    </style>
</head>

<body>
    <div class="receipt-container">
        <div class="receipt-header">
            <div class="receipt-title">دفعة لفاتورة مشتريات {{ $receipt->purchase_invoice->id ?? 'غير محدد' }}</div>
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
            <div><strong>طريقة الدفع:</strong>
                @if ($receipt->Payment_method == 1)
                    نقدي
                @elseif ($receipt->Payment_method == 2)
                    كاش
                @elseif ($receipt->Payment_method == 3)
                    شيك
                @endif
            </div>
            <div><strong>رقم المرجع:</strong> {{ $receipt->reference_number ?? 'غير محدد' }}</div>
        </div>

        <div class="signature-area">
            <div class="dots">......</div>
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
            const controls = document.querySelector('.print-controls');
            if (controls) controls.style.display = 'none';
        });

        window.addEventListener('afterprint', function() {
            const controls = document.querySelector('.print-controls');
            if (controls) controls.style.display = 'block';
        });

        // تحسين تجربة المستخدم على الموبايل
        if ('ontouchstart' in window) {
            document.body.style.fontSize = '18px';
        }
    </script>
</body>

</html>