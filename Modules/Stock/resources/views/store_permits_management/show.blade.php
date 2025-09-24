@extends('master')

@section('title')
    عرض إذن المخزن
@stop

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('assets/css/logs.css') }}">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/accept.css') }}">
@endsection

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">عرض إذن المخزن</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسية</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('store_permits_management.index') }}">أذونات المخزن</a>
                            </li>
                            <li class="breadcrumb-item active">عرض</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.alerts.error')
    @include('layouts.alerts.success')

    <!-- كارد الحالة الرئيسي -->
    <div class="invoice-header">
        <div class="row align-items-center justify-content-between">
            <!-- جهة اليمين: العنوان والمعلومات -->
            <div class="col-lg-8 text-end">
                <div class="row text-end">
                    <div class="col-md-6">
                        <h3 class="">إذن مخزن #{{ $permit->number }}</h3>
                        <p class="mb-1">
                            <strong>المستودع:</strong> {{ $permit->storeHouse->name ?? 'غير محدد' }}
                        </p>
                        <p class="mb-1">
                            <strong>تاريخ الإذن:</strong> {{ $permit->created_at->format('Y-m-d') }}
                        </p>
                        <p class="mb-1">
                            <strong>الفرع:</strong> {{ $permit->branch->name ?? 'غير محدد' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- جهة اليسار: الحالات + زر الطباعة -->
            <div class="col-lg-4 d-flex flex-column align-items-end gap-2">
                <!-- زر طباعة مصغر ومحاذى لليمين -->
                <button class="btn btn-success btn-bg action-btn" onclick="printPermit()">
                    <i class="fas fa-print ms-1"></i> طباعة الإذن
                </button>

                <!-- الحالات -->
                <div class="d-flex flex-wrap gap-2 justify-content-end">
                    @switch($permit->status)
                        @case('pending')
                            <span class="status-badge bg-warning text-dark">قيد الانتظار</span>
                            @break
                        @case('approved')
                            <span class="status-badge bg-success">مُعتمد</span>
                            @break
                        @case('rejected')
                            <span class="status-badge bg-danger">مرفوض</span>
                            @break
                        @case('completed')
                            <span class="status-badge bg-primary">مُكتمل</span>
                            @break
                        @default
                            <span class="status-badge bg-secondary">غير محدد</span>
                    @endswitch

                    @if($permit->type)
                        @switch($permit->type)
                            @case('add')
                                <span class="status-badge bg-info text-dark">إضافة مخزن</span>
                                @break
                            @case('remove')
                                <span class="status-badge bg-warning text-dark">صرف مخزن</span>
                                @break
                            @case('transfer')
                                <span class="status-badge bg-primary">نقل مخزن</span>
                                @break
                        @endswitch
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- كارد الأدوات -->
    <div class="card">
        <div class="card-title p-2 d-flex align-items-center gap-2">
            <!-- زر تعديل -->
            <a href="{{ route('store_permits_management.edit', $permit->id) }}" class="btn btn-outline-primary btn-sm">
                تعديل <i class="fa fa-edit ms-1"></i>
            </a>
            <div class="vr"></div>

            <!-- زر نسخ -->
            <a href="{{route('invoicePurchases.create')}}" class="btn btn-outline-info btn-sm" >
                 انشاء فاتورة شراء  <i class="fa fa-plus ms-1"></i>
            </a>
            <div class="vr"></div>

            <!-- زر حذف -->
            <button type="button" class="btn btn-outline-danger btn-sm" onclick="deletePermit()">
                حذف <i class="fa fa-trash-alt ms-1"></i>
            </button>
            <div class="vr"></div>

            <!-- زر طباعة -->
            <div class="dropdown">
                <button class="btn btn-outline-secondary btn-sm" type="button" id="printDropdown"
                    data-bs-toggle="dropdown">
                    طباعة <i class="fa fa-print ms-1"></i>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" onclick="printPermitPDF()"><i class="fa fa-file-pdf me-2 text-danger"></i>PDF طباعة</a>
                    </li>
                    <li><a class="dropdown-item" href="#" onclick="printPermitBarcode()"><i class="fa fa-file-pdf me-2 text-danger"></i>PDF
                            باركود</a></li>
                    <li><a class="dropdown-item" href="#" onclick="exportPermitExcel()"><i class="fa fa-file-excel me-2 text-success"></i>Excel
                            تصدير</a></li>
                </ul>
            </div>

            <!-- زر اعتماد الإذن -->
            @if($permit->status == 'pending')
                <button type="button" class="btn btn-outline-success btn-sm" onclick="approvePermit()">
                    اعتماد الإذن <i class="fa fa-check ms-1"></i>
                </button>
            @endif

            <!-- زر المطبوعات -->
            <div class="dropdown">
                <button class="btn btn-outline-info btn-sm d-inline-flex align-items-center justify-content-center px-3"
                    style="min-width: 90px;" type="button" data-bs-toggle="dropdown">
                    المطبوعات <i class="fa fa-folder-open ms-1"></i>
                </button>
                <ul class="dropdown-menu py-1">
                    <li><a class="dropdown-item d-flex align-items-center" href="#"><i
                                class="fa fa-file-alt me-2 text-primary"></i> نموذج 1</a></li>
                    <li><a class="dropdown-item d-flex align-items-center" href="#"><i
                                class="fa fa-file-alt me-2 text-primary"></i> نموذج 2</a></li>
                    <li><a class="dropdown-item d-flex align-items-center" href="#"><i
                                class="fa fa-file-alt me-2 text-primary"></i> نموذج 3</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item d-flex align-items-center" href="#"><i
                                class="fa fa-file-pdf me-2 text-danger"></i>تقرير مفصل</a></li>
                    <li><a class="dropdown-item d-flex align-items-center" href="#"><i
                                class="fa fa-file-pdf me-2 text-danger"></i>تقرير مختصر</a></li>
                </ul>
            </div>

            <!-- زر تعيين مراكز التكلفة -->
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="assignCostCenter()">
                مراكز التكلفة <i class="fa fa-building ms-1"></i>
            </button>

            <!-- زر إضافة ملاحظة أو مرفق -->
            <div class="dropdown">
                <button class="btn btn-outline-success btn-sm" type="button" data-bs-toggle="dropdown">
                    الملاحظات <i class="fa fa-paperclip ms-1"></i>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" onclick="addNoteOrAttachment()">
                            <i class="fa fa-plus me-2 text-success"></i>إضافة ملاحظة جديدة</a>
                    </li>
                    <li><a class="dropdown-item" href="#" onclick="viewAllNotes()">
                            <i class="fa fa-list me-2 text-info"></i>عرض جميع الملاحظات</a>
                    </li>
                </ul>
            </div>

            <!-- زر تتبع المخزون -->
            <button type="button" class="btn btn-outline-warning btn-sm" onclick="trackInventory()">
                تتبع المخزون <i class="fa fa-search ms-1"></i>
            </button>
        </div>

        <div class="card-body">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#details" role="tab">التفاصيل</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#items" role="tab">الأصناف</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#inventory" role="tab">حركة المخزون</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#activity" role="tab">سجل النشاطات</a>
                </li>
            </ul>

            <div class="tab-content p-3">
                <!-- تبويب التفاصيل -->
                <div class="tab-pane active" id="details" role="tabpanel">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="tab-pane fade show active"
                                style="background: lightslategray; min-height: 100vh; padding: 20px;">
                                <div class="card shadow preview-card" style="max-width: 600px; margin: 20px auto;">
                                    <div class="card-body bg-white p-4" style="min-height: 400px; overflow: auto;">
                                        <div id="print-section"
                                            style="transform: scale(0.8); transform-origin: top center;">
                                            @include('stock::store_permits_management.partials.pdf', [
                                                'permit' => $permit,
                                            ])
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- تبويب الأصناف -->
                <div class="tab-pane" id="items" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>كود الصنف</th>
                                    <th>اسم الصنف</th>
                                    <th>الكمية</th>
                                    <th>سعر الوحدة</th>
                                    <th>الإجمالي</th>
                                    <th>الوحدة</th>
                                    <th>تاريخ الانتهاء</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($permit->items as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->product->code ?? 'غير محدد' }}</td>
                                        <td>{{ $item->product->name ?? $item->item }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>{{ number_format($item->unit_price, 2) }}</td>
                                        <td>{{ number_format($item->quantity * $item->unit_price, 2) }}</td>
                                        <td>{{ $item->product->unit ?? 'قطعة' }}</td>
                                        <td>{{ $item->expiry_date ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="5" class="text-start fw-bold">الإجمالي</td>
                                    <td class="fw-bold">{{ number_format($permit->items->sum(fn($i) => $i->quantity * $i->unit_price), 2) }}</td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- تبويب حركة المخزون -->
                <div class="tab-pane" id="inventory" role="tabpanel">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h5 class="text-primary">{{ $permit->items->count() }}</h5>
                                    <small class="text-muted">عدد الأصناف</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h5 class="text-success">{{ $permit->items->sum('quantity') }}</h5>
                                    <small class="text-muted">إجمالي الكمية</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h5 class="text-info">{{ number_format($permit->items->sum(fn($i) => $i->quantity * $i->unit_price), 2) }}</h5>
                                    <small class="text-muted">إجمالي القيمة</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>الصنف</th>
                                    <th>الرصيد السابق</th>
                                    <th>الحركة</th>
                                    <th>الرصيد الحالي</th>
                                    <th>نوع الحركة</th>
                                    <th>التاريخ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($permit->items as $item)
                                    <tr>
                                        <td>{{ $item->product->name ?? $item->item }}</td>
                                        <td>-</td>
                                        <td class="text-{{ $permit->type == 'add' ? 'success' : 'danger' }}">
                                            {{ $permit->type == 'add' ? '+' : '-' }}{{ $item->quantity }}
                                        </td>
                                        <td>-</td>
                                        <td>
                                            @switch($permit->type)
                                                @case('add')
                                                    <span class="badge bg-success">إضافة</span>
                                                    @break
                                                @case('remove')
                                                    <span class="badge bg-danger">صرف</span>
                                                    @break
                                                @case('transfer')
                                                    <span class="badge bg-primary">نقل</span>
                                                    @break
                                                @default
                                                    <span class="badge bg-secondary">غير محدد</span>
                                            @endswitch
                                        </td>
                                        <td>{{ $permit->created_at->format('Y-m-d H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- تبويب سجل النشاطات -->
                <div class="tab-pane" id="activity" role="tabpanel">
                    <div class="row mt-4">
                        <div class="col-12">
                            @if ($logs && count($logs) > 0)
                                @php
                                    $previousDate = null;
                                @endphp

                                @foreach ($logs as $date => $dayLogs)
                                    @php
                                        $currentDate = \Carbon\Carbon::parse($date);
                                        $diffInDays = $previousDate ? $previousDate->diffInDays($currentDate) : 0;
                                    @endphp

                                    @if ($diffInDays > 7)
                                        <div class="timeline-date">
                                            <h4>{{ $currentDate->format('Y-m-d') }}</h4>
                                        </div>
                                    @endif

                                    <div class="timeline-day">{{ $currentDate->translatedFormat('l') }}</div>

                                    <ul class="timeline">
                                        @foreach ($dayLogs as $log)
                                            @if ($log)
                                                <li class="timeline-item">
                                                    <div class="timeline-content">
                                                        <div class="time">
                                                            <i class="far fa-clock"></i>
                                                            {{ $log->created_at->format('H:i:s') }}
                                                        </div>
                                                        <div>
                                                            <strong>{{ $log->user->name ?? 'مستخدم غير معروف' }}</strong>
                                                            {!! Str::markdown($log->description ?? 'لا يوجد وصف') !!}
                                                            <div class="text-muted">
                                                                {{ $log->user->branch->name ?? 'فرع غير معروف' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>

                                    @php
                                        $previousDate = $currentDate;
                                    @endphp
                                @endforeach
                            @else
                                <div class="alert alert-info text-center" role="alert">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <p class="mb-0">لا توجد سجلات نشاط حتى الآن!</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden Forms للعمليات -->
    <form id="deleteForm" action="" method="POST"
        style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <form id="approveForm" action="" method="POST"
        style="display: none;">
        @csrf
        @method('PATCH')
    </form>
@endsection

@section('scripts')
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // دالة اعتماد الإذن
        function approvePermit() {
            Swal.fire({
                title: 'اعتماد إذن المخزن',
                text: 'هل أنت متأكد من اعتماد إذن المخزن رقم "{{ $permit->number }}"؟',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'نعم، اعتمد الإذن',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('approveForm').submit();
                }
            });
        }

        // دالة حذف الإذن
        function deletePermit() {
            Swal.fire({
                title: 'حذف إذن المخزن',
                text: 'هل أنت متأكد من حذف إذن المخزن رقم "{{ $permit->number }}"؟ هذا الإجراء لا يمكن التراجع عنه!',
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'نعم، احذف',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('deleteForm').submit();
                }
            });
        }

        // دالة نسخ الإذن
        function copyPermit() {
            Swal.fire({
                title: 'نسخ إذن المخزن',
                text: 'سيتم إنشاء نسخة جديدة من إذن المخزن',
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#17a2b8',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'تأكيد النسخ',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {

                }
            });
        }

        // دالة تعيين مراكز التكلفة
        function assignCostCenter() {
            Swal.fire({
                title: 'تعيين مراكز التكلفة',
                text: 'سيتم توجيهك لصفحة تعيين مراكز التكلفة لهذا الإذن',
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#6c757d',
                cancelButtonColor: '#dc3545',
                confirmButtonText: 'المتابعة',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {

                }
            });
        }

        // دالة تتبع المخزون
        function trackInventory() {
            Swal.fire({
                title: 'تتبع المخزون',
                text: 'عرض تفاصيل حركة المخزون لهذا الإذن',
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'عرض التفاصيل',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {

                }
            });
        }

        // دالة طباعة الإذن
        function printPermit() {
            const content = document.getElementById('print-section').innerHTML;
            const printWindow = window.open('', '', 'height=800,width=1000');
            printWindow.document.write('<html><head><title>إذن مخزن</title>');
            printWindow.document.write('<link rel="stylesheet" href="{{ asset('assets/admin/css/style.css') }}">');
            printWindow.document.write('</head><body>');
            printWindow.document.write(content);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
            printWindow.close();
        }

        // دوال الطباعة المختلفة
        function printPermitPDF() {
                    }

        function printPermitBarcode() {

        }

        function exportPermitExcel() {

        }

        // دالة إضافة ملاحظة أو مرفق
        function addNoteOrAttachment() {
            const now = new Date();
            const currentDate = now.toISOString().split('T')[0];
            const currentTime = now.toTimeString().split(' ')[0].substring(0, 5);

            Swal.fire({
                title: 'إضافة ملاحظة أو مرفق',
                html: `
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="process" class="form-label text-start d-block">نوع الإجراء:</label>
                            <select id="process" class="form-control">
                                <option value="">اختر نوع الإجراء</option>
                                <option value="مراجعة الإذن">مراجعة الإذن</option>
                                <option value="اعتماد الإذن">اعتماد الإذن</option>
                                <option value="تنفيذ الحركة">تنفيذ الحركة</option>
                                <option value="مراجعة المخزون">مراجعة المخزون</option>
                                <option value="تدقيق الأصناف">تدقيق الأصناف</option>
                                <option value="تحديث البيانات">تحديث البيانات</option>
                                <option value="ملاحظة عامة">ملاحظة عامة</option>
                                <option value="أخرى">أخرى</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="noteDate" class="form-label text-start d-block">التاريخ:</label>
                            <input type="date" id="noteDate" class="form-control" value="${currentDate}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="noteTime" class="form-label text-start d-block">الوقت:</label>
                            <input type="time" id="noteTime" class="form-control" value="${currentTime}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="attachment" class="form-label text-start d-block">مرفق (اختياري):</label>
                            <input type="file" id="attachment" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="note" class="form-label text-start d-block">تفاصيل الملاحظة:</label>
                        <textarea id="note" class="form-control" rows="4" placeholder="اكتب تفاصيل الملاحظة هنا..."></textarea>
                    </div>
                `,
                width: '600px',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-save me-1"></i> حفظ الملاحظة',
                cancelButtonText: '<i class="fas fa-times me-1"></i> إلغاء',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    const process = document.getElementById('process').value;
                    const note = document.getElementById('note').value.trim();
                    const noteDate = document.getElementById('noteDate').value;
                    const noteTime = document.getElementById('noteTime').value;
                    const attachment = document.getElementById('attachment').files[0];

                    // التحقق من صحة البيانات
                    if (!process) {
                        Swal.showValidationMessage('يرجى اختيار نوع الإجراء');
                        return false;
                    }

                    if (!note) {
                        Swal.showValidationMessage('يرجى إدخال تفاصيل الملاحظة');
                        return false;
                    }

                    if (!noteDate) {
                        Swal.showValidationMessage('يرجى تحديد التاريخ');
                        return false;
                    }

                    if (!noteTime) {
                        Swal.showValidationMessage('يرجى تحديد الوقت');
                        return false;
                    }

                    return {
                        process,
                        note,
                        noteDate,
                        noteTime,
                        attachment
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    saveNoteToDatabase(result.value);
                }
            });
        }

        // دالة حفظ الملاحظة في قاعدة البيانات
        function saveNoteToDatabase(noteData) {
            const formData = new FormData();
            formData.append('description', noteData.note);
            formData.append('process', noteData.process);
            formData.append('date', noteData.noteDate);
            formData.append('time', noteData.noteTime);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

            if (noteData.attachment) {
                formData.append('attachment', noteData.attachment);
            }

            fetch(``, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'تم الحفظ بنجاح!',
                            text: 'تم إضافة الملاحظة بنجاح',
                            icon: 'success',
                            confirmButtonColor: '#28a745'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'خطأ!',
                            text: data.message || 'حدث خطأ أثناء حفظ الملاحظة',
                            icon: 'error',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'خطأ!',
                        text: 'حدث خطأ أثناء الاتصال بالخادم',
                        icon: 'error',
                        confirmButtonColor: '#dc3545'
                    });
                });
        }

        // دالة عرض جميع الملاحظات
        function viewAllNotes() {
            Swal.fire({
                title: 'جاري تحميل الملاحظات...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch(``)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        let notesHtml = '';

                        if (data.notes && data.notes.length > 0) {
                            data.notes.forEach((note, index) => {
                                const processName = note.process || 'غير محدد';
                                const employeeName = note.employee_name || 'غير محدد';
                                const noteDate = note.date || 'غير محدد';
                                const noteTime = note.time || 'غير محدد';
                                const description = note.description || 'لا يوجد وصف';

                                notesHtml += `
                                <div class="card mb-3 border-start border-primary border-3" id="note-${note.id}">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div class="flex-grow-1">
                                                <div class="d-flex align-items-center mb-1">
                                                    <span class="badge bg-primary me-2">${index + 1}</span>
                                                    <h6 class="mb-0 text-primary fw-bold">${processName}</h6>
                                                </div>
                                                <small class="text-muted d-block">
                                                    <i class="fas fa-user me-1 text-info"></i><strong>الموظف:</strong> ${employeeName}
                                                </small>
                                                <small class="text-muted d-block">
                                                    <i class="fas fa-calendar me-1 text-success"></i><strong>التاريخ:</strong> ${noteDate}
                                                    <i class="fas fa-clock me-2 ms-3 text-warning"></i><strong>الوقت:</strong> ${noteTime}
                                                </small>
                                            </div>
                                            <button class="btn btn-outline-danger btn-sm" onclick="deleteNote(${note.id})"
                                                    title="حذف الملاحظة">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>

                                        <div class="mt-3 p-3 bg-light rounded">
                                            <p class="mb-0 text-dark">${description}</p>
                                        </div>

                                        ${note.has_attachment ? `
                                                        <div class="mt-3 pt-2 border-top">
                                                            <a href="${note.attachment_url}" target="_blank"
                                                               class="btn btn-outline-info btn-sm">
                                                                <i class="fas fa-paperclip me-1"></i>عرض المرفق
                                                            </a>
                                                        </div>
                                                    ` : ''}

                                        ${note.created_at ? `
                                                        <div class="mt-2 pt-2 border-top">
                                                            <small class="text-muted">
                                                                <i class="fas fa-clock me-1"></i>تم الإنشاء: ${note.created_at}
                                                            </small>
                                                        </div>
                                                    ` : ''}
                                    </div>
                                </div>
                            `;
                            });
                        } else {
                            notesHtml = `
                            <div class="alert alert-info text-center py-4">
                                <i class="fas fa-info-circle fs-1 text-info mb-3"></i>
                                <h5 class="mb-2">لا توجد ملاحظات حتى الآن</h5>
                                <p class="mb-0 text-muted">يمكنك إضافة ملاحظة جديدة من خلال زر "إضافة ملاحظة جديدة"</p>
                            </div>
                        `;
                        }

                        Swal.fire({
                            title: `<i class="fas fa-sticky-note me-2 text-primary"></i>جميع الملاحظات (${data.notes ? data.notes.length : 0})`,
                            html: `
                            <div class="text-start" style="max-height: 500px; overflow-y: auto; padding: 10px;">
                                ${notesHtml}
                            </div>
                            <div class="mt-3 pt-3 border-top text-center">
                                <button type="button" class="btn btn-success btn-sm me-2" onclick="Swal.close(); addNoteOrAttachment();">
                                    <i class="fas fa-plus me-1"></i>إضافة ملاحظة جديدة
                                </button>
                                <button type="button" class="btn btn-secondary btn-sm" onclick="location.reload();">
                                    <i class="fas fa-sync me-1"></i>تحديث الصفحة
                                </button>
                            </div>
                        `,
                            width: '800px',
                            showConfirmButton: false,
                            showCancelButton: true,
                            cancelButtonText: '<i class="fas fa-times me-1"></i>إغلاق',
                            cancelButtonColor: '#6c757d',
                            customClass: {
                                popup: 'swal-rtl'
                            }
                        });
                    } else {
                        Swal.fire({
                            title: 'خطأ!',
                            text: data.message || 'لم يتم العثور على ملاحظات',
                            icon: 'error',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                })
                .catch(error => {
                    console.error('خطأ في جلب الملاحظات:', error);
                    Swal.fire({
                        title: 'خطأ في الاتصال!',
                        text: 'حدث خطأ أثناء جلب الملاحظات. يرجى التحقق من الاتصال بالإنترنت والمحاولة مرة أخرى.',
                        icon: 'error',
                        confirmButtonColor: '#dc3545',
                        footer: `<small class="text-muted">تفاصيل الخطأ: ${error.message}</small>`
                    });
                });
        }

        // دالة حذف ملاحظة
        function deleteNote(noteId) {
            Swal.fire({
                title: 'تأكيد الحذف',
                text: 'هل أنت متأكد من حذف هذه الملاحظة؟',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'نعم، احذف',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(``, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content'),
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('تم الحذف!', 'تم حذف الملاحظة بنجاح', 'success');
                                viewAllNotes();
                            } else {
                                Swal.fire('خطأ!', data.message, 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire('خطأ!', 'حدث خطأ أثناء حذف الملاحظة', 'error');
                        });
                }
            });
        }

        // عرض رسائل النجاح والخطأ
        @if (session('success'))
            Swal.fire({
                title: 'تم بنجاح!',
                text: '{{ session('success') }}',
                icon: 'success',
                confirmButtonColor: '#28a745'
            });
        @endif

        @if (session('error'))
            Swal.fire({
                title: 'خطأ!',
                text: '{{ session('error') }}',
                icon: 'error',
                confirmButtonColor: '#dc3545'
            });
        @endif

        // تأثيرات إضافية للتفاعل
        document.addEventListener('DOMContentLoaded', function() {
            const buttons = document.querySelectorAll('.status-btn');
            buttons.forEach(button => {
                button.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px) scale(1.02)';
                });

                button.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });
        });
    </script>
@endsection
