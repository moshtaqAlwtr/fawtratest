    <link rel="stylesheet" href="{{ asset('assets/css/mobile-app.css') }}">
    <!-- Mobile Business Management App Content -->
    <div class="mobile-business-app">
        <!-- Hero Section -->
        <section class="mb-hero">
            <div class="mb-container">
                <div class="mb-hero-content">
                    <h1>تطبيق فوترة المحاسبي لإدارة الأعمال</h1>
                    <p class="mb-hero-description">
                        استخدم نسخة طبق الأصل من نظام فوترة المحاسبي على هاتفك الجوال اندرويد او ايفون حتى تتمكن من
                        مباشرة
                        مهامك وإدارة أعمالك لحظيًا. أدر شئون الموظفين وعلاقات العملاء. أصدر فواتيرك، وعالج العمليات
                        المحاسبية آليًا. تابع حركة المخزون وتتبع المنتجات. أصدر التقارير الدورية وكوّن صورة واضحة حول
                        أداء
                        العمل، عبر تطبيق فوترة المدعوم من نظامي التشغيل Android وiOS وبواجهة سهلة الاستخدام.
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
                <!-- Feature 1: Main App -->
                <div class="mb-feature">
                    <div class="mb-feature-content">
                        <div class="mb-feature-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <h2>تطبيق فوترة الشامل</h2>
                        <p>نسخة متكاملة من نظام فوترة المحاسبي على هاتفك المحمول مع جميع الميزات والوظائف المتقدمة
                            لإدارة
                            أعمالك بكفاءة عالية.</p>
                        @include('userguide::layouts.programs.mobile.btn_apple_google')

                    </div>
                    <div class="mb-feature-image">
                        <img src="{{ asset('assets/images/user_guide/portable-ar.png') }}" alt="تطبيق فوترة المحاسبي">
                    </div>
                </div>

                <!-- Feature 2: Electronic Invoicing -->
                <div class="mb-feature">

                    <div class="mb-feature-content">
                        <div class="mb-feature-icon">
                            <i class="fas fa-file-invoice"></i>
                        </div>
                        <h2>فوترة إلكترونية متوافقة مع المتطلبات</h2>
                        <p>أصدر فواتيرك الإلكترونية بما يتوافق مع متطلبات واشتراطات الجهات الرسمية، سواء هيئة الزكاة
                            والضريبة والجمارك السعودية أو مصلحة الضرائب المصرية. طبق الخصومات والعروض على الفواتير،
                            وأنشئ
                            الإشعارات الدائنة والمرتجعات، وصمّم قوالب جاهزة قابلة للتخصيص.</p>
                        @include('userguide::layouts.programs.mobile.btn_apple_google')
                    </div>
                    <div class="mb-feature-image">
                        <img src="{{ asset('assets/images/user_guide/invoicing-ar.png') }}" alt="فوترة إلكترونية">
                    </div>
                </div>

                <!-- Feature 3: Inventory Management -->
                <div class="mb-feature">
                    <div class="mb-feature-content">
                        <div class="mb-feature-icon">
                            <i class="fas fa-boxes"></i>
                        </div>
                        <h2>إدارة شاملة للمخزون وتتبع دقيق لمنتجاته</h2>
                        <p>عبر جهاز الجوال الخاص بك يُمكنك متابعة حجم المخزون، وتتبع كافة المنتجات لديك بتاريخ الصلاحية
                            أو
                            الرقم المسلسل أو رقم الشحنة. تحكّم في الصادر والوارد، وراقب مستوى المخزون. أدر دورة
                            المشتريات
                            آليًا بكافة مراحلها، وحدد الصلاحيات لأمناء المخازن، وأصدر تقارير الجرد دوريًا.</p>
                        @include('userguide::layouts.programs.mobile.btn_apple_google')
                    </div>
                    <div class="mb-feature-image">
                        <img src="{{ asset('assets/images/user_guide/stock-ar.png') }}" alt="إدارة المخزون">
                    </div>
                </div>

                <!-- Feature 4: Financial Management -->
                <div class="mb-feature">

                    <div class="mb-feature-content">
                        <div class="mb-feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h2>إدارة مالية للتدفقات النقدية والحسابات العامة</h2>
                        <p>يتولى نظام فوترة آليًا إدارة التدفقات النقدية الناتجة عن عمليات البيع والشراء بمختلف أنواعها،
                            كما
                            تتم معالجة العمليات المحاسبية ذات الصلة آليًا، مع ربط ذلك بالتقارير المالية لتكوين دورة
                            محاسبية
                            متكاملة ودقيقة. يساعدك ذلك على إدارة الشئون المالية لديك بشكل مباشر ودون حاجة إلى معرفة
                            بدقائق
                            المحاسبة.</p>
                        @include('userguide::layouts.programs.mobile.btn_apple_google')
                    </div>
                    <div class="mb-feature-image">
                        <img src="{{ asset('assets/images/user_guide/finance-ar.png') }}" alt="إدارة مالية">
                    </div>
                </div>

                <!-- Feature 5: Employee Management -->
                <div class="mb-feature">
                    <div class="mb-feature-content">
                        <div class="mb-feature-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h2>متابعة لحظية لشئون الموظفين أينما كنت</h2>
                        <p>بفضل تطبيق فوترة على جهازك الجوال يمكنك إدارة موظفيك ومتابعة شئونهم بشكل لحظي عبر برنامج
                            إدارة
                            الموظفين في فوترة. حدد المستويات الوظيفية في شركتك، وأنشئ عقود الموظفين، وتابع سجلات الحضور
                            والانصراف، واتخذ القرارات حيال الطلبات والأذون والإجازات، وتوصل إلى المعلومة التي تريد في
                            غضون
                            ثوانٍ.</p>
                        @include('userguide::layouts.programs.mobile.btn_apple_google')
                    </div>
                    <div class="mb-feature-image">
                        <img src="{{ asset('assets/images/user_guide/attendance-ar.png') }}" alt="إدارة الموظفين">
                    </div>
                </div>

                <!-- Feature 6: System Customization -->
                <div class="mb-feature">

                    <div class="mb-feature-content">
                        <div class="mb-feature-icon">
                            <i class="fas fa-cogs"></i>
                        </div>
                        <h2>مرونة في تخصيص النظام لنشاط عملك</h2>
                        <p>بالإضافة إلى أن نظام فوترة مصمَّم لخدمة أكثر من 50 مجال عمل مختلف، هناك فريق من المبرمجين في
                            فوترة لاستقبال طلبات التخصيص من كل عميل على حدةٍ وتنفيذها له على وجه التحديد، بعد دراستها
                            وتسعيرها وتحديد آلية تنفيذها. يضمن لك ذلك توفير الوقت والجهد والتكاليف في إدارة نشاطك.</p>
                        @include('userguide::layouts.programs.mobile.btn_apple_google')
                    </div>
                    <div class="mb-feature-image">
                        <img src="{{ asset('assets/images/user_guide/custom-ar.png') }}" alt="تخصيص النظام">
                    </div>
                </div>

                <!-- Feature 7: Reports -->
                <div class="mb-feature">
                    <div class="mb-feature-content">
                        <div class="mb-feature-icon">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <h2>تقارير دورية شاملة ومفصلة</h2>
                        <p>في ضوء الإجراءات التي يتم اتخاذها في حسابك على فوترة، يُصدر النظام آليًا تقارير بكل المعاملات
                            والبيانات في البرامج المختلفة، بشكل تفصيلي، على هيئة رسوم بيانات وجداول بحيث يسهل عليك
                            الاطلاع
                            عليها. يضمن لك ذلك تكوين صورة واضحة حول أداء الأعمال واتخاذ القرارات السليمة.</p>
                        @include('userguide::layouts.programs.mobile.btn_apple_google')
                    </div>
                    <div class="mb-feature-image">
                        <img src="{{ asset('assets/images/user_guide/reports-ar.png') }}" alt="التقارير">
                    </div>
                </div>
            </div>
        </section>
    </div>
