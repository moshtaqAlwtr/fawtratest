<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تم إضافتك إلى مشروع</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
            direction: rtl;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            padding: 30px;
            text-align: center;
            color: white;
        }
        .content {
            padding: 30px;
        }
        .project-info {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-right: 4px solid #28a745;
        }
        .btn {
            display: inline-block;
            padding: 15px 30px;
            background: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>تم إضافتك لمشروع جديد!</h1>
        </div>

        <div class="content">
            <h2>مرحباً {{ $details['name'] }}</h2>

            <p>{{ $details['message'] }}</p>

            <div class="project-info">
                <h3 style="color: #28a745; margin: 0 0 10px 0;">{{ $details['project_title'] }}</h3>
                <p style="margin: 5px 0; color: #666;"><strong>دورك في المشروع:</strong> {{ $details['role'] }}</p>
            </div>

            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ route('projects.show', request()->route('project')) }}" class="btn">عرض المشروع الآن</a>
            </div>
        </div>
    </div>
</body>
</html>
