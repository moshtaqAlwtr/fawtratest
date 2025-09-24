<!-- قسم برنامج إدارة الجرد -->
<section class="content-section" style="padding:0px"></section>
<section class="content-section">
    <div class="container">
        <div class="section-content">
            <div class="section-text">
                <h2>برنامج إدارة الجرد</h2>
                <p>نفِّذ عمليات الجرد لمستودعاتك بسهولة ودقة في خطوات بسيطة من برنامج جرد فوترة عن طريق حساب كميات
                    المنتجات الموجودة فعليًا في مخزونك وتسجيلها على النظام ليقوم النظام بمقارنتها تلقائيًا بالكميات
                    المسجلة عليه.</p>
                <ul class="invoice-features-list-unique">
                    <li>التحقق من كمية المنتجات بخطوات بسيطة.</li>
                    <li>تتبع النقص أو الزيادة في حجم المخزون.</li>
                    <li>تسهيل إجراء عمليات الجرد على مراحل.</li>
                    <li>استعراض تقارير الجرد مع عرض ملخص للمخزون.</li>
                </ul>
                <div style="text-align: right">
                    <button class="cta-button">ابدأ الاستخدام مجانًا</button>
                </div>
            </div>
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/page-banner-gard.webp') }}" alt="برنامج إدارة الجرد">
                @include('userguide::layouts.programs.btn_google_apple')
            </div>
        </div>
    </div>
</section>

<!-- قسم المسافات -->
<section class="fotra-spacing-section-large"></section>

@include('userguide::layouts.business_areas.cta_section_first')

<!-- قسم المسافات -->
<section class="fotra-spacing-section-large"></section>


<!-- Inventory Management Features Icons Section -->
@include('userguide::layouts.programs.inventory.inventory_management_features_icons_section')
<!-- قسم تحقق من كمية المنتجات -->
<section class="content-section">
    <div class="container">
        <div class="section-content">
            <div class="section-text">
                <h2>تحقق من كمية المنتجات من برنامج جرد المخازن</h2>
                <p>تحقق يدويًا من كميات المنتجات الفعلية في مخزونك وأدخلها في شاشة سهلة الاستخدام لمقارنتها بالمسجلة على
                    النظام، أو حدد معين أو حمل كمل منتجاتك في شاشة الإدخال بنقرة زر واحدة، وعندها سيقارن النظام تلقائيًا
                    بين الكميات المتوفرة لديك بالمخزون مع المسجلة عليه ويصدر لك بيانًا بالزيادة أو النقص، إلى جانب
                    إمكانية ضبطها بسهولة.</p>
                <div style="text-align: right">
                    <button class="cta-button">ابدأ الاستخدام مجانًا</button>
                </div>
            </div>
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/verify-quantity.webp') }}"
                    alt="تحقق من كمية المنتجات من برنامج جرد المخازن">
            </div>
        </div>
    </div>
</section>

<!-- قسم ضبط المخزون -->
<section class="content-section">
    <div class="container">
        <div class="section-content">
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/adjust-inventroy.webp') }}" alt="اضبط المخزون بضغطة زر">
            </div>
            <div class="section-text">
                <h2>اضبط المخزون بضغطة زر</h2>
                <p>اضبط كميات المنتجات بسهولة من برنامج جرد المخزون بمجرد أن يحسب النظام معدل النقص أو الزيادة لكل منتج
                    بنقرة واحدة، حيث يطابق النظام الكمية الفعلية في المخزون التي أدخلتها مع الكميات المسجلة عليه عن طريق
                    إنشاء أذون مخزنية بالصرف أو الإضافة تلقائيًا لضبطها وفقًا لذلك.</p>
            </div>

        </div>
    </div>
</section>

<!-- قسم عمليات الجرد على مراحل -->
<section class="content-section">
    <div class="container">
        <div class="section-content">
            <div class="section-text">
                <h2>نفذ عمليات الجرد على مراحل من دون القلق حيال ضياع البيانات</h2>
                <p>بفضل خصائص الأتمتة في فوترة، لن تضطر إلى بدء عمليات الجرد من جديد في حال إجراء العمليات على مراحل أو
                    حدوث انقطاعات أو خلل فني عند إجرائها، فستجد بياناتك التي تم إدخالها محفوظة تلقائيًا كمسودة ومن ثم
                    يمكنك المتابعة بسهولة حيثما توقفت.</p>
            </div>
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/Auto Saved.webp') }}"
                    alt="نفذ عمليات الجرد على مراحل من دون القلق حيال ضياع البيانات">
            </div>
        </div>
    </div>
</section>

<!-- قسم تقارير الجرد -->
<section class="content-section">
    <div class="container">
        <div class="section-content">
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/stocktaking-report.webp') }}"
                    alt="تقارير جرد دقيقة لرؤية أوضح حول مخزونك">
            </div>
            <div class="section-text">
                <h2>تقارير جرد دقيقة لرؤية أوضح حول مخزونك</h2>
                <p>اعرض تقارير الجرد التفصيلية التي توضح ملخصات عمليات الجرد بما في ذلك التحقق من مستوى المخزون سواء
                    النقص أو الزيادة، وكذا الأذون المخزنية التي تمت إضافتها عند ضبط مستويات المخزون.</p>
            </div>

        </div>
    </div>
</section>

<!-- قسم الأسعار -->
@include('userguide::layouts.programs.prices', [
    'title' => 'أسعار برنامج إدارة الجرد',
])

<!-- قسم تكامل النظام -->
@include('userguide::layouts.business_areas.system_integration')

<!-- قسم لماذا تختارنا -->
@include('userguide::layouts.business_areas.why_choose_us')
