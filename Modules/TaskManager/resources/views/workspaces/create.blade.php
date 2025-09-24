@extends('master')

@section('title')
    إضافة مساحة عمل جديدة
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">إضافة مساحة عمل جديدة</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسية</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('workspaces.index') }}">مساحات العمل</a></li>
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
                        <h4 class="card-title">بيانات مساحة العمل</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <!-- النموذج -->
                            <form id="workspace-form">
                                @csrf
                                <div class="row">
                                    <!-- معلومات أساسية -->
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label>اسم مساحة العمل <span class="text-danger">*</span></label>
                                            <input type="text" name="title" class="form-control" placeholder="أدخل اسم مساحة العمل" required>
                                            <small class="form-text text-muted">مثال: قسم التطوير، فريق التسويق، المشاريع الخاصة</small>
                                        </div>

                                        <div class="form-group">
                                            <label>وصف مساحة العمل</label>
                                            <textarea name="description" class="form-control" rows="4" placeholder="وصف تفصيلي لمساحة العمل والغرض منها..."></textarea>
                                        </div>
                                    </div>

                                    <!-- الإعدادات -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" name="is_primary" id="is_primary" value="1">
                                                <label class="form-check-label" for="is_primary">
                                                    مساحة العمل الرئيسية
                                                </label>
                                            </div>
                                            <small class="form-text text-muted">ستظهر هذه المساحة كافتراضية في النظام</small>
                                        </div>

                                        <div class="alert alert-info">
                                            <h6 class="alert-heading">ملاحظة مهمة</h6>
                                            <p class="mb-0">ستكون أنت مالك هذه المساحة تلقائياً. يمكنك إضافة أعضاء عند إنشاء مشاريع جديدة.</p>
                                        </div>

                                        <div class="alert alert-warning">
                                            <h6 class="alert-heading">إدارة الأعضاء</h6>
                                            <p class="mb-0">الأعضاء سيتم إضافتهم تلقائياً عند إنشاء مشاريع وتعيين أعضاء لها. لا حاجة لإضافة أعضاء مباشرة لمساحة العمل.</p>
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
                                                <i class="feather icon-save"></i> حفظ مساحة العمل
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
    // إرسال النموذج
    $('#workspace-form').on('submit', function(e) {
        e.preventDefault();

        let submitBtn = $(this).find('button[type="submit"]');

        // تحضير البيانات
        let formData = {
            title: $('input[name="title"]').val(),
            description: $('textarea[name="description"]').val(),
            is_primary: $('#is_primary').is(':checked') ? '1' : '0',
            _token: $('input[name="_token"]').val()
        };

        // تغيير حالة الزر
        submitBtn.prop('disabled', true).html('<i class="feather icon-loader"></i> جاري الحفظ...');

        $.ajax({
            url: '{{ route("workspaces.store") }}',
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
                    text: response.message,
                    icon: 'success',
                    confirmButtonText: 'موافق',
                    confirmButtonColor: '#28a745'
                }).then((result) => {
                    if (response.data && response.data.redirect_url) {
                        window.location.href = response.data.redirect_url;
                    } else {
                        window.location.href = '{{ route("workspaces.index") }}';
                    }
                });
            } else {
                Swal.fire({
                    title: 'خطأ!',
                    text: response.message,
                    icon: 'error',
                    confirmButtonText: 'موافق',
                    confirmButtonColor: '#dc3545'
                });
            }
        })
        .fail(function(xhr) {
            let errorMessage = 'حدث خطأ أثناء حفظ مساحة العمل';

            if (xhr.status === 422) {
                let errors = xhr.responseJSON.errors;
                errorMessage = 'يرجى تصحيح الأخطاء التالية:\n';
                for (let field in errors) {
                    errorMessage += '• ' + errors[field][0] + '\n';
                }
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
            submitBtn.prop('disabled', false).html('<i class="feather icon-save"></i> حفظ مساحة العمل');
        });
    });

    // تحديث حالة Checkbox
    $('#is_primary').change(function() {
        if ($(this).is(':checked')) {
            Swal.fire({
                title: 'تنبيه',
                text: 'سيتم إلغاء كون أي مساحة أخرى رئيسية تلقائياً',
                icon: 'info',
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