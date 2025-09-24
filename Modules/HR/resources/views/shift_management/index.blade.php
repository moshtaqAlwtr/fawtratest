@extends('master')

@section('title')
    الورديات
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">الورديات</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a></li>
                            <li class="breadcrumb-item active">عرض</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <span class="badge badge-primary" id="shifts-count">
                            إجمالي الورديات: <span id="total-shifts">{{ count($shifts) }}</span>
                        </span>
                    </div>
                    <div>
                        <a href="{{ route('shift_management.create') }}" class="btn btn-outline-primary">
                            <i class="fa fa-plus"></i> أضف وردية
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @include('layouts.alerts.error')
        @include('layouts.alerts.success')

        <!-- البحث والفلترة -->
        <div class="card">
            <div class="card-content">
                <div class="card-body">
                    <div class="card-title">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div>بحث وفلترة</div>
                            <div>
                                <button type="button" id="clear-filters" class="btn btn-outline-secondary btn-sm">
                                    <i class="fa fa-times"></i> مسح الفلاتر
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <form id="search-form" class="form">
                        <div class="form-body row">
                            <div class="form-group col-md-6">
                                <label for="search-keywords">البحث بكلمة مفتاحية</label>
                                <input type="text"
                                       id="search-keywords"
                                       class="form-control"
                                       placeholder="إبحث بواسطة إسم الوردية"
                                       autocomplete="off">
                            </div>

                            <div class="form-group col-md-3">
                                <label for="filter-type">نوع الوردية</label>
                                <select id="filter-type" class="form-control">
                                    <option value="">الكل</option>
                                    <option value="1">أساسي</option>
                                    <option value="2">متقدم</option>
                                </select>
                            </div>

                            <div class="form-group col-md-3">
                                <label for="filter-days">حسب أيام العمل</label>
                                <select id="filter-days" class="form-control">
                                    <option value="">الكل</option>
                                    <option value="5">5 أيام عمل</option>
                                    <option value="6">6 أيام عمل</option>
                                    <option value="7">7 أيام عمل</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="button" id="search-btn" class="btn btn-primary mr-1 waves-effect waves-light">
                                <i class="fa fa-search"></i> بحث
                            </button>
                            <button type="button" id="export-btn" class="btn btn-outline-success waves-effect waves-light">
                                <i class="fa fa-download"></i> تصدير
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- جدول الورديات -->
        <div class="card">
            <div class="card-body">
                <!-- مؤشر التحميل -->
                <div id="loading-indicator" class="text-center" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">جارٍ التحميل...</span>
                    </div>
                    <p>جارٍ البحث...</p>
                </div>

                <!-- منطقة النتائج -->
                <div id="shifts-table-container">
                    @include('hr::shift_management.partials.shifts_table', ['shifts' => $shifts])
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
class ShiftIndexManager {
    constructor() {
        this.searchForm = document.getElementById('search-form');
        this.searchKeywords = document.getElementById('search-keywords');
        this.filterType = document.getElementById('filter-type');
        this.filterDays = document.getElementById('filter-days');
        this.searchBtn = document.getElementById('search-btn');
        this.clearBtn = document.getElementById('clear-filters');
        this.exportBtn = document.getElementById('export-btn');
        this.tableContainer = document.getElementById('shifts-table-container');
        this.loadingIndicator = document.getElementById('loading-indicator');
        this.totalShiftsSpan = document.getElementById('total-shifts');

        this.currentFilters = {};
        this.searchTimeout = null;

        this.init();
    }

    init() {
        this.bindEvents();
        this.loadUrlParameters();
    }

