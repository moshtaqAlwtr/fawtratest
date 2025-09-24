@extends('master')

@section('title')
    عرض فاتورة المرتجع
@stop

@section('content')
<style>
    /* تخصيص الأزرار */
    .custom-btn {
        min-width: 120px;
        /* تحديد عرض ثابت للأزرار */
        margin: 5px;
        /* إضافة margin بقيمة 10px بين الأزرار */
        justify-content: center;
        /* توسيط النص والأيقونات داخل الأزرار */
    }
    .custom-dropdown {
min-width: 200px; /* يمكنك تعديل العرض حسب الحاجة */
}
/* إصلاح تخطيط الصفحة الرئيسية */
.tab-content {
position: relative;
z-index: 1;
}
.pdf-iframe {
    width: 100%;
    height: 800px;
    border: none;
    display: block;
    margin: 0 auto;
}

.sidebar {
position: fixed;
z-index: 100;
/* تأكد من وجود هذه الخصائص */
top: 0;
right: 0;
height: 100vh;
width: 250px; /* تعديل حسب عرض السايد بار */
background: #f8f9fa;
box-shadow: -2px 0 5px rgba(0,0,0,0.1);
}
.invoice-wrapper {
    /* عزل الفاتورة عن تخطيط الصفحة */
    contain: content;
    position: relative;
    z-index: 1;
    width: 100%;
    overflow: visible;
    padding: 20px 0;
}
/* إصلاحات نهائية للسايد بار */
.sidebar {
position: fixed !important;
right: 0 !important;
top: 0 !important;
bottom: 0 !important;
transform: none !important;
margin: 0 !important;
}

.main-content {
transition: none !important;
transform: none !important;
}
.main-content {
margin-left: 250px; /* نفس عرض السايد بار */
padding: 20px;
width: calc(100% - 250px);
}
/* تحسينات لعرض الفاتورة ضمن التبويب */
.pdf-wrapper {
width: 100%;
overflow-x: auto;
background: white;
padding: 20px;
display: flex;
justify-content: center;
}

/* إصلاح مشكلة الـ RTL */
[dir="rtl"] .pdf-wrapper {
direction: rtl;
}

/* منع تأثيرات التبويبات على الفاتورة */
.tab-content > .active {
overflow: visible !important;
}

.custom-dropdown .dropdown-item {
padding: 0.5rem 1rem; /* تعديل الحشوة لتتناسب مع الأزرار */
font-size: 0.875rem; /* حجم الخط */
}

.custom-dropdown .dropdown-item:hover {
background-color: #f8f9fa; /* لون الخلفية عند التحويم */
color: #0056b3; /* لون النص عند التحويم */
}
    /* التأكد من أن الأزرار متساوية في الارتفاع */
    .custom-btn i {
        margin-right: 5px;
        /* إضافة مسافة بين الأيقونة والنص */
    }
</style>
    <div class="content-body">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex gap-2">
                        <span class="badge badge-pill badge-danger">مرتجع</span>
                        <strong>فاتورة مرتجع #{{ $return_invoice->id }}</strong>
                        <span>العميل: {{ $return_invoice->client->trade_name }}</span>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-success d-inline-flex align-items-center print-button">
                            <i class="fas fa-print me-1"></i> طباعة المرتجع
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div class="d-flex gap-2">
                        <!-- تعديل -->
                        <!--<a href="{{ route('ReturnIInvoices.edit', $return_invoice->id) }}"-->
                        <!--    class="btn btn-sm btn-outline-danger d-inline-flex align-items-center">-->
                        <!--    <i class="fas fa-pen me-1"></i> تعديل-->
                        <!--</a>-->

                        <!-- طباعة -->
                        <!--<a href="#" class="btn btn-sm btn-outline-success d-inline-flex align-items-center print-button">-->
                        <!--    <i class="fas fa-print me-1"></i> طباعة-->
                        <!--</a>-->
                    <a href="{{ route('ReturnIInvoices.print', $id) }}"
                        class="btn btn-sm btn-outline-success d-flex align-items-center custom-btn">
                        <i class="fas fa-print me-1"></i> طباعة
                    </a>

                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-body">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="return-details-tab" data-toggle="tab" href="#return-details"
                                   role="tab" aria-controls="return-details" aria-selected="true">تفاصيل المرتجع</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="activity-log-tab" data-toggle="tab" href="#activity-log"
                                   role="tab" aria-controls="activity-log" aria-selected="false">سجل النشاطات</a>
                            </li>
                        </ul>

                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="invoice" role="tabpanel" aria-labelledby="invoice-tab">
                                <iframe src="{{ route('ReturnIInvoices.print', ['id' => $id, 'embed' => true]) }}"
                                        class="pdf-iframe"
                                        frameborder="0"></iframe>
                            </div>

                            <div class="tab-pane fade" id="activity-log" role="tabpanel" aria-labelledby="activity-log-tab">
                                <h5>سجل النشاطات</h5>
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
    <script>
        $(document).ready(function() {
            $('.print-button').click(function() {
                window.print();
            });
        });
    </script>
@endsection
