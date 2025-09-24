{{-- resources/views/attendance/bulk-barcode-generator.blade.php --}}

@extends('master')

@section('title', 'توليد الباركود للموظفين')

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">توليد الباركود للموظفين</h2>
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">الرئيسية</a></li>
                        <li class="breadcrumb-item"><a href="#">الحضور والانصراف</a></li>
                        <li class="breadcrumb-item active">توليد الباركود</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="content-body">
    <!-- خطوات التوليد -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="mb-3">خطوات توليد الباركود:</h5>
            <div class="row">
                <div class="col-md-3 text-center">
                    <div class="step-circle bg-primary text-white mb-2">1</div>
                    <p><strong>اختيار الموظفين</strong><br>حدد الموظفين المراد توليد باركود لهم</p>
                </div>
                <div class="col-md-3 text-center">
                    <div class="step-circle bg-success text-white mb-2">2</div>
                    <p><strong>توليد الباركود</strong><br>اضغط توليد لإنشاء باركود فريد</p>
                </div>
                <div class="col-md-3 text-center">
                    <div class="step-circle bg-info text-white mb-2">3</div>
                    <p><strong>المراجعة</strong><br>راجع الباركودات المولدة</p>
                </div>
                <div class="col-md-3 text-center">
                    <div class="step-circle bg-warning text-white mb-2">4</div>
                    <p><strong>الطباعة</strong><br>اطبع الباركودات ووزعها</p>
                </div>
            </div>
        </div>
    </div>

    <!-- إحصائيات سريعة -->
    <div class="row mb-3">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h3 id="total-employees">{{ $stats['total_employees'] }}</h3>
                    <p>إجمالي الموظفين</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h3 id="with-barcode">{{ $stats['with_barcode'] }}</h3>
                    <p>لديهم باركود</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h3 id="without-barcode">{{ $stats['without_barcode'] }}</h3>
                    <p>بدون باركود</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h3 id="disabled-barcode">{{ $stats['disabled'] }}</h3>
                    <p>معطل الباركود</p>
                </div>
            </div>
        </div>
    </div>

    <!-- أدوات التوليد -->
    <div class="card mb-3">
        <div class="card-header">
            <h4 class="card-title">أدوات التوليد السريع</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <button id="generate-missing" class="btn btn-primary btn-block btn-lg">
                        <i class="fa fa-qrcode"></i><br>
                        <strong>توليد للموظفين الجدد</strong><br>
                        <small>فقط الذين بدون باركود</small>
                    </button>
                </div>
                <div class="col-md-4">
                    <button id="regenerate-all" class="btn btn-warning btn-block btn-lg">
                        <i class="fa fa-redo"></i><br>
                        <strong>إعادة توليد للجميع</strong><br>
                        <small>استبدال جميع الباركودات</small>
                    </button>
                </div>
                <div class="col-md-4">
                    <button id="print-all-cards" class="btn btn-success btn-block btn-lg">
                        <i class="fa fa-print"></i><br>
                        <strong>طباعة البطاقات</strong><br>
                        <small>جميع الباركودات</small>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- فلتر الموظفين -->
    <div class="card mb-3">
        <div class="card-header">
            <h4 class="card-title">تصفية الموظفين</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <label>القسم:</label>
                    <select id="department-filter" class="form-control">
                        <option value="">جميع الأقسام</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label>حالة الباركود:</label>
                    <select id="barcode-status-filter" class="form-control">
                        <option value="">الكل</option>
                        <option value="missing">بدون باركود</option>
                        <option value="enabled">مفعل</option>
                        <option value="disabled">معطل</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>البحث:</label>
                    <input type="text" id="employee-search" class="form-control" placeholder="ابحث عن موظف...">
                </div>
                <div class="col-md-3">
                    <label>&nbsp;</label>
                    <div>
                        <button id="select-all" class="btn btn-outline-primary">تحديد الكل</button>
                        <button id="select-none" class="btn btn-outline-secondary">إلغاء التحديد</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- جدول الموظفين -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title">قائمة الموظفين</h4>
            <div>
                <button id="generate-selected" class="btn btn-primary" disabled>
                    <i class="fa fa-qrcode"></i> توليد للمحددين (<span id="selected-count">0</span>)
                </button>
                <button id="print-selected" class="btn btn-success" disabled>
                    <i class="fa fa-print"></i> طباعة المحددين
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped" id="employees-table">
                    <thead>
                        <tr>
                            <th width="50">
                                <input type="checkbox" id="select-all-checkbox">
                            </th>
                            <th>الموظف</th>
                            <th>القسم</th>
                            <th>الباركود الحالي</th>
                            <th>الحالة</th>
                            <th>آخر استخدام</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employees as $employee)
                        <tr data-employee-id="{{ $employee->id }}" class="employee-row">
                            <td>
                                <input type="checkbox" class="employee-checkbox"
                                       value="{{ $employee->id }}"
                                       {{ !$employee->barcode ? 'checked' : '' }}>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($employee->employee_photo)
                                        <img src="{{ asset('storage/' . $employee->employee_photo) }}"
                                             class="rounded-circle mr-2" width="40" height="40" style="object-fit: cover;">
                                    @else
                                        <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center mr-2"
                                             style="width: 40px; height: 40px;">
                                            <i class="fa fa-user text-white"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <strong>{{ $employee->full_name }}</strong><br>
                                        <small class="text-muted">ID: {{ $employee->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $employee->department->name ?? 'غير محدد' }}</td>
                            <td>
                                @if($employee->barcode)
                                    <code class="barcode-code">{{ $employee->barcode }}</code>
                                @else
                                    <span class="badge badge-secondary">لم يتم التوليد</span>
                                @endif
                            </td>
                            <td>
                                @if(!$employee->barcode)
                                    <span class="badge badge-warning">لا يوجد</span>
                                @elseif($employee->barcode_enabled)
                                    <span class="badge badge-success">مفعل</span>
                                @else
                                    <span class="badge badge-danger">معطل</span>
                                @endif
                            </td>
                            <td>
                                @if($employee->todayAttendance)
                                    <small class="text-success">
                                        <i class="fa fa-check-circle"></i>
                                        {{ $employee->todayAttendance->updated_at->format('H:i') }}
                                    </small>
                                @else
                                    <small class="text-muted">لم يستخدم اليوم</small>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    @if(!$employee->barcode)
                                        <button class="btn btn-primary generate-single"
                                                data-employee-id="{{ $employee->id }}">
                                            <i class="fa fa-plus"></i> توليد
                                        </button>
                                    @else
                                        <button class="btn btn-info preview-barcode"
                                                data-employee-id="{{ $employee->id }}"
                                                data-barcode="{{ $employee->barcode }}"
                                                data-name="{{ $employee->full_name }}">
                                            <i class="fa fa-eye"></i> عرض
                                        </button>
                                        <button class="btn btn-success print-single"
                                                data-employee-id="{{ $employee->id }}">
                                            <i class="fa fa-print"></i> طباعة
                                        </button>
                                        <button class="btn btn-warning regenerate-single"
                                                data-employee-id="{{ $employee->id }}">
                                            <i class="fa fa-redo"></i> إعادة توليد
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal معاينة الباركود -->
<div class="modal fade" id="barcodePreviewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h4 class="modal-title">معاينة الباركود</h4>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <h5 id="preview-employee-name" class="mb-3"></h5>

                <!-- الباركود -->
                <div class="border rounded p-3 mb-3">
                    <h6 class="text-primary">الباركود</h6>
                    <canvas id="preview-barcode" class="mb-2"></canvas>
                    <code id="preview-barcode-text"></code>
                </div>

                <!-- QR Code -->
                <div class="border rounded p-3">
                    <h6 class="text-primary">رمز الاستجابة السريعة</h6>
                    <canvas id="preview-qr"></canvas>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">إغلاق</button>
                <button type="button" id="download-preview" class="btn btn-primary">
                    <i class="fa fa-download"></i> تحميل
                </button>
                <button type="button" id="print-preview" class="btn btn-success">
                    <i class="fa fa-print"></i> طباعة
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
.step-circle {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    font-weight: bold;
    margin: 0 auto;
}

