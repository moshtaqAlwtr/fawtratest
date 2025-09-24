<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاتورة حرارية - {{ $invoice->code }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.2;
            color: #000;
            background: #fff;
            width: 58mm;
            margin: 0 auto;
            padding: 2mm;
        }

        .receipt-container {
            width: 100%;
            max-width: 54mm;
        }

        .header {
            text-align: center;
            margin-bottom: 3mm;
            padding-bottom: 2mm;
            border-bottom: 1px dashed #000;
        }

        .company-logo {
            width: 30mm;
            height: auto;
            margin-bottom: 2mm;
        }

        .company-name {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 1mm;
            text-transform: uppercase;
        }

        .company-info {
            font-size: 10px;
            line-height: 1.3;
        }

        .invoice-info {
            margin: 3mm 0;
            font-size: 11px;
        }

        .invoice-number {
            font-size: 13px;
            font-weight: bold;
            text-align: center;
            margin: 2mm 0;
            padding: 1mm;
            background: #000;
            color: #fff;
        }

        .customer-info {
            margin: 3mm 0;
            padding: 2mm 0;
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
        }

        .items-table {
            width: 100%;
            margin: 3mm 0;
        }

        .item-row {
            margin-bottom: 2mm;
            border-bottom: 1px dotted #ccc;
            padding-bottom: 1mm;
        }

        .item-name {
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 0.5mm;
        }

        .item-details {
            display: flex;
            justify-content: space-between;
            font-size: 10px;
        }

        .quantity-price {
            display: flex;
            justify-content: space-between;
            width: 100%;
        }

        .totals {
            margin: 3mm 0;
            border-top: 1px dashed #000;
            padding-top: 2mm;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1mm;
            font-size: 11px;
        }

        .grand-total {
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            padding: 2mm 0;
            margin: 2mm 0;
            font-weight: bold;
            font-size: 14px;
            text-align: center;
            background: #f0f0f0;
        }

        .payment-info {
            margin: 3mm 0;
            text-align: center;
            font-size: 11px;
        }

        .footer {
            text-align: center;
            margin-top: 3mm;
            padding-top: 2mm;
            border-top: 1px dashed #000;
            font-size: 10px;
        }

        .barcode {
            text-align: center;
            margin: 3mm 0;
        }

        .barcode-image {
            width: 40mm;
            height: 10mm;
            background: #000;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            font-family: 'Courier New', monospace;
            font-size: 8px;
        }

        .qr-code {
            width: 20mm;
            height: 20mm;
            background: #000;
            margin: 2mm auto;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 8px;
        }

        .thank-you {
            text-align: center;
            font-weight: bold;
            margin: 3mm 0;
            font-size: 12px;
        }

        .stars {
            text-align: center;
            font-size: 16px;
            margin: 2mm 0;
        }

        .separator {
            text-align: center;
            margin: 2mm 0;
            font-size: 14px;
        }

        .status-badge {
            display: inline-block;
            padding: 1mm 2mm;
            border-radius: 2mm;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .paid {
            background: #d4edda;
            color: #155724;
        }

        .partial {
            background: #fff3cd;
            color: #856404;
        }

        .unpaid {
            background: #f8d7da;
            color: #721c24;
        }

        .vat-info {
            font-size: 9px;
            text-align: center;
            margin: 2mm 0;
            color: #666;
        }

        /* طباعة حرارية خاصة */
        @media print {
            body {
                font-size: 10px;
                width: 58mm;
            }

            .no-print {
                display: none !important;
            }

            .receipt-container {
                page-break-inside: avoid;
            }
        }

        /* تأثيرات خاصة للطباعة الحرارية */
        .thermal-line {
            height: 1px;
            background: repeating-linear-gradient(
                to right,
                #000 0px,
                #000 2px,
                transparent 2px,
                transparent 4px
            );
            margin: 1mm 0;
        }

        .thermal-double-line {
            height: 2px;
            background: #000;
            margin: 2mm 0;
        }

        .highlight-box {
            border: 2px solid #000;
            padding: 1mm;
            margin: 1mm 0;
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <!-- Header -->
        <div class="header">
            @if($posSettings['show_logo'])
                <div class="company-logo">
                    <!-- Logo placeholder - يمكن إضافة صورة هنا -->
                    <div style="width: 30mm; height: 15mm; background: #f0f0f0; border: 1px solid #ccc; display: flex; align-items: center; justify-content: center; margin: 0 auto 2mm;">
                        LOGO
                    </div>
                </div>
            @endif

            <div class="company-name">{{ $posSettings['company_name'] }}</div>
            <div class="company-info">
                {{ $posSettings['company_address'] }}<br>
                هاتف: {{ $posSettings['company_phone'] }}<br>
                @if($posSettings['tax_number'])
                الرقم الضريبي: {{ $posSettings['tax_number'] }}
                @endif
            </div>
        </div>

        <!-- Invoice Number -->
        <div class="invoice-number">{{ $invoice->code }}</div>

        <!-- Date & Time -->
        <div class="invoice-info">
            <div style="display: flex; justify-content: space-between;">
                <span>التاريخ:</span>
                <span>{{ $invoice->invoice_date->format('Y/m/d') }}</span>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span>الوقت:</span>
                <span>{{ $invoice->invoice_date->format('H:i') }}</span>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span>المندوب:</span>
                <span>{{ $invoice->employee->name ?? 'نقطة البيع' }}</span>
            </div>
        </div>

        <!-- Customer Info -->
        <div class="customer-info">
            <strong>العميل:</strong> {{ $invoice->client->trade_name }}<br>
            @if($invoice->client->phone)
                <strong>الهاتف:</strong> {{ $invoice->client->phone }}<br>
            @endif
        </div>

        <div class="thermal-line"></div>

        <!-- Items -->
        <div class="items-table">
            @foreach($invoice->items as $index => $item)
                <div class="item-row">
                    <div class="item-name">{{ $loop->iteration }}. {{ $item->item }}</div>
                    <div class="item-details">
                        <div class="quantity-price">
                            <span>{{ number_format($item->quantity, 2) }} × {{ number_format($item->unit_price, 2) }}</span>
                            <span><strong>{{ number_format($item->total, 2) }} ر.س</strong></span>
                        </div>
                    </div>
                    @if($item->discount > 0)
                        <div style="font-size: 9px; color: #666; text-align: left;">
                            خصم: -{{ number_format($item->discount, 2) }} ر.س
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="thermal-line"></div>

        <!-- Totals -->
        <div class="totals">
            <div class="total-row">
                <span>المجموع الفرعي:</span>
                <span>{{ number_format($invoice->subtotal, 2) }} ر.س</span>
            </div>

            @if($invoice->discount_amount > 0)
                <div class="total-row">
                    <span>إجمالي الخصم:</span>
                    <span>-{{ number_format($invoice->discount_amount, 2) }} ر.س</span>
                </div>
            @endif

            @if($invoice->tax_total > 0)
                <div class="total-row">
                    <span>الضريبة ({{ number_format(($invoice->tax_total / $invoice->subtotal) * 100, 1) }}%):</span>
                    <span>{{ number_format($invoice->tax_total, 2) }} ر.س</span>
                </div>
            @endif
        </div>

        <div class="thermal-double-line"></div>

        <!-- Grand Total -->
        <div class="grand-total">
            <div>الإجمالي</div>
            <div style="font-size: 18px; margin-top: 1mm;">{{ number_format($invoice->grand_total, 2) }} ر.س</div>
        </div>

        <div class="thermal-double-line"></div>

        <!-- Payment Info -->
        <div class="payment-info">
            <div style="margin-bottom: 2mm;">
                <strong>طريقة الدفع:</strong>
                @switch($invoice->payment_method)
                    @case(1) نقداً @break
                    @case(2) بطاقة ائتمان @break
                    @case(3) تحويل بنكي @break
                    @case(4) آجل @break
                    @default غير محدد
                @endswitch
            </div>

            <div>
                <strong>حالة الدفع:</strong>
                <span class="status-badge
                    @if($invoice->payment_status == 1) paid
                    @elseif($invoice->payment_status == 2) partial
                    @else unpaid @endif">
                    @if($invoice->payment_status == 1) مدفوع
                    @elseif($invoice->payment_status == 2) جزئي
                    @else غير مدفوع @endif
                </span>
            </div>

            @if($invoice->remaining_amount > 0)
                <div style="margin-top: 2mm; font-weight: bold; color: #d63384;">
                    المبلغ المتبقي: {{ number_format($invoice->remaining_amount, 2) }} ر.س
                </div>
            @endif
        </div>

        <!-- Payment Details if any -->
        @if($invoice->payments->count() > 0)
            <div class="thermal-line"></div>
            <div style="font-size: 10px; margin: 2mm 0;">
                <strong>المدفوعات:</strong><br>
                @foreach($invoice->payments as $payment)
                    <div style="display: flex; justify-content: space-between; margin: 1mm 0;">
                        <span>{{ $payment->payment_date->format('m/d H:i') }}</span>
                        <span>{{ number_format($payment->amount, 2) }} ر.س</span>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="thermal-line"></div>

        <!-- VAT Info -->
        @if($invoice->tax_total > 0)
            <div class="vat-info">
                المبلغ شامل ضريبة القيمة المضافة<br>
                الرقم الضريبي: {{ $posSettings['tax_number'] }}
            </div>
        @endif

        <!-- QR Code for digital receipt -->
        <div class="qr-code">
            QR CODE<br>
            {{ $invoice->code }}
        </div>

        <!-- Barcode -->
        <div class="barcode">
            <div class="barcode-image">
                ||||| {{ $invoice->code }} |||||
            </div>
        </div>

        <div class="separator">* * * * * * * * * *</div>

        <!-- Thank you message -->
        <div class="thank-you">
            {{ $posSettings['footer_text'] }}
        </div>

        <div class="stars">★ ★ ★ ★ ★</div>

        <!-- Additional Info -->
        <div class="footer">
            <div style="margin-bottom: 2mm;">
                للاستفسارات: {{ $posSettings['company_phone'] }}
            </div>

            @if($invoice->notes)
                <div style="margin: 2mm 0; font-style: italic; border: 1px dashed #ccc; padding: 1mm;">
                    ملاحظة: {{ $invoice->notes }}
                </div>
            @endif

            <div style="font-size: 8px; color: #999; margin-top: 2mm;">
                تم الطباعة: {{ now()->format('Y/m/d H:i:s') }}<br>
                نظام نقطة البيع - POS System
            </div>

            <!-- Return Policy -->
            <div style="font-size: 9px; margin-top: 3mm; border-top: 1px dotted #ccc; padding-top: 2mm;">
                <strong>سياسة الاسترداد:</strong><br>
                • يمكن استرداد أو استبدال المنتجات خلال 7 أيام<br>
                • يجب إحضار الفاتورة الأصلية<br>
                • المنتجات يجب أن تكون في حالتها الأصلية
            </div>

            <!-- Rating Request -->
            <div style="text-align: center; margin: 3mm 0; border: 1px solid #000; padding: 2mm;">
                <strong>قيم تجربتك معنا!</strong><br>
                <div style="font-size: 16px; margin: 1mm 0;">☆ ☆ ☆ ☆ ☆</div>
                <div style="font-size: 8px;">امسح الكود أو زر موقعنا للتقييم</div>
            </div>
        </div>

        <div class="separator">= = = = = = = = = =</div>

        <!-- Cut line indicator -->
        <div style="text-align: center; font-size: 8px; color: #999; margin: 2mm 0;">
            ✂ - - - - - قص هنا - - - - - ✂
        </div>
    </div>

    <!-- JavaScript للطباعة الحرارية -->
    <script>
        // إعدادات الطباعة الحرارية
        const thermalSettings = {
            width: 58, // mm
            fontSize: 12,
            lineSpacing: 1.2,
            cutPaper: true,
            openDrawer: true
        };

        // طباعة تلقائية عند التحميل
        window.addEventListener('load', function() {
            setTimeout(() => {
                // إعدادات خاصة للطباعة الحرارية
                const printSettings = {
                    printBackground: true,
                    margin: '0',
                    format: 'A4',
                    width: '58mm',
                    height: 'auto'
                };

                window.print();
            }, 500);
        });

        // إغلاق النافذة بعد الطباعة
        window.addEventListener('afterprint', function() {
            setTimeout(() => {
                window.close();
            }, 1000);
        });

        // دالة للطباعة المباشرة على طابعة حرارية
        function printToThermalPrinter() {
            try {
                // إرسال أمر ESC/POS للطباعة
                const escPosData = generateEscPosData();

                // يمكن إرسال البيانات للطابعة عبر WebUSB أو WebSerial
                if (navigator.serial) {
                    sendToSerialPrinter(escPosData);
                } else if (navigator.usb) {
                    sendToUSBPrinter(escPosData);
                } else {
                    // الطباعة العادية كبديل
                    window.print();
                }
            } catch (error) {
                console.error('خطأ في الطباعة الحرارية:', error);
                window.print(); // البديل
            }
        }

        function generateEscPosData() {
            // بناء أوامر ESC/POS
            let commands = [];

            // تهيئة الطابعة
            commands.push('\x1B\x40'); // ESC @

            // محاذاة الوسط
            commands.push('\x1B\x61\x01'); // ESC a 1

            // خط كبير للشركة
            commands.push('\x1D\x21\x11'); // GS ! 17
            commands.push('{{ $posSettings["company_name"] }}\n');

            // خط عادي
            commands.push('\x1D\x21\x00'); // GS ! 0
            commands.push('{{ $posSettings["company_address"] }}\n');
            commands.push('{{ $posSettings["company_phone"] }}\n\n');

            // رقم الفاتورة
            commands.push('رقم الفاتورة: {{ $invoice->code }}\n');
            commands.push('التاريخ: {{ $invoice->invoice_date->format("Y/m/d H:i") }}\n');
            commands.push('العميل: {{ $invoice->client->trade_name }}\n\n');

            // خط فاصل
            commands.push('--------------------------------\n');

            // العناصر
            @foreach($invoice->items as $item)
            commands.push('{{ $item->item }}\n');
            commands.push('{{ number_format($item->quantity, 2) }} x {{ number_format($item->unit_price, 2) }} = {{ number_format($item->total, 2) }} ر.س\n');
            @endforeach

            commands.push('--------------------------------\n');

            // المجموع
            commands.push('\x1D\x21\x11'); // خط كبير
            commands.push('المجموع: {{ number_format($invoice->grand_total, 2) }} ر.س\n');
            commands.push('\x1D\x21\x00'); // خط عادي

            commands.push('\n{{ $posSettings["footer_text"] }}\n\n');

            // قطع الورق
            if (thermalSettings.cutPaper) {
                commands.push('\x1D\x56\x41\x03'); // GS V A 3
            }

            // فتح الدرج
            if (thermalSettings.openDrawer) {
                commands.push('\x1B\x70\x00\x19\xFA'); // ESC p 0 25 250
            }

            return commands.join('');
        }

        async function sendToSerialPrinter(data) {
            try {
                const port = await navigator.serial.requestPort();
                await port.open({ baudRate: 9600 });

                const writer = port.writable.getWriter();
                await writer.write(new TextEncoder().encode(data));
                writer.releaseLock();

                await port.close();
            } catch (error) {
                console.error('خطأ في الطباعة التسلسلية:', error);
            }
        }

        async function sendToUSBPrinter(data) {
            try {
                const device = await navigator.usb.requestDevice({
                    filters: [{ vendorId: 0x04b8 }] // Epson
                });

                await device.open();
                await device.selectConfiguration(1);
                await device.claimInterface(0);

                await device.transferOut(1, new TextEncoder().encode(data));

                await device.close();
            } catch (error) {
                console.error('خطأ في الطباعة USB:', error);
            }
        }

        // إضافة أزرار التحكم للاختبار
        document.addEventListener('DOMContentLoaded', function() {
            // إضافة أزرار مخفية للتطوير
            if (location.href.includes('debug=1')) {
                const controls = document.createElement('div');
                controls.className = 'no-print';
                controls.style.cssText = 'position: fixed; top: 10px; right: 10px; z-index: 1000; background: white; padding: 10px; border: 1px solid #ccc;';
                controls.innerHTML = `
                    <button onclick="window.print()" style="margin: 2px;">طباعة عادية</button><br>
                    <button onclick="printToThermalPrinter()" style="margin: 2px;">طباعة حرارية</button><br>
                    <button onclick="window.close()" style="margin: 2px;">إغلاق</button>
                `;
                document.body.appendChild(controls);
            }
        });
    </script>
</body>
</html>