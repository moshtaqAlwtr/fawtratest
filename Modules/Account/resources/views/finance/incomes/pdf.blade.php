<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سند قبض</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            direction: rtl;
            text-align: right;
            margin: 0;
            padding: 0;
            background-color: #f7f9fc;
        }
        .container {
            background: #ffffff;
            padding: 30px;
            width: 90%;
            max-width: 600px;
            margin: 50px auto;
            border: 1px solid #d1e7dd;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #6c757d;
            font-size: 24px;
        }
        h3 {
            text-align: center;
            color: #495057;
            font-size: 20px;
        }
        .details {
            margin-top: 20px;
            font-size: 20px;
            color: #495057;
            line-height: 1.6;
        }
        .signature {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            font-weight: bold;
            color: #6c757d;
        }
        .signature div {
            width: 45%;
            text-align: center;
            border-top: 1px dashed #6c757d;
            padding-top: 10px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>مؤسسة أعمال خاصة للتجارة</h2>
        <h3>سند القبض</h3>
        <p><strong>رقم:</strong> {{ $income->code }}</p>
        <div class="details">
            <p><strong>استلمنا الى السيد:</strong> {{ $income->seller }}</p>
            <p><strong>مبلغ وقدره:</strong> {{ $income->amount }} رس</p>
            <p><strong>فقط:</strong> {{ $income->amount_in_words }}</p>
            <p><strong>بتاريخ:</strong> {{ $income->date }}</p>
            <p><strong>وذلك مقابل:</strong> {{ $income->description }}</p>
        </div>
        <div class="signature">
            <div>توقيع المستلم</div>
            <div>أمين الصندوق</div>
        </div>
        <div class="footer">© 2025 مؤسسة أعمال خاصة للتجارة</div>
    </div>
</body>
</html>
