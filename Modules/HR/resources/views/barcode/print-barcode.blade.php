{{-- resources/views/attendance/print-barcode.blade.php --}}

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>باركود الموظف - {{ $employee->full_name }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap');

        body {
            font-family: 'Cairo', sans-serif;
            margin: 0;
            padding: 20px;
            background: #f5f5f5;
        }

        .print-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .barcode-card {
            background: white;
            border: 2px solid #000;
            border-radius: 10px;
            padding: 25px;
            text-align: center;
            page-break-inside: avoid;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
        }

        .barcode-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #007bff, #28a745, #007bff);
        }

        .company-header {
            border-bottom: 2px solid #007bff;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .company-name {
            font-size: 20px;
            font-weight: 700;
            color: #007bff;
            margin-bottom: 5px;
        }

        .card-title {
            font-size: 16px;
            color: #333;
            margin-bottom: 0;
        }

        .employee-photo {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #007bff;
            margin-bottom: 15px;
        }

        .employee-name {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .employee-details {
            font-size: 14px;
            color: #666;
            margin-bottom: 20px;
        }

        .barcode-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 15px 0;
            border: 1px dashed #007bff;
        }

        .barcode-title {
            font-size: 14px;
            font-weight: 600;
            color: #007bff;
            margin-bottom: 10px;
        }

        .barcode-canvas {
            background: white;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
        }

        .barcode-text {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            background: #e9ecef;
            padding: 8px 12px;
            border-radius: 5px;
            letter-spacing: 1px;
            margin-top: 10px;
            border: 1px solid #ced4da;
        }

        .qr-section {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
        }

        .instructions {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
            text-align: right;
        }

        .instructions-title {
            font-weight: 600;
            color: #856404;
            margin-bottom: 10px;
        }

        .instructions-list {
            font-size: 13px;
            color: #856404;
            line-height: 1.6;
            margin: 0;
            padding-right: 20px;
        }

        .footer-info {
            margin-top: 25px;
            padding-top: 15px;
            border-top: 1px solid #dee2e6;
            font-size: 11px;
            color: #6c757d;
        }

        .no-print {
            background: #007bff;
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }

        .no-print button {
            background: white;
            color: #007bff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            margin: 0 5px;
            cursor: pointer;
            font-weight: 600;
        }

        .no-print button:hover {
            background: #f8f9fa;
        }

        /* طباعة */
        @media print {
            .no-print { display: none !important; }
            body { background: white; }
            .barcode-card {
                box-shadow: none;
                border: 2px solid #000;
                margin-bottom: 30px;
            }
            @page {
                margin: 1cm;
                size: A4;
            }
        }

        /* شاشات صغيرة */
        @media (max-width: 768px) {
            .print-container {
                grid-template-columns: 1fr;
                padding: 10px;
            }

            .barcode-card {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- أزرار التحكم (لا تطبع) -->
    <div class="no-print">
        <h3>باركود الموظف: {{ $employee->full_name }}</h3>
        <button onclick="window.print()">
            <i class="fa fa-print"></i> طباعة الباركود
        </button>
        <button onclick="downloadBarcode()">
            <i class="fa fa-download"></i> تحميل كصورة
        </button>
        <button onclick="window.close()">
            <i class="fa fa-times"></i> إغلاق
        </button>
    </div>

    <div class="print-container">
        <!-- كارت الباركود الرئيسي -->
        <div class="barcode-card">
            <!-- رأس الشركة -->
            <div class="company-header">
                <div class="company-name">{{ config('app.name', 'شركتك') }}</div>
                <div class="card-title">بطاقة حضور وانصراف</div>
            </div>

            <!-- معلومات الموظف -->
            @if($employee->employee_photo)
                <img src="{{ asset('storage/' . $employee->employee_photo) }}"
                     alt="{{ $employee->full_name }}" class="employee-photo">
            @else
                <div class="employee-photo" style="background: #007bff; display: flex; align-items: center; justify-content: center;">
                    <i class="fa fa-user" style="color: white; font-size: 24px;"></i>
                </div>
            @endif

            <div class="employee-name">{{ $employee->full_name }}</div>
            <div class="employee-details">
                <strong>القسم:</strong> {{ $employee->department->name ?? 'غير محدد' }}<br>
                <strong>رقم الموظف:</strong> {{ $employee->id }}<br>
                <strong>تاريخ الإصدار:</strong> {{ Carbon\Carbon::now()->format('d/m/Y') }}
            </div>

            <!-- الباركود -->
            <div class="barcode-section">
                <div class="barcode-title">الباركود</div>
                <div class="barcode-canvas">
                    <canvas id="main-barcode" style="max-width: 100%;"></canvas>
                </div>
                <div class="barcode-text">{{ $employee->barcode }}</div>
            </div>

            <!-- QR Code -->
            <div class="qr-section">
                <div class="barcode-title">رمز الاستجابة السريعة</div>
                <canvas id="qr-code" style="margin: 10px auto; display: block;"></canvas>
            </div>

            <!-- تعليمات الاستخدام -->
            <div class="instructions">
                <div class="instructions-title">تعليمات الاستخدام:</div>
                <ol class="instructions-list">
                    <li>احتفظ بهذه البطاقة في مكان آمن</li>
                    <li>امسح الباركود عند الحضور والانصراف</li>
                    <li>تأكد من نظافة الباركود للمسح السليم</li>
                    <li>في حالة فقدان البطاقة، اتصل بقسم الموارد البشرية</li>
                </ol>
            </div>

            <!-- معلومات إضافية -->
            <div class="footer-info">
                <strong>رقم البطاقة:</strong> {{ $employee->barcode }}<br>
                <strong>صالحة حتى:</strong> {{ Carbon\Carbon::now()->addYear()->format('d/m/Y') }}
            </div>
        </div>

        <!-- نسخة مصغرة للمحفظة -->
        <div class="barcode-card" style="transform: scale(0.7);">
            <div class="company-name" style="font-size: 16px;">{{ config('app.name', 'شركتك') }}</div>
            <div style="margin: 15px 0;">
                <strong>{{ $employee->full_name }}</strong><br>
                <small>{{ $employee->department->name ?? 'غير محدد' }}</small>
            </div>

            <canvas id="wallet-barcode" style="max-width: 100%;"></canvas>
            <div class="barcode-text" style="font-size: 10px; margin-top: 5px;">
                {{ $employee->barcode }}
            </div>
        </div>
    </div>

    <!-- مكتبات الباركود -->
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const barcode = '{{ $employee->barcode }}';
            const employeeName = '{{ $employee->full_name }}';
            const employeeId = '{{ $employee->id }}';

            // توليد الباركود الرئيسي
            JsBarcode("#main-barcode", barcode, {
                format: "CODE128",
                width: 2,
                height: 80,
                displayValue: true,
                fontSize: 14,
                margin: 10,
                background: "#ffffff",
                lineColor: "#000000"
            });

            // توليد باركود المحفظة
            JsBarcode("#wallet-barcode", barcode, {
                format: "CODE128",
                width: 1.5,
                height: 50,
                displayValue: false,
                margin: 5,
                background: "#ffffff",
                lineColor: "#000000"
            });

            // توليد QR Code
            QRCode.toCanvas(document.getElementById('qr-code'), JSON.stringify({
                employee_id: employeeId,
                employee_name: employeeName,
                barcode: barcode,
                company: '{{ config("app.name", "شركتك") }}',
                generated_at: '{{ Carbon\Carbon::now()->toISOString() }}'
            }), {
                width: 120,
                margin: 2,
                color: {
                    dark: '#000000',
                    light: '#FFFFFF'
                },
                errorCorrectionLevel: 'M'
            });
        });

        /**
         * تحميل الباركود كصورة
         */
        function downloadBarcode() {
            const canvas = document.getElementById('main-barcode');
            const link = document.createElement('a');
            link.download = `barcode-{{ $employee->full_name }}.png`;
            link.href = canvas.toDataURL();
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        /**
         * طباعة تلقائية عند التحميل (اختياري)
         */
        // window.onload = function() {
        //     setTimeout(() => {
        //         window.print();
        //     }, 1000);
        // };
    </script>
</body>
</html>