    bindEvents() {
        // البحث الفوري عند الكتابة
        this.searchKeywords.addEventListener('input', () => {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                this.performSearch();
            }, 500);
        });

        // الفلترة عند تغيير الخيارات
        this.filterType.addEventListener('change', () => this.performSearch());
        this.filterDays.addEventListener('change', () => this.performSearch());

        // زر البحث
        this.searchBtn.addEventListener('click', (e) => {
            e.preventDefault();
            this.performSearch();
        });

        // زر مسح الفلاتر
        this.clearBtn.addEventListener('click', () => this.clearFilters());

        // زر التصدير
        this.exportBtn.addEventListener('click', () => this.exportData());

        // منع إرسال الفورم
        this.searchForm.addEventListener('submit', (e) => e.preventDefault());
    }

    loadUrlParameters() {
        const urlParams = new URLSearchParams(window.location.search);

        if (urlParams.get('keywords')) {
            this.searchKeywords.value = urlParams.get('keywords');
        }
        if (urlParams.get('type')) {
            this.filterType.value = urlParams.get('type');
        }
        if (urlParams.get('days')) {
            this.filterDays.value = urlParams.get('days');
        }

        // تنفيذ البحث إذا كان هناك معاملات في URL
        if (urlParams.toString()) {
            this.performSearch();
        }
    }

    async performSearch() {
        this.showLoading();

        const filters = {
            keywords: this.searchKeywords.value.trim(),
            type: this.filterType.value,
            days: this.filterDays.value
        };

        this.currentFilters = filters;
        this.updateUrlParameters(filters);

        try {
            const response = await fetch('{{ route("shift_management.search") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(filters)
            });

            if (!response.ok) {
                throw new Error('خطأ في الشبكة');
            }

            const data = await response.json();
            this.updateTable(data.html);
            this.updateCounter(data.count);

        } catch (error) {
            console.error('خطأ في البحث:', error);
            this.showError('حدث خطأ أثناء البحث. يرجى المحاولة مرة أخرى.');
        } finally {
            this.hideLoading();
        }
    }

    updateTable(html) {
        this.tableContainer.innerHTML = html;
        this.bindTableEvents();
    }

    updateCounter(count) {
        this.totalShiftsSpan.textContent = count;
    }

    bindTableEvents() {
        // ربط أحداث الحذف مع SweetAlert2
        const deleteButtons = this.tableContainer.querySelectorAll('.delete-shift-btn');
        deleteButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const shiftId = btn.getAttribute('data-shift-id');
                const shiftName = btn.getAttribute('data-shift-name');
                this.confirmDelete(shiftId, shiftName);
            });
        });
    }

    confirmDelete(shiftId, shiftName) {
        Swal.fire({
            title: 'هل أنت متأكد؟',
            html: `سيتم حذف الوردية <strong>"${shiftName}"</strong><br>هذا الإجراء لا يمكن التراجع عنه`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'نعم، احذف',
            cancelButtonText: 'إلغاء',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return this.deleteShift(shiftId);
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    icon: 'success',
                    title: 'تم الحذف!',
                    text: 'تم حذف الوردية بنجاح',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        });
    }

    async deleteShift(shiftId) {
        try {
            const response = await fetch(`{{ route('shift_management.delete', '') }}/${shiftId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            if (!response.ok) {
                throw new Error('خطأ في الحذف');
            }

            // تحديث الجدول بعد الحذف
            this.performSearch();

            return true;
        } catch (error) {
            console.error('خطأ في الحذف:', error);
            Swal.fire({
                icon: 'error',
                title: 'خطأ في الحذف',
                text: 'حدث خطأ أثناء حذف الوردية. يرجى المحاولة مرة أخرى.'
            });
            throw error;
        }
    }

    clearFilters() {
        this.searchKeywords.value = '';
        this.filterType.value = '';
        this.filterDays.value = '';
        this.currentFilters = {};

        // تحديث URL
        window.history.pushState({}, '', window.location.pathname);

        // إعادة تحميل البيانات
        this.performSearch();
    }

    async exportData() {
        try {
            Swal.fire({
                title: 'جارٍ التصدير...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const response = await fetch('{{ route("shift_management.export") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(this.currentFilters)
            });

            if (!response.ok) {
                throw new Error('خطأ في التصدير');
            }

            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `الورديات_${new Date().toISOString().split('T')[0]}.xlsx`;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);

            Swal.fire({
                icon: 'success',
                title: 'تم التصدير!',
                text: 'تم تصدير البيانات بنجاح',
                timer: 2000,
                showConfirmButton: false
            });

        } catch (error) {
            console.error('خطأ في التصدير:', error);
            Swal.fire({
                icon: 'error',
                title: 'خطأ في التصدير',
                text: 'حدث خطأ أثناء تصدير البيانات. يرجى المحاولة مرة أخرى.'
            });
        }
    }

    updateUrlParameters(filters) {
        const params = new URLSearchParams();

        Object.keys(filters).forEach(key => {
            if (filters[key]) {
                params.set(key, filters[key]);
            }
        });

        const newUrl = params.toString() ?
                      `${window.location.pathname}?${params.toString()}` :
                      window.location.pathname;

        window.history.pushState({}, '', newUrl);
    }

    showLoading() {
        this.loadingIndicator.style.display = 'block';
        this.tableContainer.style.opacity = '0.5';
    }

    hideLoading() {
        this.loadingIndicator.style.display = 'none';
        this.tableContainer.style.opacity = '1';
    }

    showError(message) {
        Swal.fire({
            icon: 'error',
            title: 'خطأ',
            text: message,
            timer: 3000,
            showConfirmButton: false
        });
    }
}

// تشغيل الكلاس عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', function() {
    window.shiftIndexManager = new ShiftIndexManager();

    // معالجة رسائل النجاح والخطأ من السيشن
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'تمت العملية بنجاح',
            text: '{{ session('success') }}',
            timer: 3000,
            showConfirmButton: false
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'حدث خطأ',
            text: '{{ session('error') }}',
            timer: 3000,
            showConfirmButton: false
        });
    @endif
});
</script>

<style>
    .table th {
        background-color: #f8f9fa;
        border-top: none;
        font-weight: 600;
        color: #495057;
    }

    .table td {
        vertical-align: middle;
    }

    .badge-days-off {
        background-color: #dc3545;
        color: white;
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        margin: 0.125rem;
        display: inline-block;
    }

    .shift-type-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        border-radius: 1rem;
    }

    .shift-type-basic {
        background-color: #28a745;
        color: white;
    }

    .shift-type-advanced {
        background-color: #007bff;
        color: white;
    }

    .spinner-border {
        width: 3rem;
        height: 3rem;
    }

    #loading-indicator {
        padding: 2rem;
    }

    .btn-group .dropdown-toggle::after {
        display: none;
    }
</style>
@endsection
