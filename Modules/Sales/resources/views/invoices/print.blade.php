<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>فاتورة #{{ $invoice->id }}</title>
    <!-- Import Arabic font -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700&display=swap');

        /* Global Styles */
        body {
            font-family: 'Tajawal', sans-serif;
            background-color: #f0f0f0;
            padding: 20px;
            direction: rtl;
            font-weight: bold;
        }

        @media print {
            #signature-form {
                display: none !important;
            }
        }


        @media print {
            #signature-form {
                display: none !important;
            }
        }


        @media print {
            .no-print {
                display: none !important;
            }
        }

        /* Receipt Container */
        .receipt-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 80vh;
        }

        .receipt {
            width: 80mm;
            /* Standard thermal receipt width */
            max-width: 100%;
            background-color: white;
            padding: 10px;
            border: 1px solid #ddd;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin: 0 auto;
        }

        /* Receipt Header */
        .receipt-header {
            padding-bottom: 10px;
            border-bottom: 1px dashed #ccc;
            margin-bottom: 10px;
            text-align: center;
        }

        .receipt-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        /* Invoice To Section */
        .invoice-to {
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #ccc;
        }

        /* Invoice Details Section */
        .invoice-details {
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #ccc;
        }

        /* Invoice Items Table */
        .invoice-items {
            margin-bottom: 10px;
        }

        .table {
            width: 100%;
            margin-bottom: 10px;
            font-size: 12px;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            text-align: center;
            padding: 5px;
            border-bottom: 1px dashed #ddd;
        }

        .table th {
            background-color: #f5f5f5;
            border-bottom: 1px solid #333;
        }

        /* Invoice Summary */
        .invoice-summary {
            border-top: 1px dashed #ccc;
            padding-top: 10px;
            margin-bottom: 15px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }


        /* QR Code */
        .qr-code {
            margin: 15px 0;
            text-align: center;
        }

        /* Signature */
        .signature {
            margin: 15px auto 0;
            padding-top: 10px;
            border-top: 1px dashed #333;
            width: 90%;
            text-align: center;
        }

        .signature-pad {
            border: 1px dashed #000;
            width: 100%;
            height: 100px;
            margin: 10px 0;
        }

        .signature-controls {
            display: flex;
            justify-content: space-around;
            margin-bottom: 10px;
        }

        .signature-btn {
            padding: 5px 10px;
            font-size: 12px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        .signature-save {
            background-color: #4CAF50;
            color: white;
        }

        .signature-clear {
            background-color: #f44336;
            color: white;
        }

        .signature-history {
            margin-top: 10px;
            font-size: 12px;
        }

        .signature-item {
            margin-bottom: 5px;
            padding: 5px;
            border: 1px dashed #ccc;
        }

        .thank-you {
            font-style: italic;
            margin-top: 5px;
        }

        /* Toastr Styles */
        #toast-container>.toast {
            background-image: none !important;
        }

        #toast-container>.toast:before {
            position: fixed;
            font-family: FontAwesome;
            font-size: 24px;
            line-height: 18px;
            float: right;
            color: #FFF;
            padding-right: 0.5em;
            margin: auto 0.5em auto -1.5em;
        }

        #toast-container>.toast-success:before {
            content: "\f00c";
        }

        #toast-container>.toast-error:before {
            content: "\f00d";
        }

        /* Print Styles */
        @media print {
            body {
                background-color: white;
                padding: 0;
                margin: 0;
                display: block !important;
                width: 80mm !important;
                font-weight: bold !important;
            }

            .receipt {
                width: 100%;
                box-shadow: none;
                border: none;
                padding: 0;
                margin: 0 auto !important;
            }

            .receipt-container {
                min-height: auto;
            }

            .qr-code svg {
                width: 70px !important;
                height: 70px !important;
            }


        }

        /* Responsive Styles */
        @media (max-width: 576px) {
            .receipt {
                width: 100%;
            }
        }
    </style>
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
</head>

