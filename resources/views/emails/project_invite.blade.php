<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>دعوة للانضمام إلى مشروع</title>
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
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            padding: 30px;
            text-align: center;
            color: white;
        }
        .content {
            padding: 30px;
        }
        .project-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-right: 4px solid #4f46e5;
        }
        .btn {
            display: inline-block;
            padding: 15px 30px;
            background: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 10px 5px;
        }
        .btn-decline {
            background: #dc3545;
            padding: 10px 20px;
            font-size: 14px;
        }
        .message-box {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border-right: 3px solid #2196f3;
        }
        .note {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 20px;
            border-radius: 5px;
            margin: 25px 0;
            color: #856404;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #6c757d;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>دعوة للانضمام إلى مشروع</h1>
        </div>

        <div class="content">
            <h2>مرحباً!</h2>

            <p>تمت دعوتك من قبل <strong>{{ $details['inviter_name'] }}</strong> للانضمام إلى:</p>

            <div class="project-info">
                <h3 style="color: #4f46e5; margin: 0 0 10px 0;">{{ $details['project_title'] }}</h3>
                <p style="margin: 5px 0; color: #666;"><strong>مساحة العمل:</strong> {{ $details['workspace_title'] }}</p>
                <p style="margin: 5px 0; color: #666;"><strong>دورك في المشروع:</strong> {{ $details['role'] }}</p>
            </div>

            @if(isset($details['invite_message']) && $details['invite_message'])
            <div class="message-box">
                <p style="margin: 0; color: #1565c0;">
                    <strong>رسالة من {{ $details['inviter_name'] }}:</strong><br>
                    {{ $details['invite_message'] }}
                </p>
            </div>
            @endif

            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ $details['accept_url'] }}" class="btn">قبول الدعوة والانضمام</a>
                <br>
                <a href="{{ str_replace('/accept', '/decline', $details['accept_url']) }}" class="btn btn-decline">رفض الدعوة</a>
            </div>

            <div class="note">
                <p style="margin: 0; line-height: 1.5;">
                    <strong>ملاحظة مهمة:</strong><br>
                    • هذه الدعوة صالحة لمدة 7 أيام فقط<br>
                    • عند قبول الدعوة، ستحتاج لإنشاء حساب جديد وتعيين كلمة مرور<br>
                    • بعد التسجيل، ستتمكن من الوصول للمشروع والمشاركة في المهام<br>
                    • كلمة المرور المؤقتة: <strong>{{ $details['password'] }}</strong>
                </p>
            </div>

            <div class="footer">
                <p>
                    إذا لم تكن تتوقع هذه الدعوة، يمكنك تجاهل هذا البريد الإلكتروني بأمان.<br>
                    أو يمكنك الضغط على 'رفض الدعوة' لرفضها نهائياً.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
