@extends('master')

@section('title')
تفاصيل عرض السعرٍٍ
@stop

@section('content')

    @include('layouts.alerts.error')
    @include('layouts.alerts.success')
    <div class="content-body">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex gap-2">
                        <span class="badge badge-pill badge-warning">تحت المراجعة</span>
                        <strong> عرض الأسعار #{{ $quote->id }}</strong>
                        <span>العميل: {{ $quote->client->trade_name }}</span>
                    </div>
                    <div class="d-flex gap-2">
                        <form action="{{ route('questions.convert-to-invoice', $quote->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success d-inline-flex align-items-center">
                                <i class="fas fa-dollar-sign me-1"></i> تحويل لفاتورة
                            </button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div class="d-flex gap-2">
                        <!-- تعديل -->
                        {{-- <a href="{{ route('questions.edit', $quote->id) }}"
                            class="btn btn-sm btn-outline-danger d-inline-flex align-items-center">
                            <i class="fas fa-pen me-1"></i> تعديل
                        </a> --}}

                        <!-- طباعة -->
                        <button id="printQuoteBtn" class="btn btn-sm btn-outline-success d-inline-flex align-items-center">
                            <i class="fas fa-print me-1"></i> طباعة
                        </button>

                        <!-- PDF -->

                        <a href=""
                            class="btn btn-sm btn-outline-info d-inline-flex align-items-center">
                            <i class="fas fa-file-pdf me-1"></i> PDF
                        </a>

                        <!-- إرسال عبر -->
                        <div class="btn-group">
                            <div class="dropdown">
                                <button
                                    class="btn btn-sm btn-outline-success dropdown-toggle d-flex align-items-center custom-btn"
                                    type="button" id="dropdownMenuButton200" data-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false">
                                    <i class="fas fa me-1"></i> ارسال عبر
                                </button>
                                <div class="dropdown-menu custom-dropdown" aria-labelledby="dropdownMenuButton200">
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton2">
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center" target="_blank"
                                                href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $quote->client->phone) }}?text={{ urlencode(
                                                    'مرحبًا ' .
                                                        $quote->client->trade_name .
                                                        ',' .
                                                        "\n\n" .
                                                        'يسعدنا إعلامكم بأن فاتورتكم أصبحت جاهزة. يمكنكم الاطلاع عليها من خلال الرابط التالي:' .
                                                        "\n" .

                                                        "\n\n" .
                                                        'مع أطيب التحيات،' .
                                                        "\n" .
                                                        ($account_setting->trade_name ?? 'مؤسسة أعمال خاصة للتجارة'),
                                                ) }}">
                                                <i class="fab fa-whatsapp me-2 text-success"></i> واتساب
                                            </a>

                                        </li>




                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- خيارات أخر>ى -->

                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <ul class="nav nav-tabs mt-3" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="quote-tab" data-toggle="tab" href="#quote" role="tab"
                                    aria-controls="quote" aria-selected="true">عرض الأسعار</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="quote-details-tab" data-toggle="tab" href="#quote-details"
                                    role="tab" aria-controls="quote-details" aria-selected="false">تفاصيل عرض
                                    الأسعار</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="activity-log-tab" data-toggle="tab" href="#activity-log"
                                    role="tab" aria-controls="activity-log" aria-selected="false">سجل النشاطات</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade show active d-flex justify-content-center align-items-center"
                                id="quote" role="tabpanel" aria-labelledby="quote-tab" style="height: 100%;">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="tab-pane fade show active"
                                            style="background: lightslategray; min-height: 100vh; padding: 20px;">
                                            <div class="card shadow" style="max-width: 600px; margin: 20px auto;">
                                                <div class="card-body bg-white p-4"
                                                    style="min-height: 400px; overflow: auto;">
                                                    <div style="transform: scale(0.8); transform-origin: top center;">
                                                        @include('sales::qoution.pdf', [
                                                            'quote' => $quote,
                                                        ])
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="quote-details" role="tabpanel"
                                aria-labelledby="quote-details-tab">
                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>المنتج</th>
                                                <th>الكمية</th>
                                                <th>سعر الوحدة</th>
                                                <th>الإجمالي</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($quote->items as $item)
                                                <tr>
                                                    <td>{{ $item->product->name }}</td>
                                                    <td>{{ $item->quantity }}</td>
                                                    <td>{{ $item->price }}</td>
                                                    <td>{{ $item->total }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="activity-log" role="tabpanel" aria-labelledby="activity-log-tab">
                                <div class="activity-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <span
                                                class="badge badge-pill badge-success">{{ $quote->created_at->format('d M') }}</span>
                                            <p class="mb-0 ml-2">أنشأ {{ $quote->employee->name ?? '' }} عرض الأسعار رقم
                                                <strong>#{{ $quote->id }}</strong>
                                                للعميل <strong>{{ $quote->client->trade_name ?? '' }}</strong> بإجمالي
                                                <strong>{{ $quote->total }}</strong>
                                            </p>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <span class="mr-2">{{ $quote->created_at->format('H:i:s') ?? '' }} -
                                                {{ $quote->employee->name ?? '' }}</span>
                                            <span class="badge badge-pill badge-info">Main Branch</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="{{ asset('assets/js/applmintion.js') }}"></script>
@section('scripts')
    <script>
        function convertToInvoice(quoteId) {
            if (confirm('هل أنت متأكد من تحويل عرض الأسعار إلى فاتورة؟')) {
                fetch(`/quotes/${quoteId}/convert-to-invoice`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('تم تحويل عرض الأسعار إلى فاتورة بنجاح!');
                            window.location.href = '/invoices/' + data.invoice_id; // توجيه المستخدم إلى صفحة الفاتورة
                        } else {
                            alert('حدث خطأ أثناء التحويل: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('حدث خطأ أثناء التحويل.');
                    });
            }
        }
    </script>

    <script>
        document.getElementById('printQuoteBtn').addEventListener('click', function() {
            // احصل على محتوى الـ div الذي تريد طباعته
            var printContent = document.querySelector('div[style*="transform: scale(0.8);"]').innerHTML;

            // احتفظ بمحتوى الـ head الأصلي (لضمان استيراد أنماط CSS)
            var headContent = document.head.innerHTML;

            // أنشئ نافذة جديدة للطباعة
            var printWindow = window.open('', '_blank');

            // اكتب محتوى الصفحة الجديدة
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>طباعة عرض الأسعار #{{ $quote->id }}</title>
                    ${headContent}
                    <style>
                        @media print {
                            body * {
                                visibility: hidden;
                            }
                            .print-content, .print-content * {
                                visibility: visible;
                            }
                            .print-content {
                                position: absolute;
                                left: 0;
                                top: 0;
                                width: 100%;
                                transform: scale(1) !important;
                            }
                        }
                    </style>
                </head>
                <body>
                    <div class="print-content">
                        ${printContent}
                    </div>
                    <script>
                        window.onload = function() {
                            setTimeout(function() {
                                window.print();
                                window.close();
                            }, 200);
                        };
                    <\/script>
                </body>
                </html>
            `);

            printWindow.document.close();
        });

        // دالة تحويل إلى فاتورة (إن وجدت)
        function convertToInvoice(quoteId) {
            if (confirm('هل أنت متأكد من تحويل عرض الأسعار إلى فاتورة؟')) {
                fetch(`/quotes/${quoteId}/convert-to-invoice`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('تم تحويل عرض الأسعار إلى فاتورة بنجاح!');
                            window.location.href = '/invoices/' + data.invoice_id;
                        } else {
                            alert('حدث خطأ أثناء التحويل: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('حدث خطأ أثناء التحويل.');
                    });
            }
        }
    </script>

@endsection
@endsection
