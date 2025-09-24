@extends('master')

@section('title')
    إعدادات فواتير الشراء
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/sitting.css') }}">
@endsection

@section('content')
    <div class="content-wrapper">
        <!-- رأس الصفحة -->
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="main-title">🧾 إعدادات فواتير الشراء</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb breadcrumb-custom">
                                    <li class="breadcrumb-item">
                                        <a href="">🏠 الرئيسية</a>
                                    </li>
                                    <li class="breadcrumb-item active" aria-current="page">
                                        ⚙️ إعدادات فواتير الشراء
                                    </li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('purchase_invoices.settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- بطاقة معلومات الحفظ -->
            <div class="custom-card">
                <div class="card-header-custom">
                    <h5 class="mb-0">💾 إعدادات الحفظ</h5>
                </div>
                <div class="card-body-custom">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div class="required-text">
                            <i class="fas fa-info-circle me-2"></i>
                            الحقول التي عليها علامة <span class="required-star">*</span> إلزامية
                        </div>
                        <div>
                            <button type="submit" class="btn btn-save">
                                <i class="fa fa-save me-2"></i> تحديث الإعدادات
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- رسالة النجاح -->
            @if (Session::has('success'))
                <div class="alert alert-success-custom" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle me-3" style="font-size: 24px;"></i>
                        <p class="mb-0 font-weight-bold">
                            {{ Session::get('success') }}
                        </p>
                    </div>
                </div>
            @endif

            <!-- رسالة الخطأ -->
            @if (Session::has('error'))
                <div class="alert alert-danger" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-circle me-3" style="font-size: 24px;"></i>
                        <p class="mb-0 font-weight-bold">
                            {{ Session::get('error') }}
                        </p>
                    </div>
                </div>
            @endif

            <!-- بطاقة رقم فاتورة الشراء التالي -->
            <div class="custom-card">
                <div class="card-header-custom">
                    <h5 class="mb-0">🔢 رقم فاتورة الشراء التالي</h5>
                </div>
                <div class="card-body-custom">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">رقم فاتورة الشراء التالي <span class="required-star">*</span></label>
                                <input type="number" class="form-control" name="next_invoice_number"
                                       value="{{ old('next_invoice_number', 5) }}"
                                       min="1" required>
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    سيتم إنشاء فواتير الشراء الجديدة بدءاً من هذا الرقم
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- بطاقة خيارات الإعدادات -->
            <div class="custom-card">
                <div class="card-header-custom">
                    <h5 class="mb-0">⚙️ إعدادات فواتير الشراء</h5>
                </div>
                <div class="card-body-custom">
                    <div class="permissions-container">
                        <h6 class="text-muted mb-4">
                            <i class="fas fa-cogs me-2"></i>
                            اختر الإعدادات المناسبة لفواتير الشراء:
                        </h6>

                        <div class="permissions-grid">
                            @forelse ($settings as $setting)
                                <div class="permission-item {{ $setting->is_active ? 'checked' : '' }}"
                                     onclick="toggleCheckbox('{{ $setting->setting_key }}')">
                                    <input type="checkbox" class="custom-checkbox" id="{{ $setting->setting_key }}"
                                           name="settings[]" value="{{ $setting->setting_key }}"
                                           {{ $setting->is_active ? 'checked' : '' }}
                                           onchange="updateItemStyle(this)">
                                    <span class="checkmark"></span>
                                    <label class="permission-label" for="{{ $setting->setting_key }}">
                                        <i class="fas fa-key me-2" style="color: #667eea;"></i>
                                        {{ $setting->setting_name }}
                                    </label>
                                </div>
                            @empty
                                <div class="text-center py-4">
                                    <i class="fas fa-info-circle text-muted" style="font-size: 48px;"></i>
                                    <p class="text-muted mt-2">لا توجد إعدادات متاحة</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        // وظيفة لتبديل حالة الـ checkbox
        function toggleCheckbox(id) {
            const checkbox = document.getElementById(id);
            checkbox.checked = !checkbox.checked;
            updateItemStyle(checkbox);
        }

        // وظيفة لتحديث مظهر العنصر
        function updateItemStyle(checkbox) {
            const item = checkbox.closest('.permission-item');
            if (checkbox.checked) {
                item.classList.add('checked');
            } else {
                item.classList.remove('checked');
            }
        }

        // تهيئة المظهر عند تحميل الصفحة
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('.custom-checkbox');
            checkboxes.forEach(checkbox => {
                updateItemStyle(checkbox);
            });
        });
    </script>

@endsection
