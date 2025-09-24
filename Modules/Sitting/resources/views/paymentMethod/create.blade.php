@extends('master')

@section('content')
    <div class="content-body">
        <div class="row">
            <!-- القائمة الجانبية -->
            {{-- <div class="col-md-3 mb-2">
                <div class="card">
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <a href="#" class="list-group-item list-group-item-action">
                                <i class="fas fa-info-circle me-2"></i>
                                معلومات الحساب
                            </a>
                            <a href="#" class="list-group-item list-group-item-action">
                                <i class="fas fa-cog me-2"></i>
                                إعدادات الحساب
                            </a>
                            <a href="#" class="list-group-item list-group-item-action">
                                <i class="fas fa-envelope me-2"></i>
                                إعدادات الـ SMTP
                            </a>
                            <a href="#" class="list-group-item list-group-item-action active">
                                <i class="fas fa-credit-card me-2"></i>
                                طرق الدفع
                            </a>
                            <a href="#" class="list-group-item list-group-item-action">
                                <i class="fas fa-sms me-2"></i>
                                إعدادات الـ SMS
                            </a>
                            <a href="#" class="list-group-item list-group-item-action">
                                <i class="fas fa-sort-numeric-up me-2"></i>
                                إعدادات الترقيم المتسلسل
                            </a>
                            <a href="#" class="list-group-item list-group-item-action">
                                <i class="fas fa-percent me-2"></i>
                                إعدادات الضرائب
                            </a>
                            <a href="#" class="list-group-item list-group-item-action">
                                <i class="fas fa-tasks me-2"></i>
                                إدارة التطبيقات
                            </a>
                            <a href="#" class="list-group-item list-group-item-action">
                                <i class="fas fa-palette me-2"></i>
                                شعار وألوان النظام
                            </a>
                            <a href="#" class="list-group-item list-group-item-action">
                                <i class="fas fa-code me-2"></i>
                                API
                            </a>
                        </div>
                    </div>
                </div>
            </div> --}}

            <!-- نموذج إضافة وسيلة دفع -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">إضافة وسيلة دفع مخصصة</h4>
                    </div>
                    <div class="card-body">
                        <form id="paymentStatusForm" action="{{ route('PaymentMethods.store') }}" method="POST">
                            @csrf
                            <!-- الاسم -->
                            <div class="mb-3">
                                <label class="form-label">الاسم</label>
                                <input type="text" class="form-control" name="name">
                            </div>

                            <!-- التعليمات -->
                            <div class="mb-3">
                                <label class="form-label">التعليمات</label>
                                <textarea class="form-control" name="description" rows="3"></textarea>
                            </div>

                            <!-- التفعيل للعملاء -->
                            <div class="mb-3">
                                <label class="form-label">التفعيل للعملاء على الانترنت</label>
                                <div class="mt-2">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="is_online" id="active" value="active" checked>
                                        <label class="form-check-label" for="active">تم تفعيله</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="is_online" id="inactive" value="inactive">
                                        <label class="form-check-label" for="inactive">تم تعطيله</label>
                                    </div>
                                </div>
                            </div>

                            <!-- العملة الافتراضية -->
                            {{-- <div class="mb-3">
                                <label class="form-label">العملة الافتراضية</label>
                                <select class="form-select" name="currency">
                                    <option value="">لا شيء</option>
                                    <option value="SAR">ريال سعودي</option>
                                    <option value="USD">دولار أمريكي</option>
                                </select>
                            </div> --}}

                            <!-- مصاريف الدفع -->
                            {{-- <div class="mb-3">
                                <label class="form-label">مصاريف الدفع</label>
                                <div class="mt-2">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="fees" id="noFees" value="0" checked>
                                        <label class="form-check-label" for="noFees">إيقاف</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="fees" id="hasFees" value="1">
                                        <label class="form-check-label" for="hasFees">حساب</label>
                                    </div>
                                </div>
                            </div> --}}

                            <!-- الحالة -->
                            <div class="mb-4">
                                <label class="form-label">الحالة</label>
                                <div class="mt-2">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="status" id="statusActive" value="active">
                                        <label class="form-check-label" for="statusActive">نشط</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="status" id="statusInactive" value="inactive" checked>
                                        <label class="form-check-label" for="statusInactive">غير نشط</label>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label">الحالة</label>
                                <div class="mt-2">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="type" id="statusActive" value="normal">
                                        <label class="form-check-label" for="statusActive">عادية</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="type" id="statusInactive" value="electronic" checked>
                                        <label class="form-check-label" for="statusInactive">الكترونية</label>
                                    </div>
                                </div>
                            </div>
                            <!-- أزرار التحكم -->
                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-outline-secondary" onclick="window.history.back()">
                                    <i class="fas fa-arrow-right me-1"></i> رجوع
                                </button>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save me-1"></i> حفظ
                                </button>
                            </div>
                            
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const form = document.getElementById('paymentMethodForm');
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            // هنا يمكنك إضافة كود معالجة النموذج
            window.history.back();
        });
    </script>
    @endpush
@endsection
