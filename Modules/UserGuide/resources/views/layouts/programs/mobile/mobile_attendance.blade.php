<link rel="stylesheet" href="{{ asset('assets/css/mobile-app.css') }}">
<!-- Mobile ESS Attendance App Content -->
<div class="mobile-business-app">
    <!-- Hero Section -->
    <section class="mb-hero">
        <div class="mb-container">
            <div class="mb-hero-content">
                <h1>تطبيق تسجيل الحضور ESS من فوترة</h1>
                <p class="mb-hero-description">
                    تطبيق تسجيل الحضور الذاتي للموظفين هو جزءٌ من برنامج الموارد البشرية، والذي يساعدك على إدارة حضور
                    وانصراف موظفيك الذين لهم صلاحية العمل عن بُعد، كما يتميز تطبيق ESS بسهولة الاستخدام ليناسب كافة
                    الموظفين على اختلاف مستوياتهم.
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
            <!-- Feature 1: Self Attendance Registration -->
            <div class="mb-feature">
                <div class="mb-feature-content">
                    <div class="mb-feature-icon">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <h2>التسجيل الذاتي للحضور عن بعد</h2>
                    <ul class="invoice-features-list-unique">
                        <li>تسجيل الموظف حضوره بنفسه عن طريق أحد محددات الحضور.</li>
                        <li>إدارة حضور الموظفين عن بعد باحترافية.</li>
                        <li>واجهة عمل سلسة وسهلة الاستخدام.</li>
                    </ul>
                    @include('userguide::layouts.programs.mobile.btn_apple_google')

                </div>
                <div class="mb-feature-image">
                    <img src="{{ asset('assets/images/user_guide/home-screen-ar.png') }}" style="height: 400px"
                        alt="التسجيل الذاتي للحضور عن بعد">
                </div>
            </div>

            <!-- Feature 2: Remote Attendance -->
            <div class="mb-feature">

                <div class="mb-feature-content">
                    <div class="mb-feature-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h2>مكّن موظفيك من تسجيل حضورهم ذاتياً عن بعد</h2>
                    <p>عبر تطبيق تسجيل الحضور من فوترة يمكن لكل موظف إثبات حضوره ذاتيًا عن بُعد عبر إحدى محددات الحضور:
                        عنوان بروتوكول الإنترنت (IP Adress)، أو الموقع الجغرافي، أو التقاط صورة حية. يمكن تطبيق المحددات
                        الثلاثة على الموظفين، أو الاكتفاء بواحدة أو اثنتين حسب حاجة العمل.</p>
                    @include('userguide::layouts.programs.mobile.btn_apple_google')
                </div>
                <div class="mb-feature-image">
                    <img src="{{ asset('assets/images/user_guide/take-attendance-ar.png') }}" alt="تسجيل الحضور عن بعد">
                </div>
            </div>

            <!-- Feature 3: Attendance Management -->
            <div class="mb-feature">
                <div class="mb-feature-content">
                    <div class="mb-feature-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <h2>أدر سجلات الحضور بدقة وتحقق من مواعيد الحضور والانصراف</h2>
                    <p>يمنحك تطبيق تسجيل الحضور مزايا عديدة تساعدك على إدارة شئون موظفيك بشكل أفضل. تحقق من مواعيد حضور
                        وانصراف الموظفين، واحصر أيام الحضور والإجازات، وحافظ على استقرار بيئة العمل ومعدل الإنتاجية مهما
                        اختلفت الظروف.</p>
                    @include('userguide::layouts.programs.mobile.btn_apple_google')
                </div>
                <div class="mb-feature-image">
                    <img src="{{ asset('assets/images/user_guide/attendance-log-ar.png') }}" alt="إدارة سجلات الحضور">
                </div>
            </div>

            <!-- Feature 4: Easy to Use -->
            <div class="mb-feature">

                <div class="mb-feature-content">
                    <div class="mb-feature-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h2>استخدم التطبيق بسهولة</h2>
                    <p>يتميز تطبيق تسجيل الحضور بسهولة الاستخدام حيث يعرض للموظف شاشة واحدة فقط تتضمن محددات الحضور،
                        وبكل سهولة يختار الموظفُ محدد الحضور المطلوب ويضغط على زر تسجيل الحضور، وبمجرد قراءة التطبيق
                        للبيانات بالشكل الصحيح يتم اعتماد تسجيل الحضور.</p>
                    @include('userguide::layouts.programs.mobile.btn_apple_google')
                </div>
                <div class="mb-feature-image">
                    <img src="{{ asset('assets/images/user_guide/signin-ar.png') }}" style="height: 400px"
                        alt="سهولة الاستخدام">
                </div>
            </div>

            <!-- Feature 5: Business Integration -->
            <div class="mb-feature">
                <div class="mb-feature-content">
                    <div class="mb-feature-icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h2>ابدء الأعمال التجارية بثقة</h2>
                    <p>يتكامل تطبيق تسجيل حضور الموظفين مع تطبيقات فوترة الأخرى، ويعد الحل الأمثل لاحتياجات عملك، جربه
                        الآن ولاحظ الفرق.</p>


                    @include('userguide::layouts.programs.mobile.btn_apple_google')
                </div>
                <div class="mb-feature-image">
                    <img src="{{ asset('assets/images/user_guide/home-screen-ar.png') }}" style="height: 400px"
                        alt="التكامل مع الأنظمة">
                </div>
            </div>
        </div>
    </section>
</div>
