<div class="widget rtl-text">
    <div class="widget-header widget-header-wrapper">
        <h5 class="heading">تغيير كلمة السر</h5>
        <i class="heading-icon fas fa-lock"></i>
    </div>

    <div style="padding: 30px;">
        @if (session('success'))
            <div class="alert alert-success rtl-text"
                style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger rtl-text"
                style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('changePassword') }}" method="POST" class="rtl-text">
            @csrf

            <div class="form-group" style="margin-bottom: 25px;">
                <label for="current_password"
                    style="display: block; margin-bottom: 8px; font-weight: 200; color: #333;">كلمة السر الحالية
                    *</label>
                <input type="password" id="current_password" name="current_password" required class="rtl-text"
                    style="width: 100%; padding: 12px 15px; border: 2px solid #e9ecef; border-radius: 8px; font-size: 14px; transition: border-color 0.3s ease;"
                    placeholder="أدخل كلمة السر الحالية">
                @error('current_password')
                    <small style="color: #dc3545; font-size: 12px;">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group" style="margin-bottom: 25px;">
                <label for="new_password"
                    style="display: block; margin-bottom: 8px; font-weight: 200; color: #333;">كلمة السر الجديدة
                    *</label>
                <input type="password" id="new_password" name="new_password" required class="rtl-text"
                    style="width: 100%; padding: 12px 15px; border: 2px solid #e9ecef; border-radius: 8px; font-size: 14px; transition: border-color 0.3s ease;"
                    placeholder="أدخل كلمة السر الجديدة">
                @error('new_password')
                    <small style="color: #dc3545; font-size: 12px;">{{ $message }}</small>
                @enderror
                <small style="color: #666; font-size: 12px;">يجب أن تحتوي على 8 أحرف على الأقل</small>
            </div>

            <div class="form-group" style="margin-bottom: 25px;">
                <label for="confirm_password"
                    style="display: block; margin-bottom: 8px; font-weight: 200; color: #333;">تأكيد كلمة السر الجديدة
                    *</label>
                <input type="password" id="confirm_password" name="confirm_password" required class="rtl-text"
                    style="width: 100%; padding: 12px 15px; border: 2px solid #e9ecef; border-radius: 8px; font-size: 14px; transition: border-color 0.3s ease;"
                    placeholder="أعد إدخال كلمة السر الجديدة">
                @error('confirm_password')
                    <small style="color: #dc3545; font-size: 12px;">{{ $message }}</small>
                @enderror
            </div>

            <div class="password-strength" style="margin-bottom: 25px;">
                <div class="strength-meter"
                    style="height: 4px; background: #e9ecef; border-radius: 2px; margin-bottom: 10px;">
                    <div class="strength-bar"
                        style="height: 100%; width: 0%; background: #dc3545; border-radius: 2px; transition: all 0.3s ease;">
                    </div>
                </div>
                <div class="strength-text" style="font-size: 12px; color: #666;">قوة كلمة السر: ضعيفة</div>
            </div>

            <div class="form-actions" style="display: flex; gap: 15px; justify-content: flex-start;">
                <button type="submit" class="btn btn-primary"
                    style="background: #1a365d; color: white; padding: 12px 30px; border: none; border-radius: 8px; font-weight: 200; cursor: pointer; transition: all 0.3s ease;">
                    <i class="fas fa-save"></i> حفظ كلمة السر الجديدة
                </button>

                <button type="reset" class="btn btn-secondary"
                    style="background: #6c757d; color: white; padding: 12px 30px; border: none; border-radius: 8px; font-weight: 200; cursor: pointer; transition: all 0.3s ease;">
                    <i class="fas fa-undo"></i> إعادة تعيين
                </button>
            </div>
        </form>

        <div class="info-section rtl-text"
            style="margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 8px; border-right: 4px solid #1a365d;">
            <h6 style="color: #1a365d; margin-bottom: 10px; font-weight: 200;">
                <i class="fas fa-shield-alt"></i> نصائح الأمان
            </h6>
            <ul style="margin: 0; padding-right: 20px; color: #666; font-size: 14px; line-height: 1.6;">
                <li>استخدم كلمة سر قوية تحتوي على أحرف كبيرة وصغيرة وأرقام ورموز</li>
                <li>تجنب استخدام معلومات شخصية في كلمة السر</li>
                <li>لا تشارك كلمة السر مع أي شخص آخر</li>
                <li>قم بتغيير كلمة السر بانتظام لضمان الأمان</li>
                <li>استخدم كلمة سر مختلفة لكل حساب</li>
            </ul>
        </div>
    </div>
</div>

<style>
    .form-group input:focus {
        border-color: #1a365d !important;
        outline: none;
        box-shadow: 0 0 0 3px rgba(26, 54, 93, 0.1);
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .alert {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .password-strength .strength-bar.weak {
        background: #dc3545;
        width: 25%;
    }

    .password-strength .strength-bar.medium {
        background: #ffc107;
        width: 50%;
    }

    .password-strength .strength-bar.good {
        background: #28a745;
        width: 75%;
    }

    .password-strength .strength-bar.strong {
        background: #20c997;
        width: 100%;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const newPasswordInput = document.getElementById('new_password');
        const confirmPasswordInput = document.getElementById('confirm_password');
        const strengthBar = document.querySelector('.strength-bar');
        const strengthText = document.querySelector('.strength-text');

        function checkPasswordStrength(password) {
            let strength = 0;
            let strengthLabel = 'ضعيفة';
            let strengthClass = 'weak';

            if (password.length >= 8) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;

            switch (strength) {
                case 0:
                case 1:
                    strengthLabel = 'ضعيفة جداً';
                    strengthClass = 'weak';
                    break;
                case 2:
                    strengthLabel = 'ضعيفة';
                    strengthClass = 'weak';
                    break;
                case 3:
                    strengthLabel = 'متوسطة';
                    strengthClass = 'medium';
                    break;
                case 4:
                    strengthLabel = 'جيدة';
                    strengthClass = 'good';
                    break;
                case 5:
                    strengthLabel = 'قوية';
                    strengthClass = 'strong';
                    break;
            }

            strengthBar.className = `strength-bar ${strengthClass}`;
            strengthText.textContent = `قوة كلمة السر: ${strengthLabel}`;
        }

        function checkPasswordMatch() {
            const newPassword = newPasswordInput.value;
            const confirmPassword = confirmPasswordInput.value;

            if (confirmPassword && newPassword !== confirmPassword) {
                confirmPasswordInput.style.borderColor = '#dc3545';
            } else {
                confirmPasswordInput.style.borderColor = '#e9ecef';
            }
        }

        newPasswordInput.addEventListener('input', function() {
            checkPasswordStrength(this.value);
            checkPasswordMatch();
        });

        confirmPasswordInput.addEventListener('input', checkPasswordMatch);
    });
</script>
