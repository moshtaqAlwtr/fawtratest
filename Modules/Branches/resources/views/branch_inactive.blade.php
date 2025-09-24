<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الفرع غير نشط</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="text-center d-flex align-items-center justify-content-center vh-100">
    <div class="container">
        <h1 class="text-danger">⚠️ الفرع غير نشط</h1>
        <p class="lead">عذرًا، لا يمكنك تنفيذ أي عمليات لأن الفرع الخاص بك غير نشط. يرجى التواصل مع الإدارة.</p>
        <a href="{{ route('logout') }}" class="btn btn-primary">تسجيل الخروج</a>
    </div>
</body>
</html>
