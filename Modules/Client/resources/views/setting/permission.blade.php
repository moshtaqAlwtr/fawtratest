@extends('master')

@section('title')
    صلاحيات العميل
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
                        <h2 class="main-title">⚙️ إعدادات صلاحيات العميل</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb breadcrumb-custom">
                                    <li class="breadcrumb-item">
                                        <a href="">🏠 الرئيسية</a>
                                    </li>
                                    <li class="breadcrumb-item active" aria-current="page">
                                        ➕ إضافة صلاحيات
                                    </li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('clients.store_permission') }}" method="POST" enctype="multipart/form-data">
            @csrf

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
                                <i class="fa fa-save me-2"></i> حفظ الصلاحيات
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

            <!-- بطاقة الصلاحيات -->
            <div class="custom-card">
                <div class="card-header-custom">
                    <h5 class="mb-0">🔐 إدارة الصلاحيات</h5>
                </div>
                <div class="card-body-custom">
                    <div class="permissions-container">
                        <h6 class="text-muted mb-4">
                            <i class="fas fa-users me-2"></i>
                            اختر الصلاحيات المناسبة للعميل:
                        </h6>

                        <div class="permissions-grid">
                            @foreach ($ClientPermissions as $index => $ClientPermission)
                                <div class="permission-item {{ $ClientPermission->is_active ? 'checked' : '' }}"
                                    onclick="toggleCheckbox('setting_{{ $ClientPermission->id }}')">
                                    <input type="checkbox" class="custom-checkbox" id="setting_{{ $ClientPermission->id }}"
                                        name="settings[]" value="{{ $ClientPermission->id }}"
                                        {{ $ClientPermission->is_active ? 'checked' : '' }}
                                        onchange="updateItemStyle(this)">
                                    <span class="checkmark"></span>
                                    <label class="permission-label" for="setting_{{ $ClientPermission->id }}">
                                        <i class="fas fa-key me-2" style="color: #667eea;"></i>
                                        {{ $ClientPermission->name }}
                                    </label>
                                </div>
                            @endforeach
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
