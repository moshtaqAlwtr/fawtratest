<div class="widget rtl-text">
    <div class="widget-header widget-header-wrapper">
        <h5 class="heading">تغيير البريد الإلكتروني</h5>
        <i class="heading-icon fas fa-envelope"></i>
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

        <form action="{{ route('changeEmail') }}" method="POST" class="rtl-text">
            @csrf

            <div class="form-group" style="margin-bottom: 25px;">
                <label for="current_email"
                    style="display: block; margin-bottom: 8px; font-weight: 200; color: #333;">البريد الإلكتروني
                    الحالي</label>
                <input type="email" id="current_email" name="current_email" class="rtl-text"
                    style="width: 100%; padding: 12px 15px; border: 2px solid #e9ecef; border-radius: 8px; font-size: 14px; background: #f8f9fa;"
                    value="{{ auth()->user()->email ?? 'user@example.com' }}" readonly>
                <small style="color: #666; font-size: 12px;">هذا هو بريدك الإلكتروني المسجل حالياً</small>
            </div>

            <div class="form-group" style="margin-bottom: 25px;">
                <label for="new_email" style="display: block; margin-bottom: 8px; font-weight: 200; color: #333;">البريد
                    الإلكتروني الجديد *</label>
                <input type="email" id="new_email" name="new_email" required class="rtl-text"
                    style="width: 100%; padding: 12px 15px; border: 2px solid #e9ecef; border-radius: 8px; font-size: 14px; transition: border-color 0.3s ease;"
                    placeholder="أدخل البريد الإلكتروني الجديد">
                @error('new_email')
                    <small style="color: #dc3545; font-size: 12px;">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group" style="margin-bottom: 25px;">
                <label for="confirm_email"
                    style="display: block; margin-bottom: 8px; font-weight: 200; color: #333;">تأكيد البريد الإلكتروني
                    الجديد *</label>
                <input type="email" id="confirm_email" name="confirm_email" required class="rtl-text"
                    style="width: 100%; padding: 12px 15px; border: 2px solid #e9ecef; border-radius: 8px; font-size: 14px; transition: border-color 0.3s ease;"
                    placeholder="أعد إدخال البريد الإلكتروني الجديد">
                @error('confirm_email')
                    <small style="color: #dc3545; font-size: 12px;">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group" style="margin-bottom: 30px;">
                <label for="password" style="display: block; margin-bottom: 8px; font-weight: 200; color: #333;">كلمة
                    المرور الحالية *</label>
                <input type="password" id="password" name="password" required class="rtl-text"
                    style="width: 100%; padding: 12px 15px; border: 2px solid #e9ecef; border-radius: 8px; font-size: 14px; transition: border-color 0.3s ease;"
                    placeholder="أدخل كلمة المرور الحالية للتأكيد">
                @error('password')
                    <small style="color: #dc3545; font-size: 12px;">{{ $message }}</small>
                @enderror
                <small style="color: #666; font-size: 12px;">مطلوب لتأكيد هويتك</small>
            </div>

            <div class="form-actions" style="display: flex; gap: 15px; justify-content: flex-start;">
                <button type="submit" class="btn btn-primary"
                    style="background: #1a365d; color: white; padding: 12px 30px; border: none; border-radius: 8px; font-weight: 200; cursor: pointer; transition: all 0.3s ease;">
                    <i class="fas fa-save"></i> حفظ التغييرات
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
                <i class="fas fa-info-circle"></i> معلومات مهمة
            </h6>
            <ul style="margin: 0; padding-right: 20px; color: #666; font-size: 14px; line-height: 1.6;">
                <li>سيتم إرسال رسالة تأكيد إلى البريد الإلكتروني الجديد</li>
                <li>يجب تأكيد البريد الإلكتروني الجديد خلال 24 ساعة</li>
                <li>لن يتم تغيير البريد الإلكتروني حتى يتم التأكيد</li>
                <li>تأكد من إدخال بريد إلكتروني صحيح ويمكنك الوصول إليه</li>
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
</style>
