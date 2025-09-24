<!-- Hero Section -->
<section class="mobile-hero-section">
    <div class="mobile-hero-content">
        <h1 class="mobile-hero-title">برنامج إدارة المكتبات</h1>
        <p class="mobile-hero-subtitle">
            برنامج إدارة المكتبات من فوتره يساعدك على ضبط إدارة محلات المستلزمات المكتبية بتوفير تجربة مبيعات ومشتريات
            وإدارة مخزنية ومحاسبية ومالية أكثر سلاسة. وهو ما يجعلك أكثر استبصارًا بمستقبل عملك التجاري وأكثر قدرة على
            اتخاذ التوجه الأكثر ربحية لمكتباتك.
        </p>

        <div class="mobile-hero-features">
            <div class="mobile-hero-feature">
                <i class="fas fa-receipt"></i>
                <span>فواتير وإيصالات إلكترونية أدق وأسهل.</span>
            </div>
            <div class="mobile-hero-feature">
                <i class="fas fa-desktop"></i>
                <span>تابع البيع أونلاين وأوفلاين من خلال كاشير الأدوات المكتبية.</span>
            </div>
            <div class="mobile-hero-feature">
                <i class="fas fa-credit-card"></i>
                <span>أنشئ باركود لمستلزماتك المكتبية.</span>
            </div>
            <div class="mobile-hero-feature">
                <i class="fas fa-barcode"></i>
                <span>صنف المستلزمات المكتبية ورتب المخزون.</span>
            </div>
            <div class="mobile-hero-feature">
                <i class="fas fa-users"></i>
                <span>احصل على أفضل عروض الأسعار بالاستعانة بدورة المشتريات.</span>
            </div>
            <div class="mobile-hero-feature">
                <i class="fas fa-chart-bar"></i>
                <span>اضبط مرتجعات المبيعات والمشتريات.</span>
            </div>
        </div>

        <a href="#" class="mobile-cta-button">ابدأ الاستخدام مجانًا</a>
    </div>
</section>

<!-- إنشاء الفواتير -->
<section class="mobile-content-section mobile-animate-on-scroll">
    <div class="mobile-section-content">
        <div class="mobile-section-text">
            <h2>فواتير وإيصالات إلكترونية أدق وأسهل</h2>
            <p>أصدر فواتير بيع المستلزمات المكتبية بضغطة زر، واضمن اعتمادها من الجهات المعنية كهيئة الزكاة والضريبة
                والجمارك بالمملكة ومصلحة الضرائب المصرية.
                ويسهل عليك من خلال فواتير فوتره احتساب الضرائب والخصومات والتسويات ومصاريف الشحن إضافة إلى بعض المعاملات
                على الفواتير كالتقسيط وإصدار المرتجعات وتخصيص أسعار مميزة للمنتجات لعملاء محددين من خلال قوائم الأسعار.
            </p>
        </div>
        <div class="mobile-section-image">
            <img src="{{ asset('assets/images/user_guide/page-banner-library.jpeg') }}" alt="إنشاء الفواتير"
                onerror="...">
        </div>
    </div>
</section>

<!-- تابع البيع أونلاين وأوفلاين -->
<section class="mobile-content-section mobile-animate-on-scroll">
    <div class="mobile-section-content">
        <div class="mobile-section-image">
            <img src="{{ asset('assets/images/user_guide/operations.webp') }}" alt="تابع البيع أونلاين وأوفلاين"
                onerror="...">
        </div>
        <div class="mobile-section-text">
            <h2>تابع البيع أونلاين وأوفلاين من خلال كاشير الأدوات المكتبية</h2>
            <p>من خلال نظام POS من فوتره تستطيع مواصلة عملية البيع برتم أسرع مع توافر أو غياب الإنترنت من خلال شاشة
                اللمس أو عن طريق قارئ الباركود. تساعد نقاط البيع على تتبع ورديات البائعين وضبط عملية تسليم العهدة من
                وردية لأخرى مع إصدار تقارير تفيدك لمعرفة أي الموظفين حقق مبيعات أكثر من خلال وردية نقطة البيع الخاصة به.
            </p>
        </div>
    </div>
