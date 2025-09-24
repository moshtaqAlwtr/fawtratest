@extends('master')

@section('title')
    إعدادات الفروع
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
                        <h2 class="main-title">🏢 إعدادات الفروع</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb breadcrumb-custom">
                                    <li class="breadcrumb-item">
                                        <a href="">🏠 الرئيسية</a>
                                    </li>
                                    <li class="breadcrumb-item active" aria-current="page">
                                        ⚙️ إعدادات الفروع
                                    </li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- فحص وجود الفروع -->
        @if ($branchs->isEmpty())
            <div class="alert alert-warning-custom" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle me-3" style="font-size: 24px; color: #f39c12;"></i>
                    <div>
                        <h6 class="mb-1">⚠️ لا توجد فروع متاحة</h6>
                        <p class="mb-0">يرجى إضافة فروع أولاً قبل تكوين الإعدادات.</p>
                    </div>
                </div>
            </div>
        @else

        <form id="branch-settings-form" method="POST" enctype="multipart/form-data">
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
                            <button type="button" id="save-settings" class="btn btn-save">
                                <i class="fa fa-save me-2"></i> حفظ الإعدادات
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

            <!-- بطاقة اختيار الفرع -->
            <div class="custom-card">
                <div class="card-header-custom">
                    <h5 class="mb-0">🏢 اختيار الفرع</h5>
                </div>
                <div class="card-body-custom">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="branch_id" class="form-label">
                                <i class="fas fa-building me-2"></i>
                                الفرع الرئيسي <span class="required-star">*</span>
                            </label>
                            <select id="branch_id" class="form-control form-select-custom" name="branch_id">
                                <option value="">-- اختر الفرع --</option>
                                @foreach ($branchs as $branch)
                                    <option value="{{ $branch->id }}"
                                            {{ $selectedBranchId == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name ?? 'فرع بدون اسم' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- بطاقة الإعدادات -->
            <div class="custom-card" id="settings-card" style="{{ !isset($settings) ? 'display: none;' : '' }}">
                <div class="card-header-custom">
                    <h5 class="mb-0">⚙️ إعدادات الفرع</h5>
                </div>
                <div class="card-body-custom">
                    <div id="settings-container">
                        @if (isset($settings) && isset($branch))
                            <div class="permissions-container">
                                <h6 class="text-muted mb-4">
                                    <i class="fas fa-cogs me-2"></i>
                                    اختر الإعدادات المناسبة للفرع:
                                </h6>

                                <div class="permissions-grid">
                                    @foreach ($branch->settings as $setting)
                                        <div class="permission-item {{ isset($settings[$setting->key]) && $settings[$setting->key] == 1 ? 'checked' : '' }}"
                                            onclick="toggleCheckbox('setting_{{ $setting->id }}')">
                                            <input type="checkbox"
                                                   class="custom-checkbox"
                                                   id="setting_{{ $setting->id }}"
                                                   name="{{ $setting->key }}"
                                                   value="1"
                                                   {{ isset($settings[$setting->key]) && $settings[$setting->key] == 1 ? 'checked' : '' }}
                                                   onchange="updateItemStyle(this); saveSetting(this);">
                                            <span class="checkmark"></span>
                                            <label class="permission-label" for="setting_{{ $setting->id }}">
                                                <i class="fas fa-toggle-on me-2" style="color: #667eea;"></i>
                                                {{ $setting->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </form>
        @endif
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // عند تغيير الفرع من الـ select
            $('#branch_id').change(function () {
                var branchId = $(this).val();

                if (!branchId) {
                    $('#settings-card').hide();
                    return;
                }

                // إظهار loader
                showLoader();

                $.ajax({
                    url: '{{ route('settings.get') }}',
                    method: 'GET',
                    data: {branch_id: branchId},
                    success: function (response) {
                        var settings = response.settings;
                        var settingsHtml = '';

                        if (settings.length > 0) {
                            settingsHtml += `
                                <div class="permissions-container">
                                    <h6 class="text-muted mb-4">
                                        <i class="fas fa-cogs me-2"></i>
                                        اختر الإعدادات المناسبة للفرع:
                                    </h6>
                                    <div class="permissions-grid">
                            `;

                            settings.forEach(function(setting) {
                                var isChecked = setting.status == 1;
                                var checkedClass = isChecked ? 'checked' : '';
                                var checkedAttr = isChecked ? 'checked' : '';

                                settingsHtml += `
                                    <div class="permission-item ${checkedClass}"
                                        onclick="toggleCheckbox('setting_${setting.id}')">
                                        <input type="checkbox"
                                               class="custom-checkbox"
                                               id="setting_${setting.id}"
                                               name="${setting.key}"
                                               value="1"
                                               ${checkedAttr}
                                               onchange="updateItemStyle(this); saveSetting(this);">
                                        <span class="checkmark"></span>
                                        <label class="permission-label" for="setting_${setting.id}">
                                            <i class="fas fa-toggle-on me-2" style="color: #667eea;"></i>
                                            ${setting.name}
                                        </label>
                                    </div>
                                `;
                            });

                            settingsHtml += '</div></div>';
                        } else {
                            settingsHtml = `
                                <div class="text-center py-4">
                                    <i class="fas fa-info-circle" style="font-size: 48px; color: #6c757d;"></i>
                                    <h6 class="mt-3 text-muted">لا توجد إعدادات متاحة لهذا الفرع</h6>
                                </div>
                            `;
                        }

                        $('#settings-container').html(settingsHtml);
                        $('#settings-card').show();
                        hideLoader();
                    },
                    error: function (xhr, status, error) {
                        console.error("AJAX Error:", error);
                        showErrorMessage('حدث خطأ أثناء تحميل الإعدادات');
                        hideLoader();
                    }
                });
            });

            // حفظ الإعدادات تلقائياً عند التغيير
            function saveSetting(checkbox) {
                var settingKey = $(checkbox).attr('name');
                var status = $(checkbox).prop('checked') ? 1 : 0;
                var branchId = $('#branch_id').val();

                if (!branchId) {
                    showErrorMessage('يرجى اختيار فرع أولاً');
                    return;
                }

                $.ajax({
                    url: '{{ route('branches.settings_store') }}',
                    method: 'POST',
                    data: {
                        branch_id: branchId,
                        [settingKey]: status,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        showSuccessMessage('تم حفظ الإعداد بنجاح');
                    },
                    error: function (xhr, status, error) {
                        console.error("AJAX Error:", error);
                        showErrorMessage('حدث خطأ أثناء حفظ الإعداد');
                        // إعادة الحالة السابقة للـ checkbox
                        $(checkbox).prop('checked', !$(checkbox).prop('checked'));
                        updateItemStyle(checkbox);
                    }
                });
            }

            // جعل الدالة متاحة عالمياً
            window.saveSetting = saveSetting;
        });

        // وظيفة لتبديل حالة الـ checkbox
        function toggleCheckbox(id) {
            const checkbox = document.getElementById(id);
            if (checkbox) {
                checkbox.checked = !checkbox.checked;
                updateItemStyle(checkbox);
                saveSetting(checkbox);
            }
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

        // وظائف مساعدة للرسائل
        function showSuccessMessage(message) {
            // يمكن استبدالها بـ toast notification
            console.log("Success:", message);
        }

        function showErrorMessage(message) {
            // يمكن استبدالها بـ toast notification
            console.error("Error:", message);
            alert(message);
        }

        function showLoader() {
            $('#settings-container').html(`
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">جاري التحميل...</span>
                    </div>
                    <p class="mt-2 text-muted">جاري تحميل الإعدادات...</p>
                </div>
            `);
        }

        function hideLoader() {
            // سيتم استبداله بالمحتوى الجديد
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