<body>
    @php
        $returnedInvoice = \App\Models\Invoice::where('type', 'returned')
            ->where('reference_number', $invoice->id)
            ->first();
    @endphp

    <div class="container">
        <div class="receipt-container">
            <div class="receipt">
                <!-- Receipt Header -->
                <div class="receipt-header">
                    <h1 class="receipt-title">فاتورة ضريبية</h1>



                    <p class="mb-0">مؤسسة اعمال خاصة </p>

                    <p class="mb-0">الرياض - الرياض</p>
                    <p>رقم المسؤول: 0509992803</p>
                </div>

                <!-- Invoice To -->
                <div class="invoice-to">
                    <p class="mb-0">فاتورة الى:
                        {{ $invoice->client->trade_name ?? $invoice->client->first_name . ' ' . $invoice->client->last_name }}
                    </p>
                    <p class="mb-0">{{ $invoice->client->street1 ?? 'غير متوفر' }}</p>
                    <h1 class="mb-0"> {{ $invoice->client->code ?? 'غير متوفر' }}</h1>
                    <p class="mb-0">الرقم الضريبي: {{ $invoice->client->tax_number ?? 'غير متوفر' }}</p>
                    @if ($invoice->client->phone)
                        <p class="mb-0">رقم جوال العميل: {{ $invoice->client->phone }}</p>
                    @endif
                </div>

                <!-- Invoice Details -->
                <div class="invoice-details">
                    <div class="summary-row">
                        <span>رقم الفاتورة:</span>
                        <span>{{ str_pad($invoice->id, 5, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="summary-row">
                        <span>تاريخ الفاتورة:</span>

                        <span>{{ $invoice->invoice_date }}</span>

                        <span>{{ $invoice->invoice_date??'غير متوفر' }}</span>

                    </div>

                </div>

                <!-- Invoice Items -->
                <div class="invoice-items">
                    <table class="table">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th width="45%">المنتج</th>
                                <th width="15%">الكمية</th>
                                <th width="15%">السعر</th>
                                <th width="20%">المجموع</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoice->items as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td style="text-align: right;">{{ $item->item }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ number_format($item->unit_price, 2) }}</td>
                                    <td>{{ number_format($item->total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Invoice Summary -->
             <div class="invoice-summary">
    <div class="summary-row">
        <span>المجموع الكلي:</span>
        <span>{{ number_format($invoice->grand_total, 2) }} ر.س</span>
    </div>

    @if ($invoice->total_discount > 0)
        <div class="summary-row">
            <span>الخصم:</span>
            <span>{{ number_format($invoice->total_discount, 2) }} ر.س</span>
        </div>
    @endif

    @if ($invoice->shipping_cost > 0)
        <div class="summary-row">
            <span>تكلفة الشحن:</span>
            <span>{{ number_format($invoice->shipping_cost, 2) }} ر.س</span>
        </div>
    @endif

    @if ($invoice->adjustment_value > 0)
        <div class="summary-row">
            <span>{{ $invoice->adjustment_label ?? 'تسوية' }}:</span>
            <span>
                {{ $invoice->adjustment_type === 'discount' ? '-' : '+' }}
                {{ number_format($invoice->adjustment_value, 2) }} ر.س
            </span>
        </div>
    @endif

    @if ($invoice->advance_payment > 0)
        <div class="summary-row" id="advance-payment-row">
            <span>الدفعة المقدمة:</span>
            <span id="advance-payment-amount">{{ number_format($invoice->advance_payment, 2) }} ر.س</span>
        </div>
    @endif

    @if ($returnedInvoice)
        <div class="summary-row">
            <span>مرتجع:</span>
            <span>{{ number_format($invoice->returned_payment, 2) }} ر.س</span>
        </div>
    @endif

    <div class="summary-row">
        <span>المبلغ المستحق:</span>
        <span id="due-value-amount">{{ number_format($invoice->due_value, 2) }} ر.س</span>
    </div>
</div>

                <!-- QR Code -->


                <!-- Signature Section -->
                <div class="signature">
                    {{-- <h4 style="font-size: 14px; margin-bottom: 5px;">التوقيع الإلكتروني</h4>
 --}}


                    <!-- مكان عرض التواقيع -->
                    <div class="signature-history" style="margin-top: 20px;">
                        @foreach ($invoice->signatures as $signature)
                            <div class="signature-item">
                                <div><strong>الاسم:</strong> {{ $signature->signer_name }}</div>
                                @if ($signature->signer_role)
                                    <div><strong>الصفة:</strong> {{ $signature->signer_role }}</div>
                                @endif
                                @if ($signature->amount_paid)
                                    <div><strong>المبلغ المدفوع:</strong>
                                        {{ number_format($signature->amount_paid, 2) }} ريال</div>
                                @endif
                                <img src="{{ $signature->signature_data }}"
                                    style="max-width: 100%; height: auto; margin-top: 5px;">
                            </div>
                        @endforeach
                    </div>


                </div>

                                <div class="signature">
                    <p>الاسم: ________________</p>
                    <p>التوقيع: _______________</p>
                    <p class="thank-you">شكراً لتعاملكم معنا</p>
                </div>


               

                <div class="qr-code">

                    {!! $qrCodeSvg !!}

                </div>



            </div>
        </div>
    </div>
    <script>
        toastr.success('تم التحميل بنجاح');
    </script>


    <script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script>



    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    <script>
        const canvas = document.getElementById('signature-pad');
        const signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgb(255,255,255)',
            penColor: 'rgb(0,0,0)'
        });

        function resizeCanvas() {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext('2d').scale(ratio, ratio);
            signaturePad.clear();
        }
        window.addEventListener('resize', resizeCanvas);
        resizeCanvas();

        document.getElementById('clear-signature').addEventListener('click', () => {
            signaturePad.clear();
        });

        document.getElementById('signature-form').addEventListener('submit', async function(e) {
            e.preventDefault();

            const signerName = document.getElementById('signer-name').value.trim();
            const signerRole = document.getElementById('signer-role').value.trim();
            const amountPaid = document.querySelector('input[name="amount_paid"]').value.trim();

            if (!signerName) {
                toastr.error('الرجاء إدخال الاسم الكامل');
                return;
            }

            if (!amountPaid || isNaN(amountPaid)) {
                toastr.error('الرجاء إدخال مبلغ مدفوع صحيح');
                return;
            }

            // ✅ تحقق من تجاوز المبلغ المستحق
            const dueAmountText = document.getElementById('due-value-amount')?.textContent || '0';
            const dueAmount = parseFloat(dueAmountText.replace(/[^\d.]/g, '') || 0);

            if (parseFloat(amountPaid) > dueAmount) {
                toastr.error('المبلغ المدفوع أكبر من المبلغ المستحق');
                return;
            }

            if (signaturePad.isEmpty()) {
                toastr.error('الرجاء تقديم التوقيع أولاً');
                return;
            }

            Swal.fire({
                title: 'تأكيد الحفظ',
                text: 'هل أنت متأكد من حفظ التوقيع؟',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'نعم، احفظ',
                cancelButtonText: 'إلغاء'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const signatureData = signaturePad.toDataURL();
                        document.getElementById('signature-data').value = signatureData;

                        const formData = new FormData(this);
                        const response = await axios.post(this.action, formData);

                        if (response.data.success) {
                            const newSignature = response.data.signature;
                            const container = document.querySelector('.signature-history');

                            const signatureItem = document.createElement('div');
                            signatureItem.classList.add('signature-item');
                            signatureItem.innerHTML = `
                        <div><strong>الاسم:</strong> ${newSignature.signer_name}</div>
                        ${newSignature.signer_role ? `<div><strong>الصفة:</strong> ${newSignature.signer_role}</div>` : ''}
                        ${newSignature.amount_paid ? `<div><strong>المبلغ المدفوع:</strong> ${parseFloat(newSignature.amount_paid).toFixed(2)} ريال</div>` : ''}
                        <img src="${newSignature.signature_data}" style="max-width: 100%; height: auto; margin-top: 5px;">
                    `;
                            container.prepend(signatureItem);

                            updatePaymentSummary(newSignature.amount_paid);

                            // تنظيف الحقول
                            document.getElementById('signer-name').value = '';
                            document.getElementById('signer-role').value = '';
                            document.querySelector('input[name="amount_paid"]').value = '';
                            signaturePad.clear();
                            toastr.success('تم حفظ التوقيع بنجاح');
                        } else {
                            toastr.error(response.data.message || 'حدث خطأ في الحفظ');
                        }
                    } catch (error) {
                        toastr.error('فشل في الحفظ. يرجى المحاولة لاحقًا.');
                        console.error(error);
                    }
                }
            });
        });


        // ✅ دالة لتحديث المبلغ المدفوع والمستحق
        function updatePaymentSummary(amountPaid) {
            const paidAmount = parseFloat(amountPaid);

            const currentAdvance = parseFloat(
                (document.getElementById('advance-payment-amount')?.textContent.replace(/[^\d.]/g, '') || 0)
            );
            const currentDue = parseFloat(
                (document.getElementById('due-value-amount')?.textContent.replace(/[^\d.]/g, '') || 0)
            );

            const newAdvance = currentAdvance + paidAmount;
            const newDue = currentDue - paidAmount;

            // تحديث الدفعة المقدمة
            const advanceAmountEl = document.getElementById('advance-payment-amount');
            if (advanceAmountEl) {
                advanceAmountEl.textContent = newAdvance.toFixed(2) + ' ر.س';
            } else {
                const summaryContainer = document.querySelector('.summary-row').parentElement;
                const newAdvanceRow = document.createElement('div');
                newAdvanceRow.className = 'summary-row';
                newAdvanceRow.id = 'advance-payment-row';
                newAdvanceRow.innerHTML = `
                <span>الدفعة المقدمة:</span>
                <span id="advance-payment-amount">${newAdvance.toFixed(2)} ر.س</span>
            `;
                summaryContainer.insertBefore(newAdvanceRow, summaryContainer.firstChild);
            }

            // تحديث المبلغ المستحق
            const dueAmountEl = document.getElementById('due-value-amount');
            if (dueAmountEl) {
                dueAmountEl.textContent = Math.max(0, newDue).toFixed(2) + ' ر.س';
            }
        }
    </script>

    <!-- مكتبات JavaScript -->
    <!-- ✅ مكتبة Axios -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <!-- ✅ مكتبة Toastr -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <!-- ✅ مكتبة SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


</body>
<script>
    // طباعة تلقائية عند تحميل الصفحة
    window.onload = function() {
        setTimeout(() => {
            window.print();
        }, 500);
    };

    // إعادة الطباعة عند محاولة الإغلاق
    window.onbeforeunload = function() {
        window.print();
    };
</script>

</html>

{{--

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاتورة #{{ $invoice->id }}</title>
    <!-- Import Arabic font -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700&display=swap');

        /* Global Styles */
        body {
            font-family: 'Tajawal', sans-serif;
            background-color: #f0f0f0;
            padding: 20px;
            direction: rtl;
            font-weight: bold;
        }

        /* Receipt Container */
        .receipt-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 80vh;
        }

        .receipt {
            width: 80mm;
            /* Standard thermal receipt width */
            max-width: 100%;
            background-color: white;
            padding: 10px;
            border: 1px solid #ddd;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin: 0 auto;
        }

        /* Receipt Header */
        .receipt-header {
            padding-bottom: 10px;
            border-bottom: 1px dashed #ccc;
            margin-bottom: 10px;
            text-align: center;
        }

        .receipt-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        /* Invoice To Section */
        .invoice-to {
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #ccc;
        }

        /* Invoice Details Section */
        .invoice-details {
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #ccc;
        }

        /* Invoice Items Table */
        .invoice-items {
            margin-bottom: 10px;
        }

        .table {
            width: 100%;
            margin-bottom: 10px;
            font-size: 12px;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            text-align: center;
            padding: 5px;
            border-bottom: 1px dashed #ddd;
        }

        .table th {
            background-color: #f5f5f5;
            border-bottom: 1px solid #333;
        }

        /* Invoice Summary */
        .invoice-summary {
            border-top: 1px dashed #ccc;
            padding-top: 10px;
            margin-bottom: 15px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        /* QR Code */
        .qr-code {
            margin: 15px 0;
            text-align: center;
        }

        /* Signature */
        .signature {
            margin: 15px auto 0;
            padding-top: 10px;
            border-top: 1px dashed #333;
            width: 90%;
            text-align: center;
        }

        .thank-you {
            font-style: italic;
            margin-top: 5px;
        }

        /* Print Styles */
        @media print {
            body {
                background-color: white;
                padding: 0;
                margin: 0;
                display: block !important;
                width: 80mm !important;
                font-weight: bold !important;
            }

            .receipt {
                width: 100%;
                box-shadow: none;
                border: none;
                padding: 0;
                margin: 0 auto !important;
            }

            .receipt-container {
                min-height: auto;
            }

            .qr-code svg {
                width: 70px !important;
                height: 70px !important;
            }
        }

        /* Responsive Styles */
        @media (max-width: 576px) {
            .receipt {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="receipt-container">
            <div class="receipt">
                <!-- Receipt Header -->
                <div class="receipt-header">
                    <h1 class="receipt-title">فاتورة</h1>
                    <p class="mb-0">مؤسسة أعمال خاصة للتجارة</p>
                    <p class="mb-0">الرياض - الرياض</p>
                    <p>رقم المسؤول: 0509992803</p>
                </div>

                <!-- Invoice To -->
                <div class="invoice-to">
                    <p class="mb-0">فاتورة الى:
                        {{ $invoice->client->trade_name ?? $invoice->client->first_name . ' ' . $invoice->client->last_name }}
                    </p>
                    <p class="mb-0">{{ $invoice->client->street1 ?? 'غير متوفر' }}</p>
                    <h3 class="text-center display-3 mb-0">
                        {{ $invoice->client->code ?? 'غير متوفر' }}
                    </h3>


                    <p class="mb-0">الرقم الضريبي: {{ $invoice->client->tax_number ?? 'غير متوفر' }}</p>
                    @if ($invoice->client->phone)
                        <p class="mb-0">رقم جوال العميل: {{ $invoice->client->phone }}</p>
                    @endif
                </div>

                <!-- Invoice Details -->
                <div class="invoice-details">
                    <div class="summary-row">
                        <span>رقم الفاتورة:</span>
                        <span>{{ str_pad($invoice->id, 5, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="summary-row">
                        <span>تاريخ الفاتورة:</span>
                        <span>{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d/m/Y H:i') }}</span>
                    </div>
                </div>

                <!-- Invoice Items -->
                <div class="invoice-items">
                    <table class="table">
                        <thead>
                            <tr>
                                <th width="40%">البند</th>
                                <th width="15%">الكمية</th>
                                <th width="20%">السعر</th>
                                <th width="25%">المجموع</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoice->items as $item)
                                <tr>
                                    <td style="text-align: right;">{{ $item->item }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ number_format($item->unit_price, 2) }}</td>
                                    <td>{{ number_format($item->total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Invoice Summary -->
                <div class="invoice-summary">
                    <div class="summary-row">
                        <span>المجموع الكلي:</span>
                        <span>{{ number_format($invoice->grand_total, 2) }} ر.س</span>
                    </div>

                    @if ($invoice->total_discount > 0)
                        <div class="summary-row">
                            <span>الخصم:</span>
                            <span>{{ number_format($invoice->total_discount, 2) }} ر.س</span>
                        </div>
                    @endif

                    @if ($invoice->shipping_cost > 0)
                        <div class="summary-row">
                            <span>تكلفة الشحن:</span>
                            <span>{{ number_format($invoice->shipping_cost, 2) }} ر.س</span>
                        </div>
                    @endif

                    @if ($invoice->advance_payment > 0)
                        <div class="summary-row">
                            <span>الدفعة المقدمة:</span>
                            <span>{{ number_format($invoice->advance_payment, 2) }} ر.س</span>
                        </div>
                    @endif

                    <div class="summary-row">
                        <span>المبلغ المستحق:</span>
                        <span>{{ number_format($invoice->due_value, 2) }} ر.س</span>
                    </div>
                </div>

                <!-- QR Code -->
                <div class="qr-code">
                    {!! $qrCodeSvg !!}
                </div>

                <!-- Signature -->
                <div class="signature">
                    <p>الاسم: ________________</p>
                    <p>التوقيع: _______________</p>
                    <p class="thank-you">شكراً لتعاملكم معنا</p>
                </div>
            </div>
        </div>
    </div>

    <!--<script>-->
        // طباعة تلقائية عند تحميل الصفحة
    <!--    window.onload = function() {-->
    <!--        setTimeout(() => {-->
    <!--            window.print();-->
    <!--        }, 500);-->
    <!--    };-->

        // إعادة الطباعة عند محاولة الإغلاق
    <!--    window.onbeforeunload = function() {-->
    <!--        window.print();-->
    <!--    };-->
    <!--</script>-->
</body>

</html> --}}
