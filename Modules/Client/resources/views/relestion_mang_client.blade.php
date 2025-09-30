@extends('layouts.blank')

@section('content')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/crm-client.css') }}">
    <div class="container-fluid p-0">
        <div class="row g-0">
            <!-- القائمة الجانبية للعملاء -->
            <div class="col-md-4">
                <div class="d-flex flex-column h-100">
                    <!-- شريط البحث -->
                    <div class="search-bar">
                        <div class="d-flex gap-1 mb-1">
                            <button class="btn btn-light border btn-sm" type="button">
                                <i class="fas fa-bars"></i>
                            </button>
                            <div class="input-group">
                                <input type="text" class="form-control border-end-0" id="searchInput"
                                    placeholder="البحث عن عميل بالاسم او البريد او الكود او رقم الهاتف...">
                                <span class="input-group-text bg-white border-start-0">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                            </div>
                            <button class="btn btn-primary btn-sm" type="button" data-toggle="modal"
                                data-target="#addCustomerModal">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>

                    <!-- قائمة المجموعات والعملاء -->
                    <div class="clients-list">
                        @if ($clientGroups->count() > 0)
                            <div class="accordion" id="clientGroupsAccordion">
                                @foreach ($clientGroups as $group)
                                    <div class="accordion-item border-0">
                                        <h2 class="accordion-header" id="heading{{ $group->id }}">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#collapse{{ $group->id }}"
                                                aria-expanded="false" aria-controls="collapse{{ $group->id }}">
                                                <span>{{ $group->name }}</span>
                                                <span class="badge bg-secondary ms-1">
                                                    {{ $group->neighborhoods->pluck('client')->filter()->count() }}
                                                </span>
                                            </button>
                                        </h2>
                                        <div id="collapse{{ $group->id }}" class="accordion-collapse collapse"
                                            aria-labelledby="heading{{ $group->id }}"
                                            data-bs-parent="#clientGroupsAccordion">
                                            <div class="accordion-body p-0">
                                                @php
                                                    $groupClients = $group->neighborhoods->pluck('client')->filter();
                                                @endphp

                                                @if ($groupClients->count() > 0)
                                                    @foreach ($groupClients as $client)
                                                        <div class="client-item p-3 border-bottom"
                                                            data-client-id="{{ $client->id }}"
                                                            onclick="selectClient({{ $client->id }})">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <div class="d-flex align-items-center gap-2">
                                                                        <span class="badge country-badge">
                                                                            {{ $client->country_code ?? 'SA' }}
                                                                        </span>
                                                                        <span
                                                                            class="client-number text-muted small">#{{ $client->code }}</span>
                                                                        <span
                                                                            class="client-name text-primary fw-medium">{{ $client->trade_name }}</span>
                                                                    </div>
                                                                    <div class="client-info small text-muted mt-1">
                                                                        <i class="far fa-clock me-1"></i>
                                                                        {{ $client->created_at->format('H:i') }} |
                                                                        {{ $client->created_at->format('M d,Y') }}
                                                                    </div>
                                                                    @if ($client->phone)
                                                                        <div class="client-contact small text-muted mt-1">
                                                                            <i class="fas fa-phone-alt me-1"></i>
                                                                            {{ $client->phone }}
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="p-3 text-center text-muted">
                                                        <i class="fas fa-users-slash"></i>
                                                        <p class="mb-0 small">لا يوجد عملاء في هذه المجموعة</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="empty-state">
                                <i class="fas fa-search"></i>
                                <h6>لا توجد مجموعات عملاء</h6>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- تفاصيل العميل -->
            <div class="col-md-8">
                <!-- شريط التحكم -->
                <div class="control-header d-flex justify-content-between align-items-center">
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-primary" id="prevButton">
                            <i class="fas fa-chevron-right"></i> السابق
                        </button>
                        <button type="button" class="btn btn-outline-primary" id="nextButton">
                            التالي <i class="fas fa-chevron-left"></i>
                        </button>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-print"></i>
                        </button>
                        <a href="#" id="editClientButton" class="btn btn-outline-primary btn-sm disabled">
                            <i class="fas fa-edit"></i>
                        </a>
                    </div>
                </div>

                <div class="p-3">
                    <!-- رسالة افتراضية -->
                    <div id="defaultMessage" class="empty-state">
                        <i class="fas fa-user-circle"></i>
                        <h5 class="mb-2">اختر عميلاً لعرض تفاصيله</h5>
                        <p class="mb-0">انقر على أي عميل من القائمة الجانبية لعرض معلوماته والملاحظات الخاصة به</p>
                    </div>

                    <!-- تفاصيل العملاء -->
                    @foreach ($clients as $clientData)
                        <div class="client-details-section" id="client-{{ $clientData['id'] }}" style="display: none;">
                            <!-- معلومات العميل الأساسية -->
                            <div class="client-basic-info">
                                <div class="row">
                                    <div class="col-md-3 info-item">
                                        <div class="info-value">{{ $clientData['name'] ?? '--' }}</div>
                                        <div class="info-label">الاسم التجاري</div>
                                    </div>
                                    <div class="col-md-3 info-item">
                                        <div class="info-value">{{ $clientData['phone'] ?? '--' }}</div>
                                        <div class="info-label">رقم الهاتف</div>
                                    </div>
                                    <div class="col-md-3 info-item">
                                        <div class="info-value"
                                            style="color: {{ $clientData['balance'] >= 0 ? 'var(--success)' : 'var(--danger)' }}">
                                            {{ number_format($clientData['balance'], 2) }} ر.س
                                        </div>
                                        <div class="info-label">الرصيد</div>
                                    </div>
                                    <div class="col-md-3 info-item">
                                        <div class="info-value">{{ count($clientData['invoices']) }}</div>
                                        <div class="info-label">إجمالي الفواتير</div>
                                    </div>
                                </div>
                            </div>

                            <!-- علامات التبويب -->
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" data-bs-toggle="tab"
                                        data-bs-target="#invoices-{{ $clientData['id'] }}" type="button"
                                        role="tab">
                                        الفواتير
                                        <span class="badge bg-primary ms-1">{{ count($clientData['invoices']) }}</span>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="tab"
                                        data-bs-target="#notes-{{ $clientData['id'] }}" type="button" role="tab">
                                        الملاحظات
                                        <span
                                            class="badge bg-secondary ms-1">{{ count($clientData['clientRelations']) }}</span>
                                    </button>
                                </li>
                            </ul>

                            <!-- محتوى التبويبات -->
                            <div class="tab-content">
                                <!-- تبويب الفواتير -->
                                <div class="tab-pane fade show active" id="invoices-{{ $clientData['id'] }}"
                                    role="tabpanel">
                                    @if (count($clientData['invoices']) > 0)
                                        <!-- إحصائيات سريعة -->
                                        <div class="stats-row mb-4">
                                            @php
                                                $totalAmount = collect($clientData['invoices'])->sum('amount');
                                                $totalPaid = collect($clientData['invoices'])->sum('total_payments');
                                                $totalRemaining = $totalAmount - $totalPaid; //collect($clientData['invoices'])->sum('remaining');
                                                $paidInvoices = collect($clientData['invoices'])
                                                    ->where('is_paid', true)
                                                    ->count();
                                            @endphp
                                            <div class="card text-center">
                                                <div class="card-body">
                                                    <h6>إجمالي المبلغ</h6>
                                                    <h4>{{ number_format($totalAmount, 2) }}</h4>
                                                </div>
                                            </div>
                                            <div class="card text-center">
                                                <div class="card-body">
                                                    <h6>المدفوع</h6>
                                                    <h4 style="color: var(--success)">{{ number_format($totalPaid, 2) }}
                                                    </h4>
                                                </div>
                                            </div>
                                            <div class="card text-center">
                                                <div class="card-body">
                                                    <h6>المتبقي</h6>
                                                    <h4 style="color: var(--warning)">
                                                        {{ number_format($totalRemaining, 2) }}</h4>
                                                </div>
                                            </div>
                                            <div class="card text-center">
                                                <div class="card-body">
                                                    <h6>الفواتير المدفوعة</h6>
                                                    <h4>{{ $paidInvoices }}/{{ count($clientData['invoices']) }}</h4>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="table-responsive">
                                            <table class="table table-hover invoice-table">
                                                <thead>
                                                    <tr>
                                                        <th>رقم الفاتورة</th>
                                                        <th>تاريخ الإصدار</th>
                                                        <th>تاريخ الاستحقاق</th>
                                                        <th>المبلغ الإجمالي</th>
                                                        <th>المدفوع</th>
                                                        <th>المتبقي</th>
                                                        <th>الحالة</th>
                                                        <th>الموظف</th>
                                                        <th>التفاصيل</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($clientData['invoices'] as $invoice)
                                                        <tr class="invoice-row">
                                                            <td>
                                                                <strong
                                                                    class="text-primary">#{{ $invoice['number'] }}</strong>
                                                                @if ($invoice['type'])
                                                                    <br><small
                                                                        class="text-muted">{{ $invoice['type'] }}</small>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                {{ $invoice['date'] }}
                                                                <br><small
                                                                    class="text-muted">{{ $invoice['created_at'] }}</small>
                                                            </td>
                                                            <td>
                                                                {{ $invoice['issue_date'] ?? 'غير محدد' }}
                                                                @if ($invoice['payment_terms'])
                                                                    <br><small
                                                                        class="text-muted">{{ $invoice['payment_terms'] }}
                                                                        يوم</small>
                                                                @endif
                                                            </td>
                                                            <td class="amount-cell">
                                                                <strong>{{ number_format($invoice['amount'], 2) }}</strong>
                                                                @if ($invoice['currency'] && $invoice['currency'] != 'SAR')
                                                                    <small
                                                                        class="text-muted">{{ $invoice['currency'] }}</small>
                                                                @endif
                                                                @if ($invoice['discount_amount'] > 0)
                                                                    <br><small class="text-success">خصم:
                                                                        {{ number_format($invoice['discount_amount'], 2) }}</small>
                                                                @endif
                                                            </td>
                                                            <td class="amount-cell text-success">
                                                                {{ number_format($invoice['total_payments'], 2) }}
                                                                @if ($invoice['total_payments'] > 0)
                                                                    <div class="payment-progress">
                                                                        <div class="payment-progress-bar"
                                                                            style="width: {{ min(100, ($invoice['total_payments'] / $invoice['amount']) * 100) }}%">
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            </td>
                                                            <td class="amount-cell text-warning">
                                                                {{ number_format($invoice['amount'] - $invoice['total_payments'], 2) }}
                                                            </td>
                                                            <td>
                                                                @php
                                                                    $statusClass = 'secondary';
                                                                    $statusText = 'غير محدد';

                                                                    if ($invoice['is_paid']) {
                                                                        $statusClass = 'success';
                                                                        $statusText = 'مدفوعة بالكامل';
                                                                    } elseif ($invoice['total_payments'] > 0) {
                                                                        $statusClass = 'warning';
                                                                        $statusText = 'مدفوعة جزئياً';
                                                                    } else {
                                                                        $statusClass = 'danger';
                                                                        $statusText = 'غير مدفوعة';
                                                                    }
                                                                @endphp
                                                                <span class="badge bg-{{ $statusClass }} status-badge">
                                                                    {{ $statusText }}
                                                                </span>
                                                                @if ($invoice['paymentMethod'])
                                                                    <br><small
                                                                        class="text-muted">{{ $invoice['paymentMethod'] }}</small>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                {{ $invoice['employee'] }}
                                                                @if ($invoice['treasury'])
                                                                    <br><small
                                                                        class="text-muted">{{ $invoice['treasury'] }}</small>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <button class="btn btn-sm btn-outline-primary"
                                                                    onclick="toggleInvoiceDetails({{ $invoice['id'] }})">
                                                                    <i class="fas fa-eye"></i>
                                                                </button>
                                                                @if ($invoice['reference_number'])
                                                                    <br><small class="text-muted">مرجع:
                                                                        {{ $invoice['reference_number'] }}</small>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <!-- تفاصيل إضافية للفاتورة -->
                                                        <tr id="invoice-details-{{ $invoice['id'] }}"
                                                            style="display: none;">
                                                            <td colspan="9">
                                                                <div class="invoice-details">
                                                                    <div class="row">
                                                                        <div class="col-md-4">
                                                                            <strong>تفاصيل المبالغ:</strong>
                                                                            <ul class="list-unstyled mt-2">
                                                                                <li>المبلغ الفرعي:
                                                                                    {{ number_format($invoice['subtotal'], 2) }}
                                                                                </li>
                                                                                @if ($invoice['tax_total'] > 0)
                                                                                    <li>الضريبة:
                                                                                        {{ number_format($invoice['tax_total'], 2) }}
                                                                                    </li>
                                                                                @endif
                                                                                @if ($invoice['discount_amount'] > 0)
                                                                                    <li class="text-success">الخصم:
                                                                                        {{ number_format($invoice['discount_amount'], 2) }}
                                                                                    </li>
                                                                                @endif
                                                                            </ul>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <strong>معلومات إضافية:</strong>
                                                                            <ul class="list-unstyled mt-2">
                                                                                <li>عدد الأصناف:
                                                                                    {{ $invoice['items_count'] }}</li>
                                                                                <li>آخر تحديث: {{ $invoice['updated_at'] }}
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            @if ($invoice['notes'])
                                                                                <strong>ملاحظات:</strong>
                                                                                <p class="mt-2 text-muted">
                                                                                    {{ $invoice['notes'] }}</p>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="empty-state">
                                            <i class="fas fa-file-invoice"></i>
                                            <h6>لا توجد فواتير لهذا العميل</h6>
                                        </div>
                                    @endif
                                </div>

                                <!-- تبويب الملاحظات -->
                                <div class="tab-pane fade" id="notes-{{ $clientData['id'] }}" role="tabpanel">
                                    @if (count($clientData['clientRelations']) > 0)
                                        <div class="timeline">
                                            @foreach ($clientData['clientRelations'] as $relation)
                                                <div class="timeline-item">
                                                    <div class="timeline-content">
                                                        <div
                                                            class="d-flex justify-content-between align-items-center mb-2">
                                                            <div>
                                                                <span class="text-muted small">
                                                                    {{ $relation['created_at'] ? $relation['created_at']->format('d/m/Y h:i A') : 'تاريخ غير معروف' }}
                                                                </span>
                                                                <span class="text-muted small ms-2">
                                                                    بواسطة: {{ $relation['employee'] }}
                                                                </span>
                                                            </div>
                                                            <div>
                                                                @if ($relation['process'])
                                                                    <span class="badge bg-primary me-1">
                                                                        {{ $relation['process'] }}
                                                                    </span>
                                                                @endif
                                                                @if ($relation['status'])
                                                                    <span class="badge bg-secondary">
                                                                        {{ $relation['status'] }}
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        @if ($relation['description'])
                                                            <p class="mb-2">{{ $relation['description'] }}</p>
                                                        @endif

                                                        @if ($relation['date'] || $relation['time'])
                                                            <div class="mb-2">
                                                                <small class="text-muted">
                                                                    <i class="fas fa-calendar me-1"></i>
                                                                    {{ $relation['date'] ?? '' }}
                                                                    @if ($relation['time'])
                                                                        <i class="fas fa-clock me-1 ms-2"></i>
                                                                        {{ $relation['time'] }}
                                                                    @endif
                                                                </small>
                                                            </div>
                                                        @endif

                                                        @if ($relation['site_type'])
                                                            <div class="mb-2">
                                                                <small class="text-muted">
                                                                    <i class="fas fa-map-marker-alt me-1"></i>
                                                                    نوع الموقع: {{ $relation['site_type'] }}
                                                                </small>
                                                            </div>
                                                        @endif

                                                        @if ($relation['additional_data'] && is_array($relation['additional_data']))
                                                            <div class="mt-2 p-2 bg-light rounded">
                                                                <small class="text-muted d-block mb-1">تفاصيل
                                                                    إضافية:</small>
                                                                <div class="d-flex flex-wrap">
                                                                    @foreach ($relation['additional_data'] as $key => $value)
                                                                        <span class="me-3 small">
                                                                            <strong>{{ $key }}:</strong>
                                                                            {{ $value ?? '---' }}
                                                                        </span>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        @endif

                                                        @if ($relation['competitor_documents'])
                                                            <div class="mt-2">
                                                                <small class="text-muted">
                                                                    <i class="fas fa-file-alt me-1"></i>
                                                                    وثائق المنافسين:
                                                                    {{ $relation['competitor_documents'] }}
                                                                </small>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="empty-state">
                                            <i class="fas fa-sticky-note"></i>
                                            <h6>لا توجد ملاحظات لهذا العميل</h6>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <script>
        // دالة لاختيار العميل وعرض تفاصيله
        function selectClient(clientId) {
            // إخفاء جميع تفاصيل العملاء
            document.querySelectorAll('.client-details-section').forEach(section => {
                section.style.display = 'none';
            });

            // إخفاء الرسالة الافتراضية
            const defaultMessage = document.getElementById('defaultMessage');
            if (defaultMessage) {
                defaultMessage.style.display = 'none';
            }

            // عرض تفاصيل العميل المحدد
            const clientSection = document.getElementById('client-' + clientId);
            if (clientSection) {
                clientSection.style.display = 'block';
            }

            // تحديث العناصر المحددة في القائمة الجانبية
            document.querySelectorAll('.client-item').forEach(item => {
                item.classList.remove('selected');
                if (item.dataset.clientId == clientId) {
                    item.classList.add('selected');
                }
            });

            // تحديث زر التعديل
            const editButton = document.getElementById('editClientButton');
            if (editButton) {
                editButton.classList.remove('disabled');
                editButton.href = `/clients/${clientId}/edit`;
            }
        }

        // دالة لإظهار/إخفاء تفاصيل الفاتورة
        function toggleInvoiceDetails(invoiceId) {
            const detailsRow = document.getElementById('invoice-details-' + invoiceId);
            const button = event.target.closest('button');
            const icon = button.querySelector('i');

            if (detailsRow.style.display === 'none') {
                detailsRow.style.display = 'table-row';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                detailsRow.style.display = 'none';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // دالة البحث المحسنة
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase().trim();
                    const clientItems = document.querySelectorAll('.client-item');
                    const accordionItems = document.querySelectorAll('.accordion-item');

                    if (searchTerm === '') {
                        // إذا كان البحث فارغ، أظهر جميع العناصر
                        clientItems.forEach(item => {
                            item.style.display = 'block';
                        });
                        accordionItems.forEach(item => {
                            item.style.display = 'block';
                        });
                        return;
                    }

                    // البحث في العملاء
                    clientItems.forEach(item => {
                        const clientName = item.querySelector('.client-name')?.textContent
                            .toLowerCase() || '';
                        const clientNumber = item.querySelector('.client-number')?.textContent
                            .toLowerCase() || '';
                        const clientContact = item.querySelector('.client-contact')?.textContent
                            .toLowerCase() || '';

                        if (clientName.includes(searchTerm) ||
                            clientNumber.includes(searchTerm) ||
                            clientContact.includes(searchTerm)) {
                            item.style.display = 'block';
                            // إظهار المجموعة التي تحتوي على العميل
                            const accordionItem = item.closest('.accordion-item');
                            if (accordionItem) {
                                accordionItem.style.display = 'block';
                            }
                        } else {
                            item.style.display = 'none';
                        }
                    });

                    // إخفاء المجموعات التي لا تحتوي على نتائج بحث
                    accordionItems.forEach(accordionItem => {
                        const visibleClients = accordionItem.querySelectorAll(
                            '.client-item[style*="block"], .client-item:not([style])');
                        const hasVisibleClients = Array.from(visibleClients).some(client =>
                            client.style.display !== 'none'
                        );

                        if (!hasVisibleClients) {
                            accordionItem.style.display = 'none';
                        }
                    });
                });
            }

            // تحسين أزرار التنقل
            const prevButton = document.getElementById('prevButton');
            const nextButton = document.getElementById('nextButton');

            if (prevButton && nextButton) {
                prevButton.addEventListener('click', function() {
                    const currentSelected = document.querySelector('.client-item.selected');
                    if (currentSelected) {
                        const allVisibleClients = Array.from(document.querySelectorAll('.client-item'))
                            .filter(item => item.style.display !== 'none');
                        const currentIndex = allVisibleClients.indexOf(currentSelected);

                        if (currentIndex > 0) {
                            const prevClient = allVisibleClients[currentIndex - 1];
                            const clientId = prevClient.dataset.clientId;
                            selectClient(clientId);
                        }
                    }
                });

                nextButton.addEventListener('click', function() {
                    const currentSelected = document.querySelector('.client-item.selected');
                    if (currentSelected) {
                        const allVisibleClients = Array.from(document.querySelectorAll('.client-item'))
                            .filter(item => item.style.display !== 'none');
                        const currentIndex = allVisibleClients.indexOf(currentSelected);

                        if (currentIndex < allVisibleClients.length - 1) {
                            const nextClient = allVisibleClients[currentIndex + 1];
                            const clientId = nextClient.dataset.clientId;
                            selectClient(clientId);
                        }
                    }
                });
            }
        });

        // تحسين الأكورديون لفتح المجموعة عند البحث
        function expandGroupWithVisibleClients() {
            const accordionItems = document.querySelectorAll('.accordion-item');

            accordionItems.forEach(accordionItem => {
                const visibleClients = accordionItem.querySelectorAll(
                    '.client-item[style*="block"], .client-item:not([style])');
                const hasVisibleClients = Array.from(visibleClients).some(client =>
                    client.style.display !== 'none'
                );

                if (hasVisibleClients) {
                    const collapseElement = accordionItem.querySelector('.accordion-collapse');
                    const button = accordionItem.querySelector('.accordion-button');

                    if (collapseElement && button) {
                        collapseElement.classList.add('show');
                        button.classList.remove('collapsed');
                        button.setAttribute('aria-expanded', 'true');
                    }
                }
            });
        }
    </script>

@endsection
