<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
</head>
<body>
    <h3>مرحبًا،</h3>
    <p>نرسل لكم عرض السعر رقم <strong>{{ $quote->quotes_number }}</strong>.</p>

    <p>
        لعرض عرض السعر، يرجى الضغط على الزر أدناه:
    </p>

    <p>
        <a href="{{ $viewUrl }}" target="_blank" style="background-color: #38a169; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
            عرض عرض السعر
        </a>
    </p>

    <p>مع تحيات فريق فواتيرا.</p>
</body>
</html>
