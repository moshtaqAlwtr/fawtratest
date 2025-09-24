<link rel="stylesheet" href="{{ asset('assets/css/mobile-app.css') }}">
<!-- Mobile Invoice Reader App Content -->
<div class="mobile-business-app">
    <!-- Hero Section -->
    <section class="mb-hero">
        <div class="mb-container">
            <div class="mb-hero-content">
                <h1>تطبيق قارئ الفاتورة الإلكترونية من فوترة</h1>
                <p class="mb-hero-description">
                    باستخدام التطبيق يمكنك التحقق من صحة بيانات أي فاتورة إلكترونية او ضريبية والتأكد من امتثالها
                    لمتطلبات هيئة الزكاة والضريبة والجمارك، فقط عبر مسح رمز الاستجابة السريع QR Code على الفاتورة،
                    وسيعرض لك التطبيق في ثوان كامل بيانات الفاتورة، ويتحقق تلقائياً من صحتها.
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
            <!-- Feature 1: Invoice Data Display -->
            <div class="mb-feature">
                <div class="mb-feature-content">
                    <div class="mb-feature-icon">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    <h2>إستعراض كل بيانات الفاتورة الضريبية والالكترونية في خطوة واحدة</h2>
                    <p>بمجرد مسح رمز الاستجابة السريع عبر تطبيق قارئ الفاتورة الإلكترونية من فوترة ستظهر لك البيانات
                        المطلوبة من قبل هيئة الزكاة والضريبة والجمارك المتمثلة في الاسم التجاري للمؤسسة، ورقم التسجيل
                        الضريبي للتاجر، ووقت وتاريخ إنشاء الفاتورة، وإجمالي قيمة الفاتورة، وإجمالي قيمة الضريبة.</p>
                    @include('userguide::layouts.programs.mobile.btn_apple_google')

                </div>
                <div class="mb-feature-image">
                    <img src="{{ asset('assets/images/user_guide/read-invoice-ar.png') }}"
                        alt="إستعراض بيانات الفاتورة الضريبية">
                </div>
            </div>

            <!-- Feature 2: Invoice Verification -->
            <div class="mb-feature">

                <div class="mb-feature-content">
                    <div class="mb-feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h2>تحقق من الفاتورة الضريبية وفقًا لاشتراطات هيئة الزكاة</h2>
                    <p>استخدم تطبيق قارئ الفاتورة الإلكترونية للحفاظ على ممارسة نشاطك بما يتوافق مع اشتراطات هيئة الزكاة
                        والضريبة والجمارك التي أفادت بضرورة إنشاء وطباعة رمز استجابة سريع تتأكد به من صحة بيانات
                        الفاتورة. ومن هنا، تكمن أهمية قارئ الفاتورة في الالتزام بتعليمات الهيئة في هذا الشأن.</p>
                    @include('userguide::layouts.programs.mobile.btn_apple_google')
                </div>
                <div class="mb-feature-image">
                    <img src="{{ asset('assets/images/user_guide/valid-invoice-ar.png') }}" style="height: 400px"
                        alt="التحقق من الفاتورة الضريبية">
                </div>
            </div>
        </div>
    </section>
</div>