</section>

<!-- أنشئ باركود لمستلزماتك المكتبية -->
<section class="mobile-content-section mobile-animate-on-scroll">
    <div class="mobile-section-content">
        <div class="mobile-section-text">
            <h2>أنشئ باركود لمستلزماتك المكتبية</h2>
            <p>يساعدك فوتره على إصدار باركود يدويًا لمنتجاتك أو توليده تلقائيًا من خلال البرنامج، وعن طريق طباعته كملصق
                بلصقه على المنتجات يمكنك استدعائه بسهولة في الفواتير ونقطه البيع عن طريق المسح السريع للباركود.</p>
        </div>
        <div class="mobile-section-image">
            <img src="{{ asset('assets/images/user_guide/barcode.webp') }}" alt="أنشئ باركود لمستلزماتك المكتبية"
                onerror="...">
        </div>
    </div>
</section>
<!-- CTA Section -->
@include('userguide::layouts.business_areas.cta_section')
<!-- صنف المستلزمات المكتبية ورتب المخزون -->
<section class="mobile-content-section mobile-animate-on-scroll">
    <div class="mobile-section-content">
        <div class="mobile-section-image">
            <img src="{{ asset('assets/images/user_guide/track-products.webp') }}"
                alt="صنف المستلزمات المكتبية ورتب المخزون" onerror="...">
        </div>
        <div class="mobile-section-text">
            <h2>صنف المستلزمات المكتبية ورتب المخزون</h2>
            <p>ضع كل مجموعة متشابهة من المستلزمات المكتبية كالأقلام أو الكتب الخارجية أو الكشاكيل في صنف مستقل وميزهم من
                خلال الوسوم بحسب الألوان والأحجام واسمح للنظام بتنبيهك عند انخفاض كمية المستودع من صنف ما عن الحد
                المخزوني المريح. قم بعمليات الجرد وتأكد من مطابقة الكميات الفعلية بكميات المنتج على النظام وتتبع منتجاتك
                بأرقام التسلسل أو الكميات وأدر صرف وإضافة المنتجات من خلال الأذون المخزنية.</p>
        </div>
    </div>
</section>

<!-- دورة مشتريات تساعدك في الحصول على أفضل الأسعار -->
<section class="mobile-content-section mobile-animate-on-scroll">
    <div class="mobile-section-content">
        <div class="mobile-section-text">
            <h2>دورة مشتريات تساعدك في الحصول على أفضل الأسعار</h2>
            <p>من خلال دورة المشتريات لإدارة توريدات المستلزمات المكتبية من فوتره تستطيع البدء في الشراء فور وصول
                المخزون للحد الحرج لتلقي أكبر عدد من عروض الأسعار من الموردين، والتي تستطيع المفاضلة بينها واختيار عرض
                السعر الأفضل والترتيب مع المورد لإيصاله في الوقت المناسب، مع الاحتفاظ بقاعدة بيانات شاملة للموردين تسهل
                عليك إعادة التعامل مع المورد المراد بسهولة، مع مقارنة أسعار الشراء السابقة بسعر الشراء المقترح للطلبية
                الجديدة.</p>
        </div>
        <div class="mobile-section-image">
            <img src="{{ asset('assets/images/user_guide/save-time.webp') }}"
                alt="دورة مشتريات تساعدك في الحصول على أفضل الأسعار" onerror="...">
        </div>
    </div>
</section>

