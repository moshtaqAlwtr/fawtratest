<!-- Purchase Management System Section -->
<section class="content-section" style="padding:0px"></section>
<section class="content-section">
    <div class="container">
        <div class="section-content">
            <div class="section-text">
                <h2>برنامج إدارة المشتريات</h2>
                <p>أدِر مشترياتك بكفاءة مع برنامج ادارة المشتريات من فوترة بواجهة سهلة الاستخدام تدعم اللغة العربية،
                    تواصل مع مورديك ونظم مدفوعاتك لهم باحترافية، وأصدر فواتير الشراء وفقًا للكميات في مخزونك؛ حيث يرسل
                    النظام إشعارًا عند وصول منتجاتك إلى حد معين، ونظم الصادر والوارد إلى مستودعك عبر الأذون المخزنية،
                    واحصل على تقارير مفصلة عن الجرد والمشتريات وحالات المدفوعات والموردين وغيرها.</p>
                <div style="text-align: right">
                    <button class="cta-button">ابدأ الاستخدام مجانًا</button>
                </div>
            </div>
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/page-banner-buyer.webp') }}" alt="برنامج إدارة المشتريات">
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

<!-- Create Purchase Invoices Section -->
<section class="content-section">
    <div class="container">
        <div class="section-content">
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/create-purchase.webp') }}"
                    alt="أنشئ فواتير الشراء باحترافية">
            </div>
            <div class="section-text">
                <h2>أنشئ فواتير الشراء باحترافية</h2>
                <p>تتبع كمية المنتجات في مخزونك، وأصدر فواتير الشراء وفقًا لها من خلال برنامج تسجيل فواتير المشتريات
                    للوفاء باحتياجات أعمالك وأرسلها إلى مورديك بسهولة؛ حدد المورد المطلوب من قاعدة بيانات الموردين لديك،
                    وأضف العناصر المراد شراؤها من قاعدة بيانات كاملة للمنتجات المسجلة في حسابك، وحدد طريقة الدفع
                    المستخدمة من بين العديد من خيارات الدفع التي يدعمها فوترة.</p>
                <div style="text-align: right">
                    <button class="cta-button">ابدأ الاستخدام مجانًا</button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Monitor Stock Section -->
<section class="content-section">
    <div class="container">
        <div class="section-content">
            <div class="section-text">
                <h2>راقب كمية المنتجات في مخزونك بدقة</h2>
                <p>تتبع كميات المنتجات في مستودعاتك بدقة من خلال أدوات فوترة المتقدمة وخصائص الأتمتة به، حيث يمكنك
                    متابعة كمية كل منتج أولًا بأول سواء عند إصدار فاتورة البيع أو من خلال إشعارات النظام عند انخفاض أحد
                    المنتجات إلى حد معين.</p>
            </div>
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/stock-balance.webp') }}"
                    alt="راقب كمية المنتجات في مخزونك بدقة">
            </div>
        </div>
    </div>
</section>

<!-- Warehouse Management Section -->
<section class="content-section">
    <div class="container">
        <div class="section-content">
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/track-quantity.webp') }}"
                    alt="نظم عمليات إضافة المنتجات في مستودعاتك باحترافية">
            </div>
            <div class="section-text">
                <h2>نظم عمليات إضافة المنتجات في مستودعاتك باحترافية</h2>
                <p>يتيح لك فوترة تجربة مرنة في إدارة كمية المنتجات التي تم شراؤها، وذلك عبر تأكيد استلام المنتجات مباشرة
                    من داخل فاتورة الشراء، أو عبر تفعيل الأذون المخزينة للمشتريات لإدارة أكثر احترافية، حيث يتم تسجيل
                    عملية الإضافة وتحديث كمية المنتجات في مخزونك بعد تأكيد الإذن المخزني الخاص بالفاتورة.</p>
            </div>
        </div>
    </div>
</section>

<!-- Payment Management Section -->
<section class="content-section">
    <div class="container">
        <div class="section-content">
            <div class="section-text">
                <h2>أدر مدفوعاتك إلى الموردين باحترافية</h2>
                <p>يوفر لك النظام أساليب متعددة لتسجيل عمليات الدفع على فواتير الشراء، بإمكانك تأكيد السداد مباشرة أثناء
                    إنشاء الفاتورة، أو قم بإضافة عملية الدفع في وقت لاحق بعد إصدار فاتورة الشراء، أو اترك للنظام تسجيل
                    عملية الدفع آلياً إن كنت تملك رصيداً دائناً لدى المورد.</p>
            </div>
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/manage-payments.webp') }}"
                    alt="أدر مدفوعاتك إلى الموردين باحترافية">
            </div>
        </div>
    </div>
</section>

<!-- Invoice Status Tracking Section -->
<section class="content-section">
    <div class="container">
        <div class="section-content">
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/customize-invoice.webp') }}"
                    alt="تتبع حالة فواتير الشراء وخصصها حسب احتياجات أعمالك">
            </div>
            <div class="section-text">
                <h2>تتبع حالة فواتير الشراء وخصصها حسب احتياجات أعمالك</h2>
                <p>اعرض ملخص حالات فواتير الشراء من برنامج المشتريات، وتعرف على المدفوع وغير المدفوع منها، وما تم تأكيد
                    استلامه أو لم يتم استلامه بعد، وقم بالتصفية والبحث عبر أدوات فلترة متقدمة للحصول على فواتيرك
                    المطلوبة بسرعة، وخصيص الحالات الخاصة بك لتناسب العمليات التي يتم إجراؤها في شركتك.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
@include('userguide::layouts.business_areas.cta_section')

<!-- Detailed Reports Section -->
<section class="content-section">
    <div class="container">
        <div class="section-content">
            <div class="section-text">
                <h2>أصدر التقارير التفصيلية بدقة</h2>
                <p>يوفر لك فوترة ميزة إصدار تقارير مفصلة من برنامج المشتريات لتتبع مشترياتك من مورديك وكذلك أداء موظفيك
                    وأمناء المخازن لديك ومتابعة المدفوعات الدورية وكمية المنتجات في مخزونك ومراقبة مستوياته، بما في ذلك
                    تقارير الجرد.</p>
            </div>
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/purchases-reports.webp') }}"
                    alt="أصدر التقارير التفصيلية بدقة">
            </div>
        </div>
    </div>
</section>

<!-- Pricing Section -->
@include('userguide::layouts.programs.prices', [
    'title' => 'أسعار برنامج إدارة المشتريات',
])

<!-- System Integration Section -->
@include('userguide::layouts.business_areas.system_integration')

<!-- Why Choose Us Section -->
@include('userguide::layouts.business_areas.why_choose_us')
