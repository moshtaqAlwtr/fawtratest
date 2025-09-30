@extends('master')

@section('title')
    الاشعارات الدائنة
@stop



@section('content')

<div aria-live="polite" aria-atomic="true" class="position-fixed top-0 end-0 p-3" style="z-index: 1050;">
    <!-- Toast -->
    @if(session('message'))
        <div class="toast align-items-center text-bg-danger border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('message') }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    @endif
</div>

<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">عرض الاشعارت الدائنة </h2>
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="">الرئيسية</a>
                        </li>
                        <li class="breadcrumb-item active">عرض
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="content-body">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex gap-2">
                    <span class="badge badge-pill badge-warning">مفتوح</span>
                    <strong>#00002 عرضالاشعار الدائن  </strong>
                    <span>المستلم: تونيبات سدرة ماركه للعروض</span>
                </div>
                <div class="d-flex gap-2">

                    <button onclick="printCreditNote()" class="btn btn-sm btn-success d-inline-flex align-items-center">
                        <i class="fas fa-print me-1"></i> طباعة
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex gap-2">








                    </div>

                </div>
            </div>
            <ul class="nav nav-tabs mt-3" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="details-tab" data-toggle="tab" href="#details" role="tab" aria-controls="details" aria-selected="true">عروض الاسعار  </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="history-tab" data-toggle="tab" href="#history" role="tab" aria-controls="history" aria-selected="false">سجل النشاطات</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="notes-tab" data-toggle="tab" href="#notes" role="tab" aria-controls="notes" aria-selected="false">تعليقات </a>
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
                                                        @include('sales::creted_note.pdf', [
                                                            'credit' => $credit,
                                                        ])
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="history-tab">
                    <div class="activity-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <span class="badge badge-pill badge-success">02 يناير</span>
                                <p class="mb-0 ml-2">أنشأ محمد الإدريسي عرض الأسعار رقم <strong>#00002</strong> للعميل <strong>تونيبات سدرة ماركه للعروض</strong> بإجمالي <strong>270.00</strong> (عرض <strong>#309</strong>)</p>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="mr-2">18:11:38 - محمد الإدريسي</span>
                                <span class="badge badge-pill badge-info">Main Branch</span>
                                <button class="btn btn-outline-success btn-sm ml-2"><i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="notes" role="tabpanel" aria-labelledby="notes-tab">
                    <div class="alert alert-warning" role="alert">
                        <i class="fas fa-exclamation-circle"></i> لم يتم إدراج أي ملاحظات
                    </div>
                    <div class="form-group">
                        <label for="newComment">إدراج تعليق جديد:</label>
                        <textarea class="form-control" id="newComment" rows="3"></textarea>
                    </div>
                    <button class="btn btn-success">أرسل إلى العميل</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@section('scripts')
<script>
    // طباعة المحتوى المحدد
    function printCreditNote() {
        // احصل على محتوى الـ div الذي تريد طباعته
        var printContent = document.querySelector('div[style*="transform: scale(0.8);"]').innerHTML;

        // أنشئ نافذة جديدة للطباعة
        var printWindow = window.open('', '_blank', 'width=800,height=600');

        // اكتب محتوى الصفحة الجديدة
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>طباعة الإشعار الدائن</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        direction: rtl;
                    }
                    @page {
                        size: A4;
                        margin: 10mm;
                    }
                    @media print {
                        body {
                            transform: scale(1) !important;
                        }
                    }
                </style>
            </head>
            <body onload="window.print(); window.close();">
                <div style="transform: scale(1);">
                    ${printContent}
                </div>
            </body>
            </html>
        `);

        printWindow.document.close();
    }

    // ربط حدث الطباعة بالأزرار
    document.addEventListener('DOMContentLoaded', function() {
        // ربط جميع أزرار الطباعة
        document.querySelectorAll('.btn-outline-success, .btn-success').forEach(btn => {
            if (btn.textContent.includes('طباعة')) {
                btn.addEventListener('click', printCreditNote);
            }
        });

        // Toast البوب آب
        var toastElList = [].slice.call(document.querySelectorAll('.toast'));
        toastElList.map(function(toastEl) {
            return new bootstrap.Toast(toastEl).show();
        });
    });
</script>
@endsection
