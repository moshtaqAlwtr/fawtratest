<link rel="stylesheet" href="{{ asset('assets/css/mobile-app.css') }}">
<!-- Mobile POS App Content -->
<div class="mobile-business-app">
    <!-- Hero Section -->
    <section class="mb-hero">
        <div class="mb-container">
            <div class="mb-hero-content">
                <h1>تطبيق نقاط البيع POS للجوال من فوترة</h1>
                <p class="mb-hero-description">
                    حمّل على هاتفك الذكي تطبيق نقاط البيع POS من فوترة لتتمتع بتجربة مميزة في إتمام عمليات البيع من أي
                    مكان ودون حاجة للاتصال بالإنترنت. تم تصميم تطبيق نقاط البيع بالتكامل مع برامج العملاء والمخزون
                    والحسابات العامة. استدعِ المنتجات عبر أجهزة الباركود، وأصدر الفاتورة الإلكترونية عن كل عملية بيع.
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
            <!-- Feature 1: No Stop Sales -->
            <div class="mb-feature">
                <div class="mb-feature-content">
                    <div class="mb-feature-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h2>لا توقُّف لعمليات البيع.. في أي وقت ومن أي مكان</h2>
                    <p>تطبيق نقاط البيع POS مصمم خصيصًا لمواصلة البيع دون توقف. يناسب ذلك مندوبي المبيعات أكثر من غيرهم،
                        حيث تتطلب طبيعة عملهم التنقل بين أكثر من مكان لإتمام عمليات البيع، وبالتالي يساعدهم تطبيق نقاط
                        البيع POS على تسجيل الفواتير عبر الجوال وإصدارها لاحقًا.</p>
                    @include('userguide::layouts.programs.mobile.btn_apple_google')

                </div>
                <div class="mb-feature-image">
                    <img src="{{ asset('assets/images/user_guide/inventory-ar.png') }}" alt="تطبيق نقاط البيع للجوال">
                </div>
            </div>

            <!-- Feature 2: Offline Usage -->
            <div class="mb-feature">

                <div class="mb-feature-content">
                    <div class="mb-feature-icon">
                        <i class="fas fa-wifi"></i>
                    </div>
                    <h2>لا حاجة لشبكة إنترنت لإصدار فواتيرك الإلكترونية</h2>
                    <p>تمكن من استخدام تطبيق نقاط البيع على جوالك وبدون إنترنت، حيث يمكنك إتمام عمليات البيع وتسجيل
                        الفواتير ثم مزامنتها لاحقًا مع النظام السحابي. يضمن لك ذلك عدم توقف عمليات البيع لديك بحيث تحقق
                        المزيد من الإيرادات وتحظى برضا العملاء.</p>
                    @include('userguide::layouts.programs.mobile.btn_apple_google')
                </div>
                <div class="mb-feature-image">
                    <img src="{{ asset('assets/images/user_guide/print-invoice-ar.png') }}" alt="الفوترة بدون إنترنت">
                </div>
            </div>

            <!-- Feature 3: Business Integration -->
            <div class="mb-feature">
                <div class="mb-feature-content">
                    <div class="mb-feature-icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h2>ابدء الأعمال التجارية بثقة</h2>
                    <p>يتكامل تطبيق نقاط البيع مع تطبيقات فوترة الأخرى، ويعد الحل الأمثل لاحتياجات عملك، جربه الآن ولاحظ
                        الفرق.</p>

                    <!-- Features List -->
                    <div class="mb-features-list">
                        <div class="mb-feature-item">
                            <i class="fas fa-chart-line"></i>
                            <span>المبيعات</span>
                        </div>
                        <div class="mb-feature-item">
                            <i class="fas fa-boxes"></i>
                            <span>المخزن</span>
                        </div>
                        <div class="mb-feature-item">
                            <i class="fas fa-calculator"></i>
                            <span>الحسابات</span>
                        </div>
                        <div class="mb-feature-item">
                            <i class="fas fa-users"></i>
                            <span>إدارة العملاء</span>
                        </div>
                    </div>
                    <br>
                    @include('userguide::layouts.programs.mobile.btn_apple_google')
                </div>
                <div class="mb-feature-image">
                    <img src="{{ asset('assets/images/user_guide/pos-integration-ar.png') }}" alt="التكامل مع الأنظمة">
                </div>

            </div>
        </div>
    </section>
</div>
