@extends('master')

@section('title')
    عرض الفاتورة
@stop

@section('content')

<div class="content-body">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex gap-2">
                    <span class="badge badge-pill badge-warning">مفتوح</span>
                    <strong>#00002 عرض الأسعار</strong>
                    <span>المستلم: تونيبات سدرة ماركه للعروض</span>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-success d-inline-flex align-items-center">
                        <i class="fas fa-dollar-sign me-1"></i> تحويل لفاتورة
                    </button>
                    <button class="btn btn-sm btn-success d-inline-flex align-items-center">
                        <i class="fas fa-print me-1"></i> طباعة الفاتورة
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
                        <!-- تعديل -->
                        <a href="" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center">
                            <i class="fas fa-pen me-1"></i> تعديل
                        </a>

                        <!-- طباعة -->
                        <a href="#" class="btn btn-sm btn-outline-success d-inline-flex align-items-center">
                            <i class="fas fa-print me-1"></i> طباعة
                        </a>

                        <!-- PDF -->
                        <a href="#" class="btn btn-sm btn-outline-info d-inline-flex align-items-center">
                            <i class="fas fa-file-pdf me-1"></i> PDF
                        </a>

                        <!-- إضافة عملية دفع -->
                        <a href="{{route('paymentsClient.create', ['id' => $invoice->id])}}" class="btn btn-sm btn-outline-dark d-inline-flex align-items-center">
                            <i class="fas fa-wallet me-1"></i> إضافة عملية دفع
                        </a>

                        <!-- قسائم -->
                        <a href="" class="btn btn-sm btn-outline-warning d-inline-flex align-items-center">
                            <i class="fas fa-ticket-alt me-1"></i> قسائم
                        </a>

                        <!-- إضافة اتفاقية تقسيط -->
                        <a href="" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center">
                            <i class="fas fa-handshake me-1"></i> إضافة اتفاقية تقسيط
                        </a>

                        <!-- ارسال عبر -->
                        <a href="#" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center">
                            <i class="fas fa-share me-1"></i> ارسال عبر
                        </a>

                        <!-- مرتجع -->
                        <a href="{{route('appointments.create')}}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">
                            <i class="fas fa-undo-alt me-1"></i> مرتجع
                        </a>

                        <!-- خيارات أخرى -->
                        <div class="dropdown">
                            <button type="button" class="btn btn-sm btn-outline-dark dropdown-toggle"
                                    data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                خيارات أخرى
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item d-inline-flex align-items-center" href="#">
                                        <i class="fas fa-credit-card me-2"></i>
                                        أضف رصيد مدفوعات
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs mt-3" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="invoice-tab" data-toggle="tab" href="#invoice" role="tab" aria-controls="invoice" aria-selected="true">فاتورة</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="invoice-details-tab" data-toggle="tab" href="#invoice-details" role="tab" aria-controls="invoice-details" aria-selected="false">تفاصيل الفاتورة</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="payments-tab" data-toggle="tab" href="#payments" role="tab" aria-controls="payments" aria-selected="false">مدفوعات</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="warehouse-orders-tab" data-toggle="tab" href="#warehouse-orders" role="tab" aria-controls="warehouse-orders" aria-selected="false">الاذون المخزني</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="activity-log-tab" data-toggle="tab" href="#activity-log" role="tab" aria-controls="activity-log" aria-selected="false">سجل النشاطات</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="invoice-profit-tab" data-toggle="tab" href="#invoice-profit" role="tab" aria-controls="invoice-profit" aria-selected="false">ربح الفاتورة</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="invoice" role="tabpanel" aria-labelledby="invoice-tab">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>الاسم</th>
                                            <th>الكمية</th>
                                            <th>سعر البيع</th>
                                            <th>متوسط السعر</th>
                                            <th>الربح</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="table-info">
                                            <td>#3961 عطر 50 ملي</td>
                                            <td>12</td>
                                            <td>18.00 ر.س</td>
                                            <td>19.35 ر.س</td>
                                            <td>-16.25 ر.س</td>
                                        </tr>
                                        <tr>
                                            <td colspan="5" class="text-right font-weight-bold">الإجمالي: -16.25 ر.س</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="invoice-details" role="tabpanel" aria-labelledby="invoice-details-tab">
                            <p>محتوى تفاصيل الفاتورة هنا.</p>
                        </div>
                        <div class="tab-pane fade" id="payments" role="tabpanel" aria-labelledby="payments-tab">
                            <div class="d-flex justify-content-between mb-3">
                                <button class="btn btn-success">إضافة عملية دفع</button>
                                <div class="dropdown">
                                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        خيارات
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <a class="dropdown-item" href="#">عرض</a>
                                        <a class="dropdown-item" href="#">طباعة</a>
                                        <a class="dropdown-item" href="#">تعديل</a>
                                        <a class="dropdown-item" href="#">حذف</a>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>رقم الفاتورة</th>
                                            <th>العملة</th>
                                            <th>إجمالي الفاتورة</th>
                                            <th>مرتجع</th>
                                            <th>المدفوع</th>
                                            <th>الباقي</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="table-info">
                                            <td>09118</td>
                                            <td>SAR</td>
                                            <td>216.00 ر.س</td>
                                            <td>0.00 ر.س</td>
                                            <td>216.00 ر.س</td>
                                            <td>0.00 ر.س</td>
                                        </tr>
                                        <tr>
                                            <td>09119</td>
                                            <td>SAR</td>
                                            <td>270.00 ر.س</td>
                                            <td>0.00 ر.س</td>
                                            <td>270.00 ر.س</td>
                                            <td>0.00 ر.س</td>
                                        </tr>
                                        <tr>
                                            <td>09120</td>
                                            <td>SAR</td>
                                            <td>180.00 ر.س</td>
                                            <td>0.00 ر.س</td>
                                            <td>180.00 ر.س</td>
                                            <td>0.00 ر.س</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="warehouse-orders" role="tabpanel" aria-labelledby="warehouse-orders-tab">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>البند</th>
                                            <th>سعر الوحدة</th>
                                            <th>الكمية المطلوبة</th>
                                            <th>الكمية المستلمة</th>
                                            <th>الإجمالي</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="table-info">
                                            <td>#3961 عطر 50 ملي</td>
                                            <td>0.00</td>
                                            <td>12</td>
                                            <td>0</td>
                                            <td>0.00</td>
                                        </tr>
                                        <tr>
                                            <td colspan="5" class="text-right font-weight-bold">الإجمالي: 0.00</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <p>شركة أساس لتجارة الأغذية - فاتورة #09118</p>
                                <p>11/12/2024</p>
                                <p>محمد المنصوب مدير</p>
                                <p>إذن صرف مخزن حسن بواسطة: Main Branch 08:56 11/12/2024</p>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-link dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    ...
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item" href="#">تأكيد</a>
                                    <a class="dropdown-item" href="#">رفض</a>
                                    <a class="dropdown-item" href="#">عرض</a>
                                    <a class="dropdown-item" href="#">تعديل</a>
                                    <a class="dropdown-item" href="#">حذف</a>
                                    <a class="dropdown-item" href="#">إلغاء</a>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="card-title">قائمة المنتجات</h5>
                                        <button class="btn btn-sm btn-success">إضافة منتج</button>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-hover table-bordered">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>المنتج</th>
                                                    <th>الكمية</th>
                                                    <th>سعر الوحدة</th>
                                                    <th>الإجمالي</th>
                                                    <th>الخيارات</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="table-info">
                                                    <td>عطر 50 ملي</td>
                                                    <td>12</td>
                                                    <td>0.00</td>
                                                    <td>0.00</td>
                                                    <td>
                                                        <button class="btn btn-sm btn-success">تعديل</button>
                                                        <button class="btn btn-sm btn-danger">حذف</button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="activity-log" role="tabpanel" aria-labelledby="activity-log-tab">
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
                        <div class="tab-pane fade" id="invoice-profit" role="tabpanel" aria-labelledby="invoice-profit-tab">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>الاسم</th>
                                            <th>الكمية</th>
                                            <th>سعر البيع</th>
                                            <th>متوسط السعر</th>
                                            <th>الربح</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="table-info">
                                            <td>#3961 عطر 50 ملي</td>
                                            <td>12</td>
                                            <td>18.00 ر.س</td>
                                            <td>19.35 ر.س</td>
                                            <td>-16.25 ر.س</td>
                                        </tr>
                                        <tr>
                                            <td colspan="5" class="text-right font-weight-bold">الإجمالي: -16.25 ر.س</td>
                                        </tr>
                                    </tbody>
                                </table>
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
@endsection
