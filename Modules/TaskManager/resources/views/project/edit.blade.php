@extends('master')

@section('title')
    تعديل المشروع
@stop
@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/task.css') }}">
@endsection
@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">تعديل المشروع</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسية</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('projects.index') }}">المشاريع</a></li>
                            <li class="breadcrumb-item active">تعديل</li>
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
                                @method('PUT')
                                <div class="row">
                                    <!-- معلومات أساسية -->
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label>اسم المشروع <span class="text-danger">*</span></label>
                                            <input type="text" name="title" class="form-control" value="{{ $project->title }}" placeholder="أدخل اسم المشروع" required>
                                            <small class="form-text text-muted">مثال: تطوير موقع الشركة، حملة تسويقية، نظام إدارة المحتوى</small>
                                        </div>

                                        <div class="form-group">
                                            <label>وصف المشروع</label>
                                            <textarea name="description" class="form-control" rows="4" placeholder="وصف تفصيلي للمشروع والأهداف المطلوب تحقيقها...">{{ $project->description }}</textarea>
                                        </div>
                                    </div>

                                    <!-- الإعدادات الأساسية -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>مساحة العمل <span class="text-danger">*</span></label>
                                            <select name="workspace_id" class="form-control select2" required>
                                                <option value="">اختر مساحة العمل</option>
                                                @foreach($workspaces as $workspace)
                                                    <option value="{{ $workspace->id }}" {{ $project->workspace_id == $workspace->id ? 'selected' : '' }}>{{ $workspace->title }}</option>
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
                                                <option value="new" {{ $project->status == 'new' ? 'selected' : '' }}>جديد</option>
                                                <option value="in_progress" {{ $project->status == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                                                <option value="on_hold" {{ $project->status == 'on_hold' ? 'selected' : '' }}>متوقف</option>
                                                <option value="completed" {{ $project->status == 'completed' ? 'selected' : '' }}>مكتمل</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label>الأولوية</label>
                                            <select name="priority" class="form-control select2">
                                                <option value="low" {{ $project->priority == 'low' ? 'selected' : '' }}>منخفض</option>
                                                <option value="medium" {{ $project->priority == 'medium' ? 'selected' : '' }}>متوسط</option>
                                                <option value="high" {{ $project->priority == 'high' ? 'selected' : '' }}>عالي</option>
                                                <option value="urgent" {{ $project->priority == 'urgent' ? 'selected' : '' }}>عاجل</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- التواريخ والميزانية -->
                                    <div class="col-md-6">
                                        <hr class="d-md-none">
                                        <h5>التواريخ والميزانية</h5>

                                        <div class="form-group">
                                            <label>تاريخ البداية <span class="text-danger">*</span></label>
                                            <input type="date" name="start_date" class="form-control" value="{{ $project->start_date ? $project->start_date->format('Y-m-d') : '' }}" required>
                                        </div>

                                        <div class="form-group">
                                            <label>تاريخ النهاية <span class="text-danger">*</span></label>
                                            <input type="date" name="end_date" class="form-control" value="{{ $project->end_date ? $project->end_date->format('Y-m-d') : '' }}" required>
                                        </div>

                                        <div class="form-group">
                                            <label>تاريخ النهاية الفعلية</label>
                                            <input type="date" name="actual_end_date" class="form-control" value="{{ $project->actual_end_date ? $project->actual_end_date->format('Y-m-d') : '' }}">
                                        </div>

                                        <div class="row">
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label>الميزانية</label>
                                                    <input type="number" name="budget" class="form-control" step="0.01" min="0" value="{{ $project->budget }}" placeholder="0.00">
                                                    <small class="form-text text-muted">بالريال السعودي</small>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label>التكلفة الحالية</label>
                                                    <input type="number" name="cost" class="form-control" step="0.01" min="0" value="{{ $project->cost }}" placeholder="0.00">
                                                    <small class="form-text text-muted">بالريال السعودي</small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>نسبة الإنجاز</label>
                                            <input type="number" name="progress_percentage" class="form-control" min="0" max="100" value="{{ $project->progress_percentage }}">
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
                                                    <option value="{{ $user->id }}" {{ $project->users->contains($user->id) ? 'selected' : '' }}>{{ $user->name }} - {{ $user->email }}</option>
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
            actual_end_date: $('input[name="actual_end_date"]').val(),
            budget: $('input[name="budget"]').val() || 0,
            cost: $('input[name="cost"]').val() || 0,
            progress_percentage: $('input[name="progress_percentage"]').val() || 0,
            team_members: $('select[name="team_members[]"]').val() || [],
            _method: 'PUT',
            _token: $('input[name="_token"]').val()
        };

        // تغيير حالة الزر
        submitBtn.prop('disabled', true).html('<i class="feather icon-loader"></i> جاري الحفظ...');

        $.ajax({
            url: '{{ route("projects.update", $project->id) }}',
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
                    text: response.message || 'تم تحديث المشروع بنجاح',
                    icon: 'success',
                    confirmButtonText: 'موافق',
                    confirmButtonColor: '#28a745'
                }).then((result) => {
                    if (response.data && response.data.redirect_url) {
                        window.location.href = response.data.redirect_url;
                    } else {
                        window.location.href = '{{ route("projects.show", $project->id) }}';
                    }
                });
            } else {
                Swal.fire({
                    title: 'خطأ!',
                    text: response.message || 'حدث خطأ أثناء تحديث المشروع',
                    icon: 'error',
                    confirmButtonText: 'موافق',
                    confirmButtonColor: '#dc3545'
                });
            }
        })
        .fail(function(xhr) {
            let errorMessage = 'حدث خطأ أثناء حفظ المشروع';

            if (xhr.status === 422) {
                let errors = xhr.responseJSON.errors;
                errorMessage = 'يرجى تصحيح الأخطاء التالية:\n';
                for (let field in errors) {
                    errorMessage += '• ' + errors[field][0] + '\n';
                }
            } else if (xhr.status === 500) {
                errorMessage = 'خطأ في الخادم، يرجى المحاولة مرة أخرى';
            }

            Swal.fire({
                title: 'خطأ في البيانات!',
                text: errorMessage,
                icon: 'error',
                confirmButtonText: 'موافق',
                confirmButtonColor: '#dc3545'
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
});
</script>
@endsection