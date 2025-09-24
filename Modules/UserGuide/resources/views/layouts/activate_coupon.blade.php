<!-- Activate Coupon Widget -->
<div class="widget">
    <div class="widget-header widget-header-wrapper">
        <h5 class="heading">تفعيل قسيمة</h5>
        <i class="heading-icon fas fa-ticket-alt"></i>
    </div>
    <div class="widget-content" style="padding: 40px;">
        <div class="coupon-form" style="max-width: 500px; margin: 0 auto; text-align: center;">
            <div class="form-icon" style="margin-bottom: 30px;">
                <i class="fas fa-gift" style="font-size: 64px; color: #6366f1; margin-bottom: 20px;"></i>
                <h4 style="color: #333; margin-bottom: 10px;">أدخل رمز القسيمة</h4>
                <p style="color: #666; margin-bottom: 30px;">أدخل رمز القسيمة الخاص بك لتفعيل المكافآت والخصومات</p>
            </div>

            <form action="{{ route('activateCoupon') }}" method="POST" style="margin-bottom: 30px;">
                @csrf
                <div class="input-group" style="margin-bottom: 20px;">
                    <input type="text" name="coupon_code" class="form-control" placeholder="أدخل رمز القسيمة"
                        required
                        style="padding: 15px; font-size: 16px; border: 2px solid #e0e0e0; border-radius: 8px 0 0 8px; text-align: center; direction: ltr;">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary"
                            style="background: #6366f1; border: 2px solid #6366f1;  border-radius: 0 8px 8px 0; font-weight: 600; padding:revert">
                            <i class="fas fa-check"></i> تفعيل
                        </button>
                    </div>
                </div>
            </form>

            <div class="coupon-info"
                style="background: #f8f9fa; padding: 20px; border-radius: 8px; border-right: 4px solid #6366f1;">
                <h6 style="color: #6366f1; margin-bottom: 15px;">
                    <i class="fas fa-info-circle"></i> معلومات مهمة
                </h6>
                <ul style="text-align: right; color: #666; margin: 0; padding-right: 20px;">
                    <li style="margin-bottom: 8px;">تأكد من إدخال رمز القسيمة بشكل صحيح</li>
                    <li style="margin-bottom: 8px;">كل قسيمة يمكن استخدامها مرة واحدة فقط</li>
                    <li style="margin-bottom: 8px;">تحقق من تاريخ انتهاء صلاحية القسيمة</li>
                    <li>في حالة وجود مشكلة، تواصل مع الدعم الفني</li>
                </ul>
            </div>

            @if (session('success'))
                <div class="alert alert-success"
                    style="margin-top: 20px; padding: 15px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 8px; color: #155724;">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger"
                    style="margin-top: 20px; padding: 15px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 8px; color: #721c24;">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                </div>
            @endif
        </div>
    </div>
</div>
