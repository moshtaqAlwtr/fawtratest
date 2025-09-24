     {{-- <div class="card">
            <div class="card-body">
                <div class="accordion" id="clientAccordion">
                    <div class="row g-0">
                        <!-- التفاصيل -->
                        <div class="col-md-2">
                            <div class="accordion-item">
                                <h3 class="accordion-header">
                                    <button class="accordion-button w-100 text-end" type="button" data-bs-toggle="collapse" data-bs-target="#details" aria-expanded="false" aria-controls="details">
                                        <i class="fas fa-info-circle ms-2"></i> التفاصيل
                                    </button>
                                </h3>
                                <div id="details" class="accordion-collapse collapse" data-bs-parent="#clientAccordion">
                                    <div class="accordion-body">
                                        <p class="text-muted">تفاصيل العميل</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- المواعيد -->
                        <div class="col-md-2">
                            <div class="accordion-item">
                                <h3 class="accordion-header">
                                    <button class="accordion-button w-100 text-end" type="button" data-bs-toggle="collapse" data-bs-target="#appointments" aria-expanded="false" aria-controls="appointments">
                                        <i class="fas fa-calendar-alt ms-2"></i> المواعيد
                                        <span class="badge bg-primary ms-2">{{ $client->appointments()->count() }}</span>
                                    </button>
                                </h3>
                                <div id="appointments" class="accordion-collapse collapse" data-bs-parent="#clientAccordion">
                                    <div class="accordion-body">
                                        <p class="text-muted">مواعيد العميل</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- الفواتير -->
                        <div class="col-md-2">
                            <div class="accordion-item">
                                <h3 class="accordion-header">
                                    <button class="accordion-button w-100 text-end" type="button" data-bs-toggle="collapse" data-bs-target="#invoices" aria-expanded="false" aria-controls="invoices">
                                        <i class="fas fa-file-invoice ms-2"></i> الفواتير
                                        <span class="badge bg-primary ms-2">{{ $client->invoices()->count() }}</span>
                                    </button>
                                </h3>
                                <div id="invoices" class="accordion-collapse collapse" data-bs-parent="#clientAccordion">
                                    <div class="accordion-body">
                                        <p class="text-muted">فواتير العميل</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ملخص الرصيد -->
                        <div class="col-md-2">
                            <div class="accordion-item">
                                <h3 class="accordion-header">
                                    <button class="accordion-button w-100 text-end" type="button" data-bs-toggle="collapse" data-bs-target="#balance-summary" aria-expanded="false" aria-controls="balance-summary">
                                        <i class="fas fa-chart-pie ms-2"></i> ملخص الرصيد
                                    </button>
                                </h3>
                                <div id="balance-summary" class="accordion-collapse collapse" data-bs-parent="#clientAccordion">
                                    <div class="accordion-body">
                                        <p class="text-muted">ملخص رصيد العميل</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- العضوية -->
                        <div class="col-md-2">
                            <div class="accordion-item">
                                <h3 class="accordion-header">
                                    <button class="accordion-button w-100 text-end" type="button" data-bs-toggle="collapse" data-bs-target="#membership" aria-expanded="false" aria-controls="membership">
                                        <i class="fas fa-id-card ms-2"></i> العضوية
                                    </button>
                                </h3>
                                <div id="membership" class="accordion-collapse collapse" data-bs-parent="#clientAccordion">
                                    <div class="accordion-body">
                                        <p class="text-muted">عضوية العميل</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- الحجوزات -->
                        <div class="col-md-2">
                            <div class="accordion-item">
                                <h3 class="accordion-header">
                                    <button class="accordion-button w-100 text-end" type="button" data-bs-toggle="collapse" data-bs-target="#reservations" aria-expanded="false" aria-controls="reservations">
                                        <i class="fas fa-bookmark ms-2"></i> الحجوزات
                                    </button>
                                </h3>
                                <div id="reservations" class="accordion-collapse collapse" data-bs-parent="#clientAccordion">
                                    <div class="accordion-body">
                                        <p class="text-muted">حجوزات العميل</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- الخدمات -->
                        <div class="col-md-2">
                            <div class="accordion-item">
                                <h3 class="accordion-header">
                                    <button class="accordion-button w-100 text-end" type="button" data-bs-toggle="collapse" data-bs-target="#services" aria-expanded="false" aria-controls="services">
                                        <i class="fas fa-tools ms-2"></i> الخدمات
                                    </button>
                                </h3>
                                <div id="services" class="accordion-collapse collapse" data-bs-parent="#clientAccordion">
                                    <div class="accordion-body">
                                        <p class="text-muted">خدمات العميل</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}

        {{-- <div class="card">
            <div class="card-body">
                <div class="accordion" id="clientAccordion">
                    <div class="row">

                        <!-- تبويب التفاصيل -->
                        <div class="col-md-2 mb-1">
                            <div class="accordion-item">
                                <h3 class="accordion-header">
                                    <button class="accordion-button w-100 text-end" type="button" data-bs-toggle="collapse" data-bs-target="#details" aria-expanded="false" aria-controls="details">
                                        <i class="fas fa-info-circle ms-2"></i> التفاصيل
                                    </button>
                                </h3>
                                <div id="details" class="accordion-collapse collapse" data-bs-parent="#clientAccordion">
                                    <div class="accordion-body">
                                        <p class="text-muted">تفاصيل العميل</p>
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <p><strong>الاسم التجاري:</strong> {{ $client->trade_name }}</p>
                                                        <p><strong>الاسم الأول:</strong> {{ $client->first_name }}</p>
                                                        <p><strong>الاسم الأخير:</strong> {{ $client->last_name }}</p>
                                                        <p><strong>رقم الهاتف:</strong> {{ $client->phone }}</p>
                                                        <p><strong>الجوال:</strong> {{ $client->mobile }}</p>
                                                        <p><strong>البريد الإلكتروني:</strong> {{ $client->email }}</p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p><strong>العنوان:</strong> {{ $client->street1 }} {{ $client->street2 }}</p>
                                                        <p><strong>المدينة:</strong> {{ $client->city }}</p>
                                                        <p><strong>المنطقة:</strong> {{ $client->region }}</p>
                                                        <p><strong>الرمز البريدي:</strong> {{ $client->postal_code }}</p>
                                                        <p><strong>الدولة:</strong> {{ $client->country }}</p>
                                                        <p><strong>الرقم الضريبي:</strong> {{ $client->tax_number }}</p>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <p><strong>السجل التجاري:</strong> {{ $client->commercial_registration }}</p>
                                                        <p><strong>حد الائتمان:</strong> {{ $client->credit_limit }}</p>
                                                        <p><strong>فترة الائتمان:</strong> {{ $client->credit_period }} يوم</p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p><strong>طريقة الطباعة:</strong>
                                                            @if ($client->printing_method == 1)
                                                                طباعة عادية
                                                            @elseif($client->printing_method == 2)
                                                                طباعة حرارية
                                                            @else
                                                                غير محدد
                                                            @endif
                                                        </p>
                                                        <p><strong>نوع العميل:</strong>
                                                            @if ($client->client_type == 1)
                                                                فرد
                                                            @elseif($client->client_type == 2)
                                                                شركة
                                                            @else
                                                                غير محدد
                                                            @endif
                                                        </p>
                                                        <p><strong>الرصيد الافتتاحي:</strong> {{ $client->opening_balance }}</p>
                                                        <p><strong>تاريخ الرصيد الافتتاحي:</strong> {{ $client->opening_balance_date }}</p>
                                                    </div>
                                                </div>

                                                @if ($client->notes)
                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <p><strong>ملاحظات:</strong></p>
                                                            <p>{{ $client->notes }}</p>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- تبويب المواعيد -->
                        <div class="col-md-2 mb-2">
                            <div class="accordion-item">
                                <h3 class="accordion-header">
                                    <button class="accordion-button w-100 text-end" type="button" data-bs-toggle="collapse" data-bs-target="#appointments" aria-expanded="false" aria-controls="appointments">
                                        <i class="fas fa-calendar-alt ms-2"></i> المواعيد
                                        <span class="badge bg-primary ms-2">{{ $client->appointments()->count() }}</span>
                                    </button>
                                </h3>
                                <div id="appointments" class="accordion-collapse collapse" data-bs-parent="#clientAccordion">
                                    <div class="accordion-body">
                                        <p class="text-muted">مواعيد العميل</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- تبويب الفواتير -->
                        <div class="col-md-2 mb-2">
                            <div class="accordion-item">
                                <h3 class="accordion-header">
                                    <button class="accordion-button w-100 text-end" type="button" data-bs-toggle="collapse" data-bs-target="#invoices" aria-expanded="false" aria-controls="invoices">
                                        <i class="fas fa-file-invoice ms-2"></i> الفواتير
                                        <span class="badge bg-primary ms-2">{{ $client->invoices()->count() }}</span>
                                    </button>
                                </h3>
                                <div id="invoices" class="accordion-collapse collapse" data-bs-parent="#clientAccordion">
                                    <div class="accordion-body">
                                        <p class="text-muted">فواتير العميل</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- تبويب الملاحظات -->
                        <div class="col-md-2 mb-2">
                            <div class="accordion-item">
                                <h3 class="accordion-header">
                                    <button class="accordion-button w-100 text-end" type="button" data-bs-toggle="collapse" data-bs-target="#notes-tab" aria-expanded="false" aria-controls="notes-tab">
                                        <i class="fas fa-file-invoice ms-2"></i> الملاحظات
                                        <span class="badge bg-primary ms-2"></span>
                                    </button>
                                </h3>
                                <div id="notes-tab" class="accordion-collapse collapse" data-bs-parent="#clientAccordion">
                                    <div class="accordion-body">
                                        <p class="text-muted">ملاحظات العميل</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- تبويب المدفوعات -->
                        <div class="col-md-2 mb-1">
                            <div class="accordion-item">
                                <h3 class="accordion-header">
                                    <button class="accordion-button w-100 text-end" type="button" data-bs-toggle="collapse" data-bs-target="#payments" aria-expanded="false" aria-controls="payments">
                                        <i class="fas fa-file-invoice ms-2"></i> المدفوعات
                                        <span class="badge bg-primary ms-2">{{ $client->payments->count() }}</span>
                                    </button>
                                </h3>
                                <div id="payments" class="accordion-collapse collapse" data-bs-parent="#clientAccordion">
                                    <div class="accordion-body">
                                        <p class="text-muted">مدفوعات العميل</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- تبويب حركة الحساب -->
                        <div class="col-md-2 mb-1">
                            <div class="accordion-item">
                                <h3 class="accordion-header">
                                    <button class="accordion-button w-100 text-end" type="button" data-bs-toggle="collapse" data-bs-target="#account-movement" aria-expanded="false" aria-controls="account-movement">
                                        <i class="fas fa-file-invoice ms-1"></i> حركة الحساب
                                        <span class="badge bg-primary ms-1">{{ $client->transactions->count() }}</span>
                                    </button>
                                </h3>
                                <div id="account-movement" class="accordion-collapse collapse" data-bs-parent="#clientAccordion">
                                    <div class="accordion-body">
                                        <p class="text-muted">حركة الحساب</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- تبويب زيارات العميل -->
                        <div class="col-md-2 mb-1">
                            <div class="accordion-item">
                                <h3 class="accordion-header">
                                    <button class="accordion-button w-100 text-end" type="button" data-bs-toggle="collapse" data-bs-target="#visits-tab" aria-expanded="false" aria-controls="visits-tab">
                                        <i class="fas fa-walking ms-1"></i> زيارات العميل
                                    </button>
                                </h3>
                                <span class="badge bg-primary ms-2">{{ $client->visits->count() }}</span>
                                <div id="visits-tab" class="collapse" data-bs-parent="#clientAccordion">
                                    <div class="tab-content mt-2">
                                        <p class="text-muted">زيارات العميل</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- تبويب ملخص الرصيد -->
                        <div class="col-md-2 mb-1">
                            <div class="accordion-item">
                                <h3 class="accordion-header">
                                    <button class="accordion-button w-100 text-end" type="button" data-bs-toggle="collapse" data-bs-target="#balance-summary" aria-expanded="false" aria-controls="balance-summary">
                                        <i class="fas fa-chart-pie ms-2"></i> ملخص الرصيد
                                        <span class="badge bg-primary ms-2"></span>
                                    </button>
                                </h3>
                                <div id="balance-summary" class="collapse" data-bs-parent="#clientAccordion">
                                    <div class="tab-content mt-2">
                                        <p class="text-muted">ملخص رصيد العميل</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- تبويب العضوية -->
                        <div class="col-md-2 mb-1">
                            <div class="accordion-item">
                                <h3 class="accordion-header">
                                    <button class="accordion-button w-100 text-end" type="button" data-bs-toggle="collapse" data-bs-target="#membership" aria-expanded="false" aria-controls="membership">
                                        <i class="fas fa-id-card ms-1"></i> العضوية
                                    </button>
                                </h3>
                                <div id="membership" class="collapse" data-bs-parent="#clientAccordion">
                                    <div class="tab-content mt-2">
                                        <p class="text-muted">عضوية العميل</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- تبويب الحجوزات -->
                        <div class="col-md-2 mb-1">
                            <div class="accordion-item">
                                <h3 class="accordion-header">
                                    <button class="accordion-button w-100 text-end" type="button" data-bs-toggle="collapse" data-bs-target="#reservations" aria-expanded="false" aria-controls="reservations">
                                        <i class="fas fa-bookmark ms-1"></i> الحجوزات
                                    </button>
                                </h3>
                                <div id="reservations" class="collapse" data-bs-parent="#clientAccordion">
                                    <div class="tab-content mt-2">
                                        <p class="text-muted">حجوزات العميل</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- تبويب الخدمات -->
                        <div class="col-md-2 mb-1">
                            <div class="accordion-item">
                                <h3 class="accordion-header">
                                    <button class="accordion-button w-100 text-end" type="button" data-bs-toggle="collapse" data-bs-target="#services" aria-expanded="false" aria-controls="services">
                                        <i class="fas fa-tools ms-1"></i> الخدمات
                                    </button>
                                </h3>
                                <div id="services" class="collapse" data-bs-parent="#clientAccordion">
                                    <div class="tab-content mt-2">
                                        <p class="text-muted">خدمات العميل</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> <!-- نهاية div class="row" -->
                </div> <!-- نهاية div class="accordion" -->
            </div> <!-- نهاية div class="card-body" -->
        </div> <!-- نهاية div class="card" -->
       --}}
        {{-- <div class="card">

            <div class="card-body">



                <div class="container-fluid">
                    <div class="row">
                        <!-- تبويبات العمود الجانبي -->
                        <div class="col-12">
                            <!-- تبويبات أفقية على الشاشات الكبيرة -->
                            <ul class="nav nav-tabs d-none d-md-flex" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="details-tab" data-toggle="tab" href="#details"
                                        aria-controls="details" role="tab" aria-selected="true">
                                        <span class="badge badge-pill badge-primary">{{ $client->count() }}</span>
                                        التفاصيل
                                    </a>

                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="appointments-tab" data-toggle="tab" href="#appointments"
                                        aria-controls="appointments" role="tab" aria-selected="false">
                                        المواعيد <span
                                            class="badge badge-pill badge-primary">{{ $client->appointments()->count() }}</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="invoices-tab" data-toggle="tab" href="#invoices"
                                        aria-controls="invoices" role="tab" aria-selected="false">
                                        الفواتير <span
                                            class="badge badge-pill badge-primary">{{ $client->invoices->count() }}</span>
                                    </a>
                                </li> --}}



                                {{-- <li class="nav-item">
                                    <a class="nav-link" id="notes-tab" data-toggle="tab" href="#notes"
                                        aria-controls="notes" role="tab" aria-selected="false">
                                        الملاحظات <span class="badge badge-pill badge-primary"></span>
                                    </a>
                                </li> --}}
                                {{-- <li class="nav-item">
                                    <a class="nav-link" id="payments-tab" data-toggle="tab" href="#payments"
                                        aria-controls="payments" role="tab" aria-selected="false">
                                        المدفوعات <span
                                            class="badge badge-pill badge-primary">{{ $client->payments->count() }}</span>
                                    </a>
                                </li> --}}
                                {{-- <li class="nav-item">
                                    <a class="nav-link" id="account-movement-tab" data-toggle="tab"
                                        href="#account-movement" aria-controls="account-movement" role="tab"
                                        aria-selected="false">
                                        حركة الحساب <span
                                            class="badge badge-pill badge-info">{{ $client->transactions->count() }}</span>
                                    </a>
                                </li> --}}
                                {{-- <li class="nav-item">
                                    <a class="nav-link" id="visits-tab" data-toggle="tab" href="#visits" role="tab"
                                        aria-controls="visits" aria-selected="false">
                                        زيارات العميل <span
                                            class="badge badge-pill badge-info">{{ $client->visits->count() }}</span>
                                    </a>
                                </li> --}}
{{--
                                <li class="nav-item">
                                    <a class="nav-link" id="balance-summary-tab" data-toggle="tab"
                                        href="#balance-summary" aria-controls="balance-summary" role="tab"
                                        aria-selected="false">ملخص الرصيد</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="membership-tab" data-toggle="tab" href="#membership"
                                        aria-controls="membership" role="tab" aria-selected="false">العضوية</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="timeline-tab" data-toggle="tab" href="#timeline"
                                        aria-controls="timeline" role="tab" aria-selected="false">الجدول الزمني</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="service-tab" data-toggle="tab" href="#service"
                                        aria-controls="service" role="tab" aria-selected="false">الحجوزات /
                                        الخدمات</a>
                                </li>
                            </ul> --}}

                            <!-- تبويبات عمودية على الشاشات الصغيرة -->
                            {{-- <ul class="nav nav-tabs flex-column d-block d-md-none" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="details-tab" data-toggle="tab" href="#details"
                                        aria-controls="details" role="tab" aria-selected="true">
                                        <span class="badge badge-pill badge-primary">{{ $client->count() }}</span>
                                        التفاصيل
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="appointments-tab" data-toggle="tab" href="#appointments"
                                        aria-controls="appointments" role="tab" aria-selected="false">
                                        المواعيد <span
                                            class="badge badge-pill badge-primary">{{ $client->appointments()->count() }}</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="invoices-tab" data-toggle="tab" href="#invoices"
                                        aria-controls="invoices" role="tab" aria-selected="false">
                                        الفواتير <span
                                            class="badge badge-pill badge-primary">{{ $client->invoices->count() }}</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="notes-tab" data-toggle="tab" href="#notes"
                                        aria-controls="notes" role="tab" aria-selected="false">
                                        الملاحظات <span class="badge badge-pill badge-primary"></span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="payments-tab" data-toggle="tab" href="#payments"
                                        aria-controls="payments" role="tab" aria-selected="false">
                                        المدفوعات <span
                                            class="badge badge-pill badge-primary">{{ $client->payments->count() }}</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="account-movement-tab" data-toggle="tab"
                                        href="#account-movement" aria-controls="account-movement" role="tab"
                                        aria-selected="false">
                                        حركة الحساب <span
                                            class="badge badge-pill badge-info">{{ $client->transactions->count() }}</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="visits-tab" data-toggle="tab" href="#visits" role="tab"
                                        aria-controls="visits" aria-selected="false">
                                        زيارات العميل <span
                                            class="badge badge-pill badge-info">{{ $client->visits->count() }}</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="balance-summary-tab" data-toggle="tab"
                                        href="#balance-summary" aria-controls="balance-summary" role="tab"
                                        aria-selected="false">ملخص الرصيد</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="membership-tab" data-toggle="tab" href="#membership"
                                        aria-controls="membership" role="tab" aria-selected="false">العضوية</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="timeline-tab" data-toggle="tab" href="#timeline"
                                        aria-controls="timeline" role="tab" aria-selected="false">الجدول الزمني</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="service-tab" data-toggle="tab" href="#service"
                                        aria-controls="service" role="tab" aria-selected="false">الحجوزات /
                                        الخدمات</a>
                                </li>
                            </ul> --}}

                                                    {{-- <div class="nav-item" role="presentation">
                                                        <button class="nav-link w-100 text-end" id="membership-tab" type="button" data-bs-toggle="collapse" data-bs-target="#membership" aria-expanded="false" aria-controls="membership">
                                                            <i class="fas fa-id-card ms-1"></i> العضوية
                                                        </button>
                                                        <div id="membership" class="collapse" data-bs-parent="#clientAccordion">
                                                            <div class="tab-content mt-2">
                                                                <p class="text-muted">عضوية العميل</p>
                                                            </div>
                                                        </div>
                                                    </div> --}}
                                                    {{-- <div class="nav-item" role="presentation">
                                                        <button class="nav-link w-100 text-end" id="reservations-tab" type="button" data-bs-toggle="collapse" data-bs-target="#reservations" aria-expanded="false" aria-controls="reservations">
                                                            <i class="fas fa-bookmark ms-1"></i> الحجوزات
                                                        </button>
                                                        <div id="reservations" class="collapse" data-bs-parent="#clientAccordion">
                                                            <div class="tab-content mt-2">
                                                                <p class="text-muted">حجوزات العميل</p>
                                                            </div>
                                                        </div>
                                                    </div> --}}
                                                    {{-- <div class="nav-item" role="presentation">
                                                        <button class="nav-link w-100 text-end" id="services-tab" type="button" data-bs-toggle="collapse" data-bs-target="#services" aria-expanded="false" aria-controls="services">
                                                            <i class="fas fa-concierge-bell ms-1"></i> الخدمات
                                                        </button>

                                                        <div id="services" class="collapse" data-bs-parent="#clientAccordion">
                                                            <div class="tab-content mt-2">
                                                                <p class="text-muted">خدمات العميل</p>
                                                            </div>
                                                        </div> --}}


                        <!-- محتوى التبويبات -->
                        {{-- <div class="col-12">
                            <div class="tab-content"> --}}
                                <!-- تبويب التفاصيل -->

{{--
                                <!-- تبويب المواعيد -->
                                <div class="tab-pane" id="appointments" aria-labelledby="appointments-tab"
                                    role="tabpanel">
                                    @php
                                        $completedAppointments = $client->appointments->where(
                                            'status',
                                            App\Models\Appointment::STATUS_COMPLETED,
                                        );
                                        $ignoredAppointments = $client->appointments->where(
                                            'status',
                                            App\Models\Appointment::STATUS_IGNORED,
                                        );
                                        $pendingAppointments = $client->appointments->where(
                                            'status',
                                            App\Models\Appointment::STATUS_PENDING,
                                        );
                                        $rescheduledAppointments = $client->appointments->where(
                                            'status',
                                            App\Models\Appointment::STATUS_RESCHEDULED,
                                        );
                                    @endphp

                                    <div class="card">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <!-- زر القائمة المنسدلة للأجهزة الصغيرة -->
                                            <div class="dropdown d-block d-md-none">
                                                <button class="btn btn-sm btn-outline-primary dropdown-toggle"
                                                    type="button" id="dropdownMenuButton" data-toggle="dropdown"
                                                    aria-haspopup="true" aria-expanded="false">
                                                    تصفية المواعيد
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    <button class="dropdown-item filter-appointments" data-filter="all">
                                                        الكل <span
                                                            class="badge badge-light">{{ $client->appointments->count() }}</span>
                                                    </button>
                                                    <button class="dropdown-item filter-appointments"
                                                        data-filter="{{ App\Models\Appointment::STATUS_COMPLETED }}">
                                                        تم <span
                                                            class="badge badge-light">{{ $completedAppointments->count() }}</span>
                                                    </button>
                                                    <button class="dropdown-item filter-appointments"
                                                        data-filter="{{ App\Models\Appointment::STATUS_IGNORED }}">
                                                        تم صرف النظر عنه <span
                                                            class="badge badge-light">{{ $ignoredAppointments->count() }}</span>
                                                    </button>
                                                    <button class="dropdown-item filter-appointments"
                                                        data-filter="{{ App\Models\Appointment::STATUS_PENDING }}">
                                                        تم جدولته <span
                                                            class="badge badge-light">{{ $pendingAppointments->count() }}</span>
                                                    </button>
                                                    <button class="dropdown-item filter-appointments"
                                                        data-filter="{{ App\Models\Appointment::STATUS_RESCHEDULED }}">
                                                        تم جدولته مجددا <span
                                                            class="badge badge-light">{{ $rescheduledAppointments->count() }}</span>
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- الأزرار العادية للأجهزة الكبيرة -->
                                            <div class="d-none d-md-flex gap-2 flex-wrap">
                                                <button class="btn btn-sm btn-outline-primary filter-appointments"
                                                    data-filter="all">
                                                    الكل <span
                                                        class="badge badge-light">{{ $client->appointments->count() }}</span>
                                                </button>
                                                <button class="btn btn-sm btn-outline-success filter-appointments"
                                                    data-filter="{{ App\Models\Appointment::STATUS_COMPLETED }}">
                                                    تم <span
                                                        class="badge badge-light">{{ $completedAppointments->count() }}</span>
                                                </button>
                                                <button class="btn btn-sm btn-outline-warning filter-appointments"
                                                    data-filter="{{ App\Models\Appointment::STATUS_IGNORED }}">
                                                    تم صرف النظر عنه <span
                                                        class="badge badge-light">{{ $ignoredAppointments->count() }}</span>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger filter-appointments"
                                                    data-filter="{{ App\Models\Appointment::STATUS_PENDING }}">
                                                    تم جدولته <span
                                                        class="badge badge-light">{{ $pendingAppointments->count() }}</span>
                                                </button>
                                                <button class="btn btn-sm btn-outline-info filter-appointments"
                                                    data-filter="{{ App\Models\Appointment::STATUS_RESCHEDULED }}">
                                                    تم جدولته مجددا <span
                                                        class="badge badge-light">{{ $rescheduledAppointments->count() }}</span>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="card-body">
                                            <div id="appointments-container">
                                                @if ($client->appointments->count() > 0)
                                                    <div class="table-responsive">
                                                        <table class="table">
                                                            <thead>
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>العنوان</th>
                                                                    <th>الوصف</th>
                                                                    <th>التاريخ</th>
                                                                    <th>بواسطة</th>
                                                                    <th>الحالة</th>
                                                                    <th>الإجراءات</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($client->appointments as $appointment)
                                                                    <tr data-appointment-id="{{ $appointment->id }}"
                                                                        data-status="{{ $appointment->status }}"
                                                                        data-date="{{ $appointment->created_at->format('Y-m-d') }}">
                                                                        <td>{{ $appointment->id }}</td>
                                                                        <td>{{ $appointment->title }}</td>
                                                                        <td>{{ $appointment->description }}</td>
                                                                        <td>{{ $appointment->created_at->format('Y-m-d H:i') }}
                                                                        </td>
                                                                        <td>{{ $appointment->employee->name ?? 'غير محدد' }}
                                                                        </td>
                                                                        <td>
                                                                            <span
                                                                                class="badge status-badge {{ $appointment->status_color }}">
                                                                                {{ $appointment->status_text }}
                                                                            </span>
                                                                        </td>
                                                                        <td>
                                                                            <div class="dropdown">
                                                                                <button
                                                                                    class="btn btn-sm bg-gradient-info fa fa-ellipsis-v"
                                                                                    type="button"
                                                                                    id="dropdownMenuButton{{ $appointment->id }}"
                                                                                    data-toggle="dropdown"
                                                                                    aria-haspopup="true"
                                                                                    aria-expanded="false"></button>
                                                                                <div class="dropdown-menu dropdown-menu-end"
                                                                                    aria-labelledby="dropdownMenuButton{{ $appointment->id }}">
                                                                                    <form
                                                                                        action="{{ route('appointments.update-status', $appointment->id) }}"
                                                                                        method="POST" class="d-inline">
                                                                                        @csrf
                                                                                        @method('PATCH')
                                                                                        <input type="hidden"
                                                                                            name="status" value="1">
                                                                                        <button type="submit"
                                                                                            class="dropdown-item">
                                                                                            <i
                                                                                                class="fa fa-clock me-2 text-warning"></i>تم
                                                                                            جدولته
                                                                                        </button>
                                                                                    </form>
                                                                                    <form
                                                                                        action="{{ route('appointments.update-status', $appointment->id) }}"
                                                                                        method="POST" class="d-inline">
                                                                                        @csrf
                                                                                        @method('PATCH')
                                                                                        <input type="hidden"
                                                                                            name="status" value="2">
                                                                                        <input type="hidden"
                                                                                            name="auto_delete"
                                                                                            value="1">
                                                                                        <button type="submit"
                                                                                            class="dropdown-item">
                                                                                            <i
                                                                                                class="fa fa-check me-2 text-success"></i>تم
                                                                                        </button>
                                                                                    </form>
                                                                                    <form
                                                                                        action="{{ route('appointments.update-status', $appointment->id) }}"
                                                                                        method="POST" class="d-inline">
                                                                                        @csrf
                                                                                        @method('PATCH')
                                                                                        <input type="hidden"
                                                                                            name="status" value="3">
                                                                                        <button type="submit"
                                                                                            class="dropdown-item">
                                                                                            <i
                                                                                                class="fa fa-times me-2 text-danger"></i>صرف
                                                                                            النظر عنه
                                                                                        </button>
                                                                                    </form>
                                                                                    <form
                                                                                        action="{{ route('appointments.update-status', $appointment->id) }}"
                                                                                        method="POST" class="d-inline">
                                                                                        @csrf
                                                                                        @method('PATCH')
                                                                                        <input type="hidden"
                                                                                            name="status" value="4">
                                                                                        <button type="submit"
                                                                                            class="dropdown-item">
                                                                                            <i
                                                                                                class="fa fa-redo me-2 text-info"></i>تم
                                                                                            جدولته مجددا
                                                                                        </button>
                                                                                    </form>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @else
                                                    <div class="alert alert-info text-center">
                                                        لا توجد مواعيد
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- تبويب الفواتير -->
                                <div class="tab-pane" id="invoices" aria-labelledby="invoices-tab" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table table-hover custom-table" id="fawtra">
                                            <thead>
                                                <tr class="bg-gradient-light text-center">
                                                    <th></th>
                                                    <th class="border-start">رقم الفاتورة</th>
                                                    <th>معلومات العميل</th>
                                                    <th>تاريخ الفاتورة</th>
                                                    <th>المصدر والعملية</th>
                                                    <th>المبلغ والحالة</th>
                                                    <th style="width: 100px;">الإجراءات</th>
                                                </tr>
                                            </thead>
                                            <tbody id="invoiceTableBody">
                                                @foreach ($invoices as $invoice)
                                                    <tr class="align-middle invoice-row"
                                                        onclick="window.location.href='{{ route('invoices.show', $invoice->id) }}'"
                                                        style="cursor: pointer;"
                                                        data-status="{{ $invoice->payment_status }}">
                                                        <td onclick="event.stopPropagation()">
                                                            <input type="checkbox" class="invoice-checkbox"
                                                                name="invoices[]" value="{{ $invoice->id }}">
                                                        </td>
                                                        <td class="text-center border-start"><span
                                                                class="invoice-number">#{{ $invoice->id }}</span></td>
                                                        <td>
                                                            <div class="client-info">
                                                                <div class="client-name mb-2">
                                                                    <i class="fas fa-user text-primary me-1"></i>
                                                                    <strong>{{ $invoice->client ? ($invoice->client->trade_name ?: $invoice->client->first_name . ' ' . $invoice->client->last_name) : 'عميل غير معروف' }}</strong>
                                                                </div>
                                                                @if ($invoice->client && $invoice->client->tax_number)
                                                                    <div class="tax-info mb-1">
                                                                        <i class="fas fa-hashtag text-muted me-1"></i>
                                                                        <span class="text-muted small">الرقم الضريبي:
                                                                            {{ $invoice->client->tax_number }}</span>
                                                                    </div>
                                                                @endif
                                                                @if ($invoice->client && $invoice->client->full_address)
                                                                    <div class="address-info">
                                                                        <i
                                                                            class="fas fa-map-marker-alt text-muted me-1"></i>
                                                                        <span
                                                                            class="text-muted small">{{ $invoice->client->full_address }}</span>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="date-info mb-2">
                                                                <i class="fas fa-calendar text-info me-1"></i>
                                                                {{ $invoice->created_at ? $invoice->created_at->format($account_setting->time_formula ?? 'H:i:s d/m/Y') : '' }}
                                                            </div>
                                                            <div class="creator-info">
                                                                <i class="fas fa-user text-muted me-1"></i>
                                                                <span class="text-muted small">بواسطة:
                                                                    {{ $invoice->createdByUser->name ?? 'غير محدد' }}</span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex flex-column gap-2"
                                                                style="margin-bottom: 60px">
                                                                @php
                                                                    $payments = \App\Models\PaymentsProcess::where(
                                                                        'invoice_id',
                                                                        $invoice->id,
                                                                    )
                                                                        ->where('type', 'client payments')
                                                                        ->orderBy('created_at', 'desc')
                                                                        ->get();
                                                                @endphp

                                                                @if ($invoice->type == 'returned')
                                                                    <span class="badge bg-danger text-white"><i
                                                                            class="fas fa-undo me-1"></i>مرتجع</span>
                                                                @elseif ($invoice->type == 'normal' && $payments->count() == 0)
                                                                    <span class="badge bg-secondary text-white"><i
                                                                            class="fas fa-file-invoice me-1"></i>أنشئت
                                                                        فاتورة</span>
                                                                @endif

                                                                @if ($payments->count() > 0)
                                                                    <span class="badge bg-success text-white"><i
                                                                            class="fas fa-check-circle me-1"></i>أضيفت
                                                                        عملية دفع</span>
                                                                @endif
                                                            </div>
                                                        </td>
                                                        <td>
                                                            @php
                                                                $statusClass = match ($invoice->payment_status) {
                                                                    1 => 'success',
                                                                    2 => 'info',
                                                                    3 => 'danger',
                                                                    4 => 'secondary',
                                                                    default => 'dark',
                                                                };
                                                                $statusText = match ($invoice->payment_status) {
                                                                    1 => 'مدفوعة بالكامل',
                                                                    2 => 'مدفوعة جزئياً',
                                                                    3 => 'غير مدفوعة',
                                                                    4 => 'مستلمة',
                                                                    default => 'غير معروفة',
                                                                };
                                                            @endphp
                                                            <div class="text-center">
                                                                <span
                                                                    class="badge bg-{{ $statusClass }} text-white status-badge">{{ $statusText }}</span>
                                                            </div>
                                                            @php
                                                                $currency = $account_setting->currency ?? 'SAR';
                                                                $currencySymbol =
                                                                    $currency == '' || empty($currency)
                                                                        ? '<img src="' .
                                                                            asset('assets/images/Saudi_Riyal.svg') .
                                                                            '" alt="ريال سعودي" width="15" style="vertical-align: middle;">'
                                                                        : $currency;
                                                            @endphp
                                                            <div class="amount-info text-center mb-2">
                                                                <h6 class="amount mb-1">
                                                                    {{ number_format($invoice->grand_total ?? $invoice->total, 2) }}
                                                                    <small class="currency">{!! $currencySymbol !!}</small>
                                                                </h6>
                                                                @if ($invoice->due_value > 0)
                                                                    <div class="due-amount">
                                                                        <small class="text-danger">المبلغ المستحق:
                                                                            {{ number_format($invoice->due_value, 2) }}
                                                                            {!! $currencySymbol !!}</small>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="dropdown" onclick="event.stopPropagation()">
                                                                <button
                                                                    class="btn btn-sm bg-gradient-info fa fa-ellipsis-v "
                                                                    type="button"
                                                                    id="dropdownMenuButton{{ $invoice->id }}"
                                                                    data-bs-toggle="dropdown" data-bs-auto-close="outside"
                                                                    aria-haspopup="true" aria-expanded="false"></button>
                                                                <div class="dropdown-menu">
                                                                    <a class="dropdown-item"
                                                                        href="{{ route('invoices.edit', $invoice->id) }}">
                                                                        <i class="fa fa-edit me-2 text-success"></i>تعديل
                                                                    </a>
                                                                    <a class="dropdown-item"
                                                                        href="{{ route('invoices.show', $invoice->id) }}">
                                                                        <i class="fa fa-eye me-2 text-primary"></i>عرض
                                                                    </a>
                                                                    <a class="dropdown-item"
                                                                        href="{{ route('invoices.generatePdf', $invoice->id) }}">
                                                                        <i class="fa fa-file-pdf me-2 text-danger"></i>PDF
                                                                    </a>
                                                                    <a class="dropdown-item"
                                                                        href="{{ route('invoices.generatePdf', $invoice->id) }}">
                                                                        <i class="fa fa-print me-2 text-dark"></i>طباعة
                                                                    </a>
                                                                    <a class="dropdown-item" href="#">
                                                                        <i
                                                                            class="fa fa-envelope me-2 text-warning"></i>إرسال
                                                                        إلى العميل
                                                                    </a>
                                                                    <a class="dropdown-item"
                                                                        href="{{ route('paymentsClient.create', ['id' => $invoice->id]) }}">
                                                                        <i
                                                                            class="fa fa-credit-card me-2 text-info"></i>إضافة
                                                                        عملية دفع
                                                                    </a>
                                                                    <a class="dropdown-item" href="#">
                                                                        <i class="fa fa-copy me-2 text-secondary"></i>نسخ
                                                                    </a>
                                                                    <form
                                                                        action="{{ route('invoices.destroy', $invoice->id) }}"
                                                                        method="POST" class="d-inline">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit"
                                                                            class="dropdown-item text-danger">
                                                                            <i class="fa fa-trash me-2"></i>حذف
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </div>

                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- تبويب الملاحظات -->

                                <div class="tab-pane" id="notes" aria-labelledby="notes-tab" role="tabpanel">
                                    <div class="timeline">
                                        @foreach ($ClientRelations as $note)
                                            <div class="timeline-item">
                                                <div class="timeline-content d-flex align-items-start">
                                                    <span class="badge" style="background-color: {{ $statuses->find($client->status_id)->color }}; color: white;">
                                                        {{ $statuses->find($client->status_id)->name }}
                                                    </span>
                                                    <div
                                                        class="note-box border rounded bg-white shadow-sm p-3 ms-3 flex-grow-1">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <h6 class="mb-0"><i class="fas fa-user"></i>
                                                                {{ $note->created_by }}</h6>
                                                            <small class="text-muted">
                                                                <i class="fas fa-clock"></i>
                                                                {{ $note->created_at->format('H:i d/m/Y') }} - <span
                                                                    class="text-primary">{{ $note->status }}</span>
                                                            </small>
                                                        </div>
                                                        <hr> <i class="far fa-user me-1"></i>
                                                        <p class="mb-2">{{ $note->process ?? '' }}</p>
                                                        <small class="text-muted">{{ $note->description ?? '' }}</small>
                                                    </div>
                                                    <div class="timeline-dot bg-danger"></div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>


                                <!-- تبويب المدفوعات -->
                                <div class="tab-pane" id="payments" aria-labelledby="payments-tab" role="tabpanel">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>رقم الفاتورة</th>
                                                        <th>ملاحظات</th>
                                                        <th>تاريخ الدفع</th>
                                                        <th>بواسطة</th>
                                                        <th>المبلغ</th>
                                                        <th>الحالة</th>
                                                        <th>الإجراءات</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($client->payments as $payment)
                                                        <tr>
                                                            <td>{{ $payment->id }}</td>
                                                            <td>{{ $payment->invoice->code ?? 'غير محدد' }}</td>
                                                            <td>{{ $payment->notes }}</td>
                                                            <td>{{ $payment->payment_date }}</td>
                                                            <td>{{ $payment->employee->full_name ?? 'غير محدد' }}</td>
                                                            <td class="text-end">{{ number_format($payment->amount, 2) }}
                                                                ر.س</td>
                                                            <td class="text-center">
                                                                @php
                                                                    $statusClass = '';
                                                                    $statusText = '';
                                                                    $statusIcon = '';

                                                                    if ($payment->payment_status == 2) {
                                                                        $statusClass = 'badge-warning';
                                                                        $statusText = 'غير مكتمل';
                                                                        $statusIcon = 'fa-clock';
                                                                    } elseif ($payment->payment_status == 1) {
                                                                        $statusClass = 'badge-success';
                                                                        $statusText = 'مكتمل';
                                                                        $statusIcon = 'fa-check-circle';
                                                                    } elseif ($payment->payment_status == 4) {
                                                                        $statusClass = 'badge-info';
                                                                        $statusText = 'تحت المراجعة';
                                                                        $statusIcon = 'fa-sync';
                                                                    } elseif ($payment->payment_status == 5) {
                                                                        $statusClass = 'badge-danger';
                                                                        $statusText = 'فاشلة';
                                                                        $statusIcon = 'fa-times-circle';
                                                                    } elseif ($payment->payment_status == 3) {
                                                                        $statusClass = 'badge-secondary';
                                                                        $statusText = 'مسودة';
                                                                        $statusIcon = 'fa-file-alt';
                                                                    } else {
                                                                        $statusClass = 'badge-light';
                                                                        $statusText = 'غير معروف';
                                                                        $statusIcon = 'fa-question-circle';
                                                                    }
                                                                @endphp
                                                                <span class="badge {{ $statusClass }}">
                                                                    <i class="fas {{ $statusIcon }} me-1"></i>
                                                                    {{ $statusText }}
                                                                </span>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="btn-group">
                                                                    <div class="dropdown">
                                                                        <button
                                                                            class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1"
                                                                            type="button" id="dropdownMenuButton303"
                                                                            data-toggle="dropdown" aria-haspopup="true"
                                                                            aria-expanded="false"></button>
                                                                        <div class="dropdown-menu"
                                                                            aria-labelledby="dropdownMenuButton303">
                                                                            <li>
                                                                                <a class="dropdown-item"
                                                                                    href="{{ route('paymentsClient.show', $payment->id) }}">
                                                                                    <i
                                                                                        class="fa fa-eye me-2 text-primary"></i>عرض
                                                                                </a>
                                                                            </li>
                                                                            <li>
                                                                                <a class="dropdown-item"
                                                                                    href="{{ route('paymentsClient.edit', $payment->id) }}">
                                                                                    <i
                                                                                        class="fa fa-edit me-2 text-success"></i>تعديل
                                                                                </a>
                                                                            </li>
                                                                            <form
                                                                                action="{{ route('paymentsClient.destroy', $payment->id) }}"
                                                                                method="POST">
                                                                                @csrf
                                                                                @method('DELETE')
                                                                                <button type="submit"
                                                                                    class="dropdown-item"
                                                                                    style="border: none; background: none;">
                                                                                    <i
                                                                                        class="fa fa-trash me-2 text-danger"></i>
                                                                                    حذف
                                                                                </button>
                                                                            </form>
                                                                            <li>
                                                                                <a class="dropdown-item" href="#">
                                                                                    <i
                                                                                        class="fa fa-envelope me-2 text-warning"></i>ايصال
                                                                                    مدفوعات
                                                                                </a>
                                                                            </li>
                                                                            <li>
                                                                                <a class="dropdown-item" href="#">
                                                                                    <i
                                                                                        class="fa fa-envelope me-2 text-warning"></i>ايصال
                                                                                    مدفوعات حراري
                                                                                </a>
                                                                            </li>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- تبويب حركة الحساب -->
                                <div class="tab-pane" id="account-movement" aria-labelledby="account-movement-tab"
                                    role="tabpanel">
                                    <div class="mb-3">
                                        <div class="row">
                                            <!-- الأزرار على الشاشات الصغيرة (الهاتف والتابلت) -->
                                            <div class="col-12 d-block d-md-none mb-3">
                                                <div class="d-flex flex-column gap-2">
                                                    <a href="#" class="btn btn-sm btn-info text-white">
                                                        <i class="fas fa-file-export me-1"></i> خيارات التصدير
                                                    </a>
                                                    <a href="#" class="btn btn-sm btn-light">
                                                        <i class="fas fa-print me-1"></i> طباعة
                                                    </a>
                                                    <a href="#" class="btn btn-sm btn-light">
                                                        <i class="fas fa-cog me-1"></i> تخصيص
                                                    </a>
                                                </div>
                                            </div>

                                            <!-- الأزرار على الشاشات الكبيرة (اللاب توب) -->
                                            <div class="col-md-7 d-none d-md-block">
                                                <div class="d-flex flex-wrap gap-2">
                                                    <a href="#" class="btn btn-sm btn-info text-white">
                                                        <i class="fas fa-file-export me-1"></i> خيارات التصدير
                                                    </a>
                                                    <a href="#" class="btn btn-sm btn-light">
                                                        <i class="fas fa-print me-1"></i> طباعة
                                                    </a>
                                                    <a href="#" class="btn btn-sm btn-light">
                                                        <i class="fas fa-cog me-1"></i> تخصيص
                                                    </a>
                                                </div>
                                            </div>

                                            <!-- عناصر التحكم (على جميع الأجهزة) -->
                                            <div class="col-12 col-md-5">
                                                <div
                                                    class="d-flex flex-column flex-md-row align-items-center justify-content-end gap-2">
                                                    <!-- زر التبديل -->
                                                    <div class="form-check form-switch d-flex align-items-center w-100 w-md-auto">
                                                        <input class="form-check-input" type="checkbox" id="showDetails">
                                                        <label class="form-check-label ms-2 w-100 d-flex align-items-center justify-content-between" for="showDetails">
                                                            <span><i class="fas fa-eye me-2"></i> اعرض التفاصيل</span>
                                                        </label>
                                                    </div>

                                                    <!-- حقل التاريخ -->
                                                    <div class="input-group input-group-sm" style="width: 200px;">
                                                        <input type="date" class="form-control"
                                                            placeholder="الفترة من / إلى">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card">
                                        <div class="card-body p-4">
                                            <div class="row mb-4">
                                                <div class="col-md-6 text-start">
                                                    <h5 class="mb-2">{{ $client->trade_name }}</h5>
                                                    <p class="mb-1">{{ $client->city }}</p>
                                                    <p class="mb-1">{{ $client->region }}، {{ $client->city }}</p>
                                                    <p class="mb-0"><strong>التاريخ:</strong> {{ date('d/m/Y') }}</p>
                                                </div>
                                                <div class="col-md-6 text-end">
                                                    <h4 class="mb-2">كشف حساب</h4>
                                                    <p class="mb-1">{{ $client->trade_name }}</p>
                                                    <p class="mb-1">{{ $client->region }} - {{ $client->city }}</p>
                                                    <p class="mb-0">{{ $client->country }}</p>
                                                    <p class="mt-2"><strong>حركة الحساب حتى:</strong>
                                                        {{ date('d/m/Y') }}</p>
                                                </div>
                                            </div>

                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hover mb-0">
                                                    <thead class="bg-dark text-white">
                                                        <tr>
                                                            <th class="text-end" style="width: 20%;">التاريخ</th>
                                                            <th class="text-end" style="width: 40%;">العملية</th>
                                                            <th class="text-start" style="width: 20%;">المبلغ</th>
                                                            <th class="text-start" style="width: 20%;">المبلغ المتبقي</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $total_amount = 0; // المجموع الكلي للمبلغ
                                                            $total_due = 0; // المجموع الكلي للمبلغ المتبقي
                                                        @endphp

                                                        @foreach ($invoices as $invoice)
                                                            <!-- عرض بيانات الفاتورة -->
                                                            <tr>
                                                                <td class="text-end">{{ $invoice->invoice_date }}</td>
                                                                <td class="text-end">
                                                                    @if ($invoice->type == 'returned')
                                                                        مرتجع لفاتورة رقم {{ $invoice->code }}
                                                                    @else
                                                                        فاتورة {{ $invoice->code }}
                                                                    @endif
                                                                </td>
                                                                <td class="text-start">
                                                                    {{ number_format($invoice->grand_total, 2) }}</td>
                                                                <td class="text-start">
                                                                    {{ number_format($invoice->due_value, 2) }}</td>
                                                            </tr>

                                                            @php
                                                                $total_amount += $invoice->grand_total; // زيادة المجموع الكلي للمبلغ
                                                                $total_due += $invoice->due_value; // زيادة المجموع الكلي للمبلغ المتبقي
                                                            @endphp

                                                            <!-- عرض بيانات المدفوعات المرتبطة بالفاتورة -->
                                                            @foreach ($invoice->payments as $payment)
                                                                <tr>
                                                                    <td class="text-end">{{ $payment->payment_date }}
                                                                    </td>
                                                                    <td class="text-end">عملية دفع
                                                                        (@if ($payment->Payment_method == 1)
                                                                            نقدي
                                                                        @elseif ($payment->Payment_method == 2)
                                                                            شيك
                                                                        @else
                                                                            بطاقة ائتمان
                                                                        @endif)
                                                                    </td>
                                                                    <td class="text-start">
                                                                        @if ($invoice->advance_payment > 0)
                                                                            -{{ number_format($invoice->advance_payment, 2) }}
                                                                        @else
                                                                            {{ number_format($invoice->advance_payment, 2) }}
                                                                        @endif
                                                                    </td>
                                                                    <td class="text-start">
                                                                        {{ number_format($invoice->due_value, 2) }}</td>
                                                                </tr>
                                                            @endforeach
                                                        @endforeach
                                                    </tbody>
                                                    <!-- عرض المجموع الكلي -->
                                                    <tfoot class="bg-light">
                                                        <tr>
                                                            <th class="text-end" colspan="2">المجموع الكلي</th>
                                                            <th class="text-start">{{ number_format($total_amount, 2) }}
                                                            </th>
                                                            <th class="text-start">{{ number_format($total_due, 2) }}
                                                            </th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="tab-pane fade" id="visits" role="tabpanel" aria-labelledby="visits-tab">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>تاريخ الزيارة</th>
                                                    <th>الموظف</th>

                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($visits as $visit)
                                                    <tr>
                                                        <td>{{ $visit->id }}</td>
                                                        <td>{{ $visit->visit_date }}</td>
                                                        <td>{{ $visit->employee->name }}</td>


                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- تبويب ملخص الرصيد -->
                                <div class="tab-pane" id="balance-summary" aria-labelledby="balance-summary-tab"
                                    role="tabpanel">
                                    <div class="d-flex justify-content-end gap-2 mb-3">
                                        <a href="#" class="btn btn-info text-white">
                                            <i class="fas fa-plus"></i>
                                            أضف شحن الرصيد
                                        </a>
                                        <a href="#" class="btn btn-secondary">
                                            <i class="fas fa-history"></i>
                                            عرض السجل
                                        </a>
                                    </div>

                                    <div class="card">
                                        <div class="card-body">
                                            <div class="text-center py-5">
                                                <div class="text-muted">
                                                    لا يوجد انواع الرصيد اضيفت حتى الآن
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- تبويب العضوية -->
                                <div class="tab-pane" id="membership" aria-labelledby="membership-tab" role="tabpanel">
                                    <div class="card">
                                        <div class="card-body">
                                            <table class="table" style="font-size: 1.1rem;">
                                                <thead>
                                                    <tr>
                                                        <th>المعرف</th>
                                                        <th>بيانات العميل</th>
                                                        <th>الباقة الحالية </th>
                                                        <th>تاريخ الانتهاء</th>
                                                        <th>الحالة</th>
                                                        <th>ترتيب بواسطة</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($memberships as $membership)
                                                        <tr>
                                                            <td>#1</td>
                                                            <td>
                                                                <div class="d-flex align-items-center gap-2">
                                                                    <div class="avatar avatar-sm bg-danger">
                                                                        <span class="avatar-content">أ</span>
                                                                    </div>
                                                                    <div>
                                                                        {{ $membership->client->first_name ?? '' }}
                                                                        <br>
                                                                        <small class="text-muted"></small>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td><br><small
                                                                    class="text-muted">{{ $membership->packege->commission_name ?? '' }}</small>
                                                            </td>
                                                            <td><small
                                                                    class="text-muted">{{ $membership->end_date ?? '' }}</small>
                                                            </td>
                                                            <td>
                                                                <div class="d-flex align-items-center gap-2">
                                                                    <div class="rounded-circle bg-info"
                                                                        style="width: 8px; height: 8px;"></div>
                                                                    <span class="text-muted">
                                                                        @if ($membership->status == 'active')
                                                                            نشط
                                                                        @else
                                                                            غير نشط
                                                                        @endif
                                                                    </span>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="btn-group">
                                                                    <div class="dropdown">
                                                                        <button
                                                                            class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1 btn-sm"
                                                                            type="button" id="dropdownMenuButton303"
                                                                            data-toggle="dropdown" aria-haspopup="true"
                                                                            aria-expanded="false"></button>
                                                                        <div class="dropdown-menu"
                                                                            aria-labelledby="dropdownMenuButton303">
                                                                            <li>
                                                                                <a class="dropdown-item"
                                                                                    href="{{ route('Memberships.show', $membership->id) }}">
                                                                                    <i
                                                                                        class="fa fa-eye me-2 text-primary"></i>عرض
                                                                                </a>
                                                                            </li>
                                                                            <li>
                                                                                <a class="dropdown-item"
                                                                                    href="{{ route('Memberships.edit', $membership->id) }}">
                                                                                    <i
                                                                                        class="fa fa-edit me-2 text-success"></i>تعديل
                                                                                </a>
                                                                            </li>
                                                                            <li>
                                                                                <a class="dropdown-item text-danger"
                                                                                    href="{{ route('Memberships.delete', $membership->id) }}">
                                                                                    <i class="fa fa-trash me-2"></i>حذف
                                                                                </a>
                                                                            </li>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- تبويب الحجوزات / الخدمات -->
                                <div class="tab-pane" id="service" aria-labelledby="service-tab" role="tabpanel">
                                    <div class="card">
                                        <div class="card-body">
                                            @foreach ($bookings as $booking)
                                                <div class="row">
                                                    <div class="col-auto">
                                                        <!-- صورة افتراضية -->
                                                        <div
                                                            style="width: 50px; height: 50px; background-color: #f0f0f0; border-radius: 5px;">
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <h6>بيانات العميل</h6>
                                                        <p class="mb-1">{{ $booking->client->first_name ?? '' }}</p>
                                                        <p class="mb-1">الخدمة :{{ $booking->product->name ?? '' }}</p>
                                                    </div>
                                                    <div class="col-auto text-end">
                                                        <p class="mb-1">الوقت من {{ $booking->start_time ?? 0 }} الى
                                                            {{ $booking->end_time ?? 0 }}</p>
                                                        <p class="text-muted small mb-0">16:45:00</p>

                                                        @if ($booking->status == 'confirm')
                                                            <span class="badge bg-warning text-dark">مؤكد</span>
                                                        @elseif ($booking->status == 'review')
                                                            <span class="badge bg-warning text-dark">تحت المراجعة</span>
                                                        @elseif ($booking->status == 'bill')
                                                            <span class="badge bg-warning text-dark">حولت للفاتورة</span>
                                                        @elseif ($booking->status == 'cancel')
                                                            <span class="badge bg-warning text-dark">تم الالغاء</span>
                                                        @else
                                                            <span class="badge bg-warning text-dark">تم</span>
                                                        @endif

                                                        <a href="{{ route('Reservations.show', $booking->id) }}"
                                                            class="badge bg-danger text-dark">عرض</a>
                                                        <a href="{{ route('Reservations.edit', $booking->id) }}"
                                                            class="btn btn-sm btn-primary">
                                                            <i class="fa fa-edit"></i> تعديل
                                                        </a>
                                                    </div>
                                                </div>

                                                <!-- Horizontal line after each customer's data -->
                                                <hr>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> --}}
