<link rel="stylesheet" href="{{ asset('assets/css/mobile-app.css') }}">
<!-- Mobile Expense App Content -->
<div class="mobile-business-app">
    <!-- Hero Section -->
    <section class="mb-hero">
        <div class="mb-container">
            <div class="mb-hero-content">
                <h1>تطبيق تسجيل المصروفات السريع للجوال من فوترة</h1>
                <p class="mb-hero-description">
                    كجزء من برنامج الحسابات العامة في نظام فوترة تم إطلاق تطبيق تسجيل المصروفات السريع لمساعدتك على
                    إدارة مصروفاتك بصورة أكثر فاعلية. استخدم التطبيق المدعوم من نظامي التشغيل Android وiOS على الجوال
                    وتمتع بتجربة أكثر مرونة في إدارة مصروفاتك.
                </p>
                <div class="mb-download-buttons">
                    <a href="#" class="mb-download-btn">
                        <i class="fab fa-apple"></i>
                        <span>تحميل من App Store</span>
                    </a>
                    <a href="#" class="mb-download-btn">
                        <i class="fab fa-google-play"></i>
                        <span>تحميل من Google Play</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="mb-features">
        <div class="mb-container">
            <!-- Feature 1: Quick Expense Entry -->
            <div class="mb-feature">
                <div class="mb-feature-content">
                    <div class="mb-feature-icon">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <h2>تسجيل سريع لنفقاتك</h2>
                    <ul class="invoice-features-list-unique">
                        <li><strong>تسجيل سريع للمصروفات.</strong></li>
                        <li><strong>إضافة بيانات الضريبة.</strong></li>
                        <li><strong>إرفاق الصور والمستندات.</strong></li>
                        <li><strong>مزامنة البيانات مع حسابك في فوترة.</strong></li>
                    </ul>
                    @include('userguide::layouts.programs.mobile.btn_apple_google')

                </div>
                <div class="mb-feature-image">
                    <img src="{{ asset('assets/images/user_guide/home-screen-view-ar.png') }}" style="height: 400px"
                        alt="تسجيل سريع لنفقاتك">
                </div>
            </div>

            <!-- Feature 2: Smooth Expense Entry -->
            <div class="mb-feature">

                <div class="mb-feature-content">
                    <div class="mb-feature-icon">
                        <i class="fas fa-camera"></i>
                    </div>
                    <h2>إدخال مصروفات المؤسسة أصبح أكثر سلاسة</h2>
                    <p>عبر هذا التطبيق ستتمكن ويتمكن موظقيك من إدخال المصروفات عبر تصوير المستندات والفواتير الإلكترونية
                        بالهاتف فور الحصول عليها، وإدخال البيانات الخاصة بها بشكل سريع، ورفعها على النظام السحابي بشكل
                        متزامن، ويمكن للتطبيق العمل بدون إنترنت والمزامنة من النظام السحابي فور عودة الإنترنت.</p>
                    @include('userguide::layouts.programs.mobile.btn_apple_google')
                </div>
                <div class="mb-feature-image">
                    <img src="{{ asset('assets/images/user_guide/expense-scanner-ar.png') }}" style="height: 400px"
                        alt="إدخال مصروفات المؤسسة">
                </div>
            </div>

            <!-- Feature 3: QR Code Reading -->
            <div class="mb-feature">
                <div class="mb-feature-content">
                    <div class="mb-feature-icon">
                        <i class="fas fa-qrcode"></i>
                    </div>
                    <h2>حول فواتير المشتريات الإلكترونية لمصروفات في لحظات</h2>
                    <p>يتميز التطبيق بقدرته على قراءة الـQR كود الخاص بالفواتير الإلكترونية الخاصة بمشترياتكم وإدخال
                        البيانات المتضمنة فيه الخاصة بالمورد أو البائع بشكل آلي وقيمة الضريبة المدفوعة إلى البرنامج مما
                        يسهل عليك ويسرع عملية الإدخال ويخفض قيمة الضرائب اللازم دفعها.</p>
                    @include('userguide::layouts.programs.mobile.btn_apple_google')
                </div>
                <div class="mb-feature-image">
                    <img src="{{ asset('assets/images/user_guide/expense-qr-ar.png') }}" alt="قراءة QR Code للفواتير">
                </div>
            </div>

            <!-- Feature 4: Tax Reduction -->
            <div class="mb-feature">

                <div class="mb-feature-content">
                    <div class="mb-feature-icon">
                        <i class="fas fa-calculator"></i>
                    </div>
                    <h2>خفض قيمة الضرائب المدفوعة عبر إدخال فواتير المشتريات الإلكترنية بشكل سريع</h2>
                    <p>عبر إدخالكم المصروفات الخاصة بالفواتير الإلكترونية للمشتريات الخاصة بالمؤسسة يقوم التطبيق بحساب
                        الضريبة المدفوعة ورفعها على النظام السحابي، ويقوم النظام بخصم قيمة الضريبة المدفوعة من الضريبة
                        المحصلة لتقديم إقرار ضريبي أكثر سهولة ودقة.</p>
                    @include('userguide::layouts.programs.mobile.btn_apple_google')
                </div>
                <div class="mb-feature-image">
                    <img src="{{ asset('assets/images/user_guide/complete-control.webp') }}" alt="خفض قيمة الضرائب">
                </div>
            </div>
        </div>
    </section>
</div>
