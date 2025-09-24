@extends('layouts.blank')

@section('content')

    <style>
        .client-item {
            transition: background-color 0.3s ease;
            cursor: pointer;
            border-bottom: 1px solid #dee2e6;
        }

        .client-item:hover {
            background-color: #f8f9fa;
        }

        .client-item.selected {
            background-color: #cce5ff;
            border-left: 4px solid #007bff;
        }

        .clients-list {
            height: calc(100vh - 200px);
            overflow-y: auto;
        }

        .search-bar {
            position: sticky;
            top: 0;
            background: white;
            z-index: 100;
            padding: 1rem;
            border-bottom: 1px solid #dee2e6;
        }

        .loading {
            text-align: center;
            padding: 1rem;
        }

        .no-results {
            text-align: center;
            padding: 2rem;
            color: #6c757d;
        }

        .tab-content {
            padding: 20px;
            background: white;
            border: 1px solid #dee2e6;
            border-top: none;
            border-radius: 0 0 5px 5px;
        }

        .nav-tabs .nav-link.active {
            font-weight: bold;
            border-bottom: 3px solid #007bff;
        }

        /* Timeline styling */
        .timeline {
            position: relative;
            padding: 20px 0;
        }

        .timeline::before {
            content: '';
            position: absolute;
            width: 2px;
            height: 100%;
            background-color: #e9ecef;
            left: 50%;
            top: 0;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 20px;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            width: 12px;
            height: 12px;
            background-color: #007bff;
            border-radius: 50%;
            left: 50%;
            top: 0;
            transform: translateX(-50%);
            z-index: 1;
        }

        .timeline-content {
            background: white;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            width: 90%;
            margin-left: 5%;
        }
    </style>

    <div class="container-fluid">
        <div class="row g-0">
            <!-- القائمة الجانبية للعملاء -->
            <div class="col-md-4 border-end vh-100 overflow-hidden">
                <div class="d-flex flex-column h-100">
                    <!-- شريط البحث -->
                    <div class="search-bar p-3 border-bottom bg-white sticky-top">
                        <div class="d-flex gap-2 mb-2">
                            <button class="btn btn-light border" type="button">
                                <i class="fas fa-bars"></i>
                            </button>
                            <div class="input-group">
                                <input type="text" class="form-control border-end-0" id="searchInput"
                                    placeholder="البحث عن عميل بالاسم او البريد او الكود او رقم الهاتف...">
                                <span class="input-group-text bg-white border-start-0">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                            </div>
                            <button class="btn btn-primary" type="button" data-toggle="modal"
                                data-target="#addCustomerModal">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>


                    <!-- قائمة المجموعات والعملاء -->
                    <div class="clients-list overflow-auto">
                        <div id="clientsContainer">
                            @if ($clientGroups->count() > 0)
                                <div class="accordion" id="clientGroupsAccordion">
                                    @foreach ($clientGroups as $group)
                                        <div class="accordion-item border-0">
                                            <h2 class="accordion-header" id="heading{{ $group->id }}">
                                                <button class="accordion-button bg-light py-2 collapsed" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#collapse{{ $group->id }}"
                                                    aria-expanded="false" aria-controls="collapse{{ $group->id }}">
                                                    <span class="fw-bold">{{ $group->name }}</span>
                                                    <span class="badge bg-secondary ms-2">
                                                        {{ $group->neighborhoods->pluck('client')->filter()->count() }}
                                                    </span>
                                                </button>
                                            </h2>
                                            <div id="collapse{{ $group->id }}" class="accordion-collapse collapse"
                                                aria-labelledby="heading{{ $group->id }}"
                                                data-bs-parent="#clientGroupsAccordion">
                                                <div class="accordion-body p-0">
                                                    @php
                                                        $clients = $group->neighborhoods->pluck('client')->filter();
                                                    @endphp

                                                    @if ($clients->count() > 0)
                                                        @foreach ($clients as $client)
                                                            <div class="client-item p-3 border-bottom"
                                                                data-client-id="{{ $client->id }}"
                                                                onclick="selectClient({{ $client->id }})">
                                                                <div
                                                                    class="d-flex justify-content-between align-items-center">
                                                                    <div>
                                                                        <div class="d-flex align-items-center gap-2">
                                                                            <span class="badge bg-success country-badge">
                                                                                {{ $client->country_code ?? 'SA' }}
                                                                            </span>
                                                                            <span
                                                                                class="client-number text-muted">#{{ $client->code }}</span>
                                                                            <span
                                                                                class="client-name text-primary fw-medium">{{ $client->trade_name }}</span>
                                                                        </div>
                                                                        <div class="client-info small text-muted mt-1">
                                                                            <i class="far fa-clock me-1"></i>
                                                                            {{ $client->created_at->format('H:i') }} |
                                                                            {{ $client->created_at->format('M d,Y') }}
                                                                        </div>
                                                                        @if ($client->phone)
                                                                            <div
                                                                                class="client-contact small text-muted mt-1">
                                                                                <i class="fas fa-phone-alt me-1"></i>
                                                                                {{ $client->phone }}
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                    @php
                                                                        // نحصل آخر ملاحظة فيها الحالة الأصلية من الموظف الحالي بنوع "إبلاغ المشرف"
                                                                        $lastNote = $client
                                                                            ->appointmentNotes()
                                                                            ->where('employee_id', auth()->id())
                                                                            ->where('process', 'إبلاغ المشرف')
                                                                            ->whereNotNull('employee_view_status')
                                                                            ->latest()
                                                                            ->first();

                                                                        // الحالة الأساسية الافتراضية
                                                                        $statusToShow = $client->status_client;

                                                                        // لو الموظف هو اللي أبلغ المشرف، نعرض له الحالة الأصلية
                                                                        if (
                                                                            auth()->user()->role === 'employee' &&
                                                                            $lastNote &&
                                                                            $lastNote->employee_id == auth()->id()
                                                                        ) {
                                                                            $statusToShow = $statuses->find(
                                                                                $lastNote->employee_view_status,
                                                                            );
                                                                        }
                                                                    @endphp

                                                                    <div>
                                                                        @if ($statusToShow)
                                                                            <span class="badge rounded-pill"
                                                                                style="background-color: {{ $statusToShow->color }}; font-size: 11px;">
                                                                                <i class="fas fa-circle me-1"></i>
                                                                                {{ $statusToShow->name }}
                                                                            </span>
                                                                        @else
                                                                            <span class="badge rounded-pill bg-secondary"
                                                                                style="font-size: 11px;">
                                                                                <i class="fas fa-question-circle me-1"></i>
                                                                                غير محدد
                                                                            </span>
                                                                        @endif
                                                                    </div>

                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <div class="p-3 text-center text-muted">
                                                            <i class="fas fa-users-slash"></i>
                                                            لا يوجد عملاء في هذه المجموعة
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="no-results">
                                    <i class="fas fa-search"></i>
                                    <p>لا توجد مجموعات عملاء</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>


            <!-- تفاصيل العميل -->
            <div class="col-md-8 bg-light">
                <div class="client-details h-100">
                    <div class="card border-0 h-100">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3 border-0">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-primary btn-sm" id="prevButton"
                                    onclick="loadPreviousClient()">
                                    <i class="fas fa-chevron-right"></i> السابق
                                </button>
                                <button type="button" class="btn btn-outline-primary btn-sm" id="nextButton"
                                    onclick="loadNextClient()">
                                    التالي <i class="fas fa-chevron-left"></i>
                                </button>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-secondary btn-sm"
                                    onclick="printClientDetails()">
                                    <i class="fas fa-print"></i>
                                </button>
                                <a href="#" id="editClientButton" class="btn btn-outline-primary btn-sm disabled">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </div>

                        <div class="card-body">
                            <!-- معلومات العميل الأساسية -->
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <h5 id="clientNameDisplay">--</h5>
                                    <p class="text-muted mb-1">الاسم التجاري</p>
                                </div>
                                <div class="col-md-4">
                                    <h5 id="clientPhoneDisplay">--</h5>
                                    <p class="text-muted mb-1">رقم الهاتف</p>
                                </div>
                                <div class="col-md-4">
                                    <h5 id="clientEmailDisplay">--</h5>
                                    <p class="text-muted mb-1">البريد الإلكتروني</p>
                                </div>
                            </div>

                            <!-- علامات التبويب -->
                            <ul class="nav nav-tabs mb-3" id="clientTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="invoices-tab" data-bs-toggle="tab"
                                        data-bs-target="#invoices" type="button" role="tab"
                                        aria-controls="invoices" aria-selected="true">
                                        الفواتير
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="notes-tab" data-bs-toggle="tab" data-bs-target="#notes"
                                        type="button" role="tab" aria-controls="notes" aria-selected="false">
                                        الملاحظات
                                    </button>
                                </li>
                            </ul>

                            <!-- محتوى التبويبات -->
                            <div class="tab-content" id="clientTabsContent">
                                <!-- تبويب الفواتير -->
                                <div class="tab-pane fade show active" id="invoices" role="tabpanel">
                                    <div class="card">
                                        <div class="card-header d-flex justify-content-between align-items-center p-3">
                                            <div class="d-flex gap-2 flex-wrap">
                                                <button class="btn btn-sm btn-outline-primary"
                                                    onclick="filterInvoices('all')">
                                                    <i class="fas fa-list me-1"></i> الكل
                                                </button>
                                                <button class="btn btn-sm btn-outline-warning"
                                                    onclick="filterInvoices('late')">
                                                    <i class="fas fa-clock me-1"></i> متأخر
                                                </button>
                                                <button class="btn btn-sm btn-outline-info"
                                                    onclick="filterInvoices('due')">
                                                    <i class="fas fa-calendar-day me-1"></i> مستحقة الدفع
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger"
                                                    onclick="filterInvoices('unpaid')">
                                                    <i class="fas fa-times-circle me-1"></i> غير مدفوع
                                                </button>
                                                <button class="btn btn-sm btn-outline-secondary"
                                                    onclick="filterInvoices('draft')">
                                                    <i class="fas fa-file-alt me-1"></i> مسودة
                                                </button>
                                                <button class="btn btn-sm btn-outline-success"
                                                    onclick="filterInvoices('overpaid')">
                                                    <i class="fas fa-check-double me-1"></i> مدفوع بزيادة
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Invoice Table -->
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

                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                        @if ($invoices->isEmpty())
                                            <div class="alert alert-warning m-3" role="alert">
                                                <p class="mb-0"><i class="fas fa-exclamation-circle me-2"></i>لا توجد
                                                    فواتير</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- تبويب الملاحظات -->
                                <div class="tab-pane fade" id="notes" role="tabpanel">
                                    @if ($notes->count() > 0)
                                        <div class="timeline">
                                            @foreach ($notes as $note)
                                                <div class="timeline-item">
                                                    <div class="timeline-content">
                                                        <div
                                                            class="d-flex justify-content-between align-items-center mb-2">
                                                            <div>
                                                                <span class="text-muted small">
                                                                    {{ $note->created_at->translatedFormat('d/m/Y h:i A') }}
                                                                </span>
                                                                <span class="text-muted small ms-2">
                                                                    بواسطة: {{ $note->user->name ?? 'غير معروف' }}
                                                                </span>
                                                            </div>
                                                            <span class="badge bg-{{ $note->type_color ?? 'primary' }}">
                                                                {{ $note->type_text ?? 'ملاحظة' }}
                                                            </span>
                                                        </div>
                                                        <p class="mb-1">{{ $note->note }}</p>

                                                       
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center py-4">
                                            <i class="fas fa-sticky-note text-muted fa-3x mb-3"></i>
                                            <p class="text-muted">لا توجد ملاحظات لهذا العميل</p>
                                        </div>
                                    @endif
                                </div>

                            </div>
                        </div>

                        <!-- تبويب الملاحظات -->
                        <div class="tab-pane fade" id="notes" role="tabpanel">

                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>

    <script>
        let currentClientId = null;
        let clientsData = @json(
            $clientGroups->flatMap(function ($group) {
                return $group->neighborhoods->flatMap(function ($neighborhood) {
                    return $neighborhood->client ? [$neighborhood->client] : [];
                });
            }));

        function selectClient(clientId) {
            // تحديث العناصر المحددة في الواجهة
            document.querySelectorAll('.client-item').forEach(item => {
                item.classList.remove('selected');
                if (item.dataset.clientId == clientId) {
                    item.classList.add('selected');
                }
            });

            currentClientId = clientId;

            // عرض مؤشر التحميل
            showLoadingIndicator();

            // جلب بيانات العميل من السيرفر
            fetch(`/client-data/${clientId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    updateClientBasicInfo(data.client);
                    updateInvoicesTab(data.invoices);
                    updateNotesTab(data.notes);
                    updateNavigationButtons();
                    hideLoadingIndicator();
                })
                .catch(error => {
                    console.error('Error:', error);
                    hideLoadingIndicator();
                    alert('حدث خطأ في تحميل بيانات العميل');
                });
        }

        function updateClientBasicInfo(client) {
            if (!client) return;

            const nameEl = document.getElementById('clientNameDisplay');
            const phoneEl = document.getElementById('clientPhoneDisplay');
            const emailEl = document.getElementById('clientEmailDisplay');
            const editBtn = document.getElementById('editClientButton');

            if (nameEl) nameEl.textContent = client.trade_name || client.first_name + ' ' + client.last_name || 'بدون اسم';
            if (phoneEl) phoneEl.textContent = client.phone || 'غير متوفر';
            if (emailEl) emailEl.textContent = client.email || 'غير متوفر';

            if (editBtn) {
                editBtn.href = `/clients/${client.id}/edit`;
                editBtn.classList.remove('disabled');
            }
        }

       function updateInvoicesTab(invoices) {
    const tbody = document.getElementById('invoiceTableBody');

    if (!tbody) {
        console.error('Element #invoiceTableBody not found');
        return;
    }

    tbody.innerHTML = '';

    if (!invoices || invoices.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-4">
                    <i class="fas fa-file-invoice text-muted fa-2x mb-2"></i>
                    <p class="text-muted">لا توجد فواتير لهذا العميل</p>
                </td>
            </tr>
        `;
        return;
    }

    invoices.forEach(invoice => {
        // معالجة البيانات الناقصة
        const statusClass = getStatusClass(invoice.payment_status);
        const statusText = getStatusText(invoice.payment_status);
        const createdAt = invoice.created_at ? new Date(invoice.created_at).toLocaleDateString('ar-SA') : 'تاريخ غير معروف';
        const clientName = invoice.client?.trade_name || invoice.client?.name || 'عميل غير معروف';
        const taxNumber = invoice.client?.tax_number || '';
        const creatorName = invoice.createdByUser?.name || 'غير محدد';
        const grandTotal = invoice.grand_total || invoice.total || 0;
        const dueValue = invoice.due_value || 0;

        tbody.innerHTML += `
            <tr class="align-middle invoice-row"
                onclick="window.location.href='/invoices/${invoice.id}'"
                style="cursor: pointer;"
                data-status="${invoice.payment_status}">
                <td onclick="event.stopPropagation()">
                    <input type="checkbox" class="invoice-checkbox" name="invoices[]" value="${invoice.id}">
                </td>
                <td class="text-center border-start">
                    <span class="invoice-number">#${invoice.id}</span>
                </td>
                <td>
                    <div class="client-info">
                        <div class="client-name mb-2">
                            <i class="fas fa-user text-primary me-1"></i>
                            <strong>${clientName}</strong>
                        </div>
                        ${taxNumber ? `
                            <div class="tax-info mb-1">
                                <i class="fas fa-hashtag text-muted me-1"></i>
                                <span class="text-muted small">الرقم الضريبي: ${taxNumber}</span>
                            </div>
                        ` : ''}
                    </div>
                </td>
                <td>
                    <div class="date-info mb-2">
                        <i class="fas fa-calendar text-info me-1"></i>
                        ${createdAt}
                    </div>
                    <div class="creator-info">
                        <i class="fas fa-user text-muted me-1"></i>
                        <span class="text-muted small">بواسطة: ${creatorName}</span>
                    </div>
                </td>
                <td>
                    <div class="d-flex flex-column gap-2">
                        <span class="badge bg-secondary text-white">
                            <i class="fas fa-file-invoice me-1"></i>فاتورة
                        </span>
                    </div>
                </td>
                <td>
                    <div class="text-center">
                        <span class="badge bg-${statusClass} text-white status-badge">${statusText}</span>
                    </div>
                    <div class="amount-info text-center mb-2">
                        <h6 class="amount mb-1">
                            ${formatNumber(grandTotal)}
                            <small class="currency">ر.س</small>
                        </h6>
                        ${dueValue > 0 ? `
                            <div class="due-amount">
                                <small class="text-danger">المبلغ المستحق: ${formatNumber(dueValue)} ر.س</small>
                            </div>
                        ` : ''}
                    </div>
                </td>
                <td>
                    <div class="dropdown" onclick="event.stopPropagation()">
                        <button class="btn btn-sm bg-gradient-info fa fa-ellipsis-v"
                                type="button"
                                data-bs-toggle="dropdown">
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="/invoices/${invoice.id}/edit">
                                <i class="fa fa-edit me-2 text-success"></i>تعديل
                            </a>
                            <a class="dropdown-item" href="/invoices/${invoice.id}">
                                <i class="fa fa-eye me-2 text-primary"></i>عرض
                            </a>
                        </div>
                    </div>
                </td>
            </tr>
        `;
    });
}

  function updateNotesTab(notes) {
    const timeline = document.querySelector('#notes .timeline');

    if (!timeline) {
        console.error('Element #notes .timeline not found');
        return;
    }

    timeline.innerHTML = '';

    if (!notes || notes.length === 0) {
        timeline.innerHTML = `
            <div class="text-center py-4">
                <i class="fas fa-sticky-note text-muted fa-3x mb-3"></i>
                <p class="text-muted">لا توجد ملاحظات لهذا العميل</p>
            </div>
        `;
        return;
    }

    notes.forEach(note => {
        // التأكد من وجود البيانات الأساسية
        const createdAt = note.created_at ? new Date(note.created_at).toLocaleString('ar-SA', {
            day: 'numeric',
            month: 'numeric',
            year: 'numeric',
            hour: 'numeric',
            minute: 'numeric',
            hour12: true
        }) : 'تاريخ غير معروف';

        const userName = note.user?.name || 'غير معروف';
        const noteContent = note.note || 'لا يوجد محتوى';
        const typeText = note.type_text || 'ملاحظة';
        const typeColor = note.type_color || 'primary';

        timeline.innerHTML += `
        <div class="timeline-item">
            <div class="timeline-content">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <span class="text-muted small">${createdAt}</span>
                        <span class="text-muted small ms-2">
                            بواسطة: ${userName}
                        </span>
                    </div>
                    <span class="badge bg-${typeColor}">
                        ${typeText}
                    </span>
                </div>
                <p class="mb-1">${noteContent}</p>
                ${note.additional_data ? `
                    <div class="mt-2 p-2 bg-light rounded">
                        <small class="text-muted">تفاصيل إضافية:</small>
                        <div class="d-flex flex-wrap">
                            ${Object.entries(note.additional_data).map(([key, value]) => `
                            <span class="me-3"><strong>${key}:</strong> ${value || '---'}</span>
                        `).join('')}
                        </div>
                    </div>
                ` : ''}
            </div>
        </div>
        `;
    });
}



        // Helper functions
        function getStatusClass(status) {
            switch (status) {
                case 1:
                    return 'success';
                case 2:
                    return 'info';
                case 3:
                    return 'danger';
                case 4:
                    return 'secondary';
                default:
                    return 'dark';
            }
        }

        function getStatusText(status) {
            switch (status) {
                case 1:
                    return 'مدفوعة بالكامل';
                case 2:
                    return 'مدفوعة جزئياً';
                case 3:
                    return 'غير مدفوعة';
                case 4:
                    return 'مستلمة';
                default:
                    return 'غير معروفة';
            }
        }

        function formatNumber(number) {
            return new Intl.NumberFormat('ar-SA', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(number);
        }

        function showLoadingIndicator() {
            const tbody = document.getElementById('invoiceTableBody');
            if (tbody) {
                tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">جاري التحميل...</span>
                    </div>
                    <p class="text-muted mt-2">جاري تحميل البيانات...</p>
                </td>
            </tr>
        `;
            }
        }

        function hideLoadingIndicator() {
            // يتم إخفاء مؤشر التحميل تلقائياً عند تحديث المحتوى
        }

        function updateNavigationButtons() {
            const prevBtn = document.getElementById('prevButton');
            const nextBtn = document.getElementById('nextButton');

            if (!currentClientId || clientsData.length <= 1) {
                if (prevBtn) prevBtn.disabled = true;
                if (nextBtn) nextBtn.disabled = true;
                return;
            }

            const currentIndex = clientsData.findIndex(client => client.id == currentClientId);

            if (prevBtn) prevBtn.disabled = currentIndex <= 0;
            if (nextBtn) nextBtn.disabled = currentIndex >= clientsData.length - 1;
        }

        function loadPreviousClient() {
            if (!currentClientId) return;

            const currentIndex = clientsData.findIndex(client => client.id == currentClientId);
            if (currentIndex > 0) {
                selectClient(clientsData[currentIndex - 1].id);
            }
        }

        function loadNextClient() {
            if (!currentClientId) return;

            const currentIndex = clientsData.findIndex(client => client.id == currentClientId);
            if (currentIndex < clientsData.length - 1) {
                selectClient(clientsData[currentIndex + 1].id);
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            if (clientsData.length > 0) {
                selectClient(clientsData[0].id);
            }
        });
    </script>

    <script>
        // Initialize search functionality
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('clientSearch');

            if (searchInput) {
                searchInput.addEventListener('input', function(e) {
                    const searchTerm = e.target.value.toLowerCase();
                    filterClients(searchTerm);
                });
            }
        });

        async function filterClients(searchTerm) {
            if (!searchTerm) {
                // If search term is empty, show all clients
                document.querySelectorAll('.client-item').forEach(item => item.style.display = '');
                const noResults = document.querySelector('.no-results-message');
                if (noResults) noResults.remove();
                return;
            }

            try {
                // Show loading indicator
                const loadingIndicator = document.querySelector('.loading-indicator');
                if (!loadingIndicator) {
                    const indicator = document.createElement('div');
                    indicator.className = 'loading-indicator text-center py-3';
                    indicator.innerHTML = `
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">جاري البحث...</span>
                        </div>
                        <p class="text-muted mt-2">جاري البحث...</p>
                    `;
                    document.querySelector('.clients-list').appendChild(indicator);
                }

                // Fetch search results from server
                const response = await fetch(`/clients/search?query=${searchTerm}`);
                const data = await response.json();

                // Hide loading indicator
                const loading = document.querySelector('.loading-indicator');
                if (loading) loading.remove();

                // Update client items
                const clientItems = document.querySelectorAll('.client-item');
                clientItems.forEach(item => {
                    const clientId = item.dataset.clientId;
                    const client = data.data.find(c => c.id == clientId);
                    item.style.display = client ? '' : 'none';
                });

                // Show no results message if needed
                const visibleClients = document.querySelectorAll('.client-item:not([style*="display: none"])');
                if (visibleClients.length === 0) {
                    const noResults = document.querySelector('.no-results-message');
                    if (!noResults) {
                        const message = document.createElement('div');
                        message.className = 'no-results-message text-center py-3';
                        message.innerHTML = `
                            <i class="fas fa-search text-muted fa-2x mb-2"></i>
                            <p class="text-muted">لم يتم العثور على عملاء تطابق البحث</p>
                        `;
                        document.querySelector('.clients-list').appendChild(message);
                    }
                } else {
                    const noResults = document.querySelector('.no-results-message');
                    if (noResults) noResults.remove();
                }

            } catch (error) {
                console.error('Error searching:', error);
                alert('حدث خطأ أثناء البحث');
            }
        }

        // Update the selectClient function to handle search filtering
        function selectClient(clientId) {
            // تحديث العناصر المحددة في الواجهة
            document.querySelectorAll('.client-item').forEach(item => {
                item.classList.remove('selected');
                if (item.dataset.clientId == clientId) {
                    item.classList.add('selected');
                }
            });

            currentClientId = clientId;

            // عرض مؤشر التحميل
            showLoadingIndicator();

            // جلب بيانات العميل من السيرفر
            fetch(`/client-data/${clientId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    updateClientBasicInfo(data.client);
                    updateInvoicesTab(data.invoices);
                    updateNotesTab(data.notes);
                    updateNavigationButtons();
                    hideLoadingIndicator();
                })
                .catch(error => {
                    console.error('Error:', error);
                    hideLoadingIndicator();
                    alert('حدث خطأ في تحميل بيانات العميل');
                });
        }


        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');

            if (searchInput) {
                searchInput.addEventListener('input', function(e) {
                    const searchTerm = e.target.value.toLowerCase();
                    filterClients(searchTerm);
                });
            }
        });

        function filterClients(searchTerm) {
            const clientItems = document.querySelectorAll('.client-item');

            clientItems.forEach(item => {
                const clientName = item.querySelector('.client-name')?.textContent?.toLowerCase() || '';
                const clientCode = item.querySelector('.client-number')?.textContent?.toLowerCase() || '';
                const clientPhone = item.querySelector('.client-contact')?.textContent?.toLowerCase() || '';
                const clientEmail = ''; // يمكنك إضافة حقل البريد الإلكتروني إذا كان موجودًا

                const matchesSearch =
                    clientName.includes(searchTerm) ||
                    clientCode.includes(searchTerm) ||
                    clientPhone.includes(searchTerm) ||
                    clientEmail.includes(searchTerm);

                item.style.display = matchesSearch || searchTerm === '' ? '' : 'none';
            });

            // إظهار رسالة إذا لم توجد نتائج
            const visibleClients = document.querySelectorAll('.client-item[style*="display: none"]');
            const noResultsMessage = document.querySelector('.no-results-message');

            if (visibleClients.length === clientItems.length && searchTerm !== '') {
                if (!noResultsMessage) {
                    const message = document.createElement('div');
                    message.className = 'no-results-message text-center py-4';
                    message.innerHTML = `
                <i class="fas fa-search text-muted fa-2x mb-2"></i>
                <p class="text-muted">لم يتم العثور على عملاء</p>
            `;
                    document.querySelector('.clients-list').appendChild(message);
                }
            } else {
                if (noResultsMessage) {
                    noResultsMessage.remove();
                }
            }
        }
    </script>
@endsection
