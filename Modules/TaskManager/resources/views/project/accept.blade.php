@extends('master')

@section('title', 'قبول دعوة المشروع')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-gradient-primary text-white text-center py-4">
                    <h3 class="mb-0">
                        <i class="fas fa-envelope-open-text me-2"></i>
                        دعوة للانضمام إلى مشروع
                    </h3>
                </div>

                <div class="card-body p-4">
                    <!-- معلومات الدعوة -->
                    <div class="text-center mb-4">
                        <div class="alert alert-info border-0 shadow-sm">
                            <h5 class="alert-heading mb-3">
                                <i class="fas fa-project-diagram text-primary"></i>
                                {{ $invite->project_title }}
                            </h5>

                            <div class="row text-start">
                                <div class="col-md-6">
                                    <p class="mb-2">
                                        <strong>مساحة العمل:</strong> {{ $invite->workspace_title }}
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-2">
                                        <strong>دورك:</strong>
                                        <span class="badge bg-primary">{{ $roleText }}</span>
                                    </p>
                                </div>
                                <div class="col-12">
                                    <p class="mb-2">
                                        <strong>دعوة من:</strong> {{ $invite->inviter_name }}
                                    </p>
                                </div>
                            </div>

                            @if($invite->project_description)
                                <div class="mt-3 p-3 bg-light rounded">
                                    <small class="text-muted">{{ $invite->project_description }}</small>
                                </div>
                            @endif

                            @if($invite->invite_message)
                                <div class="mt-3 p-3 bg-warning-subtle border border-warning rounded">
                                    <strong>رسالة خاصة:</strong><br>
                                    <em>{{ $invite->invite_message }}</em>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- نموذج قبول الدعوة -->
                    <form id="acceptInviteForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="name" class="form-label">
                                    <i class="fas fa-user text-primary me-1"></i>
                                    الاسم الكامل <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control form-control-lg" id="name" name="name" required>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock text-primary me-1"></i>
                                    كلمة المرور <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="password" class="form-control form-control-lg" id="password" name="password" required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                                        <i class="fas fa-eye" id="password-icon"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback"></div>
                                <small class="form-text text-muted">يجب أن تكون 8 أحرف على الأقل</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">
                                    <i class="fas fa-lock text-primary me-1"></i>
                                    تأكيد كلمة المرور <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="password" class="form-control form-control-lg" id="password_confirmation" name="password_confirmation" required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation')">
                                        <i class="fas fa-eye" id="password_confirmation-icon"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-12 mb-4">
                                <label for="phone" class="form-label">
                                    <i class="fas fa-phone text-primary me-1"></i>
                                    رقم الهاتف (اختياري)
                                </label>
                                <input type="tel" class="form-control form-control-lg" id="phone" name="phone" placeholder="مثال: +966501234567">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <!-- أزرار العمل -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                            <button type="submit" class="btn btn-success btn-lg px-5" id="acceptBtn">
                                <i class="fas fa-check-circle me-2"></i>
                                قبول الدعوة والانضمام
                                <span class="spinner-border spinner-border-sm ms-2 d-none" id="acceptSpinner"></span>
                            </button>

                            <a href="{{ url("/projects/invite/{$invite->invite_token}/decline") }}"
                               class="btn btn-outline-danger btn-lg px-4"
                               onclick="return confirm('هل أنت متأكد من رفض الدعوة؟')">
                                <i class="fas fa-times-circle me-2"></i>
                                رفض الدعوة
                            </a>
                        </div>

                        <!-- تحذير انتهاء الصلاحية -->
                        <div class="mt-4 p-3 bg-warning-subtle border border-warning rounded text-center">
                            <small class="text-warning-emphasis">
                                <i class="fas fa-clock me-1"></i>
                                <strong>تنبيه:</strong> تنتهي صلاحية هذه الدعوة في
                                <span class="fw-bold">{{ \Carbon\Carbon::parse($invite->expires_at)->format('Y-m-d H:i') }}</span>
                            </small>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript للتعامل مع النموذج -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('acceptInviteForm');
    const acceptBtn = document.getElementById('acceptBtn');
    const acceptSpinner = document.getElementById('acceptSpinner');

    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        // تعطيل الزر وإظهار الـ spinner
        acceptBtn.disabled = true;
        acceptSpinner.classList.remove('d-none');

        // مسح الأخطاء السابقة
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');

        try {
            const formData = new FormData(form);
            const response = await fetch('{{ url("/projects/invite/{$invite->invite_token}/accept") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('[name="_token"]').value
                }
            });

            const data = await response.json();

            if (data.success) {
                // إظهار رسالة نجاح
                Swal.fire({
                    title: 'مرحباً بك!',
                    text: data.message,
                    icon: 'success',
                    confirmButtonText: 'متابعة',
                    allowOutsideClick: false
                }).then(() => {
                    window.location.href = data.redirect_url;
                });
            } else {
                throw new Error(data.message);
            }

        } catch (error) {
            console.error('Error:', error);

            if (error.response && error.response.status === 422) {
                // أخطاء التحقق
                const errors = await error.response.json();
                Object.keys(errors.errors || {}).forEach(field => {
                    const input = document.querySelector(`[name="${field}"]`);
                    const feedback = input.parentNode.querySelector('.invalid-feedback');

                    input.classList.add('is-invalid');
                    feedback.textContent = errors.errors[field][0];
                });
            } else {
                // خطأ عام
                Swal.fire({
                    title: 'خطأ!',
                    text: error.message || 'حدث خطأ غير متوقع',
                    icon: 'error',
                    confirmButtonText: 'موافق'
                });
            }
        } finally {
            // إعادة تفعيل الزر وإخفاء الـ spinner
            acceptBtn.disabled = false;
            acceptSpinner.classList.add('d-none');
        }
    });
});

function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '-icon');

    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>
@endsection
