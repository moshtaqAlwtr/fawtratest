<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تفاصيل تسجيل الدخول</title>
</head>
<body style="direction: rtl; text-align: right; font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">

    <table align="center" width="600" style="background: #ffffff; padding: 20px; border-radius: 10px; box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);">
        <tr>
            <td>
                <h2 style="color: #333;">مرحبًا {{ $details['name'] }}</h2>
                <p style="font-size: 16px; color: #555; line-height: 1.8;">تم إنشاء حساب لك بنجاح. إليك تفاصيل تسجيل الدخول:</p>

                <p style="background: #f8f9fa; padding: 10px; border-radius: 5px; font-size: 18px;"><strong>البريد الإلكتروني:</strong> {{ $details['email'] }}</p>
                <p style="background: #f8f9fa; padding: 10px; border-radius: 5px; font-size: 18px;"><strong>كلمة المرور:</strong> {{ $details['password'] }}</p>

                <p style="font-size: 16px;">يرجى تغيير كلمة المرور بعد تسجيل الدخول لأول مرة.</p>

                <div style="margin-top: 20px; font-size: 14px; color: #888; text-align: center;">
                    <p>شكرًا لك،</p>
                    <p>فريق الدعم</p>
                </div>
            </td>
        </tr>
    </table>

</body>
</html>
