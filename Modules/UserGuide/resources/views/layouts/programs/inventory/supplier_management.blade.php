<!-- Supplier Management Program Section -->
<section class="content-section" style="padding:0px"></section>
<section class="content-section">
    <div class="container">
        <div class="section-content">
            <div class="section-text">
                <h2>برنامج إدارة الموردين</h2>
                <p>نظم مورديك باحترافية مع الأدوات المتقدمة التي يوفرها برنامج فوتره في واجهة سهلة الاستخدام تدعم اللغة
                    العربية، حيث يمكنك إنشاء ملف خاص لكل مورد يتضمن كافة بياناته بما في ذلك معلومات التواصل ومعلومات
                    شركته، مما يسهل عملية إصدار فواتير الشراء وإرسالها إلى مورديك.</p>
                <div style="text-align: right">
                    <button class="cta-button">ابدأ الاستخدام مجانًا</button>
                </div>
            </div>
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/page-banner-buyer.webp') }}" alt="برنامج إدارة الموردين">
                @include('userguide::layouts.programs.btn_google_apple')

            </div>
        </div>
    </div>
</section>

<!-- Spacing Section -->
<section class="fotra-spacing-section-large"></section>

@include('userguide::layouts.business_areas.cta_section_first')

<!-- Spacing Section -->
<section class="fotra-spacing-section-large"></section>

<!-- Inventory Management Features Icons Section -->
@include('userguide::layouts.programs.inventory.inventory_management_features_icons_section')

<!-- Efficient Workflow Section -->
<section class="content-section">
    <div class="container">
        <div class="section-content">

            <div class="section-text">
                <h2>أدِر تدفق أعمال الموردين وعمليات الشراء بكفاءة</h2>
                <p>أنشئ ملفًا لكل مورد لديك يتضمن بيانات التواصل وطرق الدفع المتفق عليها وغيرها من البيانات المطلوبة
                    لتسهيل المعاملات. كما يتيح لك فوتره إضافة رصيد افتتاحي في حساب المورد وإصدار كشف حساب مفصل له.</p>
                <div style="text-align: right">
                    <button class="cta-button">ابدأ الاستخدام مجانًا</button>
                </div>

            </div>
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/manage-purchases.webp') }}"
                    alt="أدِر تدفق أعمال الموردين وعمليات الشراء بكفاءة">
            </div>
        </div>
    </div>
</section>

<!-- Purchase Invoices Section -->
<section class="content-section">
    <div class="container">
        <div class="section-content">
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/track-pis.webp') }}"
                    alt="أصدر فواتير الشراء لكل مورد لديك">
            </div>
            <div class="section-text">
                <h2>أصدر فواتير الشراء لكل مورد لديك</h2>
                <p>يوفر لك فوتره الأدوات اللازمة لإصدار فواتير الشراء وإرسالها إلى المورد سواء عبر البريد الإلكتروني أو
                    في حسابه على النظام، بالإضافة إلى تتبع دقيق للمدفوعات والكميات المستلمة.</p>
            </div>
        </div>
    </div>
</section>

<!-- Payments Tracking Section -->
<section class="content-section">
    <div class="container">
        <div class="section-content">

            <div class="section-text">
                <h2>تتبع المدفوعات من خلال كشف حساب مفصل</h2>
                <p>أصدر كشف حساب لكل مورد لديك على حدة، سواء كشف حساب ملخص أو مفصل حسب حاجة أعمالك لتتبع حالة كل فواتير
                    شراء تم إصدارها والمدفوعات ذات الصلة.</p>
            </div>
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/track-payments.webp') }}"
                    alt="تتبع المدفوعات من خلال كشف حساب مفصل">
            </div>
        </div>
    </div>
</section>

<!-- Reports Section -->
<section class="content-section">
    <div class="container">
        <div class="section-content">
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/keep-informed.webp') }}"
                    alt="كن على اطلاع دائم بمجريات أعمالك مع التقارير المفصلة">
            </div>
            <div class="section-text">
                <h2>كن على اطلاع دائم بمجريات أعمالك مع التقارير المفصلة</h2>
                <p>أصدر تقارير مفصلة لتتبع مشترياتك من كل مورد، وكذا الرصيد وفواتير الشراء وحالات الدفع وأنشطة الموظفين
                    ذوي الصلة.</p>
            </div>
        </div>
    </div>
</section>

<!-- Pricing Section -->
@include('userguide::layouts.programs.prices', [
    'title' => 'أسعار برنامج إدارة الموردين',
])

<!-- System Integration Section -->
@include('userguide::layouts.business_areas.system_integration')

<!-- Why Choose Us Section -->
@include('userguide::layouts.business_areas.why_choose_us')
