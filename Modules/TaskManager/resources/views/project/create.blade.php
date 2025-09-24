@extends('master')

@section('title')
    إضافة مشروع جديد
@stop
@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/task.css') }}">
@endsection
@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">إضافة مشروع جديد</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسية</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('projects.index') }}">المشاريع</a></li>
                            <li class="breadcrumb-item active">إضافة</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">بيانات المشروع</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <!-- النموذج -->
                            <form id="project-form">
                                @csrf
                                <div class="row">
                                    <!-- معلومات أساسية -->
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label>اسم المشروع <span class="text-danger">*</span></label>
                                            <input type="text" name="title" class="form-control" placeholder="أدخل اسم المشروع" required>
                                            <small class="form-text text-muted">مثال: تطوير موقع الشركة، حملة تسويقية، نظام إدارة المحتوى</small>
                                        </div>

                                        <div class="form-group">
                                            <label>وصف المشروع</label>
                                            <textarea name="description" class="form-control" rows="4" placeholder="وصف تفصيلي للمشروع والأهداف المطلوب تحقيقها..."></textarea>
                                        </div>
                                    </div>

                                    <!-- الإعدادات الأساسية -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>مساحة العمل <span class="text-danger">*</span></label>
                                            <select name="workspace_id" class="form-control select2" required>
                                                <option value="">اختر مساحة العمل</option>
                                                @foreach($workspaces as $workspace)
                                                    <option value="{{ $workspace->id }}">{{ $workspace->title }}</option>
                                                @endforeach
                                            </select>
                                            <small class="form-text text-muted">المساحة التي سيتم تخصيص المشروع لها</small>
                                        </div>

                                        <div class="alert alert-info">
                                            <h6 class="alert-heading">ملاحظة مهمة</h6>
                                            <p class="mb-0">تأكد من اختيار التواريخ والميزانية بدقة لضمان تتبع دقيق للمشروع.</p>
                                        </div>
                                    </div>

                                    <!-- إعدادات المشروع -->
                                    <div class="col-md-6">
                                        <hr>
                                        <h5>إعدادات المشروع</h5>

                                        <div class="form-group">
                                            <label>الحالة</label>
                                            <select name="status" class="form-control select2">
                                                <option value="new" selected>جديد</option>
                                                <option value="in_progress">قيد التنفيذ</option>
                                                <option value="on_hold">متوقف</option>
                                                <option value="completed">مكتمل</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label>الأولوية</label>
                                            <select name="priority" class="form-control select2">
                                                <option value="low">منخفض</option>
                                                <option value="medium" selected>متوسط</option>
                                                <option value="high">عالي</option>
                                                <option value="urgent">عاجل</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- التواريخ والميزانية -->
                                    <div class="col-md-6">
                                        <hr class="d-md-none">
                                        <h5>التواريخ والميزانية</h5>

                                        <div class="form-group">
                                            <label>تاريخ البداية <span class="text-danger">*</span></label>
                                            <input type="date" name="start_date" class="form-control" required>
                                        </div>

                                        <div class="form-group">
                                            <label>تاريخ النهاية <span class="text-danger">*</span></label>
                                            <input type="date" name="end_date" class="form-control" required>
                                        </div>

                                        <div class="row">
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label>الميزانية</label>
                                                    <input type="number" name="budget" class="form-control" step="0.01" min="0" placeholder="0.00">
                                                    <small class="form-text text-muted">بالريال السعودي</small>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label>التكلفة الحالية</label>
                                                    <input type="number" name="cost" class="form-control" step="0.01" min="0" placeholder="0.00" value="0">
                                                    <small class="form-text text-muted">بالريال السعودي</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- أعضاء الفريق -->
                                    <div class="col-12">
                                        <hr>
                                        <h5>إضافة أعضاء الفريق</h5>
                                        <p class="text-muted">يمكنك اختيار الأعضاء الذين سيعملون على هذا المشروع</p>

                                        <div class="form-group">
                                            <label>أعضاء الفريق</label>
                                            <select name="team_members[]" class="form-control select2" multiple>
                                                @foreach($users as $user)
                                                    <option value="{{ $user->id }}">{{ $user->name }} - {{ $user->email }}</option>
                                                @endforeach
                                            </select>
                                            <small class="form-text text-muted">يمكنك اختيار عدة أعضاء أو تركه فارغاً وإضافة الأعضاء لاحقاً</small>
                                        </div>
                                    </div>

                                    <!-- أزرار الحفظ -->
                                    <div class="col-12">
                                        <hr>
                                        <div class="form-group text-right">
                                            <button type="button" class="btn btn-outline-secondary" onclick="window.history.back()">
                                                <i class="feather icon-x"></i> إلغاء
                                            </button>
                                            <button type="submit" class="btn btn-primary ml-1">
                                                <i class="feather icon-save"></i> حفظ المشروع
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // تفعيل Select2 لجميع العناصر
    $('.select2').select2({
        width: '100%',
        allowClear: true
    });

    // تخصيص Select2 لأعضاء الفريق
    $('select[name="team_members[]"]').select2({
        placeholder: 'اختر أعضاء الفريق',
        allowClear: true,
        width: '100%'
    });

    // تخصيص Select2 لمساحة العمل
    $('select[name="workspace_id"]').select2({
        placeholder: 'اختر مساحة العمل',
        allowClear: false,
        width: '100%'
    });

    // التحقق من تواريخ المشروع
    $('input[name="start_date"], input[name="end_date"]').on('change', function() {
        let startDate = new Date($('input[name="start_date"]').val());
        let endDate = new Date($('input[name="end_date"]').val());

        if (startDate && endDate && startDate >= endDate) {
            Swal.fire({
                title: 'تنبيه',
                text: 'تاريخ النهاية يجب أن يكون بعد تاريخ البداية',
                icon: 'warning',
                confirmButtonText: 'موافق',
                confirmButtonColor: '#ffc107'
            });
            $(this).val('');
        }
    });

    // إرسال النموذج
    $('#project-form').on('submit', function(e) {
        e.preventDefault();

        let submitBtn = $(this).find('button[type="submit"]');

        // التحقق من البيانات المطلوبة
        if (!$('input[name="title"]').val().trim()) {
            Swal.fire({
                title: 'خطأ!',
                text: 'يرجى إدخال اسم المشروع',
                icon: 'error',
                confirmButtonText: 'موافق',
                confirmButtonColor: '#dc3545'
            });
            return;
        }

        if (!$('select[name="workspace_id"]').val()) {
            Swal.fire({
                title: 'خطأ!',
                text: 'يرجى اختيار مساحة العمل',
                icon: 'error',
                confirmButtonText: 'موافق',
                confirmButtonColor: '#dc3545'
            });
            return;
        }

        // تحضير البيانات
        let formData = {
            title: $('input[name="title"]').val().trim(),
            description: $('textarea[name="description"]').val().trim(),
            workspace_id: $('select[name="workspace_id"]').val(),
            status: $('select[name="status"]').val(),
            priority: $('select[name="priority"]').val(),
            start_date: $('input[name="start_date"]').val(),
            end_date: $('input[name="end_date"]').val(),
            budget: $('input[name="budget"]').val() || 0,
            cost: $('input[name="cost"]').val() || 0,
            team_members: $('select[name="team_members[]"]').val() || [],
            _token: $('input[name="_token"]').val()
        };

        // تغيير حالة الزر
        submitBtn.prop('disabled', true).html('<i class="feather icon-loader"></i> جاري الحفظ...');

        $.ajax({
            url: '{{ route("projects.store") }}',
            method: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        .done(function(response) {
            if (response.success) {
                Swal.fire({
                    title: 'تم بنجاح!',
                    text: response.message || 'تم إنشاء المشروع بنجاح',
                    icon: 'success',
                    confirmButtonText: 'موافق',
                    confirmButtonColor: '#28a745'
                }).then((result) => {
                    if (response.data && response.data.redirect_url) {
                        window.location.href = response.data.redirect_url;
                    } else {
                        window.location.href = '{{ route("projects.index") }}';
                    }
                });
            } else {
                Swal.fire({
                    title: 'خطأ!',
                    text: response.message || 'حدث خطأ أثناء إنشاء المشروع',
                    icon: 'error',
                    confirmButtonText: 'موافق',
                    confirmButtonColor: '#dc3545'
                });
            }
        })
        .fail(function(xhr) {
            console.error('=== تفاصيل كاملة للخطأ ===');
            console.error('Status:', xhr.status);
            console.error('Response:', xhr.responseJSON);
            console.error('Raw Text:', xhr.responseText);

            let errorTitle = 'خطأ في النظام!';
            let errorContent = '';

            if (xhr.status === 500 && xhr.responseJSON) {
                const response = xhr.responseJSON;

                errorTitle = response.message || 'خطأ في الخادم';

                // بناء محتوى الخطأ المفصل
                errorContent += '<div class="error-details" style="text-align: right; direction: rtl;">';

                // تحليل الخطأ إذا كان متاحاً
                if (response.error_analysis) {
                    const analysis = response.error_analysis;

                    errorContent += '<div class="alert alert-danger mb-3">';
                    errorContent += '<h6><i class="fas fa-exclamation-triangle"></i> تحليل الخطأ:</h6>';

                    if (analysis.error_type === 'Missing Column') {
                        errorContent += '<p><strong>نوع الخطأ:</strong> عمود مفقود في قاعدة البيانات</p>';
                        errorContent += '<p><strong>العمود المفقود:</strong> <code>' + analysis.missing_column + '</code></p>';
                        errorContent += '<p><strong>المكان:</strong> ' + analysis.location + '</p>';
                        errorContent += '<p><strong>الحل المقترح:</strong> ' + analysis.suggestion + '</p>';

                    } else if (analysis.error_type === 'Missing Table') {
                        errorContent += '<p><strong>نوع الخطأ:</strong> جدول مفقود في قاعدة البيانات</p>';
                        errorContent += '<p><strong>الجدول المفقود:</strong> <code>' + analysis.missing_table + '</code></p>';
                        errorContent += '<p><strong>الحل المقترح:</strong> ' + analysis.suggestion + '</p>';

                    } else if (analysis.error_type === 'Foreign Key Constraint') {
                        errorContent += '<p><strong>نوع الخطأ:</strong> خطأ في القيود الخارجية</p>';
                        errorContent += '<p><strong>الجدول:</strong> ' + analysis.table + '</p>';
                        errorContent += '<p><strong>المفتاح الخارجي:</strong> <code>' + analysis.foreign_key + '</code></p>';
                        errorContent += '<p><strong>يشير إلى:</strong> ' + analysis.referenced_table + '.' + analysis.referenced_column + '</p>';
                        errorContent += '<p><strong>المشكلة:</strong> ' + analysis.suggestion + '</p>';
                    }
                    errorContent += '</div>';
                }

                // هيكل الجدول
                if (response.table_structure && response.table_structure.length) {
                    errorContent += '<div class="mb-3">';
                    errorContent += '<h6><i class="fas fa-table"></i> هيكل جدول project_user الحالي:</h6>';
                    errorContent += '<div style="max-height: 200px; overflow-y: auto; background: #f8f9fa; padding: 10px; border-radius: 5px;">';
                    errorContent += '<table class="table table-sm table-bordered">';
                    errorContent += '<thead><tr><th>العمود</th><th>النوع</th><th>يقبل NULL</th><th>المفتاح</th></tr></thead>';
                    errorContent += '<tbody>';

                    response.table_structure.forEach(function(column) {
                        errorContent += '<tr>';
                        errorContent += '<td><code>' + column.field + '</code></td>';
                        errorContent += '<td>' + column.type + '</td>';
                        errorContent += '<td>' + column.null + '</td>';
                        errorContent += '<td>' + (column.key || '-') + '</td>';
                        errorContent += '</tr>';
                    });

                    errorContent += '</tbody></table>';
                    errorContent += '</div>';
                    errorContent += '</div>';
                }

                // معلومات التصحيح
                if (response.debug_info) {
                    errorContent += '<div class="mb-3">';
                    errorContent += '<h6><i class="fas fa-bug"></i> معلومات التصحيح:</h6>';
                    errorContent += '<div style="background: #f1f3f4; padding: 10px; border-radius: 5px; font-family: monospace; font-size: 12px;">';
                    errorContent += '<p><strong>الملف:</strong> ' + response.debug_info.file + '</p>';
                    errorContent += '<p><strong>السطر:</strong> ' + response.debug_info.line + '</p>';
                    errorContent += '<p><strong>الوقت:</strong> ' + response.debug_info.timestamp + '</p>';

                    if (response.debug_info.request_summary) {
                        const summary = response.debug_info.request_summary;
                        errorContent += '<p><strong>أعضاء الفريق:</strong> ' + JSON.stringify(summary.team_members) + '</p>';
                        errorContent += '<p><strong>عدد الأعضاء:</strong> ' + summary.team_members_count + '</p>';
                        errorContent += '<p><strong>معرف المستخدم:</strong> ' + summary.auth_user_id + '</p>';
                    }
                    errorContent += '</div>';
                    errorContent += '</div>';
                }

                // تفاصيل SQL إذا كانت متاحة
                if (response.error_analysis && response.error_analysis.sql_details) {
                    const sql = response.error_analysis.sql_details;
                    errorContent += '<details class="mb-3">';
                    errorContent += '<summary><strong><i class="fas fa-database"></i> تفاصيل استعلام SQL</strong></summary>';
                    errorContent += '<div style="background: #fff3cd; padding: 10px; border-radius: 5px; margin-top: 10px;">';
                    errorContent += '<p><strong>الاستعلام:</strong></p>';
                    errorContent += '<pre style="font-size: 11px; background: white; padding: 8px; border: 1px solid #ddd;">' + sql.query + '</pre>';
                    errorContent += '<p><strong>المتغيرات:</strong> ' + JSON.stringify(sql.bindings) + '</p>';
                    errorContent += '<p><strong>كود الخطأ:</strong> ' + sql.error_code + '</p>';
                    errorContent += '</div>';
                    errorContent += '</details>';
                }

                errorContent += '</div>';

            } else {
                // خطأ عام
                errorContent = '<div class="text-center"><p>' + (xhr.responseJSON?.message || 'حدث خطأ غير متوقع') + '</p></div>';
            }

            Swal.fire({
                title: errorTitle,
                html: errorContent,
                icon: 'error',
                width: '900px',
                confirmButtonText: 'فهمت - سأقوم بالإصلاح',
                confirmButtonColor: '#dc3545',
                customClass: {
                    popup: 'error-popup-developer'
                },
                didOpen: function() {
                    // إضافة أنماط CSS للنافذة
                    const style = document.createElement('style');
                    style.textContent = `
                        .error-popup-developer .swal2-html-container {
                            max-height: 600px;
                            overflow-y: auto;
                            text-align: right;
                        }
                        .error-popup-developer code {
                            background: #f1f1f1;
                            padding: 2px 4px;
                            border-radius: 3px;
                            color: #d63384;
                        }
                    `;
                    document.head.appendChild(style);
                }
            });
        })
        .always(function() {
            submitBtn.prop('disabled', false).html('<i class="feather icon-save"></i> حفظ المشروع');
        });
    });

    // تحديث التكلفة تلقائياً عند تغيير الميزانية
    $('input[name="budget"]').on('input', function() {
        let budget = parseFloat($(this).val()) || 0;
        let cost = parseFloat($('input[name="cost"]').val()) || 0;

        if (cost > budget && budget > 0) {
            Swal.fire({
                title: 'تنبيه',
                text: 'التكلفة الحالية أكبر من الميزانية المحددة',
                icon: 'warning',
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        }
    });

    // تعيين تاريخ اليوم كافتراضي لتاريخ البداية
    let today = new Date().toISOString().split('T')[0];
    $('input[name="start_date"]').val(today);
});
</script>
@endsection
