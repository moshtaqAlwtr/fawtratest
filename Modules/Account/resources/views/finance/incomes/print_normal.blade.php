<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سند قبض</title>
    <style>
        /* أنماط مشتركة بين النسختين */
        :root {
            --primary-color: #28a745; /* تغيير اللون الأساسي إلى الأخضر لسند القبض */
            --secondary-color: #6c757d;
            --border-color: #ddd;
            --highlight-color: #f8f9fa;
        }

        body {
            font-family: 'Arial', sans-serif;
            direction: rtl;
            text-align: right;
            margin: 0;
            padding: 0;
            background-color: white;
        }

        .container {
            padding: 15px;
            margin: 0 auto;
            position: relative;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--primary-color);
        }

        .header h2 {
            color: var(--secondary-color);
            margin: 5px 0;
        }

        .header h3 {
            color: var(--primary-color);
            margin: 5px 0;
            font-weight: bold;
        }

        .details {
            margin: 15px 0;
        }

        .details p {
            margin: 8px 0;
            padding: 5px 0;
            display: flex;
            justify-content: space-between;
        }

        .details .label {
            font-weight: bold;
            color: var(--secondary-color);
        }

        .details .value {
            font-weight: 600;
        }

        .amount-row {
            background: var(--highlight-color);
            padding: 8px;
            margin: 15px 0;
            border: 2px solid var(--primary-color);
            border-radius: 5px;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
        }

        .amount-row .value {
            color: var(--primary-color);
        }

        .amount-in-words {
            padding: 8px;
            margin: 10px 0;
            background: var(--highlight-color);
            border-radius: 5px;
        }

        .signature {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px dashed var(--secondary-color);
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            color: var(--secondary-color);
        }

        /* أنماط خاصة بالنسخة العادية (A4) */
        @media screen and (min-width: 200mm) {
            body {
                background-color: #f7f9fc;
            }

            .container {
                background: #ffffff;
                padding: 20px;
                width: 210mm;
                margin: 20px auto;
                border: 1px solid var(--border-color);
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            }

            .header h2 {
                font-size: 24px;
            }

            .header h3 {
                font-size: 22px;
            }

            .details {
                font-size: 16px;
            }

            .amount-row .value {
                font-size: 18px;
            }

            .watermark {
                position: absolute;
                opacity: 0.1;
                font-size: 80px;
                transform: rotate(-45deg);
                z-index: -1;
                top: 40%;
                left: 25%;
                color: var(--primary-color);
            }

            .signature {
                font-size: 16px;
            }

            .footer {
                font-size: 14px;
            }
        }

        /* أنماط خاصة بالنسخة الحرارية */
        @media screen and (max-width: 80mm) {
            body {
                width: 80mm;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }

            .container {
                padding: 5px;
                width: 100%;
            }

            .header h2 {
                font-size: 18px;
            }

            .header h3 {
                font-size: 20px;
            }

            .details {
                font-size: 14px;
            }

            .amount-row .value {
                font-size: 16px;
            }

            .signature {
                font-size: 14px;
            }

            .footer {
                font-size: 12px;
            }

            .barcode {
                text-align: center;
                margin: 15px 0;
                font-family: 'Libre Barcode 39', sans-serif;
                font-size: 24px;
            }

            .stamp {
                text-align: left;
                margin-top: 10px;
            }

            .stamp-content {
                border: 2px solid var(--secondary-color);
                color: var(--secondary-color);
                padding: 3px 10px;
                transform: rotate(-15deg);
                display: inline-block;
                font-weight: bold;
                font-size: 14px;
                border-radius: 3px;
                background: rgba(108, 117, 125, 0.1);
            }
        }

        @media print {
            .no-print {
                display: none;
            }

            .container {
                box-shadow: none;
                border: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>مؤسسة أعمال خاصة للتجارة</h2>
            <h3>سند قبض</h3>
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
            <div class="amount-in-words">
                <span>فقط: {{ $income->amount_in_words }}</span>
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
                <span class="value">{{ $income->treasury->name ?? 'الخزينة الرئيسية' }}</span>
            </p>
        </div>

        <div class="signature">
            <div>توقيع العميل</div>
            <div>أمين الصندوق</div>
        </div>

        <!-- عناصر خاصة بالنسخة الحرارية -->
        <div class="thermal-only">
            <div class="stamp">
                <div class="stamp-content">مسوغ</div>
            </div>
            <div class="barcode">
                *{{ $income->code }}*
            </div>
        </div>

        <div class="footer">
            © {{ date('Y') }} مؤسسة أعمال خاصة للتجارة
        </div>

        <!-- عنصر خاص بالنسخة العادية -->
        <div class="a4-only watermark">
            سند قبض
        </div>
    </div>

    <div class="no-print" style="text-align:center;margin:20px;">
        <button onclick="window.print()" style="padding:10px 20px;background:var(--primary-color);color:white;border:none;border-radius:5px;cursor:pointer;">
            طباعة السند
        </button>
        <button onclick="window.close()" style="padding:10px 20px;background:var(--secondary-color);color:white;border:none;border-radius:5px;cursor:pointer;margin-right:10px;">
            إغلاق النافذة
        </button>
    </div>

    <script>
        // إظهار/إخفاء العناصر حسب نوع السند
        function adjustLayout() {
            const isThermal = window.innerWidth <= 80 * 3.78; // 80mm تحويل إلى بكسل تقريبي

            document.querySelector('.thermal-only').style.display = isThermal ? 'block' : 'none';
            document.querySelector('.a4-only').style.display = isThermal ? 'none' : 'block';

            // طباعة تلقائية للنسخة الحرارية
            if (isThermal) {
                setTimeout(function() {
                    window.print();
                }, 200);
            }
        }

        window.onload = adjustLayout;
        window.onresize = adjustLayout;
    </script>
</body>
</html>
