<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
     <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <title>ملصق الطرد</title>
    <style>
        /* إعادة تعيين الهوامش */
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            font-size: 12pt;
            line-height: 1.4;
        }
        .header {
            display: flex;
            justify-content: space-between;
            border-bottom: 1px solid #000;
            padding-bottom: 5mm;
            margin-bottom: 5mm;
        }
        .contact-info {
            text-align: left;
            direction: ltr;
        }
        .title {
            font-size: 16pt;
            font-weight: bold;
            text-align: right;
        }
        .invoice-number {
            text-align: center;
            margin: 5mm 0;
        }
       .address-section {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10mm;
    align-items: stretch; /* يجعل العناصر بنفس الارتفاع */
}

.invoice-to, .ship-to {
    width: 48%;
    display: flex;
    flex-direction: column;
    border: 1px solid #ddd; /* إطار اختياري للرؤية */
    padding: 5px;
    min-height: 100%; /* يتكيف مع المحتوى */
}

.invoice-to > div, .ship-to > div {
    flex: 1; /* يجعل العناصر الداخلية متساوية في الارتفاع */
}
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10mm;
        }
        th, td {
            border: 1px solid #000;
            padding: 3mm;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        .barcode {
            text-align: center;
            font-family: 'Libre Barcode 39', cursive;
            font-size: 24pt;
            margin-top: 10mm;
        }
          .barcode-container {
            text-align: center;
            margin-top: 20px;
        }
        .barcode {
            display: inline-block;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="title">ملصق الطرد</h1>
        <div class="contact-info">
            <div></div>
            <div>مؤسسة اعمال خاصة للتجارة</div>
            <div>info@fawtrasmart.com</div>
        </div>
    </div>
    
    <div class="invoice-number">فاتورة # {{$invoice->code ??"" }}:</div>
    
   <div class="address-section">
    <div class="invoice-to">
        <div style="font-weight: bold;">فاتورة إلى</div>
        <div style="margin-top: 5px;">{{$invoice->client->trade_name ?? ""}}</div>
    </div>
    <div class="ship-to">
        <div style="font-weight: bold;">انشحن إلى</div>
        <div style="margin-top: 5px;">{{$invoice->client->trade_name ?? ""}}</div>
    </div>
</div>
    
    <table>
        <thead>
            <tr>
                <th>البند</th>
                <th>الوصف</th>
                <th>الكمية</th>
                 <th>المجموع</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
            <tr>
                <td style="text-align: right;">{{ $item->item }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($item->unit_price, 2) }}</td>
                        <td>{{ number_format($item->total, 2) }}</td>
            </tr>
              @endforeach
        </tbody>
    </table>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // إنشاء الباركود
        JsBarcode('.barcode').init();
        
        // للطباعة: تأكد من تحميل الباركود قبل الطباعة
        window.addEventListener('beforeprint', function() {
            JsBarcode('.barcode').init();
        });
    });
</script>
</body>
</html>