.barcode-code {
    font-family: 'Courier New', monospace;
    font-size: 11px;
    background: #f8f9fa;
    padding: 3px 6px;
    border-radius: 4px;
    border: 1px solid #dee2e6;
}

.employee-row.selected {
    background-color: #e3f2fd !important;
}

.generation-progress {
    display: none;
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 20px;
    margin: 15px 0;
}

.progress-step {
    display: flex;
    align-items: center;
    padding: 8px 0;
}

.progress-step .spinner {
    width: 20px;
    height: 20px;
    border: 2px solid #f3f3f3;
    border-top: 2px solid #007bff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-left: 10px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.step-complete {
    color: #28a745;
}

.step-error {
    color: #dc3545;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let selectedEmployees = [];
    let currentPreviewEmployee = null;

    // عناصر DOM
    const generateMissingBtn = document.getElementById('generate-missing');
    const regenerateAllBtn = document.getElementById('regenerate-all');
    const printAllCardsBtn = document.getElementById('print-all-cards');
    const generateSelectedBtn = document.getElementById('generate-selected');
    const printSelectedBtn = document.getElementById('print-selected');
    const selectAllBtn = document.getElementById('select-all');
    const selectNoneBtn = document.getElementById('select-none');
    const selectAllCheckbox = document.getElementById('select-all-checkbox');
    const employeeCheckboxes = document.querySelectorAll('.employee-checkbox');
    const selectedCountSpan = document.getElementById('selected-count');

    // إعداد الأحداث
    setupEventListeners();
    updateSelectedCount();

    /**
     * إعداد مستمعي الأحداث
     */
    function setupEventListeners() {
        // أزرار التوليد السريع
        generateMissingBtn.addEventListener('click', () => generateMissingBarcodes());
        regenerateAllBtn.addEventListener('click', () => regenerateAllBarcodes());
        printAllCardsBtn.addEventListener('click', () => printAllCards());

        // أزرار التحديد المجمع
        selectAllBtn.addEventListener('click', () => selectAllEmployees());
        selectNoneBtn.addEventListener('click', () => selectNoneEmployees());
        selectAllCheckbox.addEventListener('change', (e) => toggleAllCheckboxes(e.target.checked));

        // أزرار الإجراءات المحددة
        generateSelectedBtn.addEventListener('click', () => generateSelectedBarcodes());
        printSelectedBtn.addEventListener('click', () => printSelectedCards());

        // checkboxes الموظفين
        employeeCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectedEmployees);
        });

        // أزرار الإجراءات الفردية
        document.addEventListener('click', handleIndividualActions);

        // الفلاتر
        document.getElementById('department-filter').addEventListener('change', applyFilters);
        document.getElementById('barcode-status-filter').addEventListener('change', applyFilters);
        document.getElementById('employee-search').addEventListener('input', applyFilters);
    }

    /**
     * توليد باركود للموظفين الجدد فقط
     */
    async function generateMissingBarcodes() {
        const result = await Swal.fire({
            title: 'توليد باركود للموظفين الجدد؟',
            text: 'سيتم توليد باركود فقط للموظفين الذين ليس لديهم باركود',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'نعم، ولد',
            cancelButtonText: 'إلغاء'
        });

        if (result.isConfirmed) {
            await performBulkGeneration('missing');
        }
    }

    /**
     * إعادة توليد جميع الباركودات
     */
    async function regenerateAllBarcodes() {
        const result = await Swal.fire({
            title: 'إعادة توليد جميع الباركودات؟',
            text: 'تحذير: سيتم استبدال جميع الباركودات الحالية بأخرى جديدة',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'نعم، أعد التوليد',
            cancelButtonText: 'إلغاء',
            confirmButtonColor: '#ffc107'
        });

        if (result.isConfirmed) {
            await performBulkGeneration('all');
        }
    }

    /**
     * توليد باركود للموظفين المحددين
     */
    async function generateSelectedBarcodes() {
        if (selectedEmployees.length === 0) {
            Swal.fire('تنبيه', 'يرجى تحديد موظف أو أكثر', 'warning');
            return;
        }

        const result = await Swal.fire({
            title: `توليد باركود لـ ${selectedEmployees.length} موظف؟`,
            text: 'سيتم توليد باركود للموظفين المحددين',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'نعم، ولد',
            cancelButtonText: 'إلغاء'
        });

        if (result.isConfirmed) {
            await performBulkGeneration('selected', selectedEmployees);
        }
    }

    /**
     * تنفيذ التوليد المجمع
     */
    async function performBulkGeneration(type, employeeIds = null) {
        // إظهار شريط التقدم
        showGenerationProgress();

        try {
            let url = '/barcode-attendance/generate-all';
            let body = { type: type };

            if (employeeIds) {
                body.employee_ids = employeeIds;
            }

            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(body)
            });

            const data = await response.json();
            hideGenerationProgress();

            if (data.success) {
                await Swal.fire({
                    icon: 'success',
                    title: 'تم التوليد بنجاح!',
                    text: data.message,
                    confirmButtonText: 'موافق'
                });

                // إعادة تحميل الصفحة لإظهار التحديثات
                location.reload();
            } else {
                throw new Error(data.message || 'فشل في التوليد');
            }

        } catch (error) {
            hideGenerationProgress();
            Swal.fire({
                icon: 'error',
                title: 'خطأ!',
                text: error.message || 'حدث خطأ أثناء توليد الباركودات',
                confirmButtonText: 'موافق'
            });
        }
    }

    /**
     * إظهار شريط التقدم
     */
    function showGenerationProgress() {
        const progressHtml = `
            <div class="generation-progress" id="generation-progress">
                <h5 class="mb-3">جاري توليد الباركودات...</h5>
                <div class="progress-step">
                    <span>جاري التحقق من الموظفين...</span>
                    <div class="spinner"></div>
                </div>
                <div class="progress mb-3">
                    <div class="progress-bar progress-bar-striped progress-bar-animated"
                         style="width: 0%" id="progress-bar"></div>
                </div>
                <small class="text-muted">يرجى عدم إغلاق هذه النافذة أثناء العملية</small>
            </div>
        `;

        const targetCard = document.querySelector('.card .card-body');
        targetCard.insertAdjacentHTML('afterbegin', progressHtml);

        document.getElementById('generation-progress').style.display = 'block';

        // محاكاة التقدم
        let progress = 0;
        const progressBar = document.getElementById('progress-bar');
        const interval = setInterval(() => {
            progress += Math.random() * 20;
            if (progress > 90) progress = 90;
            progressBar.style.width = progress + '%';
        }, 500);

        // حفظ المرجع لإيقافه لاحقاً
        window.progressInterval = interval;
    }

    /**
     * إخفاء شريط التقدم
     */
    function hideGenerationProgress() {
        const progressElement = document.getElementById('generation-progress');
        if (progressElement) {
            progressElement.remove();
        }

        if (window.progressInterval) {
            clearInterval(window.progressInterval);
        }
    }

    /**
     * معالجة الإجراءات الفردية
     */
    function handleIndividualActions(e) {
        const button = e.target.closest('button');
        if (!button) return;

        const employeeId = button.dataset.employeeId;

        if (button.classList.contains('generate-single')) {
            generateSingleBarcode(employeeId);
        } else if (button.classList.contains('preview-barcode')) {
            previewBarcode(button.dataset.barcode, button.dataset.name, employeeId);
        } else if (button.classList.contains('print-single')) {
            printSingleCard(employeeId);
        } else if (button.classList.contains('regenerate-single')) {
            regenerateSingleBarcode(employeeId);
        }
    }

    /**
     * توليد باركود لموظف واحد
     */
    async function generateSingleBarcode(employeeId) {
        try {
            showLoadingForEmployee(employeeId);

            const response = await fetch(`/barcode-attendance/employee/${employeeId}/generate`);
            const data = await response.json();

            hideLoadingForEmployee(employeeId);

            if (data.success) {
                showSuccessForEmployee(employeeId);
                setTimeout(() => location.reload(), 1000);
            } else {
                throw new Error(data.message);
            }

        } catch (error) {
            hideLoadingForEmployee(employeeId);
            showErrorForEmployee(employeeId);
        }
    }

    /**
     * معاينة الباركود
     */
    function previewBarcode(barcode, employeeName, employeeId) {
        currentPreviewEmployee = { barcode, employeeName, employeeId };

        // تحديث المودال
        document.getElementById('preview-employee-name').textContent = employeeName;
        document.getElementById('preview-barcode-text').textContent = barcode;

        // رسم الباركود
        JsBarcode("#preview-barcode", barcode, {
            format: "CODE128",
            width: 2,
            height: 80,
            displayValue: true,
            fontSize: 14,
            margin: 10
        });

        // رسم QR Code
        QRCode.toCanvas(document.getElementById('preview-qr'), barcode, {
            width: 150,
            margin: 2
        });

        // إظهار المودال
        $('#barcodePreviewModal').modal('show');
    }

    /**
     * طباعة بطاقة واحدة
     */
    function printSingleCard(employeeId) {
        window.open(`/barcode-attendance/employee/${employeeId}/print`, '_blank');
    }

    /**
     * طباعة جميع البطاقات
     */
    function printAllCards() {
        const employeesWithBarcode = Array.from(document.querySelectorAll('.barcode-code'))
                                         .map(el => el.closest('tr').dataset.employeeId);

        if (employeesWithBarcode.length === 0) {
            Swal.fire('تنبيه', 'لا توجد باركودات للطباعة', 'warning');
            return;
        }

        // إنشاء صفحة طباعة مجمعة
        const printWindow = window.open('', '_blank');
        generateBulkPrintPage(printWindow, employeesWithBarcode);
    }

    /**
     * إنشاء صفحة الطباعة المجمعة
     */
    function generateBulkPrintPage(printWindow, employeeIds) {
        let htmlContent = `
            <!DOCTYPE html>
            <html lang="ar" dir="rtl">
            <head>
                <meta charset="UTF-8">
                <title>باركودات الموظفين</title>
                <style>
                    body { font-family: 'Cairo', sans-serif; margin: 0; padding: 20px; }
                    .cards-grid {
                        display: grid;
                        grid-template-columns: repeat(3, 1fr);
                        gap: 15px;
                        margin: 20px 0;
                    }
                    .barcode-card {
                        border: 2px solid #000;
                        padding: 15px;
                        text-align: center;
                        page-break-inside: avoid;
                        background: white;
                        border-radius: 8px;
                    }
                    .employee-name { font-weight: bold; font-size: 14px; margin-bottom: 10px; }
                    .employee-id { font-size: 12px; color: #666; margin-bottom: 15px; }
                    .barcode-text { font-family: monospace; font-size: 10px; margin-top: 8px; }
                    @page { margin: 1cm; }
                </style>
                <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
            </head>
            <body>
                <h2 style="text-align: center;">باركودات الموظفين - ${new Date().toLocaleDateString('ar-SA')}</h2>
                <div class="cards-grid">
        `;

        // جمع بيانات الموظفين
        employeeIds.forEach((employeeId, index) => {
            const row = document.querySelector(`tr[data-employee-id="${employeeId}"]`);
            const employeeName = row.querySelector('strong').textContent;
            const department = row.querySelector('td:nth-child(3)').textContent;
            const barcode = row.querySelector('.barcode-code')?.textContent || '';

            if (barcode) {
                htmlContent += `
                    <div class="barcode-card">
                        <div class="employee-name">${employeeName}</div>
                        <div class="employee-id">رقم الموظف: ${employeeId} | ${department}</div>
                        <canvas id="barcode-${index}"></canvas>
                        <div class="barcode-text">${barcode}</div>
                    </div>
                `;
            }
        });

        htmlContent += `
                </div>
                <script>
                    window.onload = function() {
                        ${employeeIds.map((employeeId, index) => {
                            const row = document.querySelector(`tr[data-employee-id="${employeeId}"]`);
                            const barcode = row?.querySelector('.barcode-code')?.textContent || '';
                            return barcode ? `
                                JsBarcode("#barcode-${index}", "${barcode}", {
                                    format: "CODE128",
                                    width: 1.5,
                                    height: 50,
                                    displayValue: false,
                                    margin: 5
                                });
                            ` : '';
                        }).join('\n')}

                        setTimeout(() => {
                            window.print();
                            window.close();
                        }, 1500);
                    };
                </script>
            </body>
            </html>
        `;

        printWindow.document.write(htmlContent);
    }

    /**
     * تحديث قائمة الموظفين المحددين
     */
    function updateSelectedEmployees() {
        selectedEmployees = Array.from(employeeCheckboxes)
                                .filter(cb => cb.checked)
                                .map(cb => cb.value);

        updateSelectedCount();
        updateActionButtons();

        // تحديد الصفوف المحددة
        document.querySelectorAll('.employee-row').forEach(row => {
            const checkbox = row.querySelector('.employee-checkbox');
            if (checkbox && checkbox.checked) {
                row.classList.add('selected');
            } else {
                row.classList.remove('selected');
            }
        });
    }

    /**
     * تحديث عداد المحددين
     */
    function updateSelectedCount() {
        const count = selectedEmployees.length;
        selectedCountSpan.textContent = count;

        // تحديث checkbox الرئيسي
        if (count === 0) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = false;
        } else if (count === employeeCheckboxes.length) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = true;
        } else {
            selectAllCheckbox.indeterminate = true;
        }
    }

    /**
     * تحديث أزرار الإجراءات
     */
    function updateActionButtons() {
        const hasSelection = selectedEmployees.length > 0;
        generateSelectedBtn.disabled = !hasSelection;
        printSelectedBtn.disabled = !hasSelection;
    }

    /**
     * تحديد جميع الموظفين
     */
    function selectAllEmployees() {
        employeeCheckboxes.forEach(cb => {
            if (cb.closest('tr').style.display !== 'none') {
                cb.checked = true;
            }
        });
        updateSelectedEmployees();
    }

    /**
     * إلغاء تحديد جميع الموظفين
     */
    function selectNoneEmployees() {
        employeeCheckboxes.forEach(cb => cb.checked = false);
        updateSelectedEmployees();
    }

    /**
     * تبديل جميع الcheckboxes
     */
    function toggleAllCheckboxes(checked) {
        employeeCheckboxes.forEach(cb => {
            if (cb.closest('tr').style.display !== 'none') {
                cb.checked = checked;
            }
        });
        updateSelectedEmployees();
    }

    /**
     * تطبيق الفلاتر
     */
    function applyFilters() {
        const searchTerm = document.getElementById('employee-search').value.toLowerCase();
        const departmentFilter = document.getElementById('department-filter').value;
        const barcodeStatusFilter = document.getElementById('barcode-status-filter').value;

        document.querySelectorAll('.employee-row').forEach(row => {
            const employeeName = row.querySelector('strong').textContent.toLowerCase();
            const department = row.querySelector('td:nth-child(3)').textContent;
            const hasBarcodeCode = row.querySelector('.barcode-code');
            const statusBadge = row.querySelector('.badge');

            let show = true;

            // فلتر البحث
            if (searchTerm && !employeeName.includes(searchTerm)) {
                show = false;
            }

            // فلتر القسم
            if (departmentFilter && !department.includes(departmentFilter)) {
                show = false;
            }

            // فلتر حالة الباركود
            if (barcodeStatusFilter) {
                switch (barcodeStatusFilter) {
                    case 'missing':
                        if (hasBarcodeCode) show = false;
                        break;
                    case 'enabled':
                        if (!statusBadge || !statusBadge.classList.contains('badge-success')) show = false;
                        break;
                    case 'disabled':
                        if (!statusBadge || !statusBadge.classList.contains('badge-danger')) show = false;
                        break;
                }
            }

            row.style.display = show ? '' : 'none';
        });

        updateSelectedEmployees();
    }

    /**
     * إظهار تحميل لموظف محدد
     */
    function showLoadingForEmployee(employeeId) {
        const row = document.querySelector(`tr[data-employee-id="${employeeId}"]`);
        const actionsCell = row.querySelector('td:last-child');
        actionsCell.innerHTML = '<div class="spinner-border spinner-border-sm text-primary"></div>';
    }

    /**
     * إخفاء التحميل وإظهار النجاح
     */
    function showSuccessForEmployee(employeeId) {
        const row = document.querySelector(`tr[data-employee-id="${employeeId}"]`);
        const actionsCell = row.querySelector('td:last-child');
        actionsCell.innerHTML = '<i class="fa fa-check-circle text-success"></i> تم';
    }

    /**
     * إظهار خطأ لموظف محدد
     */
    function showErrorForEmployee(employeeId) {
        const row = document.querySelector(`tr[data-employee-id="${employeeId}"]`);
        const actionsCell = row.querySelector('td:last-child');
        actionsCell.innerHTML = '<i class="fa fa-times-circle text-danger"></i> فشل';
    }

    /**
     * إخفاء التحميل وإعادة الأزرار
     */
    function hideLoadingForEmployee(employeeId) {
        // سيتم إعادة تحميل الصفحة عادة
    }

    // إعداد المودال
    document.getElementById('download-preview')?.addEventListener('click', function() {
        if (currentPreviewEmployee) {
            const canvas = document.getElementById('preview-barcode');
            const link = document.createElement('a');
            link.download = `barcode-${currentPreviewEmployee.employeeName}.png`;
            link.href = canvas.toDataURL();
            link.click();
        }
    });

    document.getElementById('print-preview')?.addEventListener('click', function() {
        if (currentPreviewEmployee) {
            printSingleCard(currentPreviewEmployee.employeeId);
        }
    });
});
</script>
@endsection
