<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title> </title>
    <style>
        /* أنماط الصفحة الرئيسية - جميع النصوص عريضة */
        body {
            font-family: Arial, sans-serif;
            direction: rtl;
            background: white;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            font-size: 14px !important; /* زيادة حجم الخط العام */
            font-weight: bold !important;
        }

        .invoice-main-container {
            width: 80mm;
            padding: 8px; /* زيادة الحشوة قليلاً */
            margin: 0 auto;
            text-align: center;
        }

        /* أنماط الرأس */
        .header {
            text-align: center;
            margin-bottom: 8px;
            padding-bottom: 8px;
            border-bottom: 1px dashed #333;
        }

        .header h1 {
            margin: 0;
            padding: 0;
            font-size: 20px; /* زيادة حجم العنوان الرئيسي */
            font-weight: bold;
        }

        .header h2 {
            margin: 5px 0;
            font-size: 16px; /* زيادة حجم النص الثانوي */
        }

        .header p, .header h4 {
            margin: 4px 0;
            font-size: 14px; /* زيادة حجم النص */
        }

        /* أنماط معلومات العميل */
        .client-info {
            margin: 10px 0;
            text-align: right;
            padding-bottom: 8px;
            border-bottom: 1px dashed #333;
        }

        .client-info h3 {
            margin: 8px 0;
            font-size: 16px;
            font-weight: bold;
        }

        .client-info p {
            margin: 5px 0;
            font-size: 14px;
        }

        .invoice-meta {
            margin-top: 10px;
            padding-top: 8px;
            border-top: 1px dashed #ccc;
            font-size: 14px;
        }

        /* أنماط جدول العناصر */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px auto;
            font-size: 14px; /* زيادة حجم خط الجدول */
        }

        .items-table th {
            background-color: #f5f5f5;
            font-weight: bold;
            padding: 8px 5px; /* زيادة الحشوة */
            border-bottom: 1px solid #333;
            text-align: center;
        }

        .items-table td {
            padding: 8px 5px;
            border-bottom: 1px dashed #ddd;
            text-align: center;
            font-weight: bold;
        }

        /* أنماط قسم المجموع */
        .total-section {
            margin: 15px auto 0;
            padding-top: 8px;
            border-top: 1px dashed #333;
            width: 100%;
            font-size: 14px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin: 6px 0;
            padding: 4px 0;
        }

        .total-row:last-child {
            border-top: 1px dashed #333;
            padding-top: 8px;
        }

        /* أنماط التوقيع والباركود */
        .signature {
            margin: 15px auto 0;
            padding-top: 10px;
            border-top: 1px dashed #333;
            width: 90%;
            text-align: center;
            font-size: 14px;
        }

        .barcode {
            text-align: center;
            margin: 10px auto;
            padding: 8px 0;
        }

        .thank-you {
            font-style: italic;
            margin-top: 5px;
            font-size: 14px;
        }

        /* أنماط الطباعة */
        @media print {
            body {
                display: block !important;
                width: 80mm !important;
                font-size: 14px !important;
                background: white !important;
                font-weight: bold !important;
            }

            .invoice-main-container {
                box-shadow: none !important;
                margin: 0 auto !important;
                padding: 5px !important;
            }

            .barcode svg {
                width: 70px !important;
                height: 70px !important;
            }
        }
    </style>
</head>
<body>
    {!! $html !!}
    
    <!--<script>-->
    <!--    window.onload = function() {-->
    <!--        setTimeout(() => {-->
    <!--            window.print();-->
    <!--            setTimeout(() => {-->
    <!--                window.close();-->
    <!--            }, 500);-->
    <!--        }, 500);-->
    <!--    };-->
    <!--</script>-->
</body>
</html>