<!-- اضبط مرتجعات المبيعات والمشتريات -->
<section class="mobile-content-section mobile-animate-on-scroll">
    <div class="mobile-section-content">
        <div class="mobile-section-image">
            <img src="{{ asset('assets/images/user_guide/invoice-sc.png') }}" alt="اضبط مرتجعات المبيعات والمشتريات"
                onerror="...">
        </div>
        <div class="mobile-section-text">
            <h2>اضبط مرتجعات المبيعات والمشتريات</h2>
            <p>يمكنك إصدار مرتجعات الفواتير كمرتجع أو في صورة إشعار مدين أو دائن يتناسب مع متطلبات هيئة الزكاة والضريبة
                والجمارك. ويطابق المرتجع أو الإشعار بيانات الفاتورة بشكل كلي أو جزئي بحسب طبيعته، وييسر عليك فوتره
                المعالجة المحاسبية والإدارية للمرتجعات.</p>
        </div>
    </div>
</section>

<!-- إدارة محاسبية أكثر سلاسة -->
<section class="mobile-content-section mobile-animate-on-scroll">
    <div class="mobile-section-content">
        <div class="mobile-section-text">
            <h2>إدارة محاسبية أكثر سلاسة</h2>

            <p>يقوم برنامج إدارة المكتبات من فوتره بإنشاء القيود وترحيلها بطريقة آلية، وتجد دليل الحسابات معد مسبقًا مع
                إمكانية التعديل عليه، ويسهل إدارة الأصول وإعادة تقييمها وبيعها واحتساب الإهلاكات عليها بطرق مختلفة
                للإهلاك. كما يمكنك احتساب المصاريف وصافي الربح بسهولة وتعيين مراكز التكلفة بالطريقة الأكثر فائدة لك.</p>
        </div>
        <div class="mobile-section-image">
            <img src="{{ asset('assets/images/user_guide/automate-cost.webp') }}" alt="إدارة محاسبية أكثر سلاسة"
                onerror="...">
        </div>
    </div>
</section>

<!-- قيّم وحسّن أداء موظفي مكتبتك -->
<section class="mobile-content-section mobile-animate-on-scroll">
    <div class="mobile-section-content">
        <div class="mobile-section-image">
            <img src="{{ asset('assets/images/user_guide/commision-rule-sc.png') }}" alt="قيّم وحسّن أداء موظفي مكتبتك"
                onerror="...">
        </div>
        <div class="mobile-section-text">
            <h2>قيّم وحسّن أداء موظفي مكتبتك</h2>
            <p>احتسب حضور الموظفين واستقبل طلبات الإجازة واضبط الورديات وحدد العمولات على المبيعات والمكافآت والجزاءات
                بحسب كفاءة كل فرد. كما يمكنك إتاحة بعض الصلاحيات لبعض الموظفين بحسب ما تحدده.</p>
        </div>
    </div>
</section>

<!-- أتمتة أعلى .. تقارير أكثر دقة -->
<section class="mobile-content-section mobile-animate-on-scroll">
    <div class="mobile-section-content">
        <div class="mobile-section-text">
            <h2>أتمتة أعلى .. تقارير أكثر دقة</h2>
            <p>يتم إعداد كافة التقارير والحسابات الختامية آليًا وهو ما يعينك على تقليل التكاليف وتتبع الأرباح وزيادتها.
                تتحكم في القوائم المالية والتقارير بحسب الفترة الزمنية المراد إصدارهم عنها والبيانات المراد تضمينها
                والهدف من التقرير وصورته، وهو ما يعينك على معرفة ما يجري في كواليس إدارة المكتبة لديك من خلال نظرة سريعة
                على ما تريد من التقارير.</p>
        </div>
        <div class="mobile-section-image">
            <img src="{{ asset('assets/images/user_guide/perfor.jpeg') }}" alt="أتمتة أعلى .. تقارير أكثر دقة"
                onerror="...">
        </div>
    </div>
</section>
<!-- System Integration Section -->
@include('userguide::layouts.business_areas.system_integration')

<!-- Why Choose Us Section -->
@include('userguide::layouts.business_areas.why_choose_us')

<!-- تضمين ملف JavaScript المنفصل -->
<script src="{{ asset('assets/js/mobile-shop.js') }}"></script>
