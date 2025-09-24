<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>قسيمة الراتب</title>
</head>
<body style="direction: rtl; text-align: right; font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">

    <table align="center" width="600" style="background: #ffffff; padding: 20px; border-radius: 10px; box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);">
        <tr>
            <td>
                <h2 style="color: #333; text-align: center;">قسيمة راتبك للفترة من {{ $details['from'] }} إلى {{ $details['to'] }}</h2>

                <p style="font-size: 16px; color: #555; line-height: 1.8;">عزيزي/عزيزتي <strong>{{ $details['name'] }}</strong>,</p>
                <p style="font-size: 16px;">أتمنى أن تكون بخير. فيما يلي تفاصيل راتبك:</p>

                <p style="background: #f8f9fa; padding: 10px; border-radius: 5px; font-size: 18px;">
                    <strong>تاريخ النشر:</strong> {{ $details['create'] }}
                </p>

                <p style="background: #f8f9fa; padding: 10px; border-radius: 5px; font-size: 18px;">
                    <strong>إجمالي الراتب:</strong> {{ number_format($details['total_salary'], 2) }} ريال
                </p>

                <p style="background: #f8f9fa; padding: 10px; border-radius: 5px; font-size: 18px;">
                    <strong>الخصومات:</strong> {{ number_format($details['total_deductions'], 2) }} ريال
                </p>

                <p style="background: #f8f9fa; padding: 10px; border-radius: 5px; font-size: 18px;">
                    <strong>صافي الراتب:</strong> {{ number_format($details['net_salary'], 2) }} ريال
                </p>

                <p style="font-size: 16px;">شكرًا لك على جهدك المستمر وتفانيك في العمل.</p>

                <div style="margin-top: 20px; font-size: 14px; color: #888; text-align: center;">
                    <p>مع أطيب التحيات،</p>
                    <p>إدارة الموارد البشرية</p>
                </div>
            </td>
        </tr>
    </table>

</body>
</html